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
                id="posyanduFilterDropdown"
                label="Pilih Posyandu"
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

    <div class="overflow-x-auto px-0 md:px-15 lg:px-20">
        <table class="w-full table-fixed border border-[#00000033] text-sm text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
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

        tbody.innerHTML = `<tr><td colspan="2" class="text-center text-gray-500 py-4">Silakan pilih Siklus terlebih dahulu.</td></tr>`;

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
                tbody.innerHTML = `<tr><td colspan="2" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="2" class="text-center text-gray-500 py-4">
                            Tidak ada data NIK pada posyandu/siklus ini.
                        </td>
                    </tr>`;
                return;
            }

            let hasData = false;

            list.forEach((item) => {
                const totalNik = item.siklus.reduce((sum, s) => sum + (s.jumlah_nik || 0), 0);

                if (totalNik > 0) {
                    hasData = true;
                }

                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        ${item.posyandu}
                    </td>
                    <td class="border border-[#00000033] text-center px-3 py-2">
                        ${totalNik}
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (!hasData) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center text-orange-500 py-4">
                        Tidak ada NIK terdaftar pada posyandu yang dipilih
                    </td>
                </tr>`;
            }
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

                    setDropdownDisabled('posyanduFilterDropdown', false);
                    setDropdownDisabled('urutDropdown', false);
                };

                dropdown.appendChild(btn);
            });
        }

        function getKelurahanId() {
            return window.App?.user?.nakesDetail?.kelurahan_id ?? null;
        }

        let posyanduData = [];

        async function loadPosyandu() {
            const kelurahanId = getKelurahanId();

            const json = await fetchWithAuth(`{{ url('api/kelurahan') }}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            });

            const kelurahan = (json.data.list || []).find(k => k.id === kelurahanId);

            if (!kelurahan) {
                showErrorToast("Kelurahan tidak valid");
                return;
            }

            document.getElementById("kelurahan_id").value = kelurahan.id;

            posyanduData = kelurahan.posyandu || [];
            renderPosyanduDropdown(posyanduData);
        }

        function renderPosyanduDropdown(posyanduList = []) {
            const dropdownWrapper = document.getElementById('posyanduFilterDropdown');
            const dropdown = dropdownWrapper.querySelector('.dropdown-menu');

            dropdown.innerHTML = '';
            document.getElementById('posyandu_id').value = '';
            setDropdownLabel('posyanduFilterDropdown', null, 'Pilih Posyandu');

            if (!posyanduList.length) {
                dropdown.innerHTML = `
            <div class="px-4 py-2 text-sm text-gray-400 text-center">
                Tidak ada posyandu
            </div>`;
                return;
            }

            posyanduList.forEach(p => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = p.nama_posyandu;

                btn.onclick = () => {
                    setDropdownLabel('posyanduFilterDropdown', p.nama_posyandu, 'Pilih Posyandu');
                    document.getElementById('posyandu_id').value = p.id;
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
                tbody.innerHTML = `<tr><td colspan="2" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        loadSiklus();
        loadPosyandu();
        setDropdownDisabled('posyanduFilterDropdown', true);
    });
</script>
@endsection