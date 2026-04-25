@extends('layouts.main')

@section('title', 'Monitoring NIK Per Siklus')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Monitoring NIK Per Siklus</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-center gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <x-dropdown
                id="siklusDropdown"
                label="Pilih Siklus"
                :options="[]"
                width="w-full sm:w-48 h-9"
                data-dropdown="filter" />

            <x-dropdown
                id="kelurahanFilterDropdown"
                label="Pilih Kelurahan"
                :options="[]"
                width="w-full sm:w-48 h-9"
                data-dropdown="filter" />

            <x-dropdown
                id="urutDropdown"
                label="Urutkan dari"
                :options="['Terbanyak → Terkecil', 'Terkecil → Terbanyak']"
                width="w-full sm:w-48 h-9"
                data-dropdown="filter" />

            <button id="searchBtn"
                class="h-9 flex items-center justify-center bg-[#61359C] text-white
                   border border-[#00000033] px-3 rounded-lg text-sm
                   hover:bg-[#61359C]/80 transition w-full sm:w-auto">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <input type="hidden" id="kelurahan_id" value="">
            <input type="hidden" id="posyandu_id" value="">
            <input type="hidden" id="siklus_id" value="">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[25%]">Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[25%]">Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%] text-center">Jumlah NIK</th>
                </tr>
            </thead>
            <tbody id="kaderTableBody"></tbody>
        </table>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("kaderTableBody");

        tbody.innerHTML = `<tr><td colspan="4" class="text-center text-gray-500 py-4">Silakan pilih Siklus terlebih dahulu.</td></tr>`;

        setDropdownDisabled('kelurahanFilterDropdown', true);
        setDropdownDisabled('urutDropdown', true);
        setDropdownDisabled('posyanduFilterDropdown', true);

        async function fetchNikSiklus() {
            try {
                const res = await fetchWithAuth("{{ url('api/monitoring/nik-per-siklus') }}", {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                });

                if (!res) return;

                const result = await res.json?.() ?? res;

                if (!result || !result.data) return;

                renderTable(result.data || []);
            } catch (error) {
                console.error("Gagal memuat data kader skrining:", error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center text-gray-500 py-4">
                            Tidak ada data NIK pada kelurahan/siklus ini.
                        </td>
                    </tr>`;
                return;
            }

            list.forEach((item) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                const totalNik = item.siklus.reduce((sum, s) => sum + (s.jumlah_nik || 0), 0);

                tr.innerHTML = `
                    <td class="border border-[#00000033] px-3 py-3">${item.kelurahan}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.posyandu}</td>
                    <td class="border border-[#00000033] text-center px-3 py-2">${totalNik}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function setDropdownLabel(id, text, fallback) {
            const el = document.getElementById(id);
            if (!el) return;

            const label = el.querySelector('.dropdown-selected');
            if (label) label.textContent = text || fallback;
        }

        let siklusData = [];

        async function loadSiklus() {
            try {
                const res = await fetchWithAuth("{{ url('api/kategori') }}", {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                });

                const json = await res.json?.() ?? res;

                const allData = json.data.list || [];

                siklusData = allData
                    .filter(item => item.target_skrining?.toLowerCase() === 'nik')
                    .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                renderSiklusDropdown();

            } catch (error) {
                console.error('Gagal load siklus:', error);
            }
        }

        function renderSiklusDropdown() {
            const dropdown = document
                .getElementById('siklusDropdown')
                .querySelector('.dropdown-menu');

            dropdown.innerHTML = '';

            if (!siklusData.length) {
                dropdown.innerHTML = `
                <div class="px-4 py-2 text-sm text-gray-400 text-center">
                    Tidak ada data siklus
                </div>
            `;
                return;
            }

            siklusData.forEach(siklus => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = siklus.nama_kategori;

                btn.onclick = () => {
                    setDropdownLabel('siklusDropdown', siklus.nama_kategori, 'Pilih Siklus');
                    document.getElementById('siklus_id').value = siklus.id;
                    document.getElementById('siklus_id').dataset.nama = siklus.nama_kategori;

                    setDropdownDisabled('kelurahanFilterDropdown', false);
                    setDropdownDisabled('urutDropdown', false);
                };

                dropdown.appendChild(btn);
            });
        }

        let kelurahanData = [];

        async function loadKelurahan() {
            const json = await fetchWithAuth(`{{ url('api/kelurahan') }}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            });

            kelurahanData = json.data.list || [];
            renderKelurahanDropdown();
        }

        function renderKelurahanDropdown() {
            const dropdown = document
                .getElementById('kelurahanFilterDropdown')
                .querySelector('.dropdown-menu');

            dropdown.innerHTML = '';

            const semuaBtn = document.createElement('button');
            semuaBtn.type = 'button';
            semuaBtn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100 font-semibold';
            semuaBtn.textContent = 'Semua';
            semuaBtn.onclick = () => {
                setDropdownLabel('kelurahanFilterDropdown', 'Semua', 'Pilih Kelurahan');
                document.getElementById('kelurahan_id').value = '';
                setDropdownDisabled('posyanduFilterDropdown', true);
            };
            dropdown.appendChild(semuaBtn);

            kelurahanData.forEach(kel => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = kel.nama_kelurahan;

                btn.onclick = () => {
                    setDropdownLabel('kelurahanFilterDropdown', kel.nama_kelurahan, 'Pilih Kelurahan');
                    document.getElementById('kelurahan_id').value = kel.id;

                    setDropdownDisabled('posyanduFilterDropdown', false);
                };

                dropdown.appendChild(btn);
            });
        }

        function setDropdownDisabled(id, disabled = true) {
            const wrapper = document.getElementById(id);
            if (!wrapper) return;

            const button = wrapper.querySelector('button');
            const menu = wrapper.querySelector('.dropdown-menu');

            if (disabled) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.setAttribute('disabled', true);
                if (menu) menu.classList.add('hidden');
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.removeAttribute('disabled');
            }
        }

        document.getElementById("searchBtn").addEventListener("click", () => {
            const siklus_id = document.getElementById("siklus_id").value;
            const kelurahan_id = document.getElementById("kelurahan_id").value;
            const posyandu_id = document.getElementById("posyandu_id").value;
            const urut = document.getElementById("urutDropdown").querySelector('.dropdown-selected').textContent;

            fetchNikSiklusWithFilter({
                siklus_id,
                kelurahan_id,
                posyandu_id,
                sort: urut
            });
        });

        async function fetchNikSiklusWithFilter(filters = {}) {
            const url = new URL("{{ url('api/monitoring/nik-per-siklus') }}", window.location.origin);

            Object.keys(filters).forEach(key => {
                if (filters[key]) url.searchParams.append(key, filters[key]);
            });

            try {
                const res = await fetchWithAuth(url.toString(), {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                });

                const result = await res.json?.() ?? res;

                renderTable(result.data || []);
            } catch (err) {
                console.error('Gagal memuat data:', err);
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        loadSiklus();
        loadKelurahan();
        setDropdownDisabled('posyanduFilterDropdown', true);
    });
</script>
@endsection