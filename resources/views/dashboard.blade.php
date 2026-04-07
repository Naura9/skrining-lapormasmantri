@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="flex flex-col gap-4 w-full max-w-3xl mx-auto text-base">
    <div class="grid grid-cols-2 gap-3 mb-3">
        <button id="btnSkriningKK"
            class="h-14 rounded-full bg-[#61359C] text-white border-2 border-[#61359C]
           flex items-center justify-center gap-3 px-4
           hover:bg-[#4B1F8B] transition-all">
            <i class="fa-solid fa-users text-xl"></i>
            <span class="text-sm font-semibold">Skrining KK</span>
        </button>

        <button id="btnSkriningNIK"
            class="h-14 rounded-full bg-white text-[#61359C] border-2 border-[#61359C]/40
           flex items-center justify-center gap-3 px-4
           hover:bg-[#F4E8FF] transition-all">
            <i class="fa-solid fa-user text-xl"></i>
            <span class="text-sm font-semibold">Skrining NIK</span>
        </button>
    </div>
    
    <x-dropdown
        id="nikCategoryDropdown"
        label="Pilih Siklus"
        :options="[]"
        width="w-full h-10"
        data-dropdown="filter" />

    <x-dropdown
        id="pertanyaanFilterDropdown"
        label="Cari Pertanyaan"
        :options="[]"
        width="w-full h-10"
        data-dropdown="filter" />

    <div class="flex flex-col sm:flex-row gap-3 w-full flex-wrap">
        <x-dropdown
            id="kelurahanFilterDropdown"
            label="Pilih Kelurahan"
            :options="[]"
            width="w-full sm:flex-1 h-10"
            data-dropdown="filter" />

        <x-dropdown
            id="posyanduFilterDropdown"
            label="Pilih Posyandu"
            :options="[]"
            width="w-full sm:flex-1 h-10"
            data-dropdown="filter" />

        <button id="searchBtn"
            class="h-9 px-4 bg-[#61359C] text-white text-sm rounded-lg shadow-sm hover:bg-[#4B1F8B] transition-all duration-200 w-full sm:w-auto flex items-center justify-center">
            <i class="fa-solid fa-magnifying-glass mr-2"></i> Cari
        </button>
    </div>

    <input type="hidden" id="skrining_type" value="">
    <input type="hidden" id="pertanyaan_id" value="">
    <input type="hidden" id="kelurahan_id" value="">
    <input type="hidden" id="posyandu_id" value="">

    <div id="noJawabanText" class="mt-4 text-center text-sm text-gray-500 hidden">
        Tidak ada jawaban untuk pertanyaan ini
    </div>

    <div class="mt-4 mx-auto w-full max-w-xs" style="height:250px;">
        <canvas id="jawabanPieChart"></canvas>
    </div>

    <div id="skriningTableContainer" class="mt-4 w-full !max-w-full overflow-x-auto text-sm"></div>
</div>

<div id="skriningDetailModal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50 px-4">
    <div class="bg-white rounded-xl shadow-lg w-full sm:w-11/12 md:w-10/12 lg:w-5/12 xl:w-4/12 max-w-3xl flex flex-col relative max-h-[90vh]">
        <div class="w-full px-4">
            <h3 id="skriningDetailModalTitle" class="text-lg font-bold mt-2">Detail</h3>
        </div>

        <div id="skriningDetailBody" class="px-4 py-4 w-full space-y-2 text-sm overflow-y-auto"></div>

        <div class="flex justify-center px-4 py-3">
            <button id="closeSkriningDetailModalBtn"
                class="w-full bg-[#61359C] text-white text-sm font-semibold py-2 rounded-lg hover:bg-[#61359C]/80 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        let pertanyaanData = [];
        setDropdownDisabled('kelurahanFilterDropdown', true);
        setDropdownDisabled('posyanduFilterDropdown', true);

        async function loadPertanyaan() {
            const res = await fetch("{{ url('api/pertanyaan') }}", {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            });
            const result = await res.json();
            pertanyaanData = result?.data?.list || [];

            setActiveButton('btnSkriningKK');
            document.getElementById('skrining_type').value = 'kk';
            document.getElementById('nikCategoryDropdown').classList.add('hidden');
            const filtered = pertanyaanData.filter(p => p.target_skrining.toLowerCase() === 'kk');
            renderPertanyaanDropdown(filtered);
        }

        function setActiveButton(activeId) {
            ['btnSkriningKK', 'btnSkriningNIK'].forEach(id => {
                const btn = document.getElementById(id);

                const icon = btn.querySelector("i");
                const label = btn.querySelector("span");

                if (id === activeId) {
                    // ACTIVE
                    btn.classList.remove(
                        'bg-white',
                        'text-[#61359C]',
                        'border-[#61359C]/40',
                        'hover:bg-[#F4E8FF]'
                    );
                    btn.classList.add(
                        'bg-[#61359C]',
                        'text-white',
                        'border-[#61359C]',
                        'hover:bg-[#4B1F8B]'
                    );

                    icon.classList.remove('text-[#61359C]');
                    icon.classList.add('text-white');

                } else {
                    // NON-ACTIVE
                    btn.classList.remove(
                        'bg-[#61359C]',
                        'text-white',
                        'border-[#61359C]',
                        'hover:bg-[#4B1F8B]'
                    );
                    btn.classList.add(
                        'bg-white',
                        'text-[#61359C]',
                        'border-[#61359C]/40',
                        'hover:bg-[#F4E8FF]'
                    );

                    icon.classList.remove('text-white');
                    icon.classList.add('text-[#61359C]');
                }
            });

            // Disable dropdown jika mode NIK
            if (activeId === 'btnSkriningNIK') {
                setDropdownDisabled('kelurahanFilterDropdown', true);
                setDropdownDisabled('posyanduFilterDropdown', true);

                setDropdownLabel('kelurahanFilterDropdown', null, 'Pilih Kelurahan');
                setDropdownLabel('posyanduFilterDropdown', null, 'Pilih Posyandu');

                document.getElementById('kelurahan_id').value = '';
                document.getElementById('posyandu_id').value = '';
            }
        }

        function renderPertanyaanDropdown(data) {
            const wrapper = document.getElementById('pertanyaanFilterDropdown');
            const menu = wrapper.querySelector('.dropdown-menu');
            menu.innerHTML = '';

            if (!data.length) {
                menu.innerHTML = `<div class="px-4 py-2 text-sm text-gray-400 text-center">Tidak ada pertanyaan</div>`;
                return;
            }

            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Cari pertanyaan...';
            searchInput.className =
                'w-full px-3 py-1 mb-2 text-sm border border-gray-300 rounded focus:outline-none focus:ring focus:ring-gray-300';
            menu.appendChild(searchInput);

            const listContainer = document.createElement('div');
            menu.appendChild(listContainer);

            function renderList(filteredData) {
                listContainer.innerHTML = '';

                if (!filteredData.length) {
                    listContainer.innerHTML =
                        `<div class="px-4 py-2 text-sm text-gray-400 text-center">Tidak ada pertanyaan</div>`;
                    return;
                }

                filteredData.forEach(p => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'dropdown-item block w-full text-left px-4 py-1 text-sm hover:bg-gray-100';
                    btn.textContent = p.pertanyaan;

                    btn.onclick = () => {
                        wrapper.querySelector('.dropdown-selected').textContent = p.pertanyaan;
                        menu.classList.add('hidden');
                        document.getElementById('pertanyaan_id').value = p.id;

                        setDropdownDisabled('kelurahanFilterDropdown', false);
                        setDropdownLabel('kelurahanFilterDropdown', null, 'Pilih Kelurahan');
                        document.getElementById('kelurahan_id').value = '';
                        setDropdownDisabled('posyanduFilterDropdown', true);
                        setDropdownLabel('posyanduFilterDropdown', null, 'Pilih Posyandu');
                    };

                    listContainer.appendChild(btn);
                });
            }

            renderList(data);

            searchInput.addEventListener('input', (e) => {
                const text = e.target.value.toLowerCase();
                const filtered = data.filter(p => p.pertanyaan.toLowerCase().includes(text));
                renderList(filtered);
            });

            setDropdownDisabled('pertanyaanFilterDropdown', false);
        }

        document.getElementById("searchBtn").addEventListener("click", async () => {
            const skriningType = document.getElementById("skrining_type").value;
            const pertanyaanId = document.getElementById("pertanyaan_id").value;
            const kelurahanId = document.getElementById("kelurahan_id").value;
            const posyanduId = document.getElementById("posyandu_id").value;

            if (!pertanyaanId) {
                showErrorToast("Silakan pilih pertanyaan terlebih dahulu.");
                return;
            }

            const url = new URL("{{ url('api/monitoring/hasil-skrining-chart') }}");

            if (kelurahanId) url.searchParams.append("kelurahan_id", kelurahanId);
            if (posyanduId) url.searchParams.append("posyandu_id", posyanduId);

            const res = await fetch(url, {
                headers: {
                    "Accept": "application/json"
                },
            });

            const result = await res.json();

            if (!result.status) {
                showErrorToast("Gagal mengambil data skrining");
                return;
            }

            const jawabanArray = [];
            const tableData = [];

            result.data.forEach(item => {
                if (skriningType === "kk") {
                    const kkArray = Object.values(item.skrining_kk || {});
                    kkArray.forEach(j => {
                        if (j.pertanyaan_id == pertanyaanId || j.pertanyaan === wrapperSelectedText()) {
                            jawabanArray.push(j.jawaban);
                            item.kk_di_unit.forEach(kk => {
                                tableData.push({
                                    tanggal_skrining: kk.tanggal_skrining,
                                    no_kk: kk.no_kk,
                                    jumlah_kk: item.kk_di_unit.length,
                                    alamat: `${kk.alamat_ktp ?? kk.alamat_unit ?? 'null'}, RT ${kk.rt_ktp ?? kk.rt_unit ?? 'null'}/RW ${kk.rw_ktp ?? kk.rw_unit ?? 'null'}`,
                                    kepala_keluarga: kk.kepala_keluarga,
                                    nik_kepala_keluarga: kk.nik_kepala_keluarga,
                                    no_telepon: kk.no_telepon,
                                    jawaban: j.jawaban,
                                    kelurahan: item.kelurahan ?? '-',
                                    posyandu: item.posyandu ?? '-',
                                    alamat_unit: item.alamat_unit ?? '-',
                                    rt_unit: item.rt_unit ?? '-',
                                    rw_unit: item.rw_unit ?? '-',
                                    alamat_ktp: kk.alamat_ktp,
                                    rt_ktp: kk.rt_ktp,
                                    rw_ktp: kk.rw_ktp
                                });
                            });
                        }
                    });
                } else {
                    item.skrining_nik.forEach(anggota => {
                        anggota.jawaban.forEach(j => {
                            if (j.pertanyaan === wrapperSelectedText()) {
                                jawabanArray.push(j.jawaban);
                                tableData.push({
                                    tanggal_skrining: anggota.tanggal_skrining,
                                    no: anggota.nik,
                                    nama_lengkap: anggota.nama,
                                    jenis_kelamin: anggota.jenis_kelamin,
                                    hubungan_keluarga: anggota.hubungan_keluarga,
                                    siklus: anggota.siklus ?? '-',
                                    jawaban: j.jawaban
                                });
                            }
                        });
                    });
                }
            });

            const noJawabanEl = document.getElementById("noJawabanText");
            if (jawabanArray.length === 0) {
                noJawabanEl.classList.remove("hidden");

                document.getElementById("jawabanPieChart").getContext('2d').clearRect(0, 0, 250, 250);
                document.getElementById("skriningTableContainer").innerHTML = '';

                return;
            } else {
                noJawabanEl.classList.add("hidden");
            }

            renderJawabanPieChart(jawabanArray);
            renderSkriningTable(tableData, skriningType);
        });

        function wrapperSelectedText() {
            try {
                return document
                    .querySelector('#pertanyaanFilterDropdown .dropdown-selected')
                    .textContent.trim();
            } catch {
                return "";
            }
        }

        function renderSkriningTable(data, type) {
            const tableContainer = document.getElementById('skriningTableContainer');
            tableContainer.innerHTML = '';

            if (type === 'kk') {
                const groupedByJawaban = data.reduce((acc, row) => {
                    const jawaban = row.jawaban ?? '-';

                    if (!acc[jawaban]) acc[jawaban] = [];
                    acc[jawaban].push(row);
                    return acc;
                }, {});

                Object.keys(groupedByJawaban).forEach(jawaban => {
                    const rows = groupedByJawaban[jawaban];

                    const title = document.createElement('h3');
                    title.className = 'font-semibold mt-4 mb-2 bg-gray-100 border border-[#00000033] text-sm rounded-lg px-4 py-2';
                    title.textContent = `Jawaban: ${jawaban}`;
                    tableContainer.appendChild(title);

                    const table = document.createElement('table');
                    table.className = 'min-w-full table-fixed border border-gray-200 mb-4 text-sm';

                    const thead = document.createElement('thead');
                    const tbody = document.createElement('tbody');

                    thead.innerHTML = `
                        <tr class="bg-gray-100 font-semibold text-sm">
                            <th class="px-3 py-2 border border-[#00000033] w-[15%]">No</th>
                            <th class="px-3 py-2 border border-[#00000033] w-[50%]">Alamat</th>
                            <th class="px-3 py-2 border border-[#00000033] w-[20%]">Jumlah KK</th>
                            <th class="px-3 py-2 border border-[#00000033] w-[15%]">Aksi</th>
                        </tr>
                    `;

                    const groupedByUnit = rows.reduce((acc, row) => {
                        const key = row.unit_rumah || 'unknown_unit';
                        if (!acc[key]) acc[key] = [];
                        acc[key].push(row);
                        return acc;
                    }, {});

                    Object.values(groupedByUnit).forEach((unitRows, index) => {
                        const firstKK = unitRows[0];

                        const kk_di_unit = unitRows.map(kk => ({
                            no_kk: kk.no_kk,
                            kepala_keluarga: kk.kepala_keluarga,
                            nik_kepala_keluarga: kk.nik_kepala_keluarga,
                            no_telepon: kk.no_telepon,
                            alamat_unit: kk.alamat_unit ?? '-',
                            rt_unit: kk.rt_unit ?? '-',
                            rw_unit: kk.rw_unit ?? '-',
                            alamat_ktp: kk.alamat_ktp,
                            rt_ktp: kk.rt_ktp,
                            rw_ktp: kk.rw_ktp,
                            tanggal_skrining: kk.tanggal_skrining
                        }));

                        tbody.innerHTML += `
                            <tr class="bg-white">
                                <td class="px-3 py-2 border border-[#00000033] text-center">${index + 1}</td>
                                <td class="px-3 py-2 border border-[#00000033]">${firstKK.alamat_unit}</td>
                                <td class="px-3 py-2 border border-[#00000033] text-center">${unitRows.length}</td>
                                <td class="px-3 py-2 border border-[#00000033] text-center">
                                    <button class="btn-detail px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700"
                                        data-detail='${JSON.stringify({
                                            tanggal_skrining: firstKK.tanggal_skrining ?? '',
                                            kk_di_unit: kk_di_unit,
                                            skrining_kk: firstKK.skrining_kk ?? {},
                                            alamat_unit: firstKK.alamat_unit ?? '-',
                                            rt_unit: firstKK.rt_unit ?? '-',
                                            rw_unit: firstKK.rw_unit ?? '-',
                                            kelurahan: firstKK.kelurahan ?? '-',
                                            posyandu: firstKK.posyandu ?? '-',
                                        })}'
                                        data-type="kk">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    table.appendChild(thead);
                    table.appendChild(tbody);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'overflow-x-auto w-full min-w-full';
                    wrapper.appendChild(table);

                    tableContainer.appendChild(wrapper);
                });
            } else {
                const groupedByJawaban = data.reduce((acc, row) => {
                    const key = row.jawaban ?? '-';
                    if (!acc[key]) acc[key] = [];
                    acc[key].push(row);
                    return acc;
                }, {});

                Object.keys(groupedByJawaban).forEach(jawaban => {
                    const rows = groupedByJawaban[jawaban];

                    const title = document.createElement('h3');
                    title.className = 'font-semibold mt-4 mb-2 bg-gray-100 border border-[#00000033] text-sm rounded-lg px-4 py-2';
                    title.textContent = `Jawaban: ${jawaban}`;
                    tableContainer.appendChild(title);

                    const table = document.createElement('table');
                    table.className = 'min-w-full border border-gray-200 mb-4 text-sm  whitespace-nowrap';

                    const thead = document.createElement('thead');
                    const tbody = document.createElement('tbody');

                    thead.innerHTML = `
                        <tr class="bg-gray-100 font-semibold text-sm">
                            <th class="px-3 py-2 border border-[#00000033] w-[5%]">No</th>
                            <th class="px-3 py-2 border border-[#00000033] w-[5%]">NIK</th>
                            <th class="px-3 py-2 border border-[#00000033] w-[30%]">Nama Lengkap</th>
                            <th class="px-3 py-2 border border-[#00000033] w-[25%]">Siklus</th>
                            <th class="px-3 py-2 border border-[#00000033] w-[20%]">Aksi</th>
                        </tr>
                    `;

                    rows.forEach((row, index) => {
                        tbody.innerHTML += `
                            <tr class="bg-white">
                                <td class="px-3 py-2 border border-[#00000033] text-center">${index + 1}</td>
                                <td class="px-3 py-2 border border-[#00000033] text-center">${row.no}</td>
                                <td class="px-3 py-2 border border-[#00000033]">${row.nama_lengkap}</td>
                                <td class="px-3 py-2 border border-[#00000033] text-center">${row.siklus ?? '-'}</td>
                                <td class="px-3 py-2 border border-[#00000033] text-center">
                                    <button class="btn-detail px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700"
                                        data-detail='${JSON.stringify({
                                            tanggal_skrining: row.tanggal_skrining ?? '',
                                            no_nik: row.no,
                                            nama_lengkap: row.nama_lengkap,
                                            jenis_kelamin: row.jenis_kelamin ?? '',
                                            hubungan_keluarga: row.hubungan_keluarga,
                                            siklus: row.siklus ?? '-'
                                        })}'
                                        data-type="nik">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    table.appendChild(thead);
                    table.appendChild(tbody);
                    table.appendChild(thead);
                    table.appendChild(tbody);

                    const wrapper = document.createElement('div');
                    wrapper.className = 'overflow-x-auto w-full min-w-full';
                    wrapper.appendChild(table);

                    tableContainer.appendChild(wrapper);
                });
            }
        }

        document.addEventListener('click', function(e) {
            if (e.target.matches('.btn-detail')) {
                const data = JSON.parse(e.target.dataset.detail);
                const type = e.target.dataset.type;
                showSkriningDetailModal(data, type);
            }
        });

        const skriningDetailModal = document.getElementById("skriningDetailModal");
        const skriningDetailBody = document.getElementById("skriningDetailBody");
        const closeSkriningDetailModalBtn = document.getElementById("closeSkriningDetailModalBtn");

        closeSkriningDetailModalBtn.addEventListener("click", () => {
            skriningDetailModal.classList.add("hidden");
            skriningDetailModal.classList.remove("flex");
            document.body.style.overflow = "";
        });

        function showSkriningDetailModal(data, type) {
            skriningDetailBody.innerHTML = '';

            const createRow = (label, value) => `
                <div class="grid grid-cols-[120px_10px_1fr] items-start mb-1">
                    <div class="font-semibold">${label}</div>
                    <div>:</div>
                    <div>${value ?? '-'}</div>
                </div>
            `;

            if (type === 'kk') {
                skriningDetailBody.innerHTML += createRow('Tanggal Skrining', data.tanggal_skrining);
                skriningDetailBody.innerHTML += createRow('Kelurahan', data.kelurahan ?? '-');
                skriningDetailBody.innerHTML += createRow('Posyandu', data.posyandu ?? '-');
                skriningDetailBody.innerHTML += createRow(
                    'Alamat',
                    `${data.alamat_unit ?? '-'}, RT ${data.rt_unit ?? '-'} / RW ${data.rw_unit ?? '-'}`);

                data.kk_di_unit.forEach((kk, index) => {
                    const hasAlamatKTP = kk.alamat_ktp && kk.alamat_ktp.trim() !== '';
                    const alamatRow = hasAlamatKTP ?
                        `
                        <div class="grid grid-cols-[120px_10px_1fr] items-start mb-1">
                            <div class="font-semibold">Alamat KTP</div>
                            <div>:</div>
                            <div>
                                ${kk.alamat_ktp}
                                ${kk.rt_ktp ? ', RT ' + kk.rt_ktp : ''}
                                ${kk.rw_ktp ? ' / RW ' + kk.rw_ktp : ''}
                            </div>
                        </div>` :
                        '';

                    skriningDetailBody.innerHTML += `
                        <div class="mt-3 mb-2 p-2 border border-gray-200 rounded bg-gray-50">
                            
                            ${hasAlamatKTP ? `
                                <span class="block text-xs font-medium text-red-600 bg-red-100 px-2 py-0.5 rounded-full whitespace-nowrap mb-2">
                                    Luar Wilayah
                                </span>
                            ` : ''}

                            <div class="grid grid-cols-[120px_10px_1fr] items-start mb-1">
                                <div class="font-semibold">No KK</div>
                                <div>:</div>
                                <div>${kk.no_kk}</div>
                            </div>

                            <div class="grid grid-cols-[120px_10px_1fr] items-start mb-1">
                                <div class="font-semibold">Kepala Keluarga</div>
                                <div>:</div>
                                <div>${kk.kepala_keluarga}</div>
                            </div>

                            <div class="grid grid-cols-[120px_10px_1fr] items-start mb-1">
                                <div class="font-semibold">No Telepon</div>
                                <div>:</div>
                                <div>${kk.no_telepon}</div>
                            </div>

                            ${alamatRow}

                        </div>
                    `;
                });
            } else if (type === 'nik') {
                skriningDetailBody.innerHTML = `
                    ${createRow('Tanggal Skrining', data.tanggal_skrining)}
                    ${createRow('Siklus', data.siklus ?? '-')}
                    ${createRow('No NIK', data.no_nik)}
                    ${createRow('Nama Lengkap', data.nama_lengkap)}
                    ${createRow('Jenis Kelamin', data.jenis_kelamin)}
                    ${createRow('Hubungan Keluarga', data.hubungan_keluarga)}
                `;
            }

            skriningDetailModal.classList.remove("hidden");
            skriningDetailModal.classList.add("flex");
            document.body.style.overflow = "hidden";
        }

        function renderNikCategoryDropdown() {
            const wrapper = document.getElementById('nikCategoryDropdown');
            const menu = wrapper.querySelector('.dropdown-menu');
            menu.innerHTML = '';

            const uniqueCategories = [...new Set(
                pertanyaanData.filter(p => p.target_skrining.toLowerCase() === 'nik')
                .map(p => p.nama_kategori)
            )];

            uniqueCategories.forEach(cat => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = cat;
                btn.onclick = () => {
                    wrapper.querySelector('.dropdown-selected').textContent = cat;
                    menu.classList.add('hidden');

                    const filteredPertanyaan = pertanyaanData.filter(p => p.target_skrining.toLowerCase() === 'nik' && p.nama_kategori === cat);
                    renderPertanyaanDropdown(filteredPertanyaan);
                };
                menu.appendChild(btn);
            });

            wrapper.classList.remove('hidden');
            setDropdownDisabled('nikCategoryDropdown', false);
            setDropdownDisabled('pertanyaanFilterDropdown', true);
        }

        function setDropdownLabel(id, text, fallback) {
            const el = document.getElementById(id);
            if (!el) return;

            const label = el.querySelector('.dropdown-selected');
            if (label) label.textContent = text || fallback;
        }

        let kelurahanData = [];

        async function loadKelurahan() {
            const res = await fetch(`{{ url('api/kelurahan') }}`);
            const json = await res.json();

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
            const dropdownWrapper = document.getElementById('posyanduFilterDropdown');
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

        function renderJawabanPieChart(jawabanData) {
            const counts = jawabanData.reduce((acc, val) => {
                acc[val] = (acc[val] || 0) + 1;
                return acc;
            }, {});

            const labels = Object.keys(counts);
            const data = Object.values(counts);

            const ctx = document.getElementById('jawabanPieChart').getContext('2d');

            if (window.jawabanChart) window.jawabanChart.destroy();

            window.jawabanChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        label: 'Distribusi Jawaban',
                        data,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.chart._metasets[context.datasetIndex].total;
                                    const percent = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function setDropdownDisabled(id, disabled = true) {
            const wrapper = document.getElementById(id);
            if (!wrapper) return;
            const button = wrapper.querySelector('button');
            if (disabled) {
                button.classList.add('opacity-50', 'cursor-not-allowed');
                button.setAttribute('disabled', true);
            } else {
                button.classList.remove('opacity-50', 'cursor-not-allowed');
                button.removeAttribute('disabled');
            }
        }

        function resetPertanyaanDropdown() {
            const wrapper = document.getElementById('pertanyaanFilterDropdown');
            const selected = wrapper.querySelector('.dropdown-selected');
            if (selected) selected.textContent = 'Pilih pertanyaan';
            document.getElementById('pertanyaan_id').value = '';

            const tableContainer = document.getElementById('skriningTableContainer');
            tableContainer.innerHTML = '';

            if (window.jawabanChart) {
                window.jawabanChart.destroy();
                window.jawabanChart = null;
            }
        }

        document.getElementById('btnSkriningKK').addEventListener('click', () => {
            setActiveButton('btnSkriningKK');
            resetPertanyaanDropdown();
            document.getElementById('skrining_type').value = 'kk';
            document.getElementById('nikCategoryDropdown').classList.add('hidden');
            const filtered = pertanyaanData.filter(p => p.target_skrining.toLowerCase() === 'kk');
            renderPertanyaanDropdown(filtered);
        });

        document.getElementById('btnSkriningNIK').addEventListener('click', () => {
            setActiveButton('btnSkriningNIK');
            resetPertanyaanDropdown();
            document.getElementById('skrining_type').value = 'nik';
            renderNikCategoryDropdown();
        });

        loadPertanyaan();
        loadKelurahan();
        setDropdownDisabled('posyanduFilterDropdown', true);
    });
</script>
@endsection