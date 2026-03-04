@props([
'label' => 'Pilih Opsi',
'options' => [],
'selected' => null,
'width' => 'w-full',
'color' => 'bg-white',
'textColor' => 'text-gray-500',
'searchable' => false
])

<div {{ $attributes->merge(['class' => "relative block text-left $width"]) }} x-ignore>
    <button type="button"
        class="relative flex items-center justify-between w-full border border-[#00000033] text-sm rounded-lg px-4 py-2 transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 hover:opacity-90 {{ $color }}"
        onclick="toggleDropdown(this)">
        <span class="dropdown-selected text-left w-full truncate {{ $textColor }}">
            {{ $selected ?? $label }}
        </span>
        <svg class="w-4 h-4 absolute right-3 transition-transform duration-200" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div class="dropdown-menu hidden absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl p-2 border border-gray-100">
        @if($searchable)
        <input type="text" placeholder="Cari..." class="w-full px-3 py-2 mb-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#61359C]/50" onkeyup="filterDropdown(this)">
        @endif

        @foreach ($options as $option)
        <button type="button"
            class="dropdown-item block w-full text-center px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 transition"
            onclick="selectDropdownOption(this, '{{ $option }}')">
            {{ $option }}
        </button>
        @endforeach
    </div>

</div>

<script>
    function toggleDropdown(button) {
        const dropdown = button.parentElement.querySelector('.dropdown-menu');
        const allDropdowns = document.querySelectorAll('.dropdown-menu');
        allDropdowns.forEach(menu => {
            if (menu !== dropdown) menu.classList.add('hidden');
        });
        dropdown.classList.toggle('hidden');
    }

    function selectDropdownOption(optionEl, value) {
        const dropdown = optionEl.closest('.relative');
        const selectedSpan = dropdown.querySelector('.dropdown-selected');
        selectedSpan.textContent = value;
        dropdown.querySelector('.dropdown-menu').classList.add('hidden');

        const event = new CustomEvent('dropdown-changed', {
            detail: {
                value
            }
        });
        dropdown.dispatchEvent(event);
    }

    function filterDropdown(input) {
        const filter = input.value.toLowerCase();
        const dropdown = input.closest('.dropdown-menu');
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }
    });
</script>