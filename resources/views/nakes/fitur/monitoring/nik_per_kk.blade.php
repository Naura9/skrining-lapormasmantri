@extends('layouts.main')

@section('title', 'Monitoring NIK Per KK')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Monitoring NIK per KK</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-center gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <input id="searchInput" type="text"
                placeholder="Cari berdasarkan No KK atau NIK..."
                class="h-9 bg-white border border-[#00000033] rounded-lg px-3 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 w-full sm:w-70">

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
                    <th class="px-3 py-2 border border-[#00000033] w-[25%]">Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[15%] text-center">No KK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[35%] text-center">Kepala Keluarga</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%] text-center">Jumlah NIK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="kaderTableBody"></tbody>
        </table>
    </div>

    <div id="modal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-11/12 sm:w-10/12 md:w-9/12 lg:w-7/12 xl:w-6/12 relative py-2
                max-h-[90vh] flex flex-col">
            <div class="px-4 py-2 flex-shrink-0">
                <h2 class="text-lg font-bold">Detail</h2>
            </div>

            <div class="px-4 py-3 overflow-y-auto flex-1 space-y-3 text-sm">
                <div id="modal-detail-body"></div>
                <div id="anggota-detail-container"></div>
            </div>

            <div class="flex justify-center p-3 flex-shrink-0">
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

        const kkModalRef = document.getElementById("kaderModalRef");
        const kkModalTitle = document.getElementById("kaderModalTitle");

        async function fetchKk() {
            const url = new URL("{{ url('api/monitoring/nik-per-kk') }}", window.location.origin);

            const kelurahan_id = document.getElementById("kelurahan_id").value;
            const posyandu_id = document.getElementById("posyandu_id").value;

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
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-gray-500 py-4">Tidak ada hasil.</td></tr>`;
                return;
            }

            let no = 1;
            list.forEach((item, index) => {
                item.keluarga.forEach((kk, kkIndex) => {
                    const tr = document.createElement("tr");
                    tr.className = "hover:bg-gray-50 ";

                    tr.innerHTML = `
                        <td class="border border-[#00000033] text-center px-3 py-2">${no++}</td>
                        <td class="border border-[#00000033] px-3 py-2">${item.posyandu ?? '-'}</td>
                        <td class="border border-[#00000033] text-center px-3 py-2">${kk.no_kk}</td>
                        <td class="border border-[#00000033] text-center px-3 py-2">${kk.kepala_keluarga ?? '-'}</td>
                        <td class="border border-[#00000033] text-center px-3 py-2">${kk.jumlah_nik}</td>
                        <td class="border border-[#00000033] text-center px-3 py-2">
                            <button class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail" data-id="${kk.no_kk}">
                                Detail
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });

            document.querySelectorAll(".open-detail").forEach(btn => {
                btn.addEventListener("click", () => {
                    const container = document.getElementById("anggota-detail-container");
                    if (container) {
                        container.innerHTML = "";
                        container.dataset.currentIdx = "";
                    }

                    const noKk = btn.dataset.id;

                    let kkData;
                    for (const item of list) {
                        kkData = item.keluarga.find(k => k.no_kk === noKk);
                        if (kkData) {
                            kkData.kelurahan = item.kelurahan;
                            kkData.posyandu = item.posyandu;
                            break;
                        }
                    }
                    if (!kkData) return;

                    const detailBody = document.getElementById("modal-detail-body");
                    detailBody.innerHTML = `
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Jumlah NIK</span>
                                    <span>: ${kkData.anggota?.length ?? 0}</span>
                                </div>

                                ${kkData.is_luar_wilayah ? `
                                    <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-0.5 rounded-full">
                                        Luar Wilayah
                                    </span>
                                ` : ''}
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 mt-3">
                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">No KK</span>
                                    <span>: ${kkData.no_kk}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Kelurahan</span>
                                    <span>: ${kkData.kelurahan ?? '-'}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Kepala Keluarga</span>
                                    <span>: ${kkData.kepala_keluarga ?? '-'}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Posyandu</span>
                                    <span>: ${kkData.posyandu ?? '-'}</span>
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Alamat Domisili</span>
                                    <span>: ${kkData.alamat_unit ?? '-'}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">RT / RW Domisili</span>
                                    <span>: ${kkData.rt_unit ?? '-'} / ${kkData.rw_unit ?? '-'}</span>
                                </div>
                            </div>

                            ${kkData.is_luar_wilayah ? `
                                <div>
                                    <div class="grid grid-cols-[120px_1fr]">
                                        <span class="font-semibold">Alamat KTP</span>
                                        <span>: ${kkData.alamat_ktp ?? '-'}</span>
                                    </div>

                                    <div class="grid grid-cols-[120px_1fr]">
                                        <span class="font-semibold">RT / RW KTP</span>
                                        <span>: ${kkData.rt_ktp ?? '-'} / ${kkData.rw_ktp ?? '-'}</span>
                                    </div>
                                </div>
                            ` : ''}

                        </div>
                        `;

                    if (kkData.anggota && kkData.anggota.some(a => a.no_nik)) {
                        const anggotaTableHtml = `
                            <div class="overflow-x-auto mt-2">
                                <div class="border-t border-gray-300 my-4"></div>
                                <table class="min-w-full border mt-2">
                                    <thead class="bg-gray-100 text-sm">
                                        <tr>
                                            <th class="px-2 py-1 border border-[#00000033] w-[5%]">No</th>
                                            <th class="px-2 py-1 border border-[#00000033] w-[25%]">NIK</th>
                                            <th class="px-2 py-1 border border-[#00000033] w-[40%]">Nama Lengkap</th>
                                            <th class="px-2 py-1 border border-[#00000033] w-[25%]">Siklus</th>
                                            <th class="px-2 py-1 border border-[#00000033] w-[10%]">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${kkData.anggota.map((agt, idx) => `
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-2 py-1 border border-[#00000033] text-center">${idx+1}</td>
                                                <td class="px-2 py-1 border border-[#00000033] text-center">${agt.no_nik}</td>
                                                <td class="px-2 py-1 border border-[#00000033]">${agt.nama_lengkap}</td>
                                                <td class="px-2 py-1 border border-[#00000033] text-center">${agt.siklus ?? '-'}</td>
                                                <td class="px-2 py-1 border border-[#00000033] text-center">
                                                    <button class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 open-detail-anggota" 
                                                        data-idx="${idx}">
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                        detailBody.innerHTML += anggotaTableHtml;
                    }

                    detailBody.querySelectorAll(".open-detail-anggota").forEach(btnAgt => {
                        btnAgt.onclick = () => {
                            const idx = btnAgt.dataset.idx;
                            const agt = kkData.anggota[idx];
                            const container = document.getElementById("anggota-detail-container");

                            if (container.dataset.currentIdx === idx) {
                                container.innerHTML = "";
                                container.dataset.currentIdx = "";
                                return;
                            }

                            container.dataset.currentIdx = idx;

                            const agtDetail = `
                                <div class="p-2 border border-[#00000033] rounded bg-gray-50 space-y-2 text-sm">
                                    <div class="grid grid-cols-[130px_1fr] gap-2">
                                        <span class="font-semibold">NIK</span><span>: ${agt.no_nik}</span>
                                    </div>

                                    <div class="grid grid-cols-[130px_1fr] gap-2">
                                        <span class="font-semibold">Nama</span><span>: ${agt.nama_lengkap}</span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                                        <div class="grid grid-cols-[130px_1fr] gap-2">
                                            <span class="font-semibold">Tempat Lahir</span><span>: ${agt.tempat_lahir ?? '-'}</span>
                                        </div>
                                        <div class="grid grid-cols-[130px_1fr] gap-2">
                                            <span class="font-semibold">Tanggal Lahir</span><span>: ${agt.tanggal_lahir ?? '-'}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div class="grid grid-cols-[130px_1fr] gap-2">
                                            <span class="font-semibold">Jenis Kelamin</span><span>: ${agt.jenis_kelamin ?? '-'}</span>
                                        </div>
                                        <div class="grid grid-cols-[130px_1fr] gap-2">
                                            <span class="font-semibold">Pekerjaan</span><span>: ${agt.pekerjaan ?? '-'}</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-[130px_1fr] gap-2 mt-4">
                                        <span class="font-semibold">Pendidikan Terakhir</span><span>: ${agt.pendidikan_terakhir ?? '-'}</span>
                                    </div>

                                    <div class="grid grid-cols-[130px_1fr] gap-2">
                                        <span class="font-semibold">Hubungan Keluarga</span><span>: ${agt.hubungan_keluarga ?? '-'}</span>
                                    </div>

                                    <div class="grid grid-cols-[130px_1fr] gap-2">
                                        <span class="font-semibold">Status Perkawinan</span><span>: ${agt.status_perkawinan ?? '-'}</span>
                                    </div>
                                </div>
                                `;
                            container.innerHTML = agtDetail;
                        }
                    });

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

        function getKelurahanId() {
            return window.App?.user?.nakesDetail?.kelurahan_id ?? null;
        }

        let posyanduData = [];

        async function loadPosyandu() {
            const kelurahanId = getKelurahanId();

            if (!kelurahanId) {
                showErrorToast("Kelurahan tidak ditemukan di akun Anda");
                return;
            }

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

        document.getElementById("searchBtn").addEventListener("click", () => {
            fetchKkWithFilter();
        });

        async function fetchKkWithFilter() {
            const search = document.getElementById("searchInput").value || "";
            const kelurahan_id = document.getElementById("kelurahan_id").value || "";
            const posyandu_id = document.getElementById("posyandu_id").value || "";

            const url = new URL("{{ url('api/monitoring/nik-per-kk') }}", window.location.origin);

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
        }

        fetchKk();
        loadPosyandu();
    });
</script>
@endsection