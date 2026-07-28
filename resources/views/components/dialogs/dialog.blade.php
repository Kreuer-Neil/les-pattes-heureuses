@props([
    'id',
    'title' => null,
])

<dialog id="{{ $id }}" class="modal" @if($title) aria-labelledby="{{ $id }}-title" @endif>
    @if($title)
        <div class="flex mb-2">
            <h2 id="{{ $id }}-title" class="title">{{ $title }}</h2>
            @endif
            <button commandfor="{{ $id }}" command="close" class="dialog-close"
                    aria-label="{{ __('client.dialog.close') }}">
            </button>
            @if($title)
        </div>
    @endif
    {{ $slot }}
</dialog>
