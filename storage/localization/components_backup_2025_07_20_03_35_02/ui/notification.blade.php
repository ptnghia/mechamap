@props([
    'type' => 'info',
    'title' => '',
    'message' => '',
    'dismissable' => true,
    'timeout' => 5000,
])

@php
    $typeClasses = [
        'info' => 'bg-info-50 text-info-800 border-info-200 dark:bg-info-950 dark:text-info-200 dark:border-info-800',
        'success' => 'bg-success-50 text-success-800 border-success-200 dark:bg-success-950 dark:text-success-200 dark:border-success-800',
        'warning' => 'bg-warning-50 text-warning-800 border-warning-200 dark:bg-warning-950 dark:text-warning-200 dark:border-warning-800',
        'error' => 'bg-destructive-50 text-destructive-800 border-destructive-200 dark:bg-destructive-950 dark:text-destructive-200 dark:border-destructive-800',
    ];
    
    $iconClasses = [
        'info' => 'text-info-500 dark:text-info-400',
        'success' => 'text-success-500 dark:text-success-400',
        'warning' => 'text-warning-500 dark:text-warning-400',
        'error' => 'text-destructive-500 dark:text-destructive-400',
    ];
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95"
    x-init="@if($timeout) setTimeout(() => { show = false }, {{ $timeout }}) @endif"
    {{ $attributes->merge(['class' => 'rounded-lg border p-4 mb-4 ' . $typeClasses[$type]]) }}
    role="alert"
>
    <div class="flex items-start">
        <div class="flex-shrink-0">
            @if($type === 'info')
                <svg class="h-5 w-5 {{ $iconClasses[$type] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            @elseif($type === 'success')
                <svg class="h-5 w-5 {{ $iconClasses[$type] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            @elseif($type === 'warning')
                <svg class="h-5 w-5 {{ $iconClasses[$type] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            @elseif($type === 'error')
                <svg class="h-5 w-5 {{ $iconClasses[$type] }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            @endif
        </div>
        <div class="ml-3 w-full">
            @if($title)
                <h3 class="text-sm font-medium">{{ $title }}</h3>
            @endif
            
            <div class="text-sm mt-1">
                @if($message)
                    {{ $message }}
                @else
                    {{ $slot }}
                @endif
            </div>
        </div>
        
        @if($dismissable)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button 
                        type="button" 
                        @click="show = false" 
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $typeClasses[$type] }} opacity-70 hover:opacity-100"
                    >
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
