<div class="md:hidden flex items-center justify-between px-4 py-3 bg-white border-b">
    <h1 class="text-xl font-bold">LAPOR</h1>
    <button id="menu-toggle" class="p-2 rounded-md hover:bg-gray-100">
        <i class="fa-solid fa-bars"></i>
    </button>
</div>

<aside id="sidebar"
    class="fixed top-0 left-0 h-full w-64 bg-white border-r border-[#00000033] flex flex-col
           transform -translate-x-full md:translate-x-0 transition z-50">

    <div class="px-6 py-5 hidden md:block">
        <h1 class="text-2xl font-bold leading-tight">
            LAPOR<br>MAS MANTRI
        </h1>
    </div>

    <nav class="flex-1 overflow-y-auto px-2 py-6 space-y-2">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-2 px-2 py-2.5 rounded-lg transition
            {{ request()->routeIs('dashboard') 
                ? 'bg-[#61359C]/80 text-white font-semibold' 
                : 'text-[#00000080] font-semibold hover:bg-[#61359C] hover:text-white' }}">

            <i class="fa-solid fa-house text-lg 
            {{ request()->routeIs('dashboard') ? 'opacity-100' : 'opacity-50' }}">
            </i>
            <span>Dashboard</span>
        </a>

        @php
        $isKelolaUserActive = request()->routeIs([
        'admin.kelola_user',
        'admin.kelola_user.data_admin',
        'admin.kelola_user.data_kader',
        'admin.kelola_user.data_nakes',
        ]);
        @endphp

        <a href="#" id="kelolaUserMenuToggle"
            class="flex items-center justify-between gap-1.5 px-2 py-2.5 rounded-lg transition font-semibold
                {{ $isKelolaUserActive ? 'bg-[#61359C]/80 text-white' : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-users-gear text-lg
                    {{ $isKelolaUserActive ? 'opacity-100' : 'opacity-50' }}">
                </i>
                <span>Kelola User</span>
            </div>
        </a>

        <ul id="kelolaUserSubMenu" class="pl-5 mt-1 space-y-1 {{ $isKelolaUserActive ? 'block' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.fitur.kelola_user.data_admin') }}"
                    class="flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.kelola_user.data_admin')
                            ? 'bg-[#61359C]/80 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user-shield text-lg
                        {{ request()->routeIs('admin.fitur.kelola_user.data_admin') ? 'opacity-100' : 'opacity-50' }}">
                    </i>

                    <span>Admin</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.kelola_user.data_kader') }}"
                    class="flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.kelola_user.data_kader')
                            ? 'bg-[#61359C]/80 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user-group text-lg
                        {{ request()->routeIs('admin.fitur.kelola_user.data_kader') ? 'opacity-100' : 'opacity-50' }}">
                    </i>

                    <span>Kader</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.kelola_user.data_nakes') }}"
                    class="flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.kelola_user.data_nakes')
                            ? 'bg-[#61359C]/80 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user-doctor text-lg
                        {{ request()->routeIs('admin.fitur.kelola_user.data_nakes') ? 'opacity-100' : 'opacity-50' }}">
                    </i>

                    <span class="ml-2">Tenaga Kesehatan</span>
                </a>
            </li>
        </ul>

        <a href="{{ route('admin.fitur.data_wilayah') }}"
            class="flex items-center gap-2 px-2 py-2.5 rounded-lg transition
            {{ request()->routeIs('admin.fitur.data_wilayah') 
                ? 'bg-[#61359C]/80 text-white font-semibold' 
                : 'text-[#00000080] font-semibold hover:bg-[#61359C] hover:text-white' }}">

            <i class="fa-solid fa-map-location-dot text-lg
            {{ request()->routeIs('admin.fitur.data_wilayah') ? 'opacity-100' : 'opacity-50' }}">
            </i>
            <span>Data Wilayah</span>
        </a>
    </nav>

</aside>

<script>
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });

    const kelolaUserMenuToggle = document.getElementById('kelolaUserMenuToggle');
    const kelolaUserSubMenu = document.getElementById('kelolaUserSubMenu');

    kelolaUserMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        kelolaUserSubMenu.classList.toggle('hidden');
    });
</script>