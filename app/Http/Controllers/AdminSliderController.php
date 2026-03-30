<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminSliderController extends Controller
{
    protected $sliderPath;

    public function __construct()
    {
        // Path to public/images/slider
        $this->sliderPath = public_path('images/slider');
    }

    /**
     * Display a listing of the images.
     */
    public function index()
    {
        // Ensure directory exists
        if (!File::exists($this->sliderPath)) {
            File::makeDirectory($this->sliderPath, 0755, true);
        }

        // Get all files in directory
        $files = File::files($this->sliderPath);
        
        $images = [];
        foreach ($files as $file) {
            // Only allow image extensions
            if (in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $images[] = [
                    'name' => $file->getFilename(),
                    'path' => asset('images/slider/' . $file->getFilename()),
                    'size' => round($file->getSize() / 1024, 2) . ' KB', // Size in KB
                ];
            }
        }

        return view('admin.slider.index', compact('images'));
    }

    /**
     * Store a newly uploaded image in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
        ]);

        if (!File::exists($this->sliderPath)) {
            File::makeDirectory($this->sliderPath, 0755, true);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Generate a unique name
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Move file to public/images/slider directory
            $image->move($this->sliderPath, $filename);

            return redirect()->route('admin.slider.index')->with('success', 'Gambar slider berhasil diunggah.');
        }

        return back()->withErrors(['image' => 'Gagal mengunggah gambar.']);
    }

    /**
     * Remove the specified image from storage.
     */
    public function destroy($filename)
    {
        $filePath = $this->sliderPath . DIRECTORY_SEPARATOR . $filename;

        // Prevent directory traversal attacks
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return back()->withErrors(['error' => 'Nama file tidak valid.']);
        }

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('admin.slider.index')->with('success', 'Gambar berhasil dihapus.');
        }

        return back()->withErrors(['error' => 'Gambar tidak ditemukan.']);
    }
}
