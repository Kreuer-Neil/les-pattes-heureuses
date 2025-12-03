@props([
    'name',
    'type' => 'text',
    'folder' => '',
])

@php
    $textarea = 'textarea';
    $inputType = match ($type) {
        default => 'text',
        $textarea => $textarea,
    };

    $isTextarea = ($textarea === $inputType);

     $id = $id ?? $name;
     $placeholder = $placeholder ?? $name;
@endphp

<div {{ $attributes->merge(['class' => 'form-item']) }}>
    <label class="label" for="{{ $id }}">
        {!! __("form.label.$name") !!}
    </label>
    @if($isTextarea)
        <textarea
            id="{{ $id }}" placeholder="{!! __("form.placeholder.$placeholder") !!}"
            class="input textarea"></textarea>
    @else
        <input type="{{ $inputType }}"
               id="{{ $id }}" placeholder="{!! __("form.placeholder.$placeholder") !!}"
               class="input">
    @endif
    @error($folder . $id)
    @enderror
</div>
