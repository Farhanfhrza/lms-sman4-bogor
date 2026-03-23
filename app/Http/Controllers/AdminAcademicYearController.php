<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminAcademicYearController extends Controller
{
    public function index(): View
    {
        $academicYears = AcademicYear::orderByDesc('name')->get();

        $breadcrumbs = [
            ['label' => 'Tahun Ajaran'],
        ];

        return view('admin.academic-years.index', compact('academicYears', 'breadcrumbs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20|unique:academic_years,name',
        ], [
            'name.required' => 'Nama Tahun Ajaran wajib diisi.',
            'name.unique' => 'Tahun Ajaran dengan nama ini sudah ada.',
        ]);

        $academicYear = AcademicYear::create([
            'name' => $validated['name'],
            'is_active' => false, // Default gracefully to false
        ]);

        ActivityLogger::log(null, 'created', $academicYear, "Menambahkan Tahun Ajaran baru: {$academicYear->name}");

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun Ajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:20|unique:academic_years,name,' . $academicYear->id,
        ], [
            'name.required' => 'Nama Tahun Ajaran wajib diisi.',
            'name.unique' => 'Tahun Ajaran dengan nama ini sudah ada.',
        ]);

        $oldName = $academicYear->name;
        $academicYear->update($validated);

        ActivityLogger::log(null, 'updated', $academicYear, "Memperbarui nama Tahun Ajaran {$oldName} menjadi {$academicYear->name}");

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun Ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->is_active) {
            return redirect()->route('admin.academic-years.index')->with('error', 'Tidak dapat menghapus Tahun Ajaran yang sedang aktif.');
        }

        // Warning: This could cascade delete school_classes, class_subjects, etc.
        // It's usually better to soft delete or prevent deletion if it has relations.
        if ($academicYear->classes()->exists()) {
             return redirect()->route('admin.academic-years.index')->with('error', 'Tidak dapat menghapus Tahun Ajaran ini karena sudah memiliki data kelas.');
        }

        $name = $academicYear->name;
        $academicYear->delete();

        ActivityLogger::log(null, 'deleted', null, "Menghapus Tahun Ajaran: {$name}");

        return redirect()->route('admin.academic-years.index')->with('success', 'Tahun Ajaran berhasil dihapus.');
    }

    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        \Log::info('Activate method called for AcademicYear ID: ' . $academicYear->id);

        if ($academicYear->is_active) {
            \Log::info('AcademicYear is already active');
            return redirect()->route('admin.academic-years.index')->with('success', 'Tahun Ajaran sudah aktif.');
        }

        DB::transaction(function () use ($academicYear) {
            // Deactivate all
            $updatedRows = AcademicYear::where('is_active', true)->update(['is_active' => false]);
            \Log::info("Deactivated $updatedRows existing active years");
            
            // Activate selected
            $academicYear->is_active = true;
            $academicYear->save();
            \Log::info('Set target AcademicYear to active. Savings result: ' . ($academicYear->wasChanged('is_active') ? 'true' : 'false'));
        });

        ActivityLogger::log(null, 'updated', $academicYear, "Mengaktifkan Tahun Ajaran: {$academicYear->name}");

        return redirect()->route('admin.academic-years.index')->with('success', "Tahun Ajaran {$academicYear->name} berhasil diaktifkan.");
    }
}
