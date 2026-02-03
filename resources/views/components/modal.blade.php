@props(['id', 'size' => 'md'])

@php
$widthClass = match($size) {
    'sm' => 'w-full sm:w-3/12',
    'md' => 'w-full sm:w-5/12',
    'lg' => 'w-full sm:w-7/12',
    'xl' => 'w-full sm:w-9/12',
    'full' => 'w-full',
    default => 'w-full sm:w-5/12',
};
@endphp

<div id="{{ $id }}" 
     class="fixed inset-0 bg-slate-950/30 flex justify-center items-center z-[9999] hidden h-full"
     onclick="if(event.target.id === '{{ $id }}'){ this.classList.add('hidden'); document.body.style.overflow='auto'; }">
     
    <div class="bg-white rounded-xl shadow-2xl shadow-slate-950/30 scale-95 transition-transform duration-300 ease-out {{ $widthClass }} max-h-[90vh] overflow-y-auto"
         onclick="event.stopPropagation()"> 
        
        <!-- Header -->
        <div class="pt-4 px-4 flex justify-between items-start mb-1">
            <div class="flex flex-col">
                {{ $title ?? 'Default Title' }}
            </div>
        </div>

        <!-- Body -->
        <div class="p-4">
            {{ $slot }}
        </div>

        <!-- Footer -->
        @isset($footer)
        <div class="px-4 py-4 flex flex-col sm:flex-row justify-end gap-2">
            {{ $footer }}
        </div>
        @endisset
    </div>
</div>
