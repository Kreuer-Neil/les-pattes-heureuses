@props(['title','value'])
<div class="p-2 w-3xs rounded-lg flex flex-col gap-1 bg-background-3">
    <p class="subtitle px-8 text-center">{{ __('client.stats.' . $title) }}</p>
    <p class="title bg-white rounded-sm py-7 flex items-center justify-center">{{ $value }}</p>
</div>
