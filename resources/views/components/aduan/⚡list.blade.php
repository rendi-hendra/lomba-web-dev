<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Aduan;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $kategori = 'semua';
    public $status = 'semua';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingKategori()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function with()
    {
        $query = Aduan::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_tiket', 'like', '%' . $this->search . '%')
                    ->orWhere('deskripsi_aduan', 'like', '%' . $this->search . '%')
                    ->orWhere('alamat', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status !== 'semua') {
            $query->where('status_aduan', $this->status);
        }

        return [
            'aduans' => $query->latest('waktu_pengaduan')->paginate(10),
            'totalCount' => Aduan::count(),
            'diajukanCount' => Aduan::where('status_aduan', 'diajukan')->count(),
            'prosesCount' => Aduan::where('status_aduan', 'diproses')->count(),
            'selesaiCount' => Aduan::where('status_aduan', 'selesai')->count(),
        ];
    }
};
?>

<div class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start">
    <div class="self-stretch flex flex-col md:flex-row justify-start items-stretch min-h-screen">
        {{-- Reusable Sidebar --}}
        <x-sidebar />

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50">
            {{-- Reusable Dashboard Topbar with mobile toggle --}}
            <x-dashboard-header title="Daftar Pengaduan Masuk" :badge="'Total ' . $totalCount" />

            <div class="p-8 flex flex-col gap-6 font-['Inter']">
                {{-- Quick Metric Pills --}}
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col gap-1">
                        <span class="text-xs text-slate-500">Semua Laporan</span>
                        <span class="text-2xl font-bold text-slate-900">{{ $totalCount }}</span>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col gap-1">
                        <span class="text-xs text-amber-600 font-medium">Diajukan</span>
                        <span class="text-2xl font-bold text-amber-600">{{ $diajukanCount }}</span>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col gap-1">
                        <span class="text-xs text-blue-600 font-medium">Sedang Diproses</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $prosesCount }}</span>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col gap-1">
                        <span class="text-xs text-emerald-600 font-medium">Terselesaikan</span>
                        <span class="text-2xl font-bold text-emerald-600">{{ $selesaiCount }}</span>
                    </div>
                </div>

                {{-- Filter & Search Form --}}
                <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-sm">
                    <form wire:submit.prevent class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                        <div class="sm:col-span-8 flex flex-col gap-1.5">
                            <label for="search" class="text-xs font-semibold text-slate-700">Cari nomor tiket /
                                deskripsi / lokasi</label>
                            <div class="relative">
                                <input type="search" id="search" wire:model.live.debounce.300ms="search"
                                    placeholder="Ketik kata kunci atau kode tiket..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-2 focus:outline-blue-800 transition-all" />
                                <div class="absolute left-3 top-3 text-slate-400">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-4 flex flex-col gap-1.5">
                            <label for="status" class="text-xs font-semibold text-slate-700">Status Aduan</label>
                            <select id="status" wire:model.live="status"
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-2 focus:outline-blue-800 transition-all">
                                <option value="semua">Semua Status</option>
                                <option value="diajukan">Diajukan</option>
                                <option value="diproses">Diproses</option>
                                <option value="diteruskan">Diteruskan</option>
                                <option value="selesai">Selesai</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Table View --}}
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">Tiket & Pengaduan</th>
                                    <th class="py-3.5 px-6">Lokasi</th>
                                    <th class="py-3.5 px-6">Status</th>
                                    <th class="py-3.5 px-6">Waktu Pengaduan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($aduans as $item)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="flex flex-col gap-1">
                                                <a href="/dashboard/aduan/{{ $item->id_aduan }}" wire:navigate>
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="text-xs font-mono font-medium text-blue-800 bg-blue-50 px-2 py-0.5 rounded">
                                                            {{ $item->kode_tiket }}
                                                        </span>
                                                    </div>
                                                    <span
                                                        class="font-normal text-slate-800 line-clamp-2 text-sm whitespace-pre-line">
                                                        {{ $item->deskripsi_aduan }}
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span
                                                class="text-xs font-medium text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md">
                                                {{ $item->alamat ?: 'Tidak disertakan' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            @php
                                                $statusClasses = match (strtolower($item->status_aduan ?? 'diajukan')) {
                                                    'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                    'diproses' => 'bg-blue-50 text-blue-800 border-blue-200',
                                                    'diteruskan' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                                    'ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                                                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusClasses }}">
                                                <span class="size-1.5 rounded-full bg-current"></span>
                                                {{ ucfirst($item->status_aduan ?? 'Diajukan') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                            {{ $item->waktu_pengaduan ? $item->waktu_pengaduan->format('d M Y, H:i') : ($item->created_at ? $item->created_at->format('d M Y, H:i') : '-') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 px-6 text-center">
                                            <div class="flex flex-col items-center justify-center gap-2 text-slate-500">
                                                <svg class="size-8 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                <span class="font-medium text-sm">Tidak ada data pengaduan yang
                                                    cocok</span>
                                                <span class="text-xs text-slate-400">Coba ubah filter pencarian Anda
                                                    atau
                                                    buat pengaduan baru.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($aduans->hasPages())
                        <div class="p-4 border-t border-slate-200 bg-slate-50">
                            {{ $aduans->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>