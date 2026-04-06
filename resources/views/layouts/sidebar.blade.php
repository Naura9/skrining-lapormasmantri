<aside id="sidebar"
    class="fixed top-0 left-0 h-full w-64 bg-white border-r border-[#00000033] flex flex-col
           transform -translate-x-full md:translate-x-0 transition z-50">

   <div class="px-6 py-5 hidden md:flex items-center space-x-2">
        <img src="/logo_puskesmas.png" alt="Logo Puskesmas" class="w-15 h-15 object-contain">
        
        <h1 class="text-xl font-bold leading-tight">
            LAPOR<br>MAS MANTRI
        </h1>
    </div>

    <nav class="flex-1 overflow-y-auto px-2 py-6 space-y-2">
        <a href="{{ route('dashboard') }}"
            class="group flex items-center gap-2 px-2 py-2.5 rounded-lg transition
            {{ request()->routeIs('dashboard') 
                ? 'bg-[#61359C]/65 text-white font-semibold' 
                : 'text-[#00000080] font-semibold hover:bg-[#61359C] hover:text-white' }}">

            <i class="fa-solid fa-house text-lg 
                {{ request()->routeIs('dashboard') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
            </i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('kader.dashboard_kader') }}"
            class="group flex items-center gap-2 px-2 py-2.5 rounded-lg transition
            {{ request()->routeIs('kader.dashboard_kader') 
                ? 'bg-[#61359C]/65 text-white font-semibold' 
                : 'text-[#00000080] font-semibold hover:bg-[#61359C] hover:text-white' }}">

            <i class="fa-solid fa-house text-lg 
                {{ request()->routeIs('kader.dashboard_kader') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
            </i>
            <span>Dashboard Kader</span>
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
            class="group flex items-center justify-between gap-1.5 px-2 py-2.5 rounded-lg transition font-semibold
                {{ $isKelolaUserActive ? 'bg-[#61359C]/65 text-white' : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-users-gear text-lg
                    {{ $isKelolaUserActive ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                </i>
                <span>Kelola User</span>
            </div>
        </a>

        <ul id="kelolaUserSubMenu" class="pl-5 mt-1 space-y-1 {{ $isKelolaUserActive ? 'block' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.fitur.kelola_user.data_admin') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.kelola_user.data_admin')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user-shield text-lg
                        {{ request()->routeIs('admin.fitur.kelola_user.data_admin') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Admin</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.kelola_user.data_kader') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.kelola_user.data_kader')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user-group text-lg
                        {{ request()->routeIs('admin.fitur.kelola_user.data_kader') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Kader</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.kelola_user.data_nakes') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.kelola_user.data_nakes')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user-doctor text-lg
                        {{ request()->routeIs('admin.fitur.kelola_user.data_nakes') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span class="ml-2">Tenaga Kesehatan</span>
                </a>
            </li>
        </ul>

        @php
        $isSkriningActive = request()->routeIs([
        'admin.skrining',
        'admin.skrining.kategori',
        'admin.skrining.pertanyaan_kk',
        'admin.skrining.pertanyaan_nik',
        'admin.monitoring.hasil_skrining',
        ]);
        @endphp

        <a href="#" id="skriningMenuToggle"
            class="group flex items-center justify-between gap-1.5 px-2 py-2.5 rounded-lg transition font-semibold
                {{ $isSkriningActive ? 'bg-[#61359C]/65 text-white' : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-file-pen text-lg transition
                    {{ $isSkriningActive ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                </i>
                <span class="ml-0.5">Skrining</span>
            </div>
        </a>

        <ul id="skriningSubMenu" class="pl-5 mt-1 space-y-1 {{ $isSkriningActive ? 'block' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.fitur.skrining.kategori') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.skrining.kategori')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-tags text-lg
                        {{ request()->routeIs('admin.fitur.skrining.kategori') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Kategori</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.skrining.pertanyaan_kk') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.skrining.pertanyaan_kk')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-people-group text-lg
                        {{ request()->routeIs('admin.fitur.skrining.pertanyaan_kk') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Pertanyaan KK</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.skrining.pertanyaan_nik') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.skrining.pertanyaan_nik')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user text-lg
                        {{ request()->routeIs('admin.fitur.skrining.pertanyaan_nik') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Pertanyaan NIK</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.monitoring.hasil_skrining') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.monitoring.hasil_skrining')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user text-lg
                        {{ request()->routeIs('admin.fitur.monitoring.hasil_skrining') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Hasil Skrining</span>
                </a>
            </li>
        </ul>

        <a href="{{ route('admin.fitur.data_wilayah') }}"
            class="group flex items-center gap-2 px-2 py-2.5 rounded-lg transition
            {{ request()->routeIs('admin.fitur.data_wilayah') 
                ? 'bg-[#61359C]/65 text-white font-semibold' 
                : 'text-[#00000080] font-semibold hover:bg-[#61359C] hover:text-white' }}">

            <i class="fa-solid fa-map-location-dot text-lg
            {{ request()->routeIs('admin.fitur.data_wilayah') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
            </i>
            <span>Data Wilayah</span>
        </a>


        @php
        $isKaderSkriningActive = request()->routeIs([
        'kader.skrining',
        'kader.skrining_kk',
        'kader.skrining_nik',
        ]);
        @endphp

        <a href="#" id="kaderSkriningMenuToggle"
            class="group flex items-center justify-between gap-1.5 px-2 py-2.5 rounded-lg transition font-semibold
                {{ $isKaderSkriningActive ? 'bg-[#61359C]/65 text-white' : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-file-pen text-lg transition
                    {{ $isKaderSkriningActive ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                </i>
                <span class="ml-0.5">Skrining</span>
            </div>
        </a>

        <ul id="kaderSkriningSubMenu" class="pl-5 mt-1 space-y-1 {{ $isKaderSkriningActive ? 'block' : 'hidden' }}">
            <li>
                <a href="{{ route('kader.fitur.skrining_kk') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('kader.fitur.skrining_kk')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-people-group text-lg
                        {{ request()->routeIs('kader.fitur.skrining_kk') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Skrining KK</span>
                </a>
            </li>
            <li>
                <a href="{{ route('kader.fitur.skrining_nik') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('kader.fitur.skrining_nik')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-user text-lg
                        {{ request()->routeIs('kader.fitur.skrining_nik') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>Skrining NIK</span>
                </a>
            </li>
        </ul>

        <a href="{{ route('admin.fitur.monitoring.kader') }}"
            class="group flex items-center gap-2 px-2 py-2.5 rounded-lg transition
            {{ request()->routeIs('admin.fitur.monitoring.kader') 
                ? 'bg-[#61359C]/65 text-white font-semibold' 
                : 'text-[#00000080] font-semibold hover:bg-[#61359C] hover:text-white' }}">

            <i class="fa-solid fa-user-group text-lg
            {{ request()->routeIs('admin.fitur.monitoring.kader') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
            </i>
            <span>Monitoring Kader</span>
        </a>

        @php
        $isMonitoringActive = request()->routeIs([
        'admin.monitoring',
        'admin.monitoring.nik_per_siklus',
        'admin.monitoring.nik_per_kk',
        ]);
        @endphp

        <a href="#" id="monitoringMenuToggle"
            class="group flex items-center justify-between gap-1.5 px-2 py-2.5 rounded-lg transition font-semibold
                {{ $isMonitoringActive ? 'bg-[#61359C]/65 text-white' : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-chart-simple text-lg transition
                    {{ $isMonitoringActive ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                </i>
                <span class="ml-0.5">Monitoring</span>
            </div>
        </a>

        <ul id="monitoringSubMenu" class="pl-5 mt-1 space-y-1 {{ $isMonitoringActive ? 'block' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.fitur.monitoring.nik_per_kk') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                {{ request()->routeIs('admin.fitur.monitoring.nik_per_kk')
                ? 'bg-[#61359C]/65 text-white'
                : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-people-group text-lg
                {{ request()->routeIs('admin.fitur.monitoring.nik_per_kk') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>
                    <span>NIK Per KK</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.monitoring.nik_per_siklus') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                {{ request()->routeIs('admin.fitur.monitoring.nik_per_siklus')
                    ? 'bg-[#61359C]/65 text-white'
                    : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-circle-notch text-lg
                {{ request()->routeIs('admin.fitur.monitoring.nik_per_siklus') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>
                    <span>NIK Per Siklus</span>
                </a>
            </li>
        </ul>

        @php
        $isDataWargaActive = request()->routeIs([
        'admin.data_warga',
        'admin.data_warga_kk',
        'admin.data_warga_nik',
        ]);
        @endphp

        <a href="#" id="dataWargaMenuToggle"
            class="group flex items-center justify-between gap-1.5 px-2 py-2.5 rounded-lg transition font-semibold
                {{ $isDataWargaActive ? 'bg-[#61359C]/65 text-white' : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

            <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-address-book text-lg transition
                    {{ $isDataWargaActive ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                </i>
                <span class="ml-0.5">Data Warga</span>
            </div>
        </a>

        <ul id="dataWargaSubMenu" class="pl-5 mt-1 space-y-1 {{ $isDataWargaActive ? 'block' : 'hidden' }}">
            <li>
                <a href="{{ route('admin.fitur.data_warga.kk') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.data_warga.kk')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-people-roof text-lg
                        {{ request()->routeIs('admin.fitur.data_warga.kk') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>KK</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.fitur.data_warga.nik') }}"
                    class="group flex items-center gap-1.5 px-2 py-2 rounded-lg transition font-semibold
                        {{ request()->routeIs('admin.fitur.data_warga.nik')
                            ? 'bg-[#61359C]/65 text-white'
                            : 'text-[#00000080] hover:bg-[#61359C] hover:text-white' }}">

                    <i class="fa-solid fa-people-roof text-lg
                        {{ request()->routeIs('admin.fitur.data_warga.nik') ? 'text-[#61359C]' : 'text-[#61359C]/70 group-hover:text-white' }}">
                    </i>

                    <span>NIK</span>
                </a>
            </li>
            
        </ul>
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

    const skriningMenuToggle = document.getElementById('skriningMenuToggle');
    const skriningSubMenu = document.getElementById('skriningSubMenu');

    skriningMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        skriningSubMenu.classList.toggle('hidden');
    });

    const kaderSkriningMenuToggle = document.getElementById('kaderSkriningMenuToggle');
    const kaderSkriningSubMenu = document.getElementById('kaderSkriningSubMenu');

    kaderSkriningMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        kaderSkriningSubMenu.classList.toggle('hidden');
    });

    const monitoringMenuToggle = document.getElementById('monitoringMenuToggle');
    const monitoringSubMenu = document.getElementById('monitoringSubMenu');

    monitoringMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        monitoringSubMenu.classList.toggle('hidden');
    });

    const dataWargaMenuToggle = document.getElementById('dataWargaMenuToggle');
    const dataWargaSubMenu = document.getElementById('dataWargaSubMenu');

    dataWargaMenuToggle.addEventListener('click', (e) => {
        e.preventDefault();
        dataWargaSubMenu.classList.toggle('hidden');
    });
</script>