@extends('layouts.main')

@section('title', 'Skrining NIK')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 id="judulSkrining"
        class="text-2xl font-bold mb-6 text-center sm:text-left">
        Skrining NIK
    </h2>

    <div id="contentAwal" class="bg-white border border-[#61359C] rounded-2xl p-6 mb-6">
        <div class="max-w-md mx-auto">
            <div class="mb-4">
                <label class="block text-sm font-semibold mb-1">
                    Nomor Kartu Keluarga
                </label>
                <x-dropdown
                    id="kkDropdown"
                    label="Pilih No KK"
                    :options="[]"
                    width="w-full"
                    :searchable="true"
                    data-dropdown="filter" />
                <input type="hidden" id="keluarga_id">
                <p class="text-red-500 text-xs mt-1 hidden" id="error-keluarga_id"></p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold mb-1">
                    Siklus
                </label>
                <x-dropdown
                    id="siklusDropdown"
                    label="Pilih Siklus"
                    :options="[]"
                    width="w-full"
                    data-dropdown="filter" />
                <input type="hidden" id="selected_siklus_id">
                <p class="text-red-500 text-xs mt-1 hidden" id="error-selected_siklus_id"></p>
            </div>

            <div class="flex justify-end">
                <button type="button"
                    id="btnStartSkrining"
                    class="bg-[#61359C] text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-[#512c82] transition">
                    Selanjutnya
                </button>
            </div>
        </div>
    </div>
    <div id="skriningSection" class="hidden">
        <div class="mb-8">
            <div class="flex border-b-4 border-[#61359C]">
                <button id="tabIdentitas"
                    class="tab-btn relative flex-1 text-center py-2 text-sm font-bold text-[#61359C]">
                    <span id="labelTabIdentitas">Data Identitas</span>
                    <span class="tab-underline absolute left-0 bottom-0 w-full h-[4px] rounded-t"></span>
                </button>

                <button id="tabPertanyaan"
                    class="tab-btn relative flex-1 text-center py-2 text-sm font-bold text-gray-400">
                    Pertanyaan Skrining
                    <span class="absolute left-0 bottom-0 w-full h-[4px] bg-transparent rounded-t"></span>
                </button>
            </div>
        </div>

        <form id="formIdentitas">
            <div id="contentIdentitas">
                <div class="text-left">
                    <label for="user_id" class="block text-sm font-semibold mb-1">
                        Petugas
                    </label>
                    <x-dropdown
                        id="userDropdown"
                        label="Pilih Petugas"
                        :options="[]"
                        width="w-full sm:w-56"
                        data-dropdown="filter" />
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-user_id"></p>
                    <input type="hidden" name="user_id" id="user_id">
                </div>
                <div class="bg-white border border-[#61359C] rounded-2xl p-6 mb-4 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Hubungan Keluarga
                        </label>
                        <x-dropdown
                            id="hubunganDropdown"
                            label="Pilih Hubungan Keluarga"
                            :options="[
                                    'Kepala Keluarga', 'Istri',
                                    'Anak', 'Menantu', 'Cucu',
                                    'Orang Tua', 'Famili Lain', 'Pembantu / Asisten', 'Lainnya'
                                    ]"
                            width="w-full"
                            data-dropdown="filter" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="error-hubungan_keluarga"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            NIK / Nomor KTP
                        </label>
                        <input type="text"
                            id="nik"
                            maxlength="16"
                            inputmode="numeric"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                        <p class="text-red-500 text-xs mt-1 hidden" id="error-nik"></p>
                        <div class="flex items-center gap-2 mt-2">
                            <input type="checkbox"
                                id="tidak_punya_nik"
                                class="w-4 h-4 accent-[#61359C]">
                            <label for="tidak_punya_nik" class="text-sm text-gray-600">
                                Tidak punya NIK / Nomor KTP
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">
                            Nama Lengkap
                        </label>
                        <input type="text"
                            id="nama_lengkap"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                        <p class="text-red-500 text-xs mt-1 hidden" id="error-nama"></p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Tempat Lahir
                            </label>
                            <input type="text"
                                id="tempat_lahir"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                            <p class="text-red-500 text-xs mt-1 hidden" id="error-tempat_lahir"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Tanggal Lahir
                            </label>
                            <input type="date"
                                id="tanggal_lahir"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                            <p class="text-red-500 text-xs mt-1 hidden" id="error-tanggal_lahir"></p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Jenis Kelamin
                            </label>
                            <x-dropdown
                                id="jenisKelaminDropdown"
                                label="Pilih Jenis Kelamin"
                                :options="['Laki-laki', 'Perempuan']"
                                width="w-full"
                                data-dropdown="filter" />
                            <p class="text-red-500 text-xs mt-1 hidden" id="error-jenis_kelamin"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Pendidikan Terakhir
                            </label>
                            <x-dropdown
                                id="pendidikanDropdown"
                                label="Pilih Pendidikan"
                                :options="[
                                            'S1 / S2 / S3 (PT)', 'D1 / D2 / D3',
                                            'SMA atau sederajat', 'SMP atau sederajat', 'SD atau sederajat',
                                            'Tidak pernah sekolah', 'Belum sekolah'
                                        ]"
                                width="w-full"
                                data-dropdown="filter" />
                            <p class="text-red-500 text-xs mt-1 hidden" id="error-pendidikan_terakhir"></p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Pekerjaan
                            </label>
                            <x-dropdown
                                id="pekerjaanDropdown"
                                label="Pilih Pekerjaan"
                                :options="[ 
                                'Tidak Bekerja', 'Pelajar / Mahasiswa', 
                                'PNS / TNI / POLRI / BUMN / BUMD', 'Pegawai Swasta',
                                'Wiraswasta', 'Petani / Nelayan', 'Pedagang',
                                'Pengusaha', 'Ibu Rumah Tangga' 
                                ]"
                                width="w-full"
                                data-dropdown="filter" />
                            <p class="text-red-500 text-xs mt-1 hidden" id="error-pekerjaan"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Status Perkawinan
                            </label>
                            <x-dropdown
                                id="statusDropdown"
                                label="Pilih Status Perkawinan"
                                :options="[ 'Kawin', 'Belum Kawin', 'Cerai Hidup', 'Cerai Mati' ]"
                                width="w-full"
                                data-dropdown="filter" />
                            <p class="text-red-500 text-xs mt-1 hidden" id="error-status_perkawinan"></p>
                        </div>
                    </div>


                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <div class="flex gap-2">
                        <button type="button"
                            id="btnBackSiklus"
                            class="border border-gray-300 text-white
                                text-sm font-semibold px-4 py-1.5 rounded-lg
                                bg-gray-400 hover:opacity-90 transition">
                            Kembali
                        </button>
                        <button type="button"
                            id="btnNextTab"
                            class="bg-[#61359C] text-white px-3 py-1.5 text-sm font-semibold rounded-lg hover:bg-[#512c82] transition">
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div id="contentPertanyaan" class="hidden">
            <div id="pertanyaanContainer" class="space-y-3"></div>
            <div class="flex justify-end mt-4">
                <div class="flex gap-2">
                    <button type="button"
                        id="btnBackTab"
                        class="border border-gray-300 text-white
                       text-sm font-semibold px-4 py-1.5 rounded-lg
                       bg-gray-400 hover:opacity-90 transition">
                        Kembali
                    </button>

                    <button type="button"
                        id="btnKirim"
                        class="bg-[#61359C] text-white
                       text-sm font-semibold px-4 py-1.5 rounded-lg
                       hover:bg-[#512c82] transition">
                        Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        //section 1: pilih no kk & siklus
        let kkData = [];

        async function loadKk() {
            try {
                const res = await fetch(`{{ url('api/identitas_keluarga') }}`);
                const json = await res.json();

                const unitList = json.data.list || [];

                kkData = [];

                unitList.forEach(unit => {
                    if (unit.keluarga) {
                        kkData.push(...unit.keluarga);
                    }
                });

                renderKkDropdown();

            } catch (error) {
                console.error('Gagal load kk:', error);
            }
        }

        function renderKkDropdown() {
            const wrapper = document.getElementById('kkDropdown');
            const dropdown = wrapper.querySelector('.dropdown-menu');

            const searchInput = dropdown.querySelector('input');

            dropdown.innerHTML = '';

            if (searchInput) {
                dropdown.appendChild(searchInput);
            }

            if (!kkData.length) {
                const empty = document.createElement('div');
                empty.className = "px-4 py-2 text-sm text-gray-400 text-center";
                empty.textContent = "Tidak ada data kk";
                dropdown.appendChild(empty);
                return;
            }

            kkData.forEach(kk => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className =
                    'dropdown-item block w-full text-center px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 transition';
                btn.textContent = kk.no_kk;

                btn.onclick = () => {
                    wrapper.querySelector('.dropdown-selected').textContent = kk.no_kk;
                    dropdown.classList.add('hidden');

                    const keluargaInput = document.getElementById('keluarga_id');
                    keluargaInput.value = kk.id;

                    keluargaInput.dataset.kepala_nama =
                        kk.kepala_keluarga?.nama || '';

                    keluargaInput.dataset.kepala_nik =
                        kk.kepala_keluarga?.nik || '';

                };

                dropdown.appendChild(btn);

            });
        }

        let siklusData = [];

        async function loadSiklus() {
            try {
                const res = await fetch(`{{ url('api/kategori') }}`);
                const json = await res.json();

                const allData = json.data.list || [];

                siklusData = allData.filter(item =>
                    item.target_skrining &&
                    item.target_skrining.toLowerCase() === 'nik'
                );

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

                    document.getElementById('selected_siklus_id').value = siklus.id;

                    document.getElementById('selected_siklus_id').dataset.nama =
                        siklus.nama_kategori;
                };

                dropdown.appendChild(btn);
            });
        }

        function setDropdownLabel(id, text, fallback) {
            const el = document.getElementById(id);
            if (!el) return;

            const label = el.querySelector('.dropdown-selected');
            if (label) label.textContent = text || fallback;
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

        const btnStartSkrining = document.getElementById('btnStartSkrining');
        const contentAwal = document.getElementById('contentAwal');
        const skriningSection = document.getElementById('skriningSection');

        btnStartSkrining.addEventListener('click', () => {
            document.getElementById('error-keluarga_id').classList.add('hidden');
            document.getElementById('error-selected_siklus_id').classList.add('hidden');

            const noKk = document.getElementById('keluarga_id').value;
            const siklusInput = document.getElementById('selected_siklus_id');
            const siklusId = siklusInput.value;
            const namaSiklus = siklusInput.dataset.nama || '';

            let hasError = false;

            if (!noKk) {
                document.getElementById('error-keluarga_id').textContent = "Nomor KK wajib dipilih.";
                document.getElementById('error-keluarga_id').classList.remove('hidden');
                hasError = true;
            }

            if (!siklusId) {
                document.getElementById('error-selected_siklus_id').textContent = "Siklus wajib dipilih.";
                document.getElementById('error-selected_siklus_id').classList.remove('hidden');
                hasError = true;
            }

            if (hasError) return;

            if (namaSiklus) {
                document.getElementById('judulSkrining').innerText =
                    `Skrining NIK (${namaSiklus})`;

                document.getElementById('labelTabIdentitas').innerText =
                    `Data Identitas ${namaSiklus}`;
            }

            contentAwal.classList.add('hidden');
            skriningSection.classList.remove('hidden');

            tabIdentitas.click();
        });

        //section 2: skrining section
        const tabIdentitas = document.getElementById('tabIdentitas');
        const tabPertanyaan = document.getElementById('tabPertanyaan');

        const contentIdentitas = document.getElementById('contentIdentitas');
        const contentPertanyaan = document.getElementById('contentPertanyaan');

        function setActiveTab(activeTab) {
            const tabs = [tabIdentitas, tabPertanyaan];

            tabs.forEach(tab => {
                const underline = tab.querySelector('.tab-underline');

                tab.classList.remove('text-[#61359C]');
                tab.classList.add('text-gray-400');

                if (underline) {
                    underline.classList.remove('bg-[#61359C]/30');
                    underline.classList.add('bg-transparent');
                }
            });

            const activeUnderline = activeTab.querySelector('.tab-underline');

            activeTab.classList.remove('text-gray-400');
            activeTab.classList.add('text-[#61359C]');

            if (activeUnderline) {
                activeUnderline.classList.remove('bg-transparent');
                activeUnderline.classList.add('bg-[#61359C]/30');
            }
        }

        tabIdentitas.addEventListener('click', () => {
            contentIdentitas.classList.remove('hidden');
            contentPertanyaan.classList.add('hidden');
            setActiveTab(tabIdentitas);
        });

        tabPertanyaan.addEventListener('click', async () => {
            const valid = await validateIdentitas();
            if (!valid) return;

            contentIdentitas.classList.add('hidden');
            contentPertanyaan.classList.remove('hidden');
            setActiveTab(tabPertanyaan);

            if (!document.getElementById('pertanyaanContainer').hasChildNodes()) {
                fetchPertanyaan();
            }
        });

        const btnBackTab = document.getElementById('btnBackTab');
        const btnNextTab = document.getElementById('btnNextTab');
        const btnBackSiklus = document.getElementById('btnBackSiklus');

        btnBackTab.addEventListener('click', () => {
            tabIdentitas.click();
        });

        btnNextTab.addEventListener('click', async () => {
            const valid = await validateIdentitas();

            if (!valid) return;

            contentIdentitas.classList.add('hidden');
            contentPertanyaan.classList.remove('hidden');
            setActiveTab(tabPertanyaan);

            if (!document.getElementById('pertanyaanContainer').hasChildNodes()) {
                fetchPertanyaan();
            }
        });

        btnBackSiklus.addEventListener('click', function() {
            contentAwal.classList.remove('hidden');

            skriningSection.classList.add('hidden');

            document.getElementById('formIdentitas').reset();

            document.getElementById('pertanyaanContainer').innerHTML = '';

            document.getElementById('judulSkrining').innerText = 'Skrining NIK';
            document.getElementById('labelTabIdentitas').innerText = 'Data Identitas';

        });

        async function fetchPertanyaan() {
            try {
                const siklusId = document.getElementById('selected_siklus_id').value;

                if (!siklusId) {
                    showErrorToast("Siklus belum dipilih");
                    return;
                }

                const response = await fetch(`/api/pertanyaan?kategori_id=${siklusId}`);
                const result = await response.json();

                if (!result?.data?.list) return;

                renderPertanyaan(result.data.list);

            } catch (error) {
                document.getElementById('pertanyaanContainer').innerHTML = `
                    <div class="text-red-500 text-center py-6">
                        Gagal memuat data pertanyaan
                    </div>
                `;
            }
        }

        function renderPertanyaan(list) {
            const container = document.getElementById('pertanyaanContainer');
            container.innerHTML = "";

            const filtered = list.filter(item =>
                item.target_skrining?.toLowerCase() === 'nik'
            );

            if (!filtered.length) {
                container.innerHTML = `
                    <div class="text-gray-500 text-center py-6">
                        Tidak ada pertanyaan skrining NIK.
                    </div>
                `;
                return;
            }

            const grouped = filtered.reduce((acc, item) => {
                if (!acc[item.section_id]) {
                    acc[item.section_id] = {
                        judul_section: item.judul_section,
                        items: []
                    };
                }
                acc[item.section_id].items.push(item);
                return acc;
            }, {});

            Object.values(grouped).forEach(section => {
                const sectionTitle = document.createElement('div');
                sectionTitle.className = `
                    border border-[#61359C]/80
                    rounded-xl
                    px-5 py-3
                    font-bold
                    text-[#61359C]
                    text-sm
                    bg-[#61359C]/5
                `;
                sectionTitle.innerText = section.judul_section;

                container.appendChild(sectionTitle);

                section.items
                    .sort((a, b) => a.no_urut - b.no_urut)
                    .forEach((item, index) => {

                        const pertanyaanItem = document.createElement('div');
                        pertanyaanItem.setAttribute('data-pertanyaan-id', item.id);
                        pertanyaanItem.setAttribute('data-required', item.is_required ? 1 : 0);
                        pertanyaanItem.className = `
                            border border-gray-200
                            rounded-xl
                            px-5 py-3
                            bg-white
                            text-sm
                        `;
                        pertanyaanItem.innerHTML = `
                            <div class="font-semibold text-gray-800 leading-snug">
                                ${index + 1}. ${item.pertanyaan} 
                                ${
                                    item.is_required
                                    ? `<span class="text-red-500 ml-1">*</span>`
                                    : ''
                                }
                            </div>
                           ${
                                item.keterangan
                                    ? `<div class="text-xs text-gray-500 mt-1 leading-snug whitespace-pre-line">${item.keterangan.trim()}</div>`
                                    : ''
                            }
                            <div class="mt-3 space-y-2 text-sm text-gray-700">
                                ${renderInputJawaban(item)}
                                <p class="text-red-500 text-xs mt-2 hidden error-pertanyaan"></p>
                            </div>
                        `;
                        container.appendChild(pertanyaanItem);
                    });
            });
        }

        function renderInputJawaban(item) {
            switch (item.jenis_jawaban) {
                case 'radio':
                    return item.opsi_jawaban?.map(opt => `
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="radio"
                                name="pertanyaan_${item.id}"
                                class="w-4 h-4 accent-[#61359C]">
                            <span>${opt}</span>
                        </label>
                    `).join('') || '-';
                case 'checkbox':
                    return item.opsi_jawaban?.map(opt => `
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox"
                                class="w-4 h-4 accent-[#61359C]">
                            <span>${opt}</span>
                        </label>
                    `).join('') || '-';
                case 'select':
                    return renderCustomDropdown(item);
                case 'text':
                    return `
                        <input type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                    `;
                case 'textarea':
                    return `
                        <textarea rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"></textarea>
                    `;
                case 'date':
                    return `
                        <input type="date"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                    `;
                default:
                    return '-';
            }
        }

        function renderCustomDropdown(item) {
            const options = item.opsi_jawaban || [];
            return `
                <div class="relative block text-left w-full custom-dropdown">
                    <button type="button"
                        class="relative flex items-center justify-between w-full 
                        border border-[#00000033] text-sm rounded-lg px-4 py-2 
                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 bg-white"
                        onclick="toggleDropdown(this)">
                        
                        <span class="dropdown-selected text-left w-full truncate text-gray-500">
                            Pilih Opsi
                        </span>

                        <svg class="w-4 h-4 absolute right-3 transition-transform duration-200"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="dropdown-menu hidden absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl py-2 border border-gray-100">

                        ${options.map(opt => `
                            <button type="button"
                                class="dropdown-item block w-full text-center px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 transition"
                                onclick="selectDropdownOption(this, '${opt}')">
                                ${opt}
                            </button>
                        `).join('')}

                    </div>
                </div>
            `;
        }

        function getDropdownValue(id) {
            const wrapper = document.getElementById(id);
            if (!wrapper) return null;

            const selected = wrapper.querySelector('.dropdown-selected');
            const value = selected?.innerText.trim();

            if (!value || value === 'Pilih Jenis Kelamin' ||
                value === 'Pilih Pendidikan' ||
                value === 'Pilih Hubungan Keluarga' ||
                value === 'Pilih Status Perkawinan' ||
                value === 'Pilih Pekerjaan') {
                return null;
            }

            return value;
        }

        function mapJenisKelamin(value) {
            if (!value) return null;

            if (value === 'Laki-laki') return 'L';
            if (value === 'Perempuan') return 'P';

            return null;
        }

        const hubunganWrapper = document.getElementById('hubunganDropdown');
        const hubunganLabel = hubunganWrapper.querySelector('.dropdown-selected');

        const observer = new MutationObserver(() => {
            const hubungan = hubunganLabel.innerText.trim();
            const keluargaInput = document.getElementById('keluarga_id');

            const kepalaNama = keluargaInput.dataset.kepala_nama || '';
            const kepalaNik = keluargaInput.dataset.kepala_nik || '';

            const namaInput = document.getElementById('nama_lengkap');
            const nikInput = document.getElementById('nik');

            if (hubungan === 'Kepala Keluarga') {
                namaInput.value = kepalaNama;
                nikInput.value = kepalaNik;

                namaInput.setAttribute('readonly', true);
                nikInput.setAttribute('readonly', true);

            } else {
                namaInput.value = '';
                nikInput.value = '';

                namaInput.removeAttribute('readonly');
                nikInput.removeAttribute('readonly');
            }

        });

        observer.observe(hubunganLabel, {
            childList: true
        });

        observer.observe(hubunganLabel, {
            childList: true
        });
        async function validateIdentitas() {
            resetErrorsTextOnly();

            const identitasPayload = {
                keluarga_id: document.getElementById('keluarga_id').value.trim(),
                nik: document.getElementById('nik').value.trim(),
                nama: document.getElementById('nama_lengkap').value.trim(),
                tempat_lahir: document.getElementById('tempat_lahir').value.trim(),
                tanggal_lahir: document.getElementById('tanggal_lahir').value,
                jenis_kelamin: mapJenisKelamin(
                    getDropdownValue('jenisKelaminDropdown')
                ),
                pendidikan_terakhir: getDropdownValue('pendidikanDropdown'),
                hubungan_keluarga: getDropdownValue('hubunganDropdown'),
                status_perkawinan: getDropdownValue('statusDropdown'),
                pekerjaan: getDropdownValue('pekerjaanDropdown')
            };

            const response = await fetch(`/api/identitas_anggota?validate_only=1`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(identitasPayload)
            });

            const result = await response.json();

            if (response.status === 422) {
                showErrors(result.errors);
                return false;
            }

            return true;
        }

        function showErrors(errors) {
            Object.keys(errors).forEach(key => {

                if (key.startsWith("keluarga.")) {
                    const parts = key.split(".");
                    const index = parts[1];
                    const field = parts[2];

                    const kkItem = document.querySelectorAll('.kk-item')[index];
                    const errorEl = kkItem?.querySelector(`.error-${field}`);

                    if (errorEl) {
                        errorEl.textContent = errors[key][0];
                        errorEl.classList.remove("hidden");
                    }
                } else {
                    const errorEl =
                        document.getElementById("error-" + key) ||
                        document.querySelector(`[data-error="${key}"]`);

                    if (errorEl) {
                        errorEl.textContent = errors[key][0];
                        errorEl.classList.remove("hidden");
                    }
                }
            });
        }

        function resetErrorsTextOnly() {
            document.querySelectorAll('[id^="error-"], [class*="error-"], [data-error]')
                .forEach(el => {
                    el.textContent = "";
                    el.classList.add("hidden");
                });
        }




        const btnKirim = document.getElementById('btnKirim');
        btnKirim.addEventListener('click', async () => {
            resetErrorsTextOnly();

            function scrollToFirstError() {
                const firstError = document.querySelector(
                    '#contentIdentitas .text-red-500:not(.hidden), #contentPertanyaan .text-red-500:not(.hidden)'
                );

                if (!firstError) return;

                if (firstError.closest('#contentIdentitas')) {
                    tabIdentitas.click();
                } else if (firstError.closest('#contentPertanyaan')) {
                    tabPertanyaan.click();
                }

                const fieldWrapper = firstError.closest('div');
                const input = fieldWrapper?.querySelector('input, textarea, button');

                firstError.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    input?.focus();
                }, 400);
            }

            let pertanyaanError = false;
            const jawaban = [];

            document.querySelectorAll('#pertanyaanContainer > div[data-pertanyaan-id]')
                .forEach(wrapper => {

                    const isRequired = wrapper.dataset.required === "1";
                    const pertanyaanId = wrapper.getAttribute('data-pertanyaan-id');
                    let value = null;

                    const radio = wrapper.querySelector('input[type="radio"]:checked');
                    if (radio) {
                        value = radio.nextElementSibling?.innerText ?? null;
                    }

                    const checkboxes = wrapper.querySelectorAll('input[type="checkbox"]:checked');
                    if (checkboxes.length) {
                        value = Array.from(checkboxes)
                            .map(cb => cb.nextElementSibling?.innerText ?? '')
                            .join(', ');
                    }

                    const textInput = wrapper.querySelector('input[type="text"]');
                    if (textInput && textInput.value.trim()) {
                        value = textInput.value.trim();
                    }

                    const textarea = wrapper.querySelector('textarea');
                    if (textarea && textarea.value.trim()) {
                        value = textarea.value.trim();
                    }

                    const dateInput = wrapper.querySelector('input[type="date"]');
                    if (dateInput && dateInput.value) {
                        value = dateInput.value;
                    }

                    const dropdownSelected = wrapper.querySelector('.dropdown-selected');
                    if (dropdownSelected && dropdownSelected.innerText !== 'Pilih Opsi') {
                        value = dropdownSelected.innerText;
                    }

                    if (isRequired && !value) {
                        pertanyaanError = true;

                        wrapper.classList.add('border-red-500');
                        wrapper.classList.remove('border-gray-200');

                        const input = wrapper.querySelector('input, textarea, button, .dropdown-selected');
                        if (input) input.focus();
                    } else {
                        wrapper.classList.remove('border-red-500');
                        wrapper.classList.add('border-gray-200');
                    }

                    if (value !== null) {
                        jawaban.push({
                            pertanyaan_id: pertanyaanId,
                            anggota_keluarga_id: null,
                            value_jawaban: value
                        });
                    }
                });

            let hasError = pertanyaanError;

            document.querySelectorAll('#contentIdentitas .text-red-500:not(.hidden)').forEach(el => {
                hasError = true;
            });

            if (hasError) {
                scrollToFirstError();
                return;
            }
            try {
                const identitasPayload = {
                    keluarga_id: document.getElementById('keluarga_id').value,
                    nik: document.getElementById('nik').value,
                    nama: document.getElementById('nama_lengkap').value,
                    tempat_lahir: document.getElementById('tempat_lahir').value,
                    tanggal_lahir: document.getElementById('tanggal_lahir').value,
                    jenis_kelamin: mapJenisKelamin(
                        getDropdownValue('jenisKelaminDropdown')
                    ),
                    pendidikan_terakhir: getDropdownValue('pendidikanDropdown'),
                    hubungan_keluarga: getDropdownValue('hubunganDropdown'),
                    status_perkawinan: getDropdownValue('statusDropdown'),
                    pekerjaan: getDropdownValue('pekerjaanDropdown')
                };

                const identitasResponse = await fetch(`/api/identitas_anggota`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(identitasPayload)
                });

                const identitasResult = await identitasResponse.json();

                if (identitasResponse.status === 422) {
                    showErrors(identitasResult.errors);
                    return;
                }

                if (!identitasResponse.ok) {
                    showErrorToast("Gagal menyimpan identitas anggota");
                    return;
                }

                const anggotaId = identitasResult.data?.id;

                if (!anggotaId) {
                    showErrorToast("ID anggota tidak ditemukan");
                    return;
                }

                const userId = document.getElementById('user_id').value;

                if (!userId) {
                    showErrorToast("Petugas belum dipilih");
                    return;
                }

                const keluargaId = document.getElementById('keluarga_id').value;

                jawaban.forEach(j => {
                    j.anggota_keluarga_id = anggotaId;
                });

                const skriningPayload = {
                    keluarga_id: keluargaId,
                    anggota_keluarga_id: anggotaId,
                    user_id: userId,
                    tanggal_skrining: new Date().toISOString().split('T')[0],
                    jawaban: jawaban
                };

                const skriningResponse = await fetch(`/api/skrining`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(skriningPayload)
                });

                const skriningResult = await skriningResponse.json();

                if (skriningResponse.ok) {
                    showSuccessToast("Skrining KK berhasil terkirim!");
                    window.location.reload();
                } else {
                    showErrorToast("Gagal menyimpan skrining");
                }

            } catch (error) {
                console.error(error);
                showErrorToast("Terjadi kesalahan server");
            }
        });

        let userData = [];

        async function loadUsers() {
            try {
                const res = await fetch(`{{ url('api/users') }}`);
                const json = await res.json();

                userData = json.data.list || [];
                renderUserDropdown();
            } catch (error) {
                console.error('Gagal load users:', error);
            }
        }

        function renderUserDropdown() {
            const dropdown = document
                .getElementById('userDropdown')
                .querySelector('.dropdown-menu');

            dropdown.innerHTML = '';

            if (!userData.length) {
                dropdown.innerHTML = `
                    <div class="px-4 py-2 text-sm text-gray-400 text-center">
                        Tidak ada data petugas
                    </div>
                `;
                return;
            }

            userData.forEach(user => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = user.nama;

                btn.onclick = () => {
                    setDropdownLabel('userDropdown', user.nama, 'Pilih Petugas');
                    document.getElementById('user_id').value = user.id;
                };

                dropdown.appendChild(btn);
            });
        }

        const nikInput = document.getElementById('nik');
        const tidakPunyaNik = document.getElementById('tidak_punya_nik');

        tidakPunyaNik.addEventListener('change', function() {
            if (this.checked) {
                nikInput.value = '0'.repeat(16);

                nikInput.setAttribute('readonly', true);
                nikInput.classList.add('bg-gray-100', 'cursor-not-allowed');

                const errorNik = document.getElementById('error-nik');
                if (errorNik) {
                    errorNik.textContent = '';
                    errorNik.classList.add('hidden');
                }

            } else {
                nikInput.value = '';

                nikInput.removeAttribute('readonly');
                nikInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        });

        loadKk();
        loadUsers();
        loadSiklus();
    });
</script>
@endsection