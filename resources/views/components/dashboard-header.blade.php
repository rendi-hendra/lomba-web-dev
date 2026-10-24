@props(['title' => 'Dashboard', 'badge' => null, 'backUrl' => null])

<header class="h-20 px-4 sm:px-8 bg-white border-b border-slate-200 flex justify-between items-center shrink-0 sticky top-0 z-30 font-['Inter']">
    <div class="flex items-center gap-2 sm:gap-3 min-w-0">
        {{-- Mobile Hamburger to Open Sidebar --}}
        <button type="button" @click="$dispatch('open-sidebar')" class="lg:hidden p-2 -ml-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer shrink-0" aria-label="Buka Menu">
            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        {{-- Optional Back Link --}}
        @if($backUrl)
            <a href="{{ $backUrl }}" class="p-2 -ml-1 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors shrink-0" title="Kembali">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
        @endif

        <div class="flex items-center gap-2 min-w-0">
            <h1 class="text-sm sm:text-base md:text-lg font-bold text-slate-900 truncate">{{ $title }}</h1>
            @if($badge)
                <span class="px-2 py-0.5 bg-blue-50 text-blue-800 text-xs font-semibold rounded-md border border-blue-100 shrink-0 font-mono">
                    {{ $badge }}
                </span>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3 sm:gap-4 shrink-0">
        <a href="/" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-blue-800 transition-colors">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Portal Warga</span>
        </a>

        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

        <div class="flex items-center gap-2">
            <div class="size-8 rounded-full bg-blue-100 text-blue-800 font-bold text-xs flex items-center justify-center shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
            </div>
            <div class="hidden sm:flex flex-col">
                <span class="text-xs font-bold text-slate-900 leading-tight truncate max-w-[120px]">{{ auth()->user()->name ?? 'Administrator' }}</span>
                <span class="text-[10px] text-emerald-600 font-semibold leading-tight">● Online</span>
            </div>
        </div>
    </div>
</header>
