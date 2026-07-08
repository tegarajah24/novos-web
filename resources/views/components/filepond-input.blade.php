@props([
    'name' => 'file',
    'id' => null,
    'label' => 'Upload File',
    'accept' => 'image/*',
    'multiple' => false,
    'maxFileSize' => '5MB',
    'required' => false,
    'hint' => null,
])

@php
    $inputId = $id ?? 'filepond-' . \Illuminate\Support\Str::slug(str_replace(['[', ']', '.'], '-', $name));
@endphp

<div>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    @endif

    <input
        type="file"
        class="filepond"
        name="{{ $name }}"
        id="{{ $inputId }}"
        accept="{{ $accept }}"
        @if($multiple) multiple @endif
        @if($required) required @endif
        data-max-file-size="{{ $maxFileSize }}"
    >

    @if($hint)
        <p class="text-xs text-gray-400 mt-1">{{ $hint }}</p>
    @endif
</div>
