@props([
    'id' => 'accordion-' . uniqid(),
    'items' => [],
    'multiple' => false,
    'defaultOpen' => null,
])

<div {{ $attributes->merge(['class' => 'divide-y divide-border/40 rounded-xl border border-border/40 bg-card dark:bg-card']) }}>
    @foreach ($items as $index => $item)
        @php
            $itemId = $id . '-' . $index;
            $isOpen = $defaultOpen !== null && (is_array($defaultOpen) ? in_array($index, $defaultOpen) : $index === $defaultOpen);
        @endphp
        
        <div 
            x-data="{ open: {{ $isOpen ? 'true' : 'false' }} }"
            @if(!$multiple)
                @open-accordion.window="$event.detail.id === '{{ $itemId }}' ? open = true : open = false"
            @endif
            class="overflow-hidden"
        >
            <h3>
                <button 
                    type="button"
                    class="flex w-full items-center justify-between px-4 py-3 text-left text-sm font-medium text-card-foreground hover:bg-muted/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                    @click="open = !open; $dispatch('open-accordion', { id: '{{ $itemId }}' })"
                    :aria-expanded="open"
                    aria-controls="{{ $itemId }}"
                >
                    <span>{{ $item['title'] }}</span>
                    <svg 
                        class="h-5 w-5 text-muted-foreground transition-transform duration-200" 
                        :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 20 20" 
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </h3>
            <div 
                id="{{ $itemId }}"
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="px-4 pb-4 pt-1 text-sm text-muted-foreground"
                @if(!$isOpen) style="display: none;" @endif
            >
                {{ $item['content'] }}
            </div>
        </div>
    @endforeach
</div>
