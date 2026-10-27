<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MaterialController extends Controller
{
    public function create(MataKuliah $course): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isDosen() || $course->dosen_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan dosen pengampu mata kuliah ini.');
        }

        $nextMinggu = ($course->materials()->max('minggu_ke') ?? 0) + 1;

        return view('materials.create', compact('course', 'nextMinggu'));
    }

    public function store(Request $request, MataKuliah $course): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isDosen() || $course->dosen_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan dosen pengampu mata kuliah ini.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'konten' => ['required', 'string'],
            'minggu_ke' => ['required', 'integer', 'between:1,16'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'judul.max' => 'Judul materi maksimal 255 karakter.',
            'konten.required' => 'Konten materi wajib diisi.',
            'minggu_ke.required' => 'Minggu pertemuan wajib diisi.',
            'minggu_ke.between' => 'Minggu pertemuan harus berada dalam rentang 1 hingga 16.',
            'urutan.min' => 'Nomor urut tidak boleh negatif.',
        ]);

        // Kolom urutan tidak boleh null, gunakan 0 jika dikosongkan
        $validated['urutan'] = $validated['urutan'] ?? 0;

        $course->materials()->create($validated);

        return redirect()->route('courses.show', $course->id)
            ->with('success', "Materi \"{$validated['judul']}\" berhasil ditambahkan.");
    }

    public function edit(Material $material): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isDosen() || $material->course->dosen_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan dosen pengampu mata kuliah materi ini.');
        }

        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isDosen() || $material->course->dosen_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan dosen pengampu mata kuliah materi ini.');
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'konten' => ['required', 'string'],
            'minggu_ke' => ['required', 'integer', 'between:1,16'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ], [
            'judul.required' => 'Judul materi wajib diisi.',
            'judul.max' => 'Judul materi maksimal 255 karakter.',
            'konten.required' => 'Konten materi wajib diisi.',
            'minggu_ke.required' => 'Minggu pertemuan wajib diisi.',
            'minggu_ke.between' => 'Minggu pertemuan harus berada dalam rentang 1 hingga 16.',
            'urutan.min' => 'Nomor urut tidak boleh negatif.',
        ]);

        // Kolom urutan tidak boleh null, gunakan 0 jika dikosongkan
        $validated['urutan'] = $validated['urutan'] ?? 0;

        $material->update($validated);

        return redirect()->route('courses.show', $material->course_id)
            ->with('success', "Materi \"{$material->judul}\" berhasil diperbarui.");
    }

    public function destroy(Material $material): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isDosen() || $material->course->dosen_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan dosen pengampu mata kuliah materi ini.');
        }

        // Simpan id mata kuliah sebelum materi dihapus agar redirect tetap valid
        $courseId = $material->course_id;
        $judul = $material->judul;

        $material->delete();

        return redirect()->route('courses.show', $courseId)
            ->with('success', "Materi \"{$judul}\" berhasil dihapus.");
    }
}
