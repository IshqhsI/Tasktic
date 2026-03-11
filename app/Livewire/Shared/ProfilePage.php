<?php

namespace App\Livewire\Shared;

// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Profil Saya')]
class ProfilePage extends Component
{
    // Tab aktif: 'info' | 'password'
    public string $tab = 'info';

    // Tab info
    public string $name = '';
    public string $email = '';
    public ?string $nim_nidn = null;

    // Tab password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->nim_nidn = $user->nim_nidn;
    }

    public function saveInfo(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'nim_nidn' => 'nullable|string|max:30',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah dipakai akun lain.',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'nim_nidn' => $this->nim_nidn ?: null,
        ]);

        $this->dispatch('toast', message: 'Profil berhasil diperbarui!', type: 'success');
    }

    public function savePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password lama tidak sesuai.');
            return;
        }

        $user->update(['password' => $this->new_password]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->dispatch('toast', message: 'Password berhasil diubah!', type: 'success');
    }

    public function render()
    {
        return view('livewire.shared.profile-page', [
            'user' => auth()->user(),
        ]);
    }
}
