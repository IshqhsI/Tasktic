<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\Prodi;
use App\Models\Semester;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->getStats(),
            'recentUsers' => $this->getRecentUsers(),
            'semesterAktif' => Semester::getActive()?->nama_lengkap ?? '—',
        ]);
    }

    private function getStats(): array
    {
        return [
            [
                'label' => 'Total Dosen',
                'value' => User::where('role', 'dosen')->count(),
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="7" r="4"/><path stroke-linecap="round" stroke-linejoin="round" d="M5.5 21a7.5 7.5 0 0 1 13 0"/></svg>',
                'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                'color' => 'text-blue-600 dark:text-blue-400',
            ],
            [
                'label' => 'Total Mahasiswa',
                'value' => User::where('role', 'mahasiswa')->count(),
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                'color' => 'text-emerald-600 dark:text-emerald-400',
            ],
            [
                'label' => 'Program Studi',
                'value' => Prodi::count(),
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0v6"/></svg>',
                'bg' => 'bg-purple-50 dark:bg-purple-900/20',
                'color' => 'text-purple-600 dark:text-purple-400',
            ],
            [
                'label' => 'Kelas Aktif',
                'value' => Kelas::whereHas('semester', fn($q) => $q->where('is_active', true))->count(),
                'icon' => '<svg style="width:20px;height:20px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9h18M9 21V9"/></svg>',
                'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                'color' => 'text-amber-600 dark:text-amber-400',
            ],
        ];
    }

    private function getRecentUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with('prodi', 'kelas')
            ->whereIn('role', ['dosen', 'mahasiswa'])
            ->latest()
            ->take(8)
            ->get();
    }
}
