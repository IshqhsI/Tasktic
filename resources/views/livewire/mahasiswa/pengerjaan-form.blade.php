<div class="fade-in max-w-3xl"
     x-data="antiCheat()"
     x-init="init()"
     @visibilitychange.document="onVisibilityChange()"
     @beforeunload.window="onBeforeUnload($event)">

    {{-- ── Warning bar anti-cheat ── --}}
    <div x-show="warnings > 0" x-cloak
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        </svg>
        <p class="text-sm text-red-700 dark:text-red-400 font-500">
            Peringatan ke-<span x-text="warnings"></span>: Aktivitas mencurigakan terdeteksi.
            Dosen akan diberitahu.
        </p>
    </div>

    {{-- ── Header ── --}}
    <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-5 mb-5">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="font-display font-800 text-xl text-slate-800 dark:text-white">{{ $tugas->judul }}</h1>
                <p class="text-sm text-slate-400 mt-1">{{ $tugas->mataKuliah?->nama }}</p>
                @if($tugas->deskripsi)
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-3 leading-relaxed">{{ $tugas->deskripsi }}</p>
                @endif
            </div>
            {{-- Countdown --}}
            <div class="flex-shrink-0 text-right">
                <p class="text-xs text-slate-400">Deadline</p>
                <p class="font-700 text-slate-700 dark:text-white text-sm">{{ $tugas->deadline->format('d M Y') }}</p>
                <p class="text-xs font-600 text-amber-600 dark:text-amber-400">{{ $tugas->deadline->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    {{-- ── Soal & Jawaban ── --}}
    <div class="space-y-5">
        @foreach($soals as $soal)
        <div class="bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-5"
             wire:key="soal-{{ $soal->id }}">

            {{-- Nomor & pertanyaan --}}
            <div class="flex items-start gap-3 mb-4">
                <span class="w-7 h-7 rounded-lg bg-navy-100 dark:bg-navy-800 text-navy-700 dark:text-indigo-300 text-xs font-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                    {{ $soal->urutan }}
                </span>
                <p class="text-sm font-600 text-slate-700 dark:text-white leading-relaxed">
                    {{ $soal->pertanyaan }}
                </p>
            </div>

            {{-- Textarea jawaban --}}
            <div class="relative">
                <textarea
                    wire:model.lazy="jawabans.{{ $soal->id }}"
                    wire:change="saveDraft({{ $soal->id }})"
                    rows="6"
                    placeholder="Tulis jawaban Anda di sini..."
                    @paste="onPaste($event, {{ $soal->id }})"
                    @drop="onDrop($event, {{ $soal->id }})"
                    @dragover.prevent
                    @contextmenu.prevent
                    @keydown="onKeydown($event, {{ $soal->id }})"
                    @focus="onFocus({{ $soal->id }})"
                    @blur="onBlur({{ $soal->id }})"
                    autocomplete="off"
                    autocorrect="off"
                    spellcheck="false"
                    class="w-full bg-slate-50 dark:bg-navy-800 border border-slate-200 dark:border-navy-700 rounded-xl px-4 py-3 text-sm text-slate-700 dark:text-white outline-none focus:border-navy-400 transition-colors resize-none leading-relaxed"
                ></textarea>

                {{-- Character count --}}
                <div class="absolute bottom-3 right-3 text-[10px] text-slate-400">
                    <span x-text="charCount[{{ $soal->id }}] ?? 0"></span> karakter
                </div>
            </div>

            {{-- Auto-save indicator --}}
            <div class="mt-2 flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500" wire:loading.class="bg-amber-400" wire:loading.class.remove="bg-emerald-500" wire:target="saveDraft({{ $soal->id }})"></div>
                <span class="text-[11px] text-slate-400">
                    <span wire:loading wire:target="saveDraft({{ $soal->id }})">Menyimpan...</span>
                    <span wire:loading.remove wire:target="saveDraft({{ $soal->id }})">Draft tersimpan</span>
                </span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Submit ── --}}
    <div class="mt-6 bg-white dark:bg-navy-900 rounded-2xl border border-slate-100 dark:border-navy-800 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-600 text-slate-700 dark:text-white">Siap mengumpulkan?</p>
                <p class="text-xs text-slate-400 mt-0.5">
                    Pastikan semua soal sudah dijawab.
                    @if($tugas->allow_revision)
                    Anda masih bisa edit jawaban sebelum deadline.
                    @else
                    Setelah dikumpulkan tidak bisa diubah.
                    @endif
                </p>
            </div>
            <button
                wire:click="submit"
                wire:loading.attr="disabled"
                @click="onSubmit()"
                class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-600 bg-navy-700 dark:bg-navy-500 text-white hover:bg-navy-800 transition-colors disabled:opacity-60">
                <span wire:loading wire:target="submit">
                    <svg class="w-4 h-4 spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                </span>
                <svg wire:loading.remove wire:target="submit" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Kumpulkan Tugas
            </button>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════
     ANTI-CHEAT JAVASCRIPT
