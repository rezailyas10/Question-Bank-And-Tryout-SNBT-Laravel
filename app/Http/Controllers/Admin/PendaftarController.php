<?php

namespace App\Http\Controllers\admin;

use App\Models\Result;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RegistrationITI;
use App\Http\Controllers\Controller;

class PendaftarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = RegistrationITI::with(['result.user', 'result.exam']);
        $totalRegistrations = RegistrationITI::count();
        $contactedCount = RegistrationITI::where('status', 'sudah dihubungi')->count();
        $notContactedCount = RegistrationITI::where('status', 'belum dihubungi')->count();

        if ($request->filled('search')) {
    $query->whereHas('result.user', function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%');
    });
}

        $registrations = $query
            ->latest()
            ->simplePaginate(10)      // ← pastikan ini dipanggil di atas, TANPA ->get()
            ->withQueryString();

      

        return view('pages.sales.pendaftar.index', compact(
            'totalRegistrations',
            'contactedCount',
            'notContactedCount',
            'registrations'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $registration = RegistrationIti::with([
            'result.user', 
            'result.exam', 
            'result.aiEvaluation'
        ])->findOrFail($id);

        // Get recommended majors sama seperti di create method
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

        $recommendedMajors = $registration->result->aiEvaluation
            ->pluck('recommendation')
            ->implode("\n");

        $recommendedMajors = collect($majors)
            ->filter(function ($major) use ($recommendedMajors) {
                return Str::contains($recommendedMajors, $major . ' (S1 - Institut Teknologi Indonesia)');
            })
            ->values()
            ->all();

        return view('pages.sales.pendaftar.edit', compact( 'registration', 'majors', 'recommendedMajors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:belum dihubungi,sudah dihubungi',
            'keterangan' => 'nullable|string',
        ]);

        $registration = RegistrationIti::findOrFail($id);
        
        $registration->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pendaftar.edit', $id)
            ->with('success', 'Status pendaftaran berhasil diupdate!');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
