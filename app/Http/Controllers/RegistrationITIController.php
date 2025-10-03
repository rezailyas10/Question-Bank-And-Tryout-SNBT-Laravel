<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Models\Result;
use Illuminate\Http\Request;
use App\Models\RegistrationITI;

class RegistrationITIController extends Controller
{
    public function create($result_id)
    {
        $result = Result::with('exam', 'aiEvaluation', 'user')->findOrFail($result_id);

       $periods = [];

for ($i = 0; $i < 10; $i++) {
    $tahunAwal = 2025 + $i;
    $tahunAkhir = $tahunAwal + 1;

    $periods[] = "$tahunAwal/$tahunAkhir Ganjil";
    $periods[] = "$tahunAwal/$tahunAkhir Genap";
}

        $majors = [
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Arsitektur',
            'Teknik Kimia',
            'Teknik Industri',
            'Perencanaan Wilayah Dan Kota',
            'Teknologi Industri Pertanian',
            'Teknik Informatika',
            'Manajemen',
        ];

          $recommendedMajors = $result->aiEvaluation
    ->pluck('recommendation')
    ->implode("\n"); // Gabungkan semua jadi 1 string besar

$recommendedMajors = collect($majors)
    ->filter(function ($major) use ($recommendedMajors) {
        return Str::contains($recommendedMajors, $major . ' (S1 - Institut Teknologi Indonesia)');
    })
    ->values()
    ->all();

        return view('pages.tryout.registration-iti', compact('result', 'periods', 'majors','recommendedMajors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'result_id' => 'required|exists:results,id',
            'periode_akademik' => 'required|string',
            'program_studi' => 'required|string',
            'agree_to_contact' => 'nullable|boolean',
        ]);

        if (RegistrationITI::where('result_id', $request->result_id)->exists()) {
            return back()->withErrors(['msg' => 'Kamu sudah mengajukan pendaftaran untuk hasil tryout ini.']);
        }

        RegistrationITI::create([
            'result_id' => $request->result_id,
            'periode_akademik' => $request->periode_akademik,
            'program_studi' => $request->program_studi,
            'agree_to_contact' => $request->boolean('agree_to_contact'),
            'status'           => 'belum dihubungi', 
            'keterangan'       => null,
        ]);

        $result = Result::find($request->result_id);

    return redirect()->route(
        'tryout-result',
        ['exam' => $result->exam_id, 'id' => $result->id]  
    )->with('success', 'Pendaftaran berhasil diajukan.');
    }
}
