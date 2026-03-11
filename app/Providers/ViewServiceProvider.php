<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /*
         * Share $navGroups ke semua view yang extend layouts/app.blade.php
         * Data berbeda tergantung role user yang sedang login.
         */
        View::composer('layouts.app', function ($view) {
            if (!auth()->check())
                return;

            $role = auth()->user()->role;

            $view->with('navGroups', match ($role) {
                'admin' => $this->adminNav(),
                'dosen' => $this->dosenNav(),
                'mahasiswa' => $this->mahasiswaNav(),
                default => [],
            });
        });
    }

    // ── Nav Admin ─────────────────────────────────────────────

    private function adminNav(): array
    {
        return [
            [
                'label' => 'Main',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => route('admin.dashboard'),
                        'routeName' => 'admin.dashboard',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
                    ],
                ],
            ],
            [
                'label' => 'Master Data',
                'items' => [
                    [
                        'label' => 'Pengguna',
                        'route' => route('admin.users'),
                        'routeName' => 'admin.users',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                    ],
                    [
                        'label' => 'Program Studi',
                        'route' => route('admin.prodi'),
                        'routeName' => 'admin.prodi',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 0 1 .665 6.479A11.952 11.952 0 0 0 12 20.055a11.952 11.952 0 0 0-6.824-2.998 12.078 12.078 0 0 1 .665-6.479L12 14z"/></svg>',
                    ],
                    [
                        'label' => 'Tahun Ajaran',
                        'route' => route('admin.tahun-ajaran'),
                        'routeName' => 'admin.tahun-ajaran',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
                    ],
                    [
                        'label' => 'Semester',
                        'route' => route('admin.semester'),
                        'routeName' => 'admin.semester',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>',
                    ],
                    [
                        'label' => 'Kelas',
                        'route' => route('admin.kelas'),
                        'routeName' => 'admin.kelas',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>',
                    ],
                ],
            ],
            [
                'label' => 'Akun',
                'items' => [
                    [
                        'label' => 'Profil Saya',
                        'route' => route('admin.profil'),
                        'routeName' => 'admin.profil',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                    ],
                ],
            ],
        ];
    }

    // ── Nav Dosen ─────────────────────────────────────────────

    private function dosenNav(): array
    {
        return [
            [
                'label' => 'Main',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => route('dosen.dashboard'),
                        'routeName' => 'dosen.dashboard',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
                    ],
                ],
            ],
            [
                'label' => 'Akademik',
                'items' => [
                    [
                        'label' => 'Mata Kuliah',
                        'route' => route('dosen.matkul'),
                        'routeName' => 'dosen.matkul',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                    ],
                    [
                        'label' => 'Tugas',
                        'route' => route('dosen.tugas'),
                        'routeName' => 'dosen.tugas*',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2m-6 9 2 2 4-4"/></svg>',
                    ],
                ],
            ],
            [
                'label' => 'Akun',
                'items' => [
                    [
                        'label' => 'Profil Saya',
                        'route' => route('dosen.profil'),
                        'routeName' => 'dosen.profil',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                    ],
                ],
            ],
        ];
    }

    // ── Nav Mahasiswa ─────────────────────────────────────────

    private function mahasiswaNav(): array
    {
        return [
            [
                'label' => 'Main',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'route' => route('mahasiswa.dashboard'),
                        'routeName' => 'mahasiswa.dashboard',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
                    ],
                ],
            ],
            [
                'label' => 'Tugas',
                'items' => [
                    [
                        'label' => 'Daftar Tugas',
                        'route' => route('mahasiswa.tugas'),
                        'routeName' => 'mahasiswa.tugas*',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2m-6 9 2 2 4-4"/></svg>',
                    ],
                ],
            ],
            [
                'label' => 'Akun',
                'items' => [
                    [
                        'label' => 'Profil Saya',
                        'route' => route('mahasiswa.profil'),
                        'routeName' => 'mahasiswa.profil',
                        'icon' => '<svg style="width:18px;height:18px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                    ],
                ],
            ],
        ];
    }
}
