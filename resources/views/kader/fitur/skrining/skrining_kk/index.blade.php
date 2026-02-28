@extends('layouts.main')

@section('title', 'Skrining KK')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Skrining KK</h2>

    <div class="mb-8">
        <div class="flex border-b-4 border-[#61359C]">
            <button id="tabIdentitas"
                class="tab-btn relative flex-1 text-center py-2 text-sm font-bold text-[#61359C]">
                Data Identitas Keluarga
                <span class="absolute left-0 bottom-0 w-full h-[4px] bg-[#61359C]/50"></span>
            </button>

            <button id="tabPertanyaan"
                class="tab-btn relative flex-1 text-center py-2 text-sm font-bold text-gray-400">
                Pertanyaan Skrining
                <span class="absolute left-0 bottom-0 w-full h-[4px] bg-transparent"></span>
            </button>
        </div>
    </div>

    <form id="formIdentitas">
        <div id="contentIdentitas">
            <div class="bg-white border border-[#61359C] rounded-2xl p-6 mb-4">
                <div id="kkContainer" class="space-y-8">
                    <div class="bg-white mb-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="text-left">
                                <label for="kelurahan_id" class="block text-sm font-semibold mb-1">
                                    Kelurahan
                                </label>
                                <x-dropdown
                                    id="kelurahanDropdown"
                                    label="Pilih Kelurahan"
                                    :options="[]"
                                    width="w-full sm:w-56"
                                    data-dropdown="filter" />
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-kelurahan_id"></p>
                                <input type="hidden" name="kelurahan_id" id="kelurahan_id">
                            </div>

                            <div class="text-left">
                                <label for="posyandu_id" class="block text-sm font-semibold mb-1">
                                    Posyandu
                                </label>
                                <x-dropdown
                                    id="posyanduDropdown"
                                    label="Pilih Posyandu"
                                    :options="[]"
                                    width="w-full sm:w-56"
                                    data-dropdown="filter" />
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-posyandu_id"></p>
                                <input type="hidden" name="posyandu_id" id="posyandu_id">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold mb-1">Alamat Domisili</label>
                                <textarea name="alamat"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                </textarea>
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="alamat"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">RT Domisili</label>
                                <input type="text" name="rt"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="rt"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">RW Domisili</label>
                                <input type="text" name="rw"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="rw"></p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-[#00000033] my-6"></div>
                    <div class="kk-item border border-gray-300 rounded-xl p-6 relative bg-white">
                        <div class="mb-4">
                            <div class="flex items-start gap-2">
                                <input type="checkbox" id="kkLuarWilayah"
                                    class="mt-1 w-4 h-4 accent-[#61359C]">
                                <div>
                                    <span class="font-semibold text-sm">
                                        KK Luar Wilayah
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">
                                        *Centang jika KK berasal dari luar wilayah.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold mb-1">Nomor Kartu Keluarga</label>
                                <input type="text" name="no_kk"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="no_kk"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">NIK Kepala Keluarga</label>
                                <input type="text" name="nik_kepala_keluarga"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="nik_kepala_keluarga"></p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold mb-1">Nama Kepala Keluarga</label>
                                <input type="text" name="nama_kepala_keluarga"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="nama_kepala_keluarga"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">No Telepon</label>
                                <input type="text" name="no_telepon"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="no_telepon"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Jumlah Anggota Keluarga</label>
                                <input type="number" name="jumlah_anggota"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="jumlah_anggota"></p>
                            </div>
                        </div>
                        <div id="luarWilayahField" class="hidden mt-6">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Alamat KTP</label>
                                    <textarea name="alamat_ktp"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                            </textarea>
                                    <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="alamat_ktp"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">RT</label>
                                    <input type="text" name="rt_ktp"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                    <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="rt_ktp"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">RW</label>
                                    <input type="text" name="rw_ktp"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                    <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="rw_ktp"></p>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                            class="btn-remove hidden absolute top-4 right-4
                            flex items-center gap-1
                            bg-red-50 text-red-600
                            px-3 py-1.5 rounded-lg
                            hover:bg-red-100
                            transition text-sm font-semibold">
                            <i class="fa-solid fa-trash"></i>
                            Hapus
                        </button>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="button"
                        id="btnAddKK"
                        class="flex items-center gap-2 
                        border border-[#61359C] text-[#61359C]
                        text-sm font-semibold px-3 py-1.5 rounded-lg
                        hover:bg-[#61359C] hover:text-white
                        transition duration-200">
                        <i class="fa-solid fa-plus"></i>
                        Tambah KK
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                    id="btnKirimIdentitas"
                    class="border border-[#61359C] text-[#61359C]
               px-3 py-1.5 text-sm font-semibold rounded-lg
               hover:bg-[#61359C] hover:text-white transition">
                    Kirim
                </button>

                <button type="button"
                    id="btnNextTab"
                    class="bg-[#61359C] text-white px-3 py-1.5 text-sm font-semibold rounded-lg hover:bg-[#512c82] transition">
                    Selanjutnya
                </button>
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
                    id="btnSimpan"
                    class="bg-[#61359C] text-white
                       text-sm font-semibold px-4 py-1.5 rounded-lg
                       hover:bg-[#512c82] transition">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tabIdentitas = document.getElementById('tabIdentitas');
        const tabPertanyaan = document.getElementById('tabPertanyaan');

        const contentIdentitas = document.getElementById('contentIdentitas');
        const contentPertanyaan = document.getElementById('contentPertanyaan');

        function setActiveTab(activeTab) {

            const tabs = [tabIdentitas, tabPertanyaan];

            tabs.forEach(tab => {
                const underline = tab.querySelector('span');

                tab.classList.remove('text-[#61359C]');
                tab.classList.add('text-gray-400');

                underline.classList.remove('bg-[#61359C]/50');
                underline.classList.add('bg-transparent');
            });

            const activeUnderline = activeTab.querySelector('span');

            activeTab.classList.remove('text-gray-400');
            activeTab.classList.add('text-[#61359C]');

            activeUnderline.classList.remove('bg-transparent');
            activeUnderline.classList.add('bg-[#61359C]/50');
        }

        tabIdentitas.addEventListener('click', () => {
            contentIdentitas.classList.remove('hidden');
            contentPertanyaan.classList.add('hidden');
            setActiveTab(tabIdentitas);
        });

        tabPertanyaan.addEventListener('click', () => {
            contentIdentitas.classList.add('hidden');
            contentPertanyaan.classList.remove('hidden');

            setActiveTab(tabPertanyaan);
            fetchPertanyaan();
        });

        const checkbox = document.getElementById('kkLuarWilayah');
        const luarWilayahField = document.getElementById('luarWilayahField');

        checkbox.addEventListener('change', function() {
            if (this.checked) {
                luarWilayahField.classList.remove('hidden');
            } else {
                luarWilayahField.classList.add('hidden');
            }
        });

        const kkContainer = document.getElementById('kkContainer');
        const btnAddKK = document.getElementById('btnAddKK');
        const btnNextTab = document.getElementById('btnNextTab');

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.kk-item');
            items.forEach((item, index) => {
                const removeBtn = item.querySelector('.btn-remove');
                if (items.length > 1) {
                    removeBtn.classList.remove('hidden');
                } else {
                    removeBtn.classList.add('hidden');
                }
            });
        }

        btnAddKK.addEventListener('click', () => {
            const firstItem = document.querySelector('.kk-item');
            const clone = firstItem.cloneNode(true);

            clone.querySelectorAll('input, textarea').forEach(el => {
                if (el.type === 'checkbox') {
                    el.checked = false;
                } else {
                    el.value = '';
                }
            });

            const luarWilayah = clone.querySelector('#luarWilayahField');
            if (luarWilayah) luarWilayah.classList.add('hidden');

            kkContainer.appendChild(clone);
            updateRemoveButtons();
        });

        kkContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove')) {
                e.target.closest('.kk-item').remove();
                updateRemoveButtons();
            }
        });

        btnNextTab.addEventListener('click', () => {
            tabPertanyaan.click();
        });

        updateRemoveButtons();

        const btnBackTab = document.getElementById('btnBackTab');
        const btnSimpan = document.getElementById('btnSimpan');

        btnBackTab.addEventListener('click', () => {
            tabIdentitas.click();
        });

        //tab identitas keluarga
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
                .getElementById('kelurahanDropdown')
                .querySelector('.dropdown-menu');

            dropdown.innerHTML = '';

            kelurahanData.forEach(kel => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = kel.nama_kelurahan;

                btn.onclick = () => {
                    setDropdownLabel('kelurahanDropdown', kel.nama_kelurahan, 'Pilih Kelurahan');
                    document.getElementById('kelurahan_id').value = kel.id;

                    setDropdownDisabled('posyanduDropdown', false);
                    renderPosyanduDropdown(kel.posyandu);
                };

                dropdown.appendChild(btn);
            });
        }

        function renderPosyanduDropdown(posyanduList = []) {
            const dropdownWrapper = document.getElementById('posyanduDropdown');
            const dropdown = dropdownWrapper.querySelector('.dropdown-menu');

            dropdown.innerHTML = '';
            document.getElementById('posyandu_id').value = '';
            setDropdownLabel('posyanduDropdown', null, 'Pilih Posyandu');

            if (!posyanduList.length) {
                setDropdownDisabled('posyanduDropdown', true);
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
                    setDropdownLabel('posyanduDropdown', p.nama_posyandu, 'Pilih Posyandu');
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

        const btnKirimIdentitas = document.getElementById('btnKirimIdentitas');

        btnKirimIdentitas.addEventListener('click', async () => {

            document.querySelectorAll('[id^="error-"]').forEach(el => {
                el.textContent = "";
                el.classList.add("hidden");
            });

            document.querySelectorAll('.error-field').forEach(el => {
                el.textContent = "";
                el.classList.add("hidden");
            });

            const payload = {
                kelurahan_id: document.getElementById('kelurahan_id').value,
                posyandu_id: document.getElementById('posyandu_id').value,
                alamat: document.querySelector('[name="alamat"]').value,
                rt: document.querySelector('[name="rt"]').value,
                rw: document.querySelector('[name="rw"]').value,
                keluarga: []
            };

            document.querySelectorAll('.kk-item').forEach(item => {

                payload.keluarga.push({
                    is_luar_wilayah: item.querySelector('[type="checkbox"]').checked ? 1 : 0,
                    no_kk: item.querySelector('[name="no_kk"]').value,
                    no_telepon: item.querySelector('[name="no_telepon"]').value,
                    jumlah_anggota: item.querySelector('[name="jumlah_anggota"]').value,
                    alamat_ktp: item.querySelector('[name="alamat_ktp"]')?.value ?? null,
                    rt_ktp: item.querySelector('[name="rt_ktp"]')?.value ?? null,
                    rw_ktp: item.querySelector('[name="rw_ktp"]')?.value ?? null,
                    nik_kepala_keluarga: item.querySelector('[name="nik_kepala_keluarga"]').value,
                    nama_kepala_keluarga: item.querySelector('[name="nama_kepala_keluarga"]').value
                });
            });

            try {
                const response = await fetch(`/api/identitas_keluarga`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (response.ok) {

                    alert("Identitas berhasil disimpan");
                    document.getElementById('tabPertanyaan').click();

                } else {

                    if (typeof result.errors === "object") {
                        Object.keys(result.errors).forEach(key => {

                            const message = result.errors[key][0];

                            if (!key.startsWith('keluarga.')) {
                                const el = document.getElementById("error-" + key);
                                if (el) {
                                    el.textContent = message;
                                    el.classList.remove("hidden");
                                }
                                return;
                            }

                            const parts = key.split('.');
                            const index = parts[1];
                            const field = parts[2];

                            const kkItems = document.querySelectorAll('.kk-item');
                            const currentItem = kkItems[index];

                            if (!currentItem) return;

                            const errorEl = currentItem.querySelector(`[data-error="${field}"]`);

                            if (errorEl) {
                                errorEl.textContent = message;
                                errorEl.classList.remove("hidden");
                            }

                        });
                    } else {
                        alert(result.errors);
                    }

                }

            } catch (error) {
                console.error(error);
                alert("Terjadi kesalahan server");
            }

        });

        //tab pertanyaan
        async function fetchPertanyaan() {
            try {
                const response = await fetch(`{{ url('api/pertanyaan') }}`);
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
                item.target_skrining?.toLowerCase() === 'kk'
            );

            if (!filtered.length) {
                container.innerHTML = `
                    <div class="text-gray-500 text-center py-6">
                        Tidak ada pertanyaan skrining KK.
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
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                        </textarea>
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

        loadKelurahan();
        setDropdownDisabled('posyanduDropdown', true);
    });
</script>
@endsection