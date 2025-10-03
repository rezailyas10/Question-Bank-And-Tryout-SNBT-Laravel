<?php
// config/irt.php - Konfigurasi untuk IRT Analysis

return [
     'executable_path' => 'C:\\Program Files\\R\\R-4.5.0\\bin\\Rscript.exe',
    'timeout' => 300,
    'script_path' => storage_path('app/public/app/scripts/irt_analysis.R'),
    
    'validation' => [
        'min_sample_size' => 10,
        'max_sample_size' => 1000,
        'min_items' => 5,
        'max_items' => 50,
    ],
    
    'simulation' => [
        'theta_range' => [-2, 2],
        'difficulty_range' => [-1.5, 1.5],
        'discrimination_range' => [0.5, 2.0],
    ]
];


