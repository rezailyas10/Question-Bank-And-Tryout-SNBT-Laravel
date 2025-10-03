<?php

namespace App\Http\Controllers;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class irtController extends Controller
{



    public function index()
    {
        // Generate sample data (equivalent to R code)
        $sampleData = $this->generateSampleData();
        
        // Calculate IRT parameters
        $irtResults = $this->calculateIRT($sampleData);
        
        return view('pages.hasil_irt', [
            'sampleData' => $sampleData,
            'irtResults' => $irtResults
        ]);
    }
    
   private function generateSampleData()
{
    // Set seed untuk hasil yang dapat direproduksi (sama seperti set.seed(123) di R)
    // Ini memastikan data random yang dihasilkan selalu sama setiap kali dijalankan
    mt_srand(123);
    
    $users = [];  // Array untuk menyimpan data user/responden
    $items = [];  // Array untuk menyimpan nama-nama item/soal
    
    // Generate 15 items (sama seperti colnames(data) <- paste0("Item_", 1:15) di R)
    // Loop untuk membuat 15 item dengan nama Item_1, Item_2, dst.
    for ($i = 1; $i <= 15; $i++) {
        $items[] = "Item_$i";
    }
    
    // Generate 100 users dengan responses (sama seperti matrix(sample(0:1, 100*15)) di R)
    // Loop untuk membuat 100 responden
    for ($user = 1; $user <= 100; $user++) {
        $responses = [];  // Array untuk menyimpan jawaban responden ini
        
        // Untuk setiap item, generate jawaban random 0 atau 1
        // 0 = salah, 1 = benar (binary response seperti di IRT)
        for ($item = 1; $item <= 15; $item++) {
            $responses["Item_$item"] = rand(0, 1);  // Random 0 atau 1
        }
        
        // Simpan data user dengan ID, nama, dan responses
        $users[] = [
            'user_id' => $user,
            'name' => "User $user",
            'responses' => $responses
        ];
    }
    
    // Return data dalam format yang mudah diproses
    return [
        'users' => $users,
        'items' => $items
    ];
}

private function calculateIRT($sampleData)
{
    // Ekstrak data users dan items dari sample data
    $users = $sampleData['users'];
    $items = $sampleData['items'];
    $numUsers = count($users);    // Jumlah responden (100)
    $numItems = count($items);    // Jumlah item (15)
    
    // STEP 1: Calculate item difficulties (parameter b dalam IRT)
    // Ini menghitung tingkat kesulitan setiap item berdasarkan proporsi yang menjawab benar
    $itemDifficulties = [];
    
    foreach ($items as $item) {
        $correct = 0;  // Counter untuk jawaban benar
        
        // Hitung berapa orang yang menjawab benar untuk item ini
        foreach ($users as $user) {
            if ($user['responses'][$item] == 1) {
                $correct++;
            }
        }
        
        // Hitung proporsi yang menjawab benar (p-value)
        $proportion = $correct / $numUsers;
        
        // Convert proportion ke logit scale untuk mendapat parameter difficulty (b)
        // Formula: b = -ln(p/(1-p)) dimana p = proporsi benar
        // Logit transformation ini mengubah proporsi (0-1) ke skala (-∞ to +∞)
        $difficulty = -log($proportion / (1 - $proportion + 0.001)); // +0.001 untuk hindari pembagian nol
        
        // Simpan hasil perhitungan difficulty untuk setiap item
        $itemDifficulties[$item] = [
            'proportion_correct' => $proportion,  // Proporsi yang benar (p-value)
            'difficulty' => $difficulty           // Parameter b (tingkat kesulitan)
        ];
    }
    
    // STEP 2: Calculate person abilities (parameter theta/θ) menggunakan Newton-Raphson
    // Theta adalah kemampuan laten setiap responden
    $personAbilities = [];
    
    foreach ($users as $user) {
        // Estimasi theta untuk setiap user menggunakan Maximum Likelihood Estimation
        $theta = $this->estimateTheta($user['responses'], $itemDifficulties);
        $personAbilities[$user['user_id']] = $theta;
    }
    
    // STEP 3: Calculate Z1 scores dan CEEB scores
    // Z1 = standardized theta, CEEB = scaled score seperti SAT
    $results = [];
    
    foreach ($users as $user) {
        $userId = $user['user_id'];
        $theta = $personAbilities[$userId];  // Ambil theta yang sudah dihitung
        
        // Z1 score adalah theta yang sudah distandarisasi
        // Dalam kasus ini, Z1 = theta (karena theta sudah dalam bentuk standar)
        $z1 = $theta;
        
        // Convert ke CEEB score (College Entrance Examination Board scale)
        // CEEB score: mean=500, SD=100, jadi formula: 500 + (100 * z1)
        // Ini sama seperti T-score tapi dengan mean 500 bukan 50
        $ceebScore = 500 + (100 * $z1);
        
        // Simpan semua hasil untuk user ini
        $results[] = [
            'user_id' => $userId,
            'user_name' => $user['name'],
            'responses' => $user['responses'],           // Jawaban asli (0/1)
            'raw_score' => array_sum($user['responses']), // Total skor mentah (jumlah benar)
            'theta' => round($theta, 4),                 // Kemampuan laten (θ)
            'z1' => round($z1, 4),                      // Z1 score (standardized theta)
            'ceeb_score' => round($ceebScore, 2)        // CEEB score (scaled score)
        ];
    }
    
    // Return semua hasil analisis IRT
    return [
        'item_difficulties' => $itemDifficulties,  // Parameter b untuk setiap item
        'person_results' => $results,              // Theta, Z1, CEEB untuk setiap person
        'summary_stats' => $this->calculateSummaryStats($results)  // Statistik deskriptif
    ];
}

private function estimateTheta($responses, $itemDifficulties)
{
    // NEWTON-RAPHSON METHOD untuk Maximum Likelihood Estimation of theta
    // Ini adalah iterative method untuk mencari nilai theta yang memaksimalkan likelihood
    
    $theta = 0.0;           // Starting value untuk theta (neutral ability)
    $maxIterations = 20;    // Maksimal iterasi untuk konvergensi
    $tolerance = 0.001;     // Kriteria konvergensi (selisih theta harus < 0.001)
    
    // Loop Newton-Raphson iterations
    for ($iter = 0; $iter < $maxIterations; $iter++) {
        $firstDerivative = 0;   // L'(θ) - first derivative of log-likelihood
        $secondDerivative = 0;  // L''(θ) - second derivative of log-likelihood
        
        // Hitung derivatives untuk semua items yang dijawab responden ini
        foreach ($responses as $item => $response) {
            $difficulty = $itemDifficulties[$item]['difficulty'];  // Parameter b item ini
            
            // RASCH MODEL (1PL): P(θ,b) = exp(θ-b) / (1 + exp(θ-b))
            // Ini adalah Item Response Function untuk model 1 Parameter Logistic
            $exponent = $theta - $difficulty;                    // θ - b
            $probability = exp($exponent) / (1 + exp($exponent)); // P(θ,b)
            
            // FIRST DERIVATIVE (Score function): ∂L/∂θ = Σ(x - P)
            // x adalah observed response (0/1), P adalah predicted probability
            $firstDerivative += $response - $probability;
            
            // SECOND DERIVATIVE (Information function): ∂²L/∂θ² = -Σ(P(1-P))
            // Ini adalah Fisher Information (negatif dari Hessian)
            $secondDerivative -= $probability * (1 - $probability);
        }
        
        // NEWTON-RAPHSON UPDATE: θ_new = θ_old - L'(θ)/L''(θ)
        // Ini adalah formula standar Newton-Raphson untuk optimasi
        if ($secondDerivative != 0) {
            $thetaNew = $theta - ($firstDerivative / $secondDerivative);
        } else {
            break;  // Jika second derivative = 0, stop iterasi
        }
        
        // CHECK CONVERGENCE: jika perubahan theta < tolerance, stop
        if (abs($thetaNew - $theta) < $tolerance) {
            $theta = $thetaNew;
            break;  // Konvergensi tercapai
        }
        
        $theta = $thetaNew;  // Update theta untuk iterasi berikutnya
    }
    
    return $theta;  // Return estimated theta (kemampuan laten responden)
}

private function calculateSummaryStats($results)
{
    // Extract semua nilai theta, CEEB scores, dan raw scores untuk statistik deskriptif
    $thetas = array_column($results, 'theta');        // Ambil semua theta
    $ceebScores = array_column($results, 'ceeb_score'); // Ambil semua CEEB scores
    $rawScores = array_column($results, 'raw_score');   // Ambil semua raw scores
    
    // Hitung statistik deskriptif untuk setiap jenis score
    return [
        'theta' => [
            'mean' => round(array_sum($thetas) / count($thetas), 4),        // Mean theta
            'std' => round($this->calculateStandardDeviation($thetas), 4),   // SD theta
            'min' => round(min($thetas), 4),                                // Min theta
            'max' => round(max($thetas), 4)                                 // Max theta
        ],
        'ceeb' => [
            'mean' => round(array_sum($ceebScores) / count($ceebScores), 2), // Mean CEEB
            'std' => round($this->calculateStandardDeviation($ceebScores), 2), // SD CEEB
            'min' => round(min($ceebScores), 2),                            // Min CEEB
            'max' => round(max($ceebScores), 2)                             // Max CEEB
        ],
        'raw_score' => [
            'mean' => round(array_sum($rawScores) / count($rawScores), 2),   // Mean raw score
            'std' => round($this->calculateStandardDeviation($rawScores), 2), // SD raw score
            'min' => min($rawScores),                                       // Min raw score
            'max' => max($rawScores)                                        // Max raw score
        ]
    ];
}

private function calculateStandardDeviation($values)
{
    // FUNGSI untuk menghitung Standard Deviation (simpangan baku)
    
    // Step 1: Hitung mean (rata-rata)
    $mean = array_sum($values) / count($values);
    
    // Step 2: Hitung squared differences dari mean
    // (xi - x̄)² untuk setiap nilai
    $squaredDifferences = array_map(function($value) use ($mean) {
        return pow($value - $mean, 2);  // (xi - x̄)²
    }, $values);
    
    // Step 3: Hitung variance (rata-rata dari squared differences)
    // σ² = Σ(xi - x̄)² / N
    $variance = array_sum($squaredDifferences) / count($values);
    
    // Step 4: Standard deviation adalah akar dari variance
    // σ = √σ²
    return sqrt($variance);
}
}



