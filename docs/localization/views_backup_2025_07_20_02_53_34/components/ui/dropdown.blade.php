@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-card dark:bg-card',
    'trigger' => '',
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-top',
        'right' => 'origin-top-right right-0',
        'bottom' => 'origin-bottom',
        default => 'origin-top-right right-0',
    };

    $width = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        '72' => 'w-72',
        '80' => 'w-80',
        default => 'w-48',
    };
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $width }} rounded-xl shadow-soft-lg {{ $alignmentClasses }}"
        style="display: none;"
        @click="open = false"
    >
        <div class="rounded-xl ring-1 ring-border/5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
