<?php

namespace App\Http\Controllers;

use App\Services\AttendanceRecorder;
use App\Services\RoleHierarchy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request, RoleHierarchy $roleHierarchy): View
    {
        $user = $request->user();
        $attendances = $roleHierarchy->visibleAbsensi($user)
            ->with(['karyawan.team.departemen', 'karyawan.position'])
            ->latest('tanggal')
            ->latest('waktu_masuk')
            ->paginate(20);

        return view('attendances.index', compact('attendances'));
    }

    public function store(Request $request, AttendanceRecorder $recorder): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->can('attendance.create'), 403);
        abort_unless($user->karyawan_id, 403, 'Akun ini belum terhubung dengan data karyawan.');

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'waktu_masuk' => ['required', 'date_format:H:i'],
            'waktu_pulang' => ['nullable', 'date_format:H:i'],
            'alasan_overtime' => ['nullable', 'string', 'max:255'],
            'foto_masuk' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'foto_pulang' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $fotoMasuk = $request->file('foto_masuk')?->store('absensi/foto_masuk', 'public');
        $fotoPulang = $request->file('foto_pulang')?->store('absensi/foto_pulang', 'public');

        try {
            $recorder->create([
                ...$validated,
                'karyawan_id' => $user->karyawan_id,
                'foto_masuk' => $fotoMasuk,
                'foto_pulang' => $fotoPulang,
            ]);
        } catch (ValidationException $exception) {
            Storage::disk('public')->delete(array_filter([$fotoMasuk, $fotoPulang]));

            throw $exception;
        }

        return redirect()
            ->route('attendances.index')
            ->with('status', 'Absensi berhasil disimpan.');
    }
}
