@extends('layouts.main')

@section('title', 'Monitoring Kader')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Monitoring Kader</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-center gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <input id="searchInput" type="text"
                placeholder="Cari berdasarkan nama kader..."
                class="h-9 bg-white border border-[#00000033] rounded-lg px-3 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 w-full sm:w-70">

            <x-dropdown
                id="kelurahanFilterDropdown"
                label="Pilih Kelurahan"
                :options="[]"
                width="w-full sm:w-48 h-9"
                data-dropdown="filter" />

            <x-dropdown
                id="posyanduFilterDropdown"
                label="Pilih Posyandu"
                :options="[]"
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
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%] text-center">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[25%]">Nama Kader</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%] text-center">Skrining KK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%] text-center">Skrining NIK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="kaderTableBody"></tbody>
        </table>
    </div>

    <div id="modal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-11/12 sm:w-10/12 md:w-9/12 lg:w-7/12 xl:w-6/12 relative py-2">
            <div class="px-4 py-2">
                <h2 class="text-lg font-bold">
                    Detail
                </h2>
            </div>

            <div class="px-4 py-3 max-h-[70vh] overflow-y-auto">
                <div id="modal-detail-body" class="space-y-3 text-sm"></div>
            </div>

            <div class="flex justify-center p-3">
                <button id="closeModalBtn"
                    class="bg-[#61359C] text-white text-sm font-semibold w-full py-1 rounded hover:bg-[#61359C]/80 transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("kaderTableBody");

        const kaderModalRef = document.getElementById("kaderModalRef");
        const kaderModalTitle = document.getElementById("kaderModalTitle");

        async function fetchKader() {
            const result = await fetchWithAuth(`{{ url('api/monitoring/kader') }}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            });

            if (!result || result.status_code) return;

            const kader = result.data || [];
            renderTable(kader);
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-gray-500 py-4">Tidak ada hasil.</td></tr>`;
                return;
            }

            list.reverse();

            list.forEach((item, index) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">${index + 1}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama_kader}</td>
                    <td class="border border-[#00000033] px-3 py-2">${item.kelurahan ?? '-'}</td>
                    <td class="border border-[#00000033] px-3 py-2">${item.posyandu ?? '-'}</td>
                    <td class="border border-[#00000033] px-3 py-2 text-center">${item.jumlah_skrining_kk}</td>
                    <td class="border border-[#00000033] px-3 py-2 text-center">${item.jumlah_skrining_nik}</td>
                    
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        <div class="flex justify-center gap-2">
                            <button
                                class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail"
                                data-id="${item.id}">
                                Detail
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.querySelectorAll(".open-detail").forEach(btn => {
                btn.addEventListener("click", () => {

                    const id = btn.dataset.id;
                    const item = list.find(f => f.id === id);
                    if (!item) return;

                    const detailBody = document.getElementById("modal-detail-body");

                    detailBody.innerHTML = `
                        <div class="space-y-1 text-sm">
                            <div class="grid grid-cols-[100px_1fr]">
                                <span class="font-semibold">Nama Kader</span>
                                <span>: ${item.nama_kader}</span>
                            </div>

                            <div class="grid grid-cols-[100px_1fr]">
                                <span class="font-semibold">Kelurahan</span>
                                <span>: ${item.kelurahan ?? '-'}</span>
                            </div>

                            <div class="grid grid-cols-[100px_1fr]">
                                <span class="font-semibold">Posyandu</span>
                                <span>: ${item.posyandu ?? '-'}</span>
                            </div>

                            <div class="grid grid-cols-[100px_1fr]">
                                <span class="font-semibold">Jumlah KK</span>
                                <span>: ${item.detail?.length ?? 0}</span>
                            </div>
                        </div>
                    `;
                    item.detail.forEach((kk, index) => {
                        let anggotaBySiklus = {};
                        if (kk.anggota) {
                            kk.anggota.forEach(agt => {
                                if (!agt.sudah_skrining) return;
                                const siklus = agt.siklus || "Lainnya";
                                if (!anggotaBySiklus[siklus]) anggotaBySiklus[siklus] = [];
                                anggotaBySiklus[siklus].push(agt);
                            });
                        }

                        let siklusHtml = "";

                        Object.keys(anggotaBySiklus).forEach((siklus, sIndex) => {
                            let nikHtml = "";
                            anggotaBySiklus[siklus].forEach((agt, i) => {
                                nikHtml += `
                                    <div class="grid grid-cols-[70px_1fr] gap-1 ${i > 0 ? 'mt-3' : ''}">
                                        <span class="font-medium text-gray-700">NIK</span>
                                        <span>: ${agt.nik}</span>
                                    </div>
                                    <div class="grid grid-cols-[70px_1fr] gap-1 ${i > 0 ? 'mt-1' : ''}">
                                        <span class="font-medium text-gray-700">Nama</span>
                                        <span>: ${agt.nama}</span>
                                    </div>
                                `;
                            });

                            siklusHtml += `
                            <div class="mx-2 mt-2">
                                <div class="flex items-center justify-between cursor-pointer toggle-siklus 
                                    bg-gray-100 px-2 py-1 rounded" 
                                    data-target="siklus-${index}-${sIndex}">
                                    <span class="font-medium text-[#61359C]">${siklus}</span>
                                    <i class="fa-solid fa-chevron-down transition-transform"></i>
                                </div>

                                <div id="siklus-${index}-${sIndex}" class="hidden mt-2 bg-gray-50 p-2 rounded space-y-1">
                                    ${nikHtml}
                                </div>
                            </div>
                            `;
                        });

                        detailBody.innerHTML += `
                        <div class="overflow-hidden">
                            <div class="flex items-center justify-between cursor-pointer toggle-kk 
                                font-semibold px-2 py-1 rounded-lg bg-[#61359C]/10 text-[#61359C]"
                                data-target="kk-${index}">
                                <div class="flex items-center justify-between w-full">
                                    <span>KK ${index + 1}</span>
                                    ${kk.is_luar_wilayah 
                                        ? `<span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-0.5 rounded-full mr-3">
                                            Luar Wilayah
                                        </span>` 
                                        : ''}
                                </div>
                                <i class="fa-solid fa-chevron-down transition-transform duration-200"></i>
                            </div>

                            <div id="kk-${index}" class="hidden mt-2 space-y-2 px-2">
                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-medium text-gray-700">No KK</span>
                                    <span>: ${kk.no_kk}</span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-medium text-gray-700">Kepala Keluarga</span>
                                    <span>: ${kk.kepala_keluarga}</span>
                                </div>
                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-medium text-gray-700">Jumlah NIK</span>
                                    <span>: ${kk.anggota?.length ?? 0}</span>
                                </div>
                                ${kk.is_luar_wilayah ? `
                                    <div class="grid grid-cols-[120px_1fr]">
                                        <span class="font-medium text-gray-700">Alamat KTP</span>
                                        <span>: ${kk.alamat ?? '-'}</span>
                                    </div>
                                    <div class="grid grid-cols-[120px_1fr]">
                                        <span class="font-medium text-gray-700">RT / RW KTP</span>
                                        <span>: ${kk.rt ?? '-'} / ${kk.rw ?? '-'}</span>
                                    </div>
                                ` : ''}
                                ${siklusHtml} 
                            </div>
                        </div>
                        `;
                    });

                    setTimeout(() => {
                        document.querySelectorAll("[data-target]").forEach(btn => {
                            btn.onclick = () => {
                                const el = document.getElementById(btn.dataset.target);
                                const icon = btn.querySelector("i");

                                if (el) el.classList.toggle("hidden");
                                if (icon) icon.classList.toggle("rotate-180");
                            };
                        });
                    }, 50);

                    document.getElementById("modal").classList.remove("hidden");
                    document.getElementById("modal").classList.add("flex");
                });
            });

        }

        document.getElementById("closeModalBtn").onclick = () => {
            document.getElementById("modal").classList.add("hidden");
        };

        function setDropdownLabel(id, text, fallback) {
            const el = document.getElementById(id);
            if (!el) return;

            const label = el.querySelector('.dropdown-selected');
            if (label) label.textContent = text || fallback;
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

            kelurahanData.forEach(kel => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = kel.nama_kelurahan;

                btn.onclick = () => {
                    setDropdownLabel('kelurahanFilterDropdown', kel.nama_kelurahan, 'Pilih Kelurahan');
                    document.getElementById('kelurahan_id').value = kel.id;

                    setDropdownDisabled('posyanduFilterDropdown', false);
                    renderPosyanduDropdown(kel.posyandu);
                };

                dropdown.appendChild(btn);
            });
        }

        function renderPosyanduDropdown(posyanduList = []) {
            const dropdownWrapper = document.getElementById('posyanduFilterDropdown'); // <- ubah di sini
            const dropdown = dropdownWrapper.querySelector('.dropdown-menu');

            dropdown.innerHTML = '';
            document.getElementById('posyandu_id').value = '';
            setDropdownLabel('posyanduFilterDropdown', null, 'Pilih Posyandu');

            if (!posyanduList.length) {
                setDropdownDisabled('posyanduFilterDropdown', true);
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
            fetchKaderWithFilter();
        });

        document.getElementById("searchInput").addEventListener("keyup", (e) => {
            if (e.key === "Enter") fetchKaderWithFilter();
        });

        async function fetchKaderWithFilter() {
            const search = document.getElementById("searchInput").value || "";
            const kelurahan_id = document.getElementById("kelurahan_id").value || "";
            const posyandu_id = document.getElementById("posyandu_id").value || "";

            try {
                const url = new URL("{{ url('api/monitoring/kader') }}", window.location.origin);
                url.searchParams.append("search", search);
                if (kelurahan_id) url.searchParams.append("kelurahan_id", kelurahan_id);
                if (posyandu_id) url.searchParams.append("posyandu_id", posyandu_id);

                const result = await fetchWithAuth(url.toString(), {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                });

                if (!result || result.status_code) return;

                renderTable(result.data || []);

            } catch (err) {
                console.error("Gagal memuat data:", err);
            }
        }

        fetchKader();
        loadKelurahan();
        setDropdownDisabled('posyanduFilterDropdown', true);
    });
</script>
@endsection