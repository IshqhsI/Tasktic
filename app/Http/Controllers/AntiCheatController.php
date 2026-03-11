<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Services\AntiCheatService;
use Illuminate\Http\Request;

class AntiCheatController extends Controller
{
    public function __construct(private AntiCheatService $service)
    {
    }

    /**
     * POST /log-activity
     * Terima log aksi dari JS: paste, tab_switch, page_leave.
     * Rate limit: 30/menit (dikonfigurasi di routes/web.php)
     */
    public function logActivity(Request $request)
    {
        $data = $request->validate([
            'tugas_id' => 'required|integer|exists:tugas,id',
            'aksi' => 'required|string|max:50',
        ]);

        $mahasiswa = auth()->user();

        $tugas = Tugas::findOrFail($data['tugas_id']);
        abort_unless($tugas->mataKuliah->hasMahasiswa($mahasiswa->id), 403);

        $this->service->logActivity(
            $mahasiswa->id,
            $data['tugas_id'],
            $data['aksi']
        );

        return response()->json(['ok' => true]);
    }

    /**
     * POST /snapshot
     * Terima snapshot tiap 30 detik dari JS.
     * Rate limit: 10/menit (dikonfigurasi di routes/web.php)
     */
    public function snapshot(Request $request)
    {
        $data = $request->validate([
            'tugas_id' => 'required|integer|exists:tugas,id',
            'snapshots' => 'required|array',
            'snapshots.*.karakter' => 'required|integer|min:0',
            'snapshots.*.keystrokes' => 'required|integer|min:0',
        ]);

        $mahasiswa = auth()->user();

        $tugas = Tugas::findOrFail($data['tugas_id']);
        abort_unless($tugas->mataKuliah->hasMahasiswa($mahasiswa->id), 403);

        $this->service->processSnapshots(
            $mahasiswa->id,
            $data['snapshots']
        );

        return response()->json(['ok' => true]);
    }
}
