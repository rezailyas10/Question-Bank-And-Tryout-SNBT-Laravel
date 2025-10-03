<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
{
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // Simpan file tanpa mengubah nama
        // Namun hati-hati kalau file dengan nama sama sudah ada, bisa overwrite
        $path = $file->storeAs('public/images', $originalName);

        // Dapatkan url publik dari file yang sudah disimpan
        $url = Storage::url($path);

        return response()->json(['location' => $url]);
    }

    return response()->json(['error' => 'No file uploaded.'], 400);
}



    /**
     * Browse uploaded images (optional - for file browser)
     */
    public function deleteImage($url)
{
    // Contoh input: $url = '/storage/images/namafile.jpg';

    // Hilangkan prefix /storage/ supaya jadi path relatif dalam storage disk 'public'
    $relativePath = str_replace('/storage/', '', $url);

    if (Storage::disk('public')->exists($relativePath)) {
        Storage::disk('public')->delete($relativePath);
        return response()->json(['success' => 'File deleted']);
    } else {
        return response()->json(['error' => 'File not found'], 404);
    }
}
}
