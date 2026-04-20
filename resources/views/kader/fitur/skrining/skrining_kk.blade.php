@extends('layouts.main')

@section('title', 'Skrining KK')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Skrining KK</h2>

    <div class="mb-4">
        <div class="flex border-b-4 border-[#61359C]">
            <button id="tabIdentitas"
                class="tab-btn relative flex-1 text-center py-2 text-sm font-bold text-[#61359C]">
                Identitas Keluarga
                <span class="absolute left-0 bottom-0 w-full h-[4px] bg-[#61359C]/30 rounded-t"></span>
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
            <div class="bg-white border border-[#61359C] rounded-2xl p-4 mb-4">
                <div id="kkContainer" class="space-y-8">
                    <div class="bg-white mb-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="text-left">
                                <label class="block text-sm font-semibold mb-1">Kelurahan</label>
                                <input type="text" id="kelurahan_text"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 cursor-not-allowed" disabled>
                                <input type="hidden" name="kelurahan_id" id="kelurahan_id">
                                <p class="text-red-500 text-xs mt-1 hidden" id="error-kelurahan_id"></p>
                            </div>

                            <div class="text-left">
                                <label class="block text-sm font-semibold mb-1">Posyandu</label>
                                <input type="text" id="posyandu_text"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 cursor-not-allowed" disabled>
                                <input type="hidden" name="posyandu_id" id="posyandu_id">

                                <p class="text-red-500 text-xs mt-1 hidden" id="error-posyandu_id"></p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold mb-1">Alamat Domisili</label>
                                <textarea name="alamat"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"></textarea>
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="alamat"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">RT Domisili</label>
                                <input type="text"
                                    name="rt"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="3"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="rt"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">RW Domisili</label>
                                <input type="text"
                                    name="rw"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="3"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-field" data-error="rw"></p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t-2 border-[#00000033] my-6"></div>
                    <div class="kk-item border border-gray-300 rounded-xl p-4 relative bg-white">
                        <div class="mb-4">
                            <div class="flex items-start gap-2">
                                <input type="checkbox" class="kk-luar-wilayah mt-1 w-4 h-4 accent-[#61359C]">
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
                                <input type="text"
                                    name="no_kk"
                                    inputmode="numeric"
                                    maxlength="16"
                                    pattern="[0-9]{16}"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16)"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-no_kk"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">NIK Kepala Keluarga</label>
                                <input type="text"
                                    name="nik_kepala_keluarga"
                                    inputmode="numeric"
                                    maxlength="16"
                                    pattern="[0-9]{16}"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16)"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-nik_kepala_keluarga"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">Nama Kepala Keluarga</label>
                                <input type="text" name="nama_kepala_keluarga"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-nama_kepala_keluarga"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1">No Telepon</label>
                                <input type="text"
                                    name="no_telepon"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    maxlength="20"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                <p class="text-red-500 text-xs mt-1 hidden error-no_telepon"></p>
                            </div>
                        </div>
                        <div class="luar-wilayah-field hidden mt-6">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold mb-1">Alamat (KTP)</label>
                                    <textarea name="alamat_ktp"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"></textarea>
                                    <p class="text-red-500 text-xs mt-1 hidden error-alamat_ktp"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">RT (KTP)</label>
                                    <input type="text"
                                        name="rt_ktp"
                                        inputmode="numeric"
                                        maxlength="3"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                    <p class="text-red-500 text-xs mt-1 hidden error-rt_ktp"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-1">RW (KTP)</label>
                                    <input type="text"
                                        name="rw_ktp"
                                        inputmode="numeric"
                                        maxlength="3"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                    <p class="text-red-500 text-xs mt-1 hidden error-rw_ktp"></p>
                                </div>
                            </div>
                        </div>
                        <button type="button"
                            class="btn-remove hidden absolute top-4 right-4
                            flex items-center gap-1
                            bg-red-50 text-red-600
                            px-3 py-1 rounded-lg
                            hover:bg-red-100
                            transition text-sm font-semibold">
                            <i class="fa-solid fa-trash"></i>
                            Hapus
                        </button>
                    </div>
                </div>
                <div class="mt-4">
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
                    id="btnKirim"
                    class="bg-[#61359C] text-white
                       text-sm font-semibold px-4 py-1.5 rounded-lg
                       hover:bg-[#512c82] transition">
                    Kirim
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", async () => {
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

                underline.classList.remove('bg-[#61359C]/30');
                underline.classList.add('bg-transparent');
            });

            const activeUnderline = activeTab.querySelector('span');

            activeTab.classList.remove('text-gray-400');
            activeTab.classList.add('text-[#61359C]');

            activeUnderline.classList.remove('bg-transparent');
            activeUnderline.classList.add('bg-[#61359C]/30');
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

        const kkContainer = document.getElementById('kkContainer');

        kkContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('kk-luar-wilayah')) {

                const kkItem = e.target.closest('.kk-item');
                const luarField = kkItem.querySelector('.luar-wilayah-field');

                if (e.target.checked) {
                    luarField.classList.remove('hidden');
                } else {
                    luarField.classList.add('hidden');
                }
            }
        });

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

            const luarWilayah = clone.querySelector('.luar-wilayah-field');
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

        updateRemoveButtons();

        const btnBackTab = document.getElementById('btnBackTab');
        const btnKirim = document.getElementById('btnKirim');

        btnBackTab.addEventListener('click', () => {
            tabIdentitas.click();
        });


        //tab identitas keluarga
        function initKelurahanPosyandu() {
            const user = window.App.user;
            if (!user) return;

            if (user.role === 'kader') {
                const kader = user.kaderDetail;

                document.getElementById('kelurahan_id').value = kader?.kelurahan_id || '';
                document.getElementById('posyandu_id').value = kader?.posyandu_id || '';

                document.getElementById('kelurahan_text').value = kader?.nama_kelurahan || '-';
                document.getElementById('posyandu_text').value = kader?.nama_posyandu || '-';

                return;
            }

        }

        //tab pertanyaan
        async function fetchPertanyaan() {
            try {
                const result = await fetchWithAuth(`{{ url('api/pertanyaan') }}`);

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
                    return renderRadio(item);
                case 'checkbox':
                    return renderCheckbox(item);
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

        function renderRadio(item) {
            const name = `pertanyaan_${item.id}`;
            let html = '';

            (item.opsi_jawaban || []).forEach(opt => {
                html += `
                <label class="flex items-center gap-2">
                    <input type="radio"
                        name="${name}"
                        value="${opt}"
                        class="radio-option accent-[#61359C]"
                        data-pertanyaan-id="${item.id}">
                    <span>${opt}</span>
                </label>
                `;
            });

            if (item.opsi_lain) {
                html += `
                    <label class="flex items-center gap-2">
                        <input type="radio"
                            name="${name}"
                            value="lainnya"
                            class="radio-other accent-[#61359C]"
                            data-pertanyaan-id="${item.id}">
                        <span>Lainnya</span>
                    </label>

                    <input type="text"
                        class="other-input hidden border border-gray-300 rounded-lg px-3 py-1.5 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                        placeholder="Ketik lainnya...">
                `;
            }

            return `<div class="space-y-2">${html}</div>`;
        }

        document.addEventListener("change", function(e) {
            if (e.target.classList.contains("radio-other")) {

                const wrapper = e.target.closest("div");
                const otherInput = wrapper.querySelector(".other-input");

                if (otherInput) {
                    otherInput.classList.remove("hidden");
                    otherInput.focus();
                }
            }

            if (e.target.classList.contains("radio-option")) {

                const wrapper = e.target.closest("div");
                const otherInput = wrapper.querySelector(".other-input");

                if (otherInput) {
                    otherInput.classList.add("hidden");
                    otherInput.value = "";
                }
            }
        });

        function renderCheckbox(item) {
            const name = `pertanyaan_${item.id}[]`;
            let html = '';

            (item.opsi_jawaban || []).forEach(opt => {
                html += `
                    <label class="flex items-start gap-2">
                        <input type="checkbox"
                            name="${name}"
                            value="${opt}"
                            class="checkbox-option accent-[#61359C] mt-1"
                            data-pertanyaan-id="${item.id}">
                        <span>${opt}</span>
                    </label>
                `;
            });

            if (item.opsi_lain) {
                html += `
                    <label class="flex items-start gap-2">
                        <input type="checkbox"
                            name="pertanyaan_${item.id}[]"
                            value="lainnya"
                            class="checkbox-other accent-[#61359C] mt-1"
                            data-pertanyaan-id="${item.id}">
                        <span>Lainnya</span>
                    </label>

                    <input type="text"
                        class="other-input hidden border border-gray-300 rounded-lg px-3 py-1.5 text-sm mt-1 focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                        placeholder="Ketik lainnya...">
                    `;
            }
            return `<div class="space-y-2">${html}</div>`;
        }

        document.addEventListener("change", function(e) {
            if (e.target.classList.contains("checkbox-other")) {

                const wrapper = e.target.closest('.space-y-2') || e.target.closest('div');
                const otherInput = wrapper.querySelector(".other-input");

                if (!otherInput) return;

                if (e.target.checked) {
                    otherInput.classList.remove("hidden");
                    otherInput.focus();
                } else {
                    otherInput.classList.add("hidden");
                    otherInput.value = "";
                }
            }
        });

        function renderCustomDropdown(item) {
            const options = item.opsi_jawaban || [];

            let htmlOptions = options.map(opt => `
                <button type="button"
                    class="dropdown-item block w-full text-center px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 transition"
                    onclick="selectDropdownOption(this, '${opt}')">
                    ${opt}
                </button>
            `).join('');

            if (item.opsi_lain) {
                htmlOptions += `
                    <button type="button"
                        class="dropdown-item block w-full text-center px-4 py-1 text-sm text-gray-700 hover:bg-gray-100 transition"
                        onclick="selectDropdownOther(this)">
                        Lainnya
                    </button>

                    <div class="dropdown-other-wrapper hidden mt-2 pt-3 border-t border-gray-200 px-3 pb-2">
                        <div class="text-xs text-gray-500 mb-1">
                            Lainnya
                        </div>

                        <input type="text"
                            class="dropdown-other w-full border border-gray-300 rounded-lg px-3 py-1.5 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                            placeholder="Ketik lainnya...">
                    </div>
                `;
            }

            return `
                <div class="relative block text-left w-full">
                    <button type="button"
                        class="relative flex items-center justify-between w-full border border-[#00000033] text-sm rounded-lg px-4 py-2 bg-white"
                        onclick="toggleDropdown(this)">

                        <span class="dropdown-selected text-left w-full truncate text-gray-500">
                            Pilih Opsi
                        </span>

                        <svg class="w-4 h-4 absolute right-3" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div class="dropdown-menu hidden absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl p-2 border border-gray-100">
                        ${htmlOptions}
                    </div>
                </div>
            `;
        }
        
        window.toggleDropdown = function(button) {
            const dropdown = button.parentElement.querySelector('.dropdown-menu');
            const allDropdowns = document.querySelectorAll('.dropdown-menu');

            allDropdowns.forEach(menu => {
                if (menu !== dropdown) menu.classList.add('hidden');
            });

            dropdown.classList.toggle('hidden');
        };

        window.selectDropdownOption = function(optionEl, value) {
            const dropdown = optionEl.closest('.relative');
            const selectedSpan = dropdown.querySelector('.dropdown-selected');
            const otherWrapper = dropdown.querySelector('.dropdown-other-wrapper');

            selectedSpan.textContent = value;

            if (otherWrapper) {
                otherWrapper.classList.add('hidden');
            }

            dropdown.querySelector('.dropdown-menu').classList.add('hidden');
        };

        window.selectDropdownOther = function(optionEl) {
            const dropdown = optionEl.closest('.relative');
            const wrapper = dropdown.querySelector('.dropdown-other-wrapper');
            const selectedSpan = dropdown.querySelector('.dropdown-selected');

            wrapper.classList.remove('hidden');
            selectedSpan.textContent = 'Lainnya';
        };

        document.querySelectorAll('.other-input').forEach(input => {
            if (input.value) {
                payload.push({
                    pertanyaan_id: input.closest('[data-pertanyaan-id]').dataset.pertanyaanId,
                    jawaban: input.value
                });
            }
        });

        async function validateIdentitas() {
            resetErrorsTextOnly();

            const identitasPayload = {
                kelurahan_id: document.getElementById('kelurahan_id').value.trim(),
                posyandu_id: document.getElementById('posyandu_id').value.trim(),
                alamat: document.querySelector('[name="alamat"]').value.trim(),
                rt: document.querySelector('[name="rt"]').value.trim(),
                rw: document.querySelector('[name="rw"]').value.trim(),
                keluarga: []
            };

            document.querySelectorAll('.kk-item').forEach(item => {
                const isLuarWilayah =
                    item.querySelector('.kk-luar-wilayah').checked ? 1 : 0;

                identitasPayload.keluarga.push({
                    is_luar_wilayah: isLuarWilayah,
                    no_kk: item.querySelector('[name="no_kk"]').value.trim(),
                    no_telepon: item.querySelector('[name="no_telepon"]').value.trim(),
                    alamat_ktp: isLuarWilayah ?
                        item.querySelector('[name="alamat_ktp"]').value.trim() : null,
                    rt_ktp: isLuarWilayah ?
                        item.querySelector('[name="rt_ktp"]').value.trim() : null,
                    rw_ktp: isLuarWilayah ?
                        item.querySelector('[name="rw_ktp"]').value.trim() : null,
                    nik_kepala_keluarga: item.querySelector('[name="nik_kepala_keluarga"]').value.trim(),
                    nama_kepala_keluarga: item.querySelector('[name="nama_kepala_keluarga"]').value.trim()
                });
            });

            const result = await fetchWithAuth(`/api/identitas_keluarga?validate_only=1`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(identitasPayload)
            });

            if (result?.status_code === 422) {
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

        function resetErrorsTextOnly() {
            document.querySelectorAll('[id^="error-"], [class*="error-"], [data-error]')
                .forEach(el => {
                    el.textContent = "";
                    el.classList.add("hidden");
                });
        }

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

                    const checkboxes = wrapper.querySelectorAll('input[type="checkbox"]');
                    let values = [];

                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            if (cb.classList.contains('checkbox-option')) {
                                values.push(cb.nextElementSibling?.innerText ?? '');
                            }
                            if (cb.classList.contains('checkbox-other')) {
                                const otherInput = wrapper.querySelector('.other-input');
                                if (otherInput && otherInput.value.trim()) {
                                    values.push(otherInput.value.trim());
                                }
                            }
                        }
                    });

                    const textInput = wrapper.querySelector('input[type="text"]');
                    const textarea = wrapper.querySelector('textarea');

                    if (values.length) {
                        value = values.join(', ');
                    } else if (textInput && textInput.value.trim()) {
                        value = textInput.value.trim();
                    } else if (textarea && textarea.value.trim()) {
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
                    kelurahan_id: document.getElementById('kelurahan_id').value,
                    posyandu_id: document.getElementById('posyandu_id').value,
                    alamat: document.querySelector('[name="alamat"]').value,
                    rt: document.querySelector('[name="rt"]').value,
                    rw: document.querySelector('[name="rw"]').value,
                    keluarga: []
                };

                document.querySelectorAll('.kk-item').forEach(item => {
                    const isLuarWilayah =
                        item.querySelector('.kk-luar-wilayah').checked ? 1 : 0;

                    identitasPayload.keluarga.push({
                        is_luar_wilayah: isLuarWilayah,
                        no_kk: item.querySelector('[name="no_kk"]').value,
                        no_telepon: item.querySelector('[name="no_telepon"]').value,
                        alamat_ktp: isLuarWilayah ?
                            item.querySelector('[name="alamat_ktp"]').value.trim() : null,

                        rt_ktp: isLuarWilayah ?
                            item.querySelector('[name="rt_ktp"]').value.trim() : null,

                        rw_ktp: isLuarWilayah ?
                            item.querySelector('[name="rw_ktp"]').value.trim() : null,
                        nik_kepala_keluarga: item.querySelector('[name="nik_kepala_keluarga"]').value,
                        nama_kepala_keluarga: item.querySelector('[name="nama_kepala_keluarga"]').value
                    });
                });

                const identitasResult = await fetchWithAuth(`/api/identitas_keluarga`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(identitasPayload)
                });

                if (identitasResult.status === 422) {
                    const errors = identitasResult.errors;

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
                    scrollToFirstError();
                    return;
                }

                if (identitasResult.status_code && identitasResult.status_code !== 200) {
                    showErrorToast("Terjadi kesalahan server");
                    return;
                }

                const keluargaList = identitasResult.data?.keluarga || [];
                if (keluargaList.length === 0) {
                    showErrorToast("Data keluarga tidak ditemukan");
                    return;
                }

                for (const keluarga of keluargaList) {
                    const skriningPayload = {
                        keluarga_id: keluarga.id,
                        tanggal_skrining: new Date().toISOString().split('T')[0],
                        jawaban: jawaban
                    };

                    const skriningResponse = await fetchWithAuth(`/api/skrining`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(skriningPayload)
                    });

                    if (skriningResponse.status_code && skriningResponse.status_code !== 200) {
                        showErrorToast("Gagal menyimpan skrining");
                        return;
                    }
                }

                showSuccessToast("Skrining KK berhasil terkirim!");
                window.location.reload();

            } catch (error) {
                console.error(error);
                showErrorToast("Terjadi kesalahan server");
            }
        });

        await initApp();
        initKelurahanPosyandu();
    });
</script>
@endsection