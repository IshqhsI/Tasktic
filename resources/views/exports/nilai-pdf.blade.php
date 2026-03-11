<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nilai — {{ $tugas->judul }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: white;
            padding: 32px;
        }

        /* Header */
        .header {
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 800;
            color: #1e3a5f;
        }
        .header .meta {
            margin-top: 6px;
            color: #64748b;
            font-size: 11px;
            display: flex;
            gap: 16px;
        }
        .header .meta span { display: flex; align-items: center; gap: 4px; }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            text-align: center;
        }
        .stat-card .val {
            font-size: 20px;
            font-weight: 800;
            color: #1e3a5f;
        }
        .stat-card .lbl {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #f1f5f9;
        }
        th {
            text-align: left;
            padding: 8px 10px;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-a { background: #dcfce7; color: #16a34a; }
        .badge-b { background: #dbeafe; color: #2563eb; }
        .badge-c { background: #fef9c3; color: #ca8a04; }
        .badge-d { background: #ffedd5; color: #ea580c; }
        .badge-e { background: #fee2e2; color: #dc2626; }
        .badge-none { background: #f1f5f9; color: #94a3b8; }

        .anomali-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
            background: #fef9c3;
            color: #ca8a04;
        }
        .anomali-high {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* Print */
        @media print {
            body { padding: 16px; }
            .no-print { display: none !important; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>

    {{-- Print button (hilang saat print) --}}
    <div class="no-print" style="margin-bottom:20px; display:flex; gap:8px;">
        <button onclick="window.print()"
                style="padding:8px 20px; background:#1e3a5f; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:12px;">
            🖨️ Cetak / Save as PDF
        </button>
        <button onclick="window.close()"
                style="padding:8px 16px; background:#f1f5f9; color:#64748b; border:none; border-radius:8px; font-weight:600; cursor:pointer; font-size:12px;">
            Tutup
        </button>
    </div>

    {{-- Header --}}
    <div class="header">
        <h1>Rekap Nilai — {{ $tugas->judul }}</h1>
        <div class="meta">
            <span>Mata Kuliah: <strong>{{ $tugas->mataKuliah?->nama }}</strong></span>
            <span>Kode: <strong>{{ $tugas->mataKuliah?->kode }}</strong></span>
            <span>Deadline: <strong>{{ $tugas->deadline->format('d M Y, H:i') }}</strong></span>
            <span>Dicetak: <strong>{{ now()->format('d M Y, H:i') }}</strong></span>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats">
        <div class="stat-card">
            <div class="val">{{ $stats['total'] }}</div>
            <div class="lbl">Total Mahasiswa</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $stats['dinilai'] }}</div>
            <div class="lbl">Sudah Dinilai</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $stats['rata'] }}</div>
            <div class="lbl">Rata-rata</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $stats['tertinggi'] }}</div>
            <div class="lbl">Tertinggi</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ $stats['terendah'] }}</div>
            <div class="lbl">Terendah</div>
        </div>
    </div>

    {{-- Tabel nilai --}}
    <table>
        <thead>
            <tr>
                <th style="width:36px">No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Prodi / Kelas</th>
                <th style="width:60px; text-align:center">Nilai</th>
                <th style="width:50px; text-align:center">Kat.</th>
                <th>Komentar</th>
                {{-- <th style="width:80px">Anomali</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswas as $i => $mahasiswa)
            @php
                $penilaian = $penilaians->get($mahasiswa->id);
                $nilai     = $penilaian?->nilai;
                $anomali   = $anomalis->get($mahasiswa->id, 0);

                $kat = match(true) {
                    is_null($nilai) => ['—',    'badge-none'],
                    $nilai >= 85    => ['A',     'badge-a'],
                    $nilai >= 70    => ['B',     'badge-b'],
                    $nilai >= 56    => ['C',     'badge-c'],
                    $nilai >= 41    => ['D',     'badge-d'],
                    default         => ['E',     'badge-e'],
                };
            @endphp
            <tr>
                <td style="color:#94a3b8">{{ $i + 1 }}</td>
                <td style="font-weight:600">{{ $mahasiswa->name }}</td>
                <td style="color:#64748b">{{ $mahasiswa->nim_nidn ?? '—' }}</td>
                <td style="color:#64748b; font-size:11px">
                    {{ $mahasiswa->prodi?->nama ?? '—' }}<br>
                    <span style="color:#94a3b8">{{ $mahasiswa->kelas?->nama ?? '—' }}</span>
                </td>
                <td style="text-align:center; font-weight:700; font-size:14px">
                    {{ $nilai ?? '—' }}
                </td>
                <td style="text-align:center">
                    <span class="badge {{ $kat[1] }}">{{ $kat[0] }}</span>
                </td>
                <td style="color:#64748b; font-size:11px; font-style:italic">
                    {{ $penilaian?->komentar ?? '—' }}
                </td>
                {{-- <td>
                    @if($anomali > 0)
                    <span class="anomali-badge {{ $anomali >= 5 ? 'anomali-high' : '' }}">
                        {{ $anomali }}×
                    </span>
                    @else
                    <span style="color:#94a3b8">—</span>
                    @endif
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <span>Tasktic — Sistem Pengumpulan Tugas</span>
        <span>Dosen: {{ auth()->user()->name }} · {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</body>
</html>
