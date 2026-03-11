# Catatan Setup Layout Tasktic

## 1. Daftarkan ViewServiceProvider

Tambahkan di `bootstrap/app.php`:

```php
->withProviders([
    App\Providers\ViewServiceProvider::class,
])
```

Atau di `config/app.php` bagian `providers`:

```php
'providers' => ServiceProvider::defaultProviders()->merge([
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class, // ← tambahkan ini
])->toArray(),
```

---

## 2. Cara pakai layout di Livewire Component

Setiap Livewire full-page component pakai layout seperti ini:

```php
// Di class component
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    ...
}
```

Atau via method:

```php
public function render()
{
    return view('livewire.admin.dashboard')
        ->layout('layouts.app', ['title' => 'Dashboard Admin']);
}
```

---

## 3. Cara dispatch Toast dari Livewire

Dari PHP (Livewire component):

```php
$this->dispatch('toast', message: 'Data berhasil disimpan!', type: 'success');
```

Dari Blade / Alpine:

```js
$dispatch('toast', { message: 'Berhasil!', type: 'success' })
```

Type yang tersedia: `success`, `error`, `warning`, `info`

---

## 4. Cara dispatch Confirm Modal dari Livewire

Dari Blade:

```html
<button @click="$dispatch('confirm', {
    title: 'Hapus Data?',
    message: 'Data ini akan dihapus permanen.',
    onConfirm: 'delete-confirmed',
    payload: { id: {{ $item->id }} }
})">
    Hapus
</button>
```

Di Livewire component, tangkap event-nya:

```php
#[On('delete-confirmed')]
public function deleteConfirmed(int $id): void
{
    // logika hapus...
    $this->dispatch('toast', message: 'Data dihapus!', type: 'success');
}
```

---

## 5. Struktur file yang sudah dibuat

```
app/
├── Providers/
│   ├── AppServiceProvider.php    ← LoginResponse binding
│   └── ViewServiceProvider.php   ← navGroups per role

resources/views/
└── layouts/
    └── app.blade.php             ← layout utama
```
