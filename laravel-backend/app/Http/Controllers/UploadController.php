<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle single image upload
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();

        // ✅ Generate unique file name
        $filename = time() . '_' . Str::random(10) . '.' . $extension;

        // ✅ Store file in "public/uploads"
        $path = $file->storeAs('uploads', $filename, 'public');

        // ✅ Return only the relative storage path (no domain)
        return response()->json([
            'path' => 'storage/' . $path,
            'filename' => $filename,
            'message' => 'Image uploaded successfully',
        ]);
    }

    /**
     * Handle file deletion
     */
    public function destroy($filename)
    {
        $path = 'uploads/' . $filename;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json(['message' => 'Image deleted successfully']);
        }

        return response()->json(['message' => 'File not found'], 404);
    }
}
