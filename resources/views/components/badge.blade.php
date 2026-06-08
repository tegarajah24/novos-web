@props(['type' => 'gray'])

@php
    $classes = match($type) {
        'blue' => 'bg-blue-100 text-blue-700',
        'yellow' => 'bg-yellow-100 text-yellow-700',
        'green' => 'bg-green-100 text-green-700',
        'red' => 'bg-red-100 text-red-700',
        'purple' => 'bg-purple-100 text-purple-700',
        'orange' => 'bg-orange-100 text-orange-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-2.5 py-1 text-xs font-semibold rounded-full $classes"]) }}>
    {{ $slot }}
</span>
