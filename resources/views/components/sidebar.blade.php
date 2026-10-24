@php
    $pendingCount = \App\Models\Aduan::whereIn('status_aduan', ['diajukan', 'diproses'])->count();
    $userName = auth()->user()->name ?? 'Petugas Desa';
    $userUsername = auth()->user()->username ?? 'admin';
@endphp

<div x-data="{ open: false }" 
     @open-sidebar.window="open = true" 
     @close-sidebar.window="open = false" 
     class="font-['Inter']">

    {{-- Backdrop for mobile off-canvas drawer --}}
    <div x-show="open" 
         x-cloak 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false" 
         class="lg:hidden fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40">
    </div>

    {{-- Sidebar Container --}}
    <aside :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:sticky top-0 inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between p-6 transition-transform duration-200 ease-in-out h-screen shrink-0 overflow-y-auto">
        
        <div class="flex flex-col gap-6">
            {{-- Logo and Mobile Close Button --}}
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center gap-2.5 hover:opacity-90 transition-opacity">
                    <div class="size-9 bg-blue-800 rounded-xl flex justify-center items-center shadow-xs">
                        <svg class="size-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-blue-800 text-lg font-bold tracking-tight block">SuaraDesa</span>
                        <span class="text-[10px] text-slate-400 font-medium -mt-1 block">Panel Pengelola</span>
                    </div>
                </a>

                {{-- Close Button for Mobile Drawer --}}
                <button type="button" @click="open = false" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Unit Pelayanan Info Box --}}
            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 flex flex-col gap-1">
                <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider">RUANG PELAYANAN</span>
                <span class="text-slate-900 text-sm font-semibold">Desa Sukamaju</span>
                <span class="text-slate-500 text-xs font-normal">Kecamatan Harapan</span>
            </div>

            {{-- Navigation Links --}}
            <nav class="flex flex-col gap-1.5">
                <a href="/dashboard"
                    class="px-3.5 py-2.5 rounded-lg flex items-center gap-3 transition-all {{ request()->is('dashboard') ? 'bg-blue-50 text-blue-800 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                    <svg class="size-5 {{ request()->is('dashboard') ? 'text-blue-800' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="text-sm">Dashboard</span>
                </a>

                <a href="/dashboard/aduan"
                    class="px-3.5 py-2.5 rounded-lg flex items-center justify-between transition-all {{ request()->is('dashboard/aduan*') ? 'bg-blue-50 text-blue-800 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                    <div class="flex items-center gap-3">
                        <svg class="size-5 {{ request()->is('dashboard/aduan*') ? 'text-blue-800' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span class="text-sm">Daftar Pengaduan</span>
                    </div>
                    @if($pendingCount > 0)
                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-blue-100 text-blue-800 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="/"
                    class="px-3.5 py-2.5 rounded-lg flex items-center gap-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors font-medium">
                    <svg class="size-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="text-sm">Portal Warga</span>
                </a>
            </nav>

            {{-- Demo Environment Note --}}
            <div class="p-3.5 bg-blue-50/70 rounded-xl border border-blue-100 flex items-start gap-2.5">
                <svg class="size-4 text-blue-800 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                <div class="flex flex-col gap-0.5">
                    <span class="text-blue-900 text-xs font-semibold">Simulasi Desa</span>
                    <span class="text-blue-700/80 text-[11px] leading-relaxed">Data terhubung ke sistem pengaduan & evaluasi respons waktu.</span>
                </div>
            </div>
        </div>

        {{-- User info & Logout --}}
        <div class="pt-4 border-t border-slate-200 flex flex-col gap-3">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-full bg-blue-100 text-blue-800 font-semibold text-sm flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($userName, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-semibold text-slate-900 truncate">{{ $userName }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ '@' . $userUsername }} · Admin</div>
                </div>
            </div>
            
            <form method="POST" action="/logout" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full px-3 py-2 bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-200 hover:border-rose-200 rounded-lg flex items-center justify-center gap-2 text-sm font-medium transition-colors cursor-pointer">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>
</div>
