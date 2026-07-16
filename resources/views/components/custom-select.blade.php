@props([
    'options' => null,
    'optionsKey' => null,
    'placeholder' => '-- Pilih --',
    'size' => 'default',
])

@php
    $sizeClasses = match($size) {
        'sm' => 'px-2.5 py-1.5 text-xs',
        'lg' => 'px-4 py-3 text-base',
        default => 'px-3 py-2 text-sm',
    };
    $xModel = $attributes->get('xModel', '');
    $optionsExpr = $optionsKey ? $optionsKey : json_encode($options ?? []);
@endphp

<div x-data="{ open: false }" @click.away="open = false" class="relative">
    <button type="button" @click="open = !open"
        class="w-full border border-gray-200 rounded-lg {{ $sizeClasses }} text-left bg-white flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 transition-colors hover:border-gray-300 cursor-pointer">
        <span :class="{{ $xModel }} ? 'text-gray-700' : 'text-gray-400'"
            x-text="{{ $xModel }} ? ({{ $xModel }} === '' ? '{{ $placeholder }}' : (({{ $optionsExpr }}).find(o => o.value === {{ $xModel }})?.label || '{{ $placeholder }}')) : '{{ $placeholder }}'"></span>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0 ml-2" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
        <div class="max-h-48 overflow-y-auto">
            <template x-for="opt in ({{ $optionsExpr }})" :key="opt.value">
                <button type="button" @click="{{ $xModel }} = opt.value; open = false"
                    class="w-full px-3 py-2 text-sm text-left hover:bg-[#1a237e]/5 transition-colors flex items-center gap-2"
                    :class="{{ $xModel }} === opt.value ? 'bg-[#1a237e]/10 text-[#1a237e] font-semibold' : 'text-gray-700'">
                    <svg x-show="{{ $xModel }} === opt.value" class="w-4 h-4 text-[#1a237e] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="opt.label"></span>
                </button>
            </template>
        </div>
    </div>
</div>
