<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time() . '-' . $file->getClientOriginalName();

            // Simpan ke public/storage/uploads
            $path = $file->storeAs('public/uploads', $filename);

            // URL ke file
            $url = Storage::url($path);

            return response()->json([
                'url' => $url
            ]);
        }

        return response()->json(['error' => ['message' => 'Tidak ada file yang diunggah']], 400);
    }

    public function delete(Request $request)
{
    $src = $request->input('src');

    if (!$src) {
        return response()->json(['message' => 'No image src provided'], 400);
    }

    // Hapus berdasarkan path relatif
    $relativePath = str_replace('/storage/', '', parse_url($src, PHP_URL_PATH));

    if (Storage::exists('public/' . $relativePath)) {
        Storage::delete('public/' . $relativePath);
        return response()->json(['message' => 'Image deleted']);
    }

    return response()->json(['message' => 'File not found'], 404);
}


}