══════════════════════════════════════ --}}
@push('scripts')
<script>
function antiCheat() {
    return {
        warnings: 0,
        charCount: {},
        lastKeyCount: {},
        focusStart: null,
        activeSoalId: null,
        snapshotTimer: null,
        locked: false,

        init() {
            // Mulai snapshot timer setiap 30 detik
            this.snapshotTimer = setInterval(() => this.sendSnapshot(), 30000);

            // Update char count dari textarea yang sudah ada isinya (draft)
            document.querySelectorAll('textarea[wire\\:model\\.lazy]').forEach(el => {
                const soalId = this.getSoalId(el);
                if (soalId) this.charCount[soalId] = el.value.length;
            });
        },

        getSoalId(el) {
            const match = el.getAttribute('@focus')?.match(/onFocus\((\d+)\)/);
            if (match) return parseInt(match[1]);
            // Fallback: parse dari wire:model
            const model = el.getAttribute('wire:model.lazy');
            if (model) {
                const parts = model.split('.');
                return parseInt(parts[parts.length - 1]);
            }
            return null;
        },

        onFocus(soalId) {
            this.activeSoalId = soalId;
            this.focusStart = Date.now();
        },

        onBlur(soalId) {
            this.focusStart = null;
        },

        onKeydown(event, soalId) {
            // Update char count
            const el = event.target;
            this.$nextTick(() => {
                this.charCount[soalId] = el.value.length;
            });

            // Track keystroke count per soal
            if (!this.lastKeyCount[soalId]) this.lastKeyCount[soalId] = 0;
            this.lastKeyCount[soalId]++;
        },

        onPaste(event, soalId) {
            event.preventDefault();
            this.logActivity('paste_attempt', soalId);
            this.warnings++;
            this.showWarning('Paste dinonaktifkan. Ketik jawaban secara manual.');
        },

        onDrop(event, soalId) {
            event.preventDefault();
            this.logActivity('drop_attempt', soalId);
            this.warnings++;
            this.showWarning('Drag & drop dinonaktifkan. Ketik jawaban secara manual.');
        },

        onVisibilityChange() {
            if (document.hidden) {
                this.logActivity('tab_switch', this.activeSoalId);
                this.warnings++;
            }
        },

        onBeforeUnload(event) {
            if (!this.locked) {
                this.logActivity('drop_attempt', this.activeSoalId);
            }
        },

        onSubmit() {
            this.locked = true;
            clearInterval(this.snapshotTimer);
        },

        showWarning(message) {
            // Dispatch ke toast global di layout
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message, type: 'warning' }
            }));
        },

        async logActivity(aksi, soalId = null) {
            try {
                await fetch('{{ route("log.activity") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        tugas_id: {{ $tugas->id }},
                        aksi:     aksi,
                    }),
                });
            } catch (e) {
                // Silent fail — jangan ganggu pengerjaan mahasiswa
            }
        },

        async sendSnapshot() {
            if (this.locked) return;

            // Kumpulkan semua isi textarea saat ini
            const snapshots = {};
            document.querySelectorAll('textarea').forEach(el => {
                const model = el.getAttribute('wire:model.lazy');
                if (model) {
                    const parts = model.split('.');
                    const soalId = parts[parts.length - 1];
                    snapshots[soalId] = {
                        isi:        el.value,
                        karakter:   el.value.length,
                        keystrokes: this.lastKeyCount[soalId] ?? 0,
                    };
                }
            });

            try {
                await fetch('{{ route("snapshot") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        tugas_id:  {{ $tugas->id }},
                        snapshots: snapshots,
                    }),
                });
            } catch (e) {
                // Silent fail
            }
        },
    }
}
</script>
@endpush
