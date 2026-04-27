@extends('layouts.main')

@section('title', 'Edit Hasil Skrining')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-5 text-center sm:text-left">Edit Hasil Skrining</h2>

    <div class="bg-white border border-[#61359C] rounded-2xl p-4 mb-4">
        <form id="formEdit" action="{{ url('/api/monitoring/hasil-skrining/' . $data['unit_rumah'][0]['unit_rumah_id']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="text-left w-full">
                    <label name="tanggal_skrining" class="block font-semibold mb-1 text-sm">Tanggal Skrining</label>
                    <input type="date" name="tanggal_skrining"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-tanggal_skrining"></p>
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-sm">Nama Kader</label>
                    <x-dropdown
                        id="kaderDropdown"
                        label="Pilih Kader"
                        :options="[]"
                        width="w-full sm:w-56"
                        data-dropdown="filter" />
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-user_id"></p>
                    <input type="hidden" name="user_id" id="user_id">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="text-left">
                    <label class="block font-semibold mb-1 text-sm">Kelurahan</label>
                    <x-dropdown
                        id="kelurahanDropdown"
                        label="Pilih Kelurahan"
                        :options="[]"
                        width="w-full sm:w-56"
                        data-dropdown="filter" />
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-kelurahan_id"></p>
                    <input type="hidden" name="unit[kelurahan_id]" id="kelurahan_id">
                </div>
                <div>
                    <label class="block font-semibold mb-1 text-sm">Posyandu</label>
                    <x-dropdown
                        id="posyanduDropdown"
                        label="Pilih Posyandu"
                        :options="[]"
                        width="w-full sm:w-56"
                        data-dropdown="filter" />
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-posyandu_id"></p>
                    <input type="hidden" name="unit[posyandu_id]" id="posyandu_id">
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-1 text-sm">Alamat</label>
                <textarea name="unit[alamat]"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="text-left w-full">
                    <label name="rt" class="block font-semibold mb-1 text-sm">RT</label>
                    <input type="text" name="unit[rt]"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-rt"></p>
                </div>
                <div class="text-left w-full">
                    <label name="rw" class="block font-semibold mb-1 text-sm">RW</label>
                    <input type="text" name="unit[rw]"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-rw"></p>
                </div>
            </div>

            <div class="mb-4">
                <div class="flex border-b-4 border-[#61359C]">
                    <button id="tabSkriningKk"
                        class="tab-btn relative flex-1 text-center py-2 text-sm font-bold text-[#61359C]">
                        Skrining KK
                        <span class="tab-line absolute left-0 bottom-0 w-full h-[4px] bg-[#61359C]/30 rounded-t"></span>
                    </button>

                    <button id="tabSkriningNik"
                        class="tab-btn relative flex-1 text-center py-2 text-sm font-bold text-gray-400">
                        Skrining NIK
                        <span class="tab-line absolute left-0 bottom-0 w-full h-[4px] bg-transparent rounded-t"></span>
                    </button>
                </div>
            </div>

            <div id="contentSkriningKk"></div>
            <div id="contentSkriningNik" class="hidden"></div>

            <div class="flex justify-between mt-6">
                <a href="{{ url('admin/hasil-skrining') }}"
                    class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-sm">
                    Kembali
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-[#61359C] text-white rounded-lg text-sm hover:bg-[#61359C]/80">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</section>
@endsection

<script>
    let formEdit;
    const unitId = "{{ $data['unit_rumah'][0]['unit_rumah_id'] }}";

    async function loadDetail() {
        try {
            const result = await fetchWithAuth(`/api/monitoring/hasil-skrining/${unitId}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            });

            if (!result || !result.status) {
                console.error("Data kosong atau status false");
                showErrorToast("Gagal mengambil detail data");
                return;
            }

            setFormData(result.data);

        } catch (err) {
            console.error("ERROR FETCH:", err);
            showErrorToast("Terjadi kesalahan saat mengambil detail");
        }
    }

    function setDropdownLabel(id, text, fallback) {
        const el = document.getElementById(id);
        if (!el) return;

        const label = el.querySelector('.dropdown-selected');
        if (label) label.textContent = text || fallback;
    }

    let kaderData = [];

    async function loadKader() {
    try {
        const result = await fetchWithAuth(`{{ url('api/users') }}`, {
            method: "GET",
            headers: {
                "Accept": "application/json"
            }
        });

        kaderData = (result.data.list || []).filter(u => u.role === 'kader');

        renderKaderDropdown();

    } catch (error) {
        console.error('Gagal load kader:', error);
        showErrorToast("Terjadi kesalahan saat mengambil data kader");
    }
}

    function renderKaderDropdown() {
        const dropdown = document
            .getElementById('kaderDropdown')
            .querySelector('.dropdown-menu');

        dropdown.innerHTML = '';

        if (!kaderData.length) {
            dropdown.innerHTML = `
                    <div class="px-4 py-2 text-sm text-gray-400 text-center">
                        Tidak ada data kader
                    </div>
                `;
            return;
        }

        kaderData.forEach(kader => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
            btn.textContent = kader.nama;

            btn.onclick = () => {
                setDropdownLabel('kaderDropdown', kader.nama, 'Pilih Kader');
                document.getElementById('user_id').value = kader.id;
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

    const jenisKelaminDropdown = document.getElementById('jenisKelaminDropdown');

    const jkDropdown = document.getElementById('jenisKelaminDropdown');
    const jkInput = document.getElementById('jenis_kelamin');

    if (jkDropdown) {
        jkDropdown.addEventListener('dropdown-changed', (e) => {
            const label = e.detail.value;

            if (label === 'Laki-laki') {
                jkInput.value = 'L';
            } else if (label === 'Perempuan') {
                jkInput.value = 'P';
            }
        });
    }

    const formModel = {
        unit_rumah_id: "",
        tanggal_skrining: "",
        user_id: "",
        kelurahan_id: "",
        posyandu_id: "",
        alamat: "",
        rt: "",
        rw: "",
        keluarga: []
    };

    window.setFormData = (data) => {
        if (!data) {
            formEdit.reset();
            document.querySelectorAll('.dropdown-selected').forEach(label => label.textContent = 'Pilih');
            formModel.keluarga = [];
            return;
        }

        const item = data[0];
        const unit = item.unit_rumah?.[0] ?? {};

        formModel.unit_rumah_id = item.unit_rumah_id ?? "";
        formModel.tanggal_skrining = item.tanggal_skrining ?? "";
        formModel.user_id = item.user_id ?? "";
        formModel.kelurahan_id = unit.kelurahan_id ?? "";
        formModel.posyandu_id = unit.posyandu_id ?? "";
        formModel.alamat = unit.alamat ?? "";
        formModel.rt = unit.rt ?? "";
        formModel.rw = unit.rw ?? "";
        formModel.keluarga = unit.keluarga ?? [];

        const kkContainer = document.getElementById('contentSkriningKk');
        if (kkContainer) {
            kkContainer.innerHTML = '';
        }

        const skriningKk = formModel.keluarga[0]?.skrining?.find(s => s.target_skrining === 'kk');

        if (skriningKk) {
            let html = `
                <table class="w-full border border-[#00000033] text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-[#00000033] px-3 py-2 w-[40px]">No</th>
                            <th class="border border-[#00000033] px-3 py-2">Pertanyaan</th>
                            <th class="border border-[#00000033] px-3 py-2">Jawaban</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            let lastSection = null;

            skriningKk.pertanyaan.forEach((q, i) => {
                if (q.section !== lastSection) {
                    html += `
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-3 py-2 font-semibold border-t border-[#00000033]">
                                ${q.section ?? "-"}
                            </td>
                        </tr>
                    `;
                    lastSection = q.section;
                }

                html += `
                    <tr>
                        <td class="border border-[#00000033] px-3 py-2 text-center">${i + 1}</td>
                        <td class="border border-[#00000033] px-3 py-2">${q.pertanyaan ?? "-"}</td>
                        <td class="border border-[#00000033] px-3 py-2">
                        <input type="hidden" name="skrining_kk[${i}][pertanyaan_id]" value="${q.pertanyaan_id ?? q.pertanyaan?.id ?? ''}">
                            <select name="skrining_kk[${i}][jawaban]" class="border border-[#00000033] rounded p-1 w-full">
                                <option value="Ya" ${q.jawaban === 'Ya' ? 'selected' : ''}>Ya</option>
                                <option value="Tidak" ${q.jawaban === 'Tidak' ? 'selected' : ''}>Tidak</option>
                            </select>
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            kkContainer.innerHTML = html;
        }

        const nikContainer = document.getElementById('contentSkriningNik');
        nikContainer.innerHTML = '';

        formModel.keluarga.forEach((kk, kkIndex) => {
            let kkHtml = `
                <div class="mb-4 border border-gray-500 p-3 rounded space-y-3 text-sm kk-item">
                    <div class="flex justify-between items-center">
                        <p class="font-semibold text-lg">KK ${kkIndex + 1}</p>

                        <button type="button"
                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition">
                            <i class="fa-solid fa-trash text-sm"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-[140px_1fr] items-center">
                        <span class="font-semibold">No KK</span>
                        <input type="text" 
                            name="keluarga[${kkIndex}][no_kk]" 
                            value="${kk.no_kk ?? ''}" 
                            class="border border-[#00000033] rounded p-1 w-full">
                    </div>

                    <div class="grid grid-cols-[140px_1fr] items-center">
                        <span class="font-semibold">Kepala Keluarga</span>
                        <input type="text" 
                            name="keluarga[${kkIndex}][kepala_keluarga]" 
                            value="${kk.kepala_keluarga ?? ''}" 
                            class="border border-[#00000033] rounded p-1 w-full">
                    </div>

                    <div class="grid grid-cols-[140px_1fr] items-center">
                        <span class="font-semibold">No Telepon</span>
                        <input type="text" 
                            name="keluarga[${kkIndex}][no_telepon]" 
                            value="${kk.no_telepon ?? ''}" 
                            class="border border-[#00000033] rounded p-1 w-full">
                    </div>

                    <div class="flex items-start gap-2">
                        <input type="checkbox" 
                            name="keluarga[${kkIndex}][is_luar_wilayah]" 
                            class="kk-luar-wilayah mt-1 w-4 h-4 accent-[#61359C]"
                            ${kk.is_luar_wilayah == 1 ? 'checked' : ''}>

                        <div>
                            <span class="font-semibold text-sm">KK Luar Wilayah</span>
                            <p class="text-xs text-gray-500">
                                *Centang jika KK berasal dari luar wilayah.
                            </p>
                        </div>
                    </div>

                    <div class="luar-wilayah-field ${kk.is_luar_wilayah == 1 ? '' : 'hidden'} space-y-2">
                        <div class="grid grid-cols-[140px_1fr] items-start">
                            <span class="font-semibold">Alamat</span>
                            <textarea 
                                name="keluarga[${kkIndex}][alamat_ktp]" 
                                class="border border-[#00000033] rounded p-1 w-full">${kk.alamat_ktp ?? ''}</textarea>
                        </div>

                        <div class="grid grid-cols-[140px_1fr] items-center">
                            <span class="font-semibold">RT / RW</span>

                            <div class="flex items-center gap-2">
                                <input type="text" 
                                    name="keluarga[${kkIndex}][rt_ktp]" 
                                    value="${kk.rt_ktp ?? ''}" 
                                    class="border border-[#00000033] rounded p-1 w-16 text-center"
                                    placeholder="RT">

                                <span>/</span>

                                <input type="text" 
                                    name="keluarga[${kkIndex}][rw_ktp]" 
                                    value="${kk.rw_ktp ?? ''}" 
                                    class="border border-[#00000033] rounded p-1 w-16 text-center"
                                    placeholder="RW">
                            </div>
                        </div>
                    </div>
                </div>
            `;


            const skriningNik = kk.skrining?.find(s => s.target_skrining === 'nik');

            if (skriningNik?.anggota) {
                skriningNik.anggota.forEach((anggota, aIndex) => {

                    let nikTable = `
                        <div class="mb-4 p-3 border rounded bg-white space-y-3">
                            <div class="flex justify-between items-center mb-2">
                                <p class="font-semibold">${anggota.nama}</p>

                                <button type="button"
                                    class="w-7 h-7 flex items-center justify-center rounded-md bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-500 transition">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">NIK</span>
                                    <input type="text"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][nik]"
                                        value="${anggota.nik ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Nama</span>
                                    <input type="text"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][nama]"
                                        value="${anggota.nama ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Tempat Lahir</span>
                                    <input type="text"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][tempat_lahir]"
                                        value="${anggota.tempat_lahir ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Tanggal Lahir</span>
                                    <input type="date"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][tanggal_lahir]"
                                        value="${anggota.tanggal_lahir ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Jenis Kelamin</span>
                                    <select name="keluarga[${kkIndex}][anggota][${aIndex}][jenis_kelamin]"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                        <option value="L" ${anggota.jenis_kelamin === 'L' ? 'selected' : ''}>Laki-laki</option>
                                        <option value="P" ${anggota.jenis_kelamin === 'P' ? 'selected' : ''}>Perempuan</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Hubungan</span>
                                    <input type="text"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][hubungan_keluarga]"
                                        value="${anggota.hubungan_keluarga ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Status</span>
                                    <input type="text"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][status_perkawinan]"
                                        value="${anggota.status_perkawinan ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Pendidikan</span>
                                    <input type="text"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][pendidikan_terakhir]"
                                        value="${anggota.pendidikan_terakhir ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>

                                <div class="grid grid-cols-[120px_1fr] items-center">
                                    <span class="font-semibold">Pekerjaan</span>
                                    <input type="text"
                                        name="keluarga[${kkIndex}][anggota][${aIndex}][pekerjaan]"
                                        value="${anggota.pekerjaan ?? ''}"
                                        class="border border-[#00000033] rounded p-1 w-full">
                                </div>
                            </div>

                            <table class="w-full border border-[#00000033] text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border border-[#00000033] px-3 py-2 w-[40px]">No</th>
                                        <th class="border border-[#00000033] px-3 py-2">Pertanyaan</th>
                                        <th class="border border-[#00000033] px-3 py-2">Jawaban</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    let lastSection = null;

                    anggota.pertanyaan.forEach((q, qIndex) => {
                        if (q.section !== lastSection) {
                            nikTable += `
                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-3 py-2 font-semibold border-t ">
                                        ${q.section ?? "-"}
                                    </td>
                                </tr>
                            `;
                            lastSection = q.section;
                        }

                        nikTable += `
                            <tr>
                                <td class="border border-[#00000033] px-3 py-2 text-center">${qIndex + 1}</td>
                                <td class="border border-[#00000033] px-3 py-2">${q.pertanyaan ?? "-"}</td>
                                <td class="border border-[#00000033] px-3 py-2">
                                    <input type="hidden" 
                                        name="skrining_nik[${kkIndex}][${aIndex}][${qIndex}][pertanyaan]" 
                                        value="${q.pertanyaan}">
                                    
                                    <input type="hidden" 
                                        name="skrining_nik[${kkIndex}][${aIndex}][${qIndex}][section]" 
                                        value="${q.section}">

                                    <select name="skrining_nik[${kkIndex}][${aIndex}][${qIndex}][jawaban]" 
                                        class="border rounded p-1 w-full">
                                        <option value="Ya" ${q.jawaban === 'Ya' ? 'selected' : ''}>Ya</option>
                                        <option value="Tidak" ${q.jawaban === 'Tidak' ? 'selected' : ''}>Tidak</option>
                                    </select>
                                </td>
                            </tr>
                        `;
                    });

                    nikTable += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    kkHtml += nikTable;
                });
            }

            kkHtml += `</div>`;

            nikContainer.innerHTML += kkHtml;
        });

        const tanggalInput = document.querySelector('input[name="tanggal_skrining"]');
        if (tanggalInput) tanggalInput.value = unit.tanggal_skrining ?? '';

        const userInput = document.querySelector('[name="user_id"]');
        if (userInput) userInput.value = formModel.user_id;
        const selectedKader = kaderData.find(k => k.id == formModel.user_id);
        if (selectedKader) {
            setDropdownLabel('kaderDropdown', selectedKader.nama, 'Pilih Kader');
        } else {
            setDropdownLabel('kaderDropdown', 'Pilih Kader', 'Pilih Kader');
        }

        formEdit.querySelector('[name="unit[alamat]"]').value = unit.alamat ?? '';
        formEdit.querySelector('[name="unit[rt]"]').value = unit.rt ?? '';
        formEdit.querySelector('[name="unit[rw]"]').value = unit.rw ?? '';

        document.getElementById('kelurahan_id').value = unit.kelurahan_id ?? '';
        setDropdownLabel('kelurahanDropdown', unit.kelurahan || 'Pilih Kelurahan', 'Pilih Kelurahan');

        document.getElementById('posyandu_id').value = unit.posyandu_id ?? '';
        setDropdownLabel('posyanduDropdown', unit.posyandu || 'Pilih Posyandu', 'Pilih Posyandu');
        setDropdownDisabled('posyanduDropdown', !unit.posyandu_id);

    };

    document.addEventListener('DOMContentLoaded', () => {
        formEdit = document.getElementById('formEdit');

        const tabKk = document.getElementById('tabSkriningKk');
        const tabNik = document.getElementById('tabSkriningNik');

        const contentKk = document.getElementById('contentSkriningKk');
        const contentNik = document.getElementById('contentSkriningNik');

        function setActiveTab(activeTab, inactiveTab) {
            activeTab.classList.add('text-[#61359C]');
            activeTab.classList.remove('text-gray-400');

            inactiveTab.classList.remove('text-[#61359C]');
            inactiveTab.classList.add('text-gray-400');

            const activeLine = activeTab.querySelector('.tab-line');
            const inactiveLine = inactiveTab.querySelector('.tab-line');

            if (activeLine) {
                activeLine.classList.remove('bg-transparent');
                activeLine.classList.add('bg-[#61359C]/30');
            }

            if (inactiveLine) {
                inactiveLine.classList.remove('bg-[#61359C]');
                inactiveLine.classList.add('bg-transparent');
            }
        }

        if (tabKk && tabNik) {
            tabKk.addEventListener('click', (e) => {
                e.preventDefault();

                contentKk.classList.remove('hidden');
                contentNik.classList.add('hidden');

                setActiveTab(tabKk, tabNik);
            });

            tabNik.addEventListener('click', (e) => {
                e.preventDefault();

                contentNik.classList.remove('hidden');
                contentKk.classList.add('hidden');

                setActiveTab(tabNik, tabKk);
            });
        }

        const kkContainer = document.getElementById('contentSkriningNik');
        kkContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('kk-luar-wilayah')) {

                const kkItem = e.target.closest('.kk-item');
                if (!kkItem) return;

                const luarField = kkItem.querySelector('.luar-wilayah-field');
                if (!luarField) return;

                luarField.classList.toggle('hidden', !e.target.checked);
            }
        });

        loadKader();
        loadKelurahan();
        loadDetail();
        setDropdownDisabled('posyanduDropdown', true);
    });
</script>