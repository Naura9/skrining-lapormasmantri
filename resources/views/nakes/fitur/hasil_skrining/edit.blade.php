@extends('layouts.main')

@section('title', 'Edit Hasil Skrining')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-5 text-center sm:text-left">Edit Hasil Skrining</h2>

    <div class="bg-white border border-[#61359C] rounded-2xl p-4 mb-4">
        <form id="formEdit" class="space-y-4">
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
                        width="w-full"
                        data-dropdown="filter" />
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-user_id"></p>
                    <input type="hidden" name="user_id" id="user_id">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="text-left">
                    <label class="block font-semibold mb-1 text-sm">
                        Kelurahan
                    </label>
                    <input
                        type="text"
                        id="kelurahan_nama"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 bg-gray-50"
                        readonly>
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-kelurahan_id"></p>
                    <input
                        type="hidden"
                        name="unit[kelurahan_id]"
                        id="kelurahan_id">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-sm">
                        Posyandu
                    </label>
                    <input
                        type="text"
                        id="posyandu_nama"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 bg-gray-50"
                        readonly>
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-posyandu_id"></p>
                    <input
                        type="hidden"
                        name="unit[posyandu_id]"
                        id="posyandu_id">
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-1 text-sm">Alamat</label>
                <textarea name="unit[alamat]"
                    rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"></textarea>
                <p class="text-red-500 text-xs mt-1 hidden" id="error-alamat"></p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="text-left w-full">
                    <label name="rt" class="block font-semibold mb-1 text-sm">RT</label>
                    <input type="number" name="unit[rt]"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-rt"></p>
                </div>
                <div class="text-left w-full">
                    <label name="rw" class="block font-semibold mb-1 text-sm">RW</label>
                    <input type="number" name="unit[rw]"
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
                <div class="flex justify-between">
                    <a href="{{ route('nakes.fitur.hasil_skrining.index') }}"
                        class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-sm">
                        Kembali
                    </a>
                </div>
                <button type="button"
    id="btnSubmitEdit"
    class="px-4 py-2 bg-[#61359C] text-white rounded-lg text-sm hover:bg-[#61359C]/80">
    Simpan
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
                showErrorToast("Gagal mengambil detail data");
                return;
            }

            setFormData(result.data);

            const detail = result.data[0];

            if (!detail) return;

            document.getElementById('user_id').value = detail.user_id || '';

            const selectedKader = kaderData.find(
                k => String(k.id) === String(detail.user_id)
            );

            if (selectedKader) {
                setDropdownLabel(
                    'kaderDropdown',
                    selectedKader.nama,
                    'Pilih Kader'
                );

                fillKaderLocation(selectedKader);
            }

        } catch (err) {
            console.error(err);
            showErrorToast("Terjadi kesalahan saat mengambil detail");
        }
    }

    function normalizeField(key) {
        return key
            .split('.')
            .pop(); 
    }

    function showErrors(errors) {
        resetErrorsTextOnly();

        Object.keys(errors).forEach(key => {
            let fieldKey = key;

            if (key.startsWith('unit.')) {
                fieldKey = key.replace('unit.', '');
            }

            if (key.startsWith("keluarga.")) {
                const parts = key.split(".");

                const kkIndex = parts[1];

                if (parts.includes('skrining_nik')) {
                    const aIndex = parts[3];
                    const field = parts[5];

                    const errorEl = document.getElementById(
                        `error-keluarga-${kkIndex}-${field}-${aIndex}`
                    );

                    if (errorEl) {
                        errorEl.textContent = errors[key][0];
                        errorEl.classList.remove("hidden");
                    }

                    return;
                }

                const field = parts[3];

                const errorEl = document.getElementById(
                    `error-keluarga-${kkIndex}-${field}`
                );

                if (errorEl) {
                    errorEl.textContent = errors[key][0];
                    errorEl.classList.remove("hidden");
                }

                return;
            }

            if (key.startsWith("skrining_kk.")) {
                const match = key.match(/skrining_kk\.(\d+)/);

                if (match) {
                    const questionIndex = match[1];

                    const wrapper = document.querySelectorAll(
                        '#contentSkriningKk [data-pertanyaan-id]'
                    )[questionIndex];

                    if (wrapper) {
                        const errorEl =
                            wrapper.querySelector('.error-pertanyaan');

                        if (errorEl) {

                            errorEl.textContent = errors[key][0];
                            errorEl.classList.remove('hidden');
                        }

                        wrapper.classList.add('border-red-500');
                    }
                }

                return;
            }

            const errorEl =
                document.getElementById(`error-${fieldKey}`) ||
                document.querySelector(`[data-error="${fieldKey}"]`);

            if (errorEl) {
                errorEl.textContent = errors[key][0];
                errorEl.classList.remove("hidden");
            }
        });

        const firstError = document.querySelector(
            '.text-red-500:not(.hidden)'
        );

        if (firstError) {
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            const wrapper =
                firstError.closest('div');

            const input =
                wrapper?.querySelector(
                    'input, textarea, select, button'
                );

            setTimeout(() => {
                input?.focus();
            }, 300);
        }
    }

    function resetErrorsTextOnly() {
        document.querySelectorAll('[id^="error-"]')
            .forEach(el => {

                el.textContent = '';
                el.classList.add('hidden');
            });

        document.querySelectorAll('[class*="error-"]')
            .forEach(el => {

                el.textContent = '';
                el.classList.add('hidden');
            });

        document.querySelectorAll(
            '.border-red-500'
        ).forEach(el => {

            el.classList.remove('border-red-500');

            if (
                el.hasAttribute('data-pertanyaan-id')
            ) {
                el.classList.add('border-gray-200');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('formEdit');
    const btnSubmit = document.getElementById('btnSubmitEdit');

    if (!form || !btnSubmit) return;

    btnSubmit.addEventListener('click', async function () {

        resetErrorsTextOnly();

        try {

            const formData = new FormData(form);

            formData.append('_method', 'PUT');

            const result = await fetchWithAuth(
                `/api/monitoring/hasil-skrining/${unitId}`,
                {
                    method: "POST",
                    body: formData,
                    headers: {
                        "Accept": "application/json"
                    }
                }
            );

            if (result?.errors) {
                showErrors(result.errors);
                return;
            }

            if (!result?.status) {
                showErrorToast(result?.message || "Gagal memperbarui data");
                return;
            }
            showSuccessToast(
                "Data berhasil diperbarui!",
                "",
                () => {
                    window.location.href = "/nakes/hasil-skrining";
                }
            );

        } catch (error) {

            console.error(error);

            const errors =
                error?.errors ||
                error?.response?.data?.errors ||
                error?.response?.errors ||
                error?.data?.errors;

            if (errors) {
                showErrors(errors);
                return;
            }

            showErrorToast(
                error?.message || "Terjadi kesalahan saat update data"
            );
        }
    });
});

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
                console.log(kader);
                setDropdownLabel('kaderDropdown', kader.nama, 'Pilih Kader');
                document.getElementById('user_id').value = kader.id;

                fillKaderLocation(kader);
            };

            dropdown.appendChild(btn);
        });
    }

    function fillKaderLocation(kader) {
        const kaderDetail = kader.kaderDetail;

        if (!kaderDetail) return;

        document.getElementById('kelurahan_nama').value =
            kaderDetail.nama_kelurahan || '';

        document.getElementById('kelurahan_id').value =
            kaderDetail.kelurahan_id || '';

        document.getElementById('posyandu_nama').value =
            kaderDetail.nama_posyandu || '';

        document.getElementById('posyandu_id').value =
            kaderDetail.posyandu_id || '';
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

            const skriningKk = formModel.keluarga[0]?.skrining?.find(
                s => s.target_skrining === 'kk'
            );

            if (skriningKk) {
                let html = '';

                html += `
                    <div class="flex justify-end mb-4">
                        <button
                            type="button"
                            id="btnDeleteAllSkriningKk"
                            class="
                                ml-3 w-8 h-8 flex items-center justify-center
                                rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition
                            "
                        >
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `;

                const grouped = skriningKk.pertanyaan.reduce((acc, item) => {
                    const key = item.section_no_urut;

                    if (!acc[key]) {
                        acc[key] = {
                            section: item.section,
                            items: []
                        };
                    }

                    acc[key].items.push(item);

                    return acc;

                }, {});

                let globalIndex = 0;

                Object.entries(grouped).forEach(([sectionName, questions]) => {
                    let sectionNumber = 1;

                    html += `
                        <div class="
                            border border-[#61359C]/80
                            rounded-xl
                            px-5 py-3
                            font-bold
                            text-[#61359C]
                            text-sm
                            bg-[#61359C]/5
                            mb-3
                        ">
                            ${questions.section}
                        </div>
                    `;

                    questions.items
                        .sort((a, b) => Number(a.no_urut) - Number(b.no_urut))
                        .forEach((q) => {
                            const index = globalIndex++;
                            const nomor = sectionNumber++;
                            html += `
                                <div
                                    class="
                                        border border-gray-200
                                        rounded-xl
                                        px-5 py-4
                                        bg-white
                                        text-sm
                                        mb-3
                                    "
                                    data-pertanyaan-id="${q.pertanyaan_id ?? q.id}"
                                >

                                <input
                                    type="hidden"
                                    name="skrining_kk[${index}][pertanyaan_id]"
                                    value="${q.pertanyaan_id ?? q.id ?? ''}"
                                >

                                <input
                                    type="hidden"
                                    name="skrining_kk[${index}][jawaban_id]"
                                    value="${q.jawaban_id ?? ''}"
                                >

                                <div class="font-semibold text-gray-800 leading-snug">
                                    ${nomor}. ${q.pertanyaan ?? "-"}
                                    ${q.is_required
                                        ? `<span class="text-red-500 ml-1">*</span>`
                                        : ''
                                    }
                                </div>

                                ${
                                    q.keterangan
                                    ? `
                                        <div class="text-xs text-gray-500 mt-1 leading-snug whitespace-pre-line">
                                            ${q.keterangan}
                                        </div>
                                    `
                                    : ''
                                }

                                <div class="mt-3 space-y-2 text-sm text-gray-700">
                                    ${renderInputJawaban(q, `skrining_kk[${index}]`)}
                                    <p class="text-red-500 text-xs mt-2 hidden error-pertanyaan"></p>
                                </div>
                            </div>
                        `;
                        });
                });

                kkContainer.innerHTML = html;

                document.getElementById('btnDeleteAllSkriningKk')?.addEventListener('click', () => {
                    showDeleteConfirmToast(
                        'Yakin ingin hapus semua hasil skrining KK?',
                        async () => {
                            await deleteAllSkriningKk(unitId);
                        }
                    );
                });

                async function deleteAllSkriningKk(unitId) {
                    try {
                        const result = await fetchWithAuth(`/api/monitoring/hasil-skrining/${unitId}/delete-kk-all`, {
                        method: "DELETE"
                    });

                        if (result.status) {
                            kkContainer.innerHTML = '';
                            showSuccessToast("Semua skrining KK berhasil dihapus");
                        }
                    } catch (err) {
                        console.error(err);
                    }
                }

                document.querySelectorAll(
                    '#contentSkriningKk .space-y-2'
                ).forEach(wrapper => {
                    const checkbox = wrapper.querySelector(
                        'input[type="checkbox"]'
                    );

                    if (checkbox) {
                        syncCheckboxValues(checkbox);
                    }

                    const radio = wrapper.querySelector(
                        'input[type="radio"]'
                    );

                    if (radio) {
                        syncRadioValues(radio);
                    }
                });
           } else {
                kkContainer.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-10 text-gray-500">
                        <i class="fa-solid fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm font-semibold">Belum ada skrining KK</p>
                    </div>
                `;
            }

            function renderInputJawaban(item, prefix) {
                switch (item.jenis_jawaban) {

                    case 'radio':
                        return renderRadio(item, prefix);

                    case 'checkbox':
                        return renderCheckbox(item, prefix);

                    case 'select':
                        return renderCustomDropdown(item, prefix);

                    case 'text':
                        return `
                            <input
                                type="text"
                                name="${prefix}[jawaban]"
                                value="${item.jawaban ?? ''}"
                                class="
                                    w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50
                                "
                            >
                        `;

                    case 'textarea':
                        return `
                            <textarea
                                rows="3"
                                name="${prefix}[jawaban]"
                                class="
                                    w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50
                                "
                            >${item.jawaban ?? ''}</textarea>
                        `;

                    case 'date':
                        return `
                            <input
                                type="date"
                                name="${prefix}[jawaban]"
                                value="${item.jawaban ?? ''}"
                                class="
                                    w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50
                                "
                            >
                        `;

                    default:
                        return '-';
                }
            }

            function renderRadio(item, prefix) {
                const name = `${prefix}[jawaban]`;

                let html = '';

                (item.opsi_jawaban || []).forEach(opt => {
                    html += `
                    <label class="flex items-center gap-2">

                        <input
                            type="radio"
                            name="${name}"
                            value="${opt}"
                            class="radio-option accent-[#61359C]"
                            data-pertanyaan-id="${item.id}"
                            ${item.jawaban === opt ? 'checked' : ''}
                        >

                        <span>${opt}</span>

                    </label>
                `;
                });

                if (item.opsi_lain) {
                    const isOther =
                        item.jawaban &&
                        !(item.opsi_jawaban || []).includes(item.jawaban);

                    html += `
                    <label class="flex items-center gap-2">

                        <input
                            type="radio"
                            name="${name}"
                            value="lainnya"
                            class="radio-other accent-[#61359C]"
                            data-pertanyaan-id="${item.id}"
                            ${isOther ? 'checked' : ''}
                        >

                        <span>Lainnya</span>

                    </label>

                    <input
                        type="text"
                        name="${prefix}[jawaban_lainnya]"
                        value="${isOther ? item.jawaban : ''}"
                        class="
                            other-input
                            ${isOther ? '' : 'hidden'}
                            border border-gray-300 rounded-lg px-3 py-1.5 text-sm mt-1
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50
                        "
                        placeholder="Ketik lainnya..."
                    >

                    <input
                        type="hidden"
                        name="${prefix}[jawaban]"
                        class="hidden-jawaban"
                    />
                `;
                }
                return `<div class="space-y-2">${html}</div>`;
            }

            document.addEventListener("change", function(e) {
                if (e.target.classList.contains("radio-other")) {
                    const wrapper = e.target.closest('.space-y-2');
                    const otherInput = wrapper.querySelector(".other-input");

                    if (otherInput) {
                        otherInput.classList.remove("hidden");
                        otherInput.focus();
                    }
                }

                if (e.target.type === "radio") {
                    syncRadioValues(e.target);
                }
            });

            document.addEventListener("input", function(e) {
                if (e.target.classList.contains("other-input")) {
                    const wrapper = e.target.closest('.space-y-2');
                    const radio = wrapper.querySelector('.radio-other');

                    if (radio?.checked) {
                        syncRadioValues(radio);
                    }
                }
            });

            function syncRadioValues(radio) {
                const wrapper = radio.closest('.space-y-2');

                const selected = wrapper.querySelector('input[type="radio"]:checked')?.value;

                const otherInput = wrapper.querySelector('.other-input');

                let final = selected;

                if (selected === 'lainnya') {
                    const val = (otherInput?.value || '').trim();

                    if (val !== '') {
                        final = val;
                    } else {
                        final = 'lainnya';
                    }
                }

                let hidden = wrapper.querySelector('.hidden-jawaban');

                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = radio.name;
                    hidden.classList.add('hidden-jawaban');
                    wrapper.appendChild(hidden);
                }

                hidden.value = final;
            }

            function renderCheckbox(item, prefix) {
                let jawaban = [];

                try {
                    if (typeof item.jawaban === 'string') {
                        if (
                            item.jawaban.startsWith('[') &&
                            item.jawaban.endsWith(']')
                        ) {
                            jawaban = JSON.parse(item.jawaban);
                        } else {
                            jawaban = [item.jawaban];
                        }
                    } else if (Array.isArray(item.jawaban)) {
                        jawaban = item.jawaban;
                    } else if (item.jawaban) {
                        jawaban = [item.jawaban];
                    }
                } catch (e) {
                    jawaban = item.jawaban ? [item.jawaban] : [];
                }

                const name = `${prefix}[jawaban][]`;

                let html = '';

                (item.opsi_jawaban || []).forEach(opt => {
                    html += `
                    <label class="flex items-start gap-2">

                        <input
                            type="checkbox"
                            name="${name}"
                            value="${opt}"
                            class="checkbox-option accent-[#61359C] mt-1"
                            ${jawaban.includes(opt) ? 'checked' : ''}
                        >

                        <span>${opt}</span>

                    </label>
                `;
                });

                const otherValues = jawaban.filter(
                    val => !(item.opsi_jawaban || []).includes(val)
                );

                const isOtherChecked = otherValues.length > 0;
                if (item.opsi_lain) {
                    html += `
                    <label class="flex items-start gap-2">

                        <input
                            type="checkbox"
                            name="${name}"
                            value="lainnya"
                            class="checkbox-other accent-[#61359C] mt-1"
                            ${isOtherChecked ? 'checked' : ''}
                        >

                        <span>Lainnya</span>

                    </label>

                    <input
                        type="text"
                        name="${prefix}[jawaban_lainnya]"
                        value="${isOtherChecked ? otherValues.join(', ') : ''}"
                        class="
                            other-input
                            ${isOtherChecked ? '' : 'hidden'}
                            border border-gray-300
                            rounded-lg px-3 py-1.5 text-sm mt-1
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50
                        "
                        placeholder="Ketik lainnya..."
                    >\
                    <input type="hidden"
                        name="${prefix}[jawaban]"
                        class="hidden-jawaban"
                    />
                `;
                }

                return `<div class="space-y-2">${html}</div>`;
            }

            document.addEventListener("change", function(e) {
                if (e.target.classList.contains("checkbox-other")) {
                    const wrapper = e.target.closest('.space-y-2');
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

                if (e.target.type === "checkbox") {
                    syncCheckboxValues(e.target);
                }
            });

            document.addEventListener("input", function(e) {
                if (e.target.classList.contains("other-input")) {
                    const wrapper = e.target.closest('.space-y-2');
                    const checkbox = wrapper.querySelector('.checkbox-other');

                    if (checkbox?.checked) {
                        syncCheckboxValues(checkbox);
                    }
                }
            });

            function syncCheckboxValues(checkbox) {
                const wrapper = checkbox.closest('.space-y-2');

                if (!wrapper) return;

                const checked = Array.from(
                    wrapper.querySelectorAll('input[type="checkbox"]:checked')
                ).map(cb => cb.value);

                const otherInput = wrapper.querySelector('.other-input');

                let final = checked.filter(v => v !== 'lainnya');

                if (checked.includes('lainnya')) {
                    const val = (otherInput?.value || '').trim();
                    final.push(val !== '' ? val : 'lainnya');
                }

                let hidden = wrapper.querySelector('.hidden-jawaban');

                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = checkbox.name.replace('[]', '');
                    hidden.classList.add('hidden-jawaban');
                    wrapper.appendChild(hidden);
                }
                hidden.value = JSON.stringify(final);
            }

            function renderCustomDropdown(item, prefix) {
                const options = item.opsi_jawaban || [];

                let htmlOptions = options.map(opt => `
                <button
                    type="button"
                    class="
                        dropdown-item block w-full text-center
                        px-4 py-1 text-sm text-gray-700
                        hover:bg-gray-100 transition
                    "
                    onclick="selectDropdownOption(this, '${opt}')"
                >
                    ${opt}
                </button>
            `).join('');

                return `
                <div class="relative block text-left w-full">
                    <input
                        type="hidden"
                        name="${prefix}[jawaban]"
                        value="${item.jawaban ?? ''}"
                        class="dropdown-value"
                    >

                    <button
                        type="button"
                        class="
                            relative flex items-center justify-between w-full
                            border border-[#00000033]
                            text-sm rounded-lg px-4 py-2 bg-white
                        "
                        onclick="toggleDropdown(this)"
                    >

                        <span class="dropdown-selected text-left w-full truncate text-gray-500">
                            ${item.jawaban || 'Pilih Opsi'}
                        </span>

                        <svg
                            class="w-4 h-4 absolute right-3"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <div class="
                        dropdown-menu hidden absolute z-10 mt-2
                        w-full bg-white shadow-lg rounded-2xl
                        p-2 border border-gray-100
                    ">
                        ${htmlOptions}
                    </div>

                </div>
            `;
            }

            window.selectDropdownOption = function(optionEl, value) {
                const dropdown = optionEl.closest('.relative');

                const selectedSpan = dropdown.querySelector('.dropdown-selected');
                const hiddenInput = dropdown.querySelector('.dropdown-value');

                selectedSpan.textContent = value;

                hiddenInput.value = value;

                dropdown.querySelector('.dropdown-menu').classList.add('hidden');
            };

            window.selectDropdownOther = function(optionEl) {
                const dropdown = optionEl.closest('.relative');

                const wrapper = dropdown.querySelector('.dropdown-other-wrapper');
                const selectedSpan = dropdown.querySelector('.dropdown-selected');
                const hiddenInput = dropdown.querySelector('.dropdown-value');

                wrapper.classList.remove('hidden');
                selectedSpan.textContent = 'Lainnya';

                hiddenInput.value = '';
            };

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('dropdown-other')) {
                    const dropdown = e.target.closest('.relative');
                    const hiddenInput = dropdown.querySelector('.dropdown-value');

                    hiddenInput.value = e.target.value;
                }
            });

            const nikContainer = document.getElementById('contentSkriningNik');

            nikContainer.innerHTML = '';

            let kkTabsHtml = `
                <div class="flex flex-wrap gap-2 mb-5">
            `;

            formModel.keluarga.forEach((kk, kkIndex) => {
                kkTabsHtml += `
                    <button
                        type="button"
                        class="kk-tab px-4 py-2 rounded-lg border text-sm transition
                            ${kkIndex === 0
                                ? 'bg-[#61359C] text-white font-semibold border-[#61359C]'
                                : 'bg-white text-gray-700 font-semibold border-gray-300 hover:bg-gray-100'}
                        "
                        data-kk="${kkIndex}">
                        KK ${kkIndex + 1}
                    </button>
                `;
            });

            kkTabsHtml += `</div>`;

            nikContainer.innerHTML += kkTabsHtml;

            formModel.keluarga.forEach((kk, kkIndex) => {
                    let kkHtml = `
                        <div 
                            class="kk-content ${kkIndex !== 0 ? 'hidden' : ''}"
                            data-kk-content="${kkIndex}"
                        >

                            <div class="mb-4 border border-gray-300 p-4 rounded-xl bg-white space-y-4 kk-item">
                                <input type="hidden"
                                    name="keluarga[${kkIndex}][keluarga_id]"
                                    value="${kk.keluarga_id ?? kk.id ?? ''}">

                                <div class="flex justify-between items-center">
                                    <p class="font-semibold text-lg">
                                        KK ${kkIndex + 1}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">No KK</label>

                                        <input type="text"
                                            name="keluarga[${kkIndex}][identitas][no_kk]"
                                            value="${kk.no_kk ?? ''}"
                                            class="border border-[#00000033] rounded-lg p-2 w-full">
                                        <p id="error-keluarga-${kkIndex}-no_kk" class="text-red-500 text-xs mt-1 hidden"></p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">
                                            No Telepon
                                        </label>

                                        <input type="number"
                                            name="keluarga[${kkIndex}][identitas][no_telepon]"
                                            value="${kk.no_telepon ?? ''}"
                                            class="border border-[#00000033] rounded-lg p-2 w-full">
                                        <p id="error-keluarga-${kkIndex}-no_telepon" class="text-red-500 text-xs mt-1 hidden"></p>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox"
                                            class="luar-wilayah-toggle mt-1 w-4 h-4 accent-[#61359C]"
                                            name="keluarga[${kkIndex}][identitas][is_luar_wilayah]"
                                            value="1"
                                            ${kk.is_luar_wilayah == 1 ? 'checked' : ''}>

                                        <span class="text-sm font-semibold">KK Luar Wilayah</span>
                                    </label>

                                    <p class="text-xs text-gray-500 mt-1">
                                        *Centang jika KK berasal dari luar wilayah.
                                    </p>
                                </div>

                                <div class="luar-wilayah-section ${kk.is_luar_wilayah == 1 ? '' : 'hidden'}"
                                    data-index="${kkIndex}">
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-semibold mb-1">Alamat KTP</label>
                                            <input type="text"
                                                name="keluarga[${kkIndex}][identitas][alamat_ktp]"
                                                value="${kk.alamat_ktp ?? ''}"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold mb-1">RT KTP</label>
                                            <input type="text"
                                                name="keluarga[${kkIndex}][identitas][rt_ktp]"
                                                value="${kk.rt_ktp ?? ''}"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold mb-1">RW KTP</label>
                                            <input type="text"
                                                name="keluarga[${kkIndex}][identitas][rw_ktp]"
                                                value="${kk.rw_ktp ?? ''}"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

        
                    const skriningNik = kk.skrining?.find(
                        s => s.target_skrining === 'nik'
                    );

                    if (skriningNik?.anggota) {
                        skriningNik.anggota.forEach((anggota, aIndex) => {
                                let anggotaHtml = `
                                    <div class="border border-gray-200 rounded-xl overflow-hidden mb-4">
                                        <div
                                            class="flex items-center justify-between
                                            px-4 py-2 bg-gray-50 hover:bg-gray-100 transition"
                                        >
                                            <button type="button"
                                                class="toggle-anggota flex-1 flex items-center justify-between text-left"
                                            >
                                                <div class="text-left">
                                                    <div class="flex items-center gap-1">
                                                        <p class="font-semibold text-sm">
                                                            ${anggota.nama}
                                                        </p>

                                                        ${
                                                            anggota.hubungan_keluarga?.toLowerCase() === 'kepala keluarga'
                                                            ? `
                                                                <span class="text-xs text-gray-700">
                                                                    (Kepala Keluarga)
                                                                </span>
                                                            `
                                                            : ''
                                                        }
                                                    </div>

                                                    <p class="text-xs text-gray-500">
                                                        ${skriningNik.siklus ?? '-'}
                                                    </p>
                                                </div>

                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="btn-delete-anggota ml-3 w-8 h-8 flex items-center justify-center
                                                    rounded-lg bg-red-50 hover:bg-red-100 text-red-500 transition"
                                                data-keluarga-index="${kkIndex}"
                                                data-anggota-index="${aIndex}"
                                            >
                                                <i class="fa-solid fa-trash text-sm"></i>
                                            </button>
                                        </div>

                                        <div class="anggota-content hidden p-4 bg-gray-50 space-y-4">
                                `;
                                const pekerjaanOptions = [
                                    'Tidak Bekerja',
                                    'Pelajar / Mahasiswa',
                                    'PNS / TNI / POLRI / BUMN / BUMD',
                                    'Pegawai Swasta',
                                    'Wiraswasta',
                                    'Petani / Nelayan',
                                    'Pedagang',
                                    'Pengusaha',
                                    'Ibu Rumah Tangga'
                                ];

                                const isOtherPekerjaan =
                                    anggota.pekerjaan &&
                                    !pekerjaanOptions.includes(anggota.pekerjaan);

                                anggotaHtml += `
                                    <div class="kk-item bg-white border border-[#61359C] rounded-2xl p-6 mb-4 space-y-6">
                                        <input type="hidden"
                                            name="keluarga[${kkIndex}][skrining_nik][${aIndex}][anggota_id]"
                                            value="${anggota.id || ''}">

                                        <div>
                                            <label class="block text-sm font-semibold mb-1">
                                                NIK
                                            </label>
                                            <input type="text"
                                                name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][nik]"
                                                value="${anggota.nik || ''}"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                            <p id="error-keluarga-${kkIndex}-nik-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                        </div>

                                        <div class="relative edit-custom-dropdown">
                                            <label class="block text-sm font-semibold mb-1">
                                                Hubungan Keluarga
                                            </label>
                                            <button
                                                type="button"
                                                class="edit-dropdown-toggle w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                bg-white flex items-center justify-between
                                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                                            >
                                                <span class="edit-dropdown-label">
                                                    ${anggota.hubungan_keluarga || 'Pilih Hubungan Keluarga'}
                                                </span>

                                                <i class="fa-solid fa-chevron-down text-xs"></i>
                                            </button>
                                            <input
                                                type="hidden"
                                                name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][hubungan_keluarga]"
                                                value="${anggota.hubungan_keluarga || ''}"
                                                class="edit-dropdown-value"
                                            >
                                            <div class="
                                                edit-dropdown-menu hidden
                                                absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl p-2
                                                border border-gray-100
                                            ">

                                                ${[
                                                    'Kepala Keluarga',
                                                    'Istri',
                                                    'Anak',
                                                    'Menantu',
                                                    'Cucu',
                                                    'Orang Tua',
                                                    'Famili Lain',
                                                    'Pembantu / Asisten'
                                                ].map(opt => `
                                                    <button
                                                        type="button"
                                                        class="
                                                            edit-dropdown-item
                                                            w-full px-4 py-1 text-sm text-center
                                                            focus:outline-none focus:ring-2
                                                            focus:ring-[#61359C]/50
                                                            hover:bg-gray-100 transition mb-1
                                                        "
                                                        data-value="${opt}"
                                                    >
                                                        ${opt}
                                                    </button>
                                                `).join('')}

                                                <button
                                                    type="button"
                                                    class="
                                                        edit-dropdown-other-btn
                                                        w-full px-4 py-1 text-sm text-center
                                                        focus:outline-none focus:ring-2
                                                        focus:ring-[#61359C]/50
                                                        hover:bg-gray-100 transition mb-1
                                                    "
                                                >
                                                    + Lainnya
                                                </button>

                                                <div class="edit-dropdown-other hidden mt-2 border-t border-gray-200 pt-2">
                                                    <input
                                                        type="text"
                                                        class="
                                                            edit-dropdown-other-input
                                                            w-full border border-gray-300 rounded-lg
                                                            px-3 py-1.5 text-sm text-center
                                                            focus:outline-none focus:ring-2
                                                            focus:ring-[#61359C]/50
                                                        "
                                                        placeholder="Ketik hubungan keluarga..."
                                                    >
                                                </div>
                                            </div>

                                            <p id="error-keluarga-${kkIndex}-hubungan_keluarga-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold mb-1">
                                                Nama Lengkap
                                            </label>
                                            <input type="text"
                                                name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][nama]"
                                                value="${anggota.nama || ''}"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                            <p id="error-keluarga-${kkIndex}-nama-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                        </div>

                                        <div class="grid md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold mb-1">
                                                    Tempat Lahir
                                                </label>
                                                <input type="text"
                                                    name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][tempat_lahir]"
                                                    value="${anggota.tempat_lahir || ''}"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                                <p id="error-keluarga-${kkIndex}-tempat_lahir-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold mb-1">
                                                    Tanggal Lahir
                                                </label>
                                                <input type="date"
                                                    name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][tanggal_lahir]"
                                                    value="${anggota.tanggal_lahir || ''}"
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                                                    <p id="error-keluarga-${kkIndex}-tanggal_lahir-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                            </div>
                                        </div>

                                        <div class="grid md:grid-cols-2 gap-4">
                                            <div class="grid md:grid-cols-2 gap-4">
                                                <div class="relative edit-custom-dropdown">
                                                    <label class="block text-sm font-semibold mb-1">
                                                        Jenis Kelamin
                                                    </label>
                                                    <button
                                                        type="button"
                                                        class="edit-dropdown-toggle w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                        bg-white flex items-center justify-between
                                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                                                    >
                                                        <span class="edit-dropdown-label">
                                                            ${
                                                                anggota.jenis_kelamin === 'L'
                                                                    ? 'Laki-laki'
                                                                    : anggota.jenis_kelamin === 'P'
                                                                        ? 'Perempuan'
                                                                        : 'Pilih Jenis Kelamin'
                                                            }
                                                        </span>

                                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                                    </button>

                                                    <input
                                                        type="hidden"
                                                        name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][jenis_kelamin]"
                                                        value="${anggota.jenis_kelamin || ''}"
                                                        class="edit-dropdown-value"
                                                    >

                                                    <div class="
                                                        edit-dropdown-menu hidden
                                                        absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl p-2
                                                        border border-gray-100
                                                    ">

                                                        ${[
                                                            { label: 'Laki-laki', value: 'L' },
                                                            { label: 'Perempuan', value: 'P' }
                                                        ].map(opt => ` 
                                                            <button
                                                                type = "button"
                                                                class = "
                                                                    edit-dropdown-item
                                                                    w-full px-4 py-1 text-sm text-center
                                                                    focus:outline-none focus:ring-2
                                                                    focus:ring-[#61359C]/50
                                                                    hover:bg-gray-100 transition mb-1
                                                                "
                                                                data-value="${opt.value}"
                                                                data-label = "${opt.label}" 
                                                            >
                                                                ${opt.label} 
                                                            </button>
                                                        `).join('')}
                                                    </div>
                                                    <p id="error-keluarga-${kkIndex}-jenis_kelamin-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                                </div>
                                                
                                                <div class="relative edit-custom-dropdown">
                                                    <label class="block text-sm font-semibold mb-1">
                                                        Pendidikan Terakhir
                                                    </label>
                                                    <button
                                                        type="button"
                                                        class="edit-dropdown-toggle w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                        bg-white flex items-center justify-between
                                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                                                    >
                                                        <span class="edit-dropdown-label">
                                                            ${anggota.pendidikan_terakhir || 'Pilih Pendidikan'}
                                                        </span>
                                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                                    </button>

                                                    <input
                                                        type="hidden"
                                                        name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][pendidikan_terakhir]"
                                                        value="${anggota.pendidikan_terakhir || ''}"
                                                        class="edit-dropdown-value"
                                                    >

                                                    <div class="
                                                        edit-dropdown-menu hidden
                                                        absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl p-2
                                                        border border-gray-100
                                                    ">

                                                        ${[
                                                            'S1 / S2 / S3 (PT)',
                                                            'D1 / D2 / D3',
                                                            'SMA atau sederajat',
                                                            'SMP atau sederajat',
                                                            'SD atau sederajat',
                                                            'Tidak pernah sekolah',
                                                            'Belum sekolah'
                                                        ].map(opt => `
                                                            <button
                                                                type="button"
                                                                class="
                                                                    edit-dropdown-item
                                                                    w-full px-4 py-1 text-sm text-center
                                                                    focus:outline-none focus:ring-2
                                                                    focus:ring-[#61359C]/50
                                                                    hover:bg-gray-100 transition mb-1
                                                                "
                                                                data-value="${opt}"
                                                            >
                                                                ${opt}
                                                            </button>
                                                        `).join('')}
                                                    </div>
                                                    <p id="error-keluarga-${kkIndex}-pendidikan_terakhir-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                                </div>
                                            </div>

                                            <div class="grid md:grid-cols-2 gap-4">
                                                <div class="relative edit-custom-dropdown">
                                                    <label class="block text-sm font-semibold mb-1">
                                                        Pekerjaan
                                                    </label>
                                                    <button
                                                        type="button"
                                                        class="edit-dropdown-toggle w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                        bg-white flex items-center justify-between
                                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                                                    >
                                                        <span class="edit-dropdown-label">
                                                            ${anggota.pekerjaan || 'Pilih Pekerjaan'}
                                                        </span>

                                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                                    </button>
                                                    <input
                                                        type="hidden"
                                                        name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][pekerjaan]"
                                                        value="${anggota.pekerjaan || ''}"
                                                        class="edit-dropdown-value"
                                                    >
                                                    <div class="
                                                        edit-dropdown-menu hidden
                                                        absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl p-2
                                                        border border-gray-100
                                                    ">

                                                        ${pekerjaanOptions.map(opt => `
                                                            <button
                                                                type="button"
                                                                class="
                                                                    edit-dropdown-item
                                                                    w-full px-4 py-1 text-sm text-center
                                                                    focus:outline-none focus:ring-2
                                                                    focus:ring-[#61359C]/50
                                                                    hover:bg-gray-100 transition mb-1
                                                                "
                                                                data-value="${opt}"
                                                            >
                                                                ${opt}
                                                            </button>
                                                        `).join('')}

                                                        <button
                                                            type="button"
                                                            class="
                                                                edit-dropdown-other-btn
                                                                w-full px-4 py-1 text-sm text-center
                                                                focus:outline-none focus:ring-2
                                                                focus:ring-[#61359C]/50
                                                                hover:bg-gray-100 transition mb-1
                                                            "
                                                        >
                                                            + Lainnya
                                                        </button>

                                                        <div class="edit-dropdown-other ${isOtherPekerjaan ? '' : 'hidden'} mt-2 border-t border-gray-200 pt-2">
                                                            <input
                                                                    type="text"
                                                                    class="
                                                                        edit-dropdown-other-input
                                                                        w-full border border-gray-300 rounded-lg
                                                                        px-3 py-1.5 text-sm text-center
                                                                        focus:outline-none focus:ring-2
                                                                        focus:ring-[#61359C]/50
                                                                    "
                                                                    value="${isOtherPekerjaan ? anggota.pekerjaan : ''}"
                                                                    placeholder="Ketik pekerjaan..."
                                                                >
                                                        </div>
                                                    </div>
                                                    <p id="error-keluarga-${kkIndex}-pekerjaan-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                                </div>

                                                <div class="relative edit-custom-dropdown">
                                                    <label class="block text-sm font-semibold mb-1">
                                                        Status Perkawinan
                                                    </label>
                                                    <button
                                                        type="button"
                                                        class="edit-dropdown-toggle w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                        bg-white flex items-center justify-between
                                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                                                    >
                                                        <span class="edit-dropdown-label">
                                                            ${anggota.status_perkawinan || 'Pilih Status'}
                                                        </span>

                                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                                    </button>

                                                    <input
                                                        type="hidden"
                                                        name="keluarga[${kkIndex}][skrining_nik][${aIndex}][identitas][status_perkawinan]"
                                                        value="${anggota.status_perkawinan || ''}"
                                                        class="edit-dropdown-value"
                                                    >
                                                    <div class="
                                                        edit-dropdown-menu hidden
                                                        absolute z-10 mt-2 w-full bg-white shadow-lg rounded-2xl p-2
                                                        border border-gray-100
                                                    ">

                                                        ${[
                                                            'Kawin',
                                                            'Belum Kawin',
                                                            'Cerai Hidup',
                                                            'Cerai Mati'
                                                        ].map(opt => `
                                                            <button
                                                                type="button"
                                                                class="
                                                                    edit-dropdown-item
                                                                    w-full px-4 py-1 text-sm text-center
                                                                    focus:outline-none focus:ring-2
                                                                    focus:ring-[#61359C]/50
                                                                    hover:bg-gray-100 transition mb-1
                                                                "
                                                                data-value="${opt}"
                                                            >
                                                                ${opt}
                                                            </button>
                                                        `).join('')}
                                                    </div>
                                                    <p id="error-keluarga-${kkIndex}-status_perkawinan-${aIndex}" class="text-red-500 text-xs mt-1 hidden"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    `;

                                    const grouped = anggota.pertanyaan.reduce((acc, item) => {
                                        const key = item.section_no_urut;

                                        if (!acc[key]) {
                                            acc[key] = {
                                                section: item.section,
                                                items: []
                                            };
                                        }

                                        acc[key].items.push(item);

                                        return acc;
                                    }, {});

                                    let globalIndex = 0;

                                    Object.entries(grouped).forEach(([sectionName, questions]) => {
                                        let sectionNumber = 1;

                                        anggotaHtml += `
                                            <div class="
                                                border border-[#61359C]/80
                                                rounded-xl
                                                px-5 py-3
                                                font-bold
                                                text-[#61359C]
                                                text-sm
                                                bg-[#61359C]/5
                                                mb-3
                                            ">
                                                ${questions.section}
                                            </div>
                                        `;

                                        questions.items
                                            .sort((a, b) => Number(a.no_urut) - Number(b.no_urut))
                                            .forEach((q) => {

                                                const qIndex = globalIndex++;
                                                const nomor = sectionNumber++;

                                                anggotaHtml += `
                                                    <div
                                                        class="
                                                            border border-gray-200
                                                            rounded-xl
                                                            px-5 py-4
                                                            bg-white
                                                            text-sm
                                                            mb-3
                                                        "
                                                    >

                                                        <input
                                                            type="hidden"
                                                            name="keluarga[${kkIndex}][skrining_nik][${aIndex}][jawaban_list][${qIndex}][pertanyaan_id]"
                                                            value="${q.pertanyaan_id ?? q.id ?? ''}"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="keluarga[${kkIndex}][skrining_nik][${aIndex}][jawaban_list][${qIndex}][jawaban_id]"
                                                            value="${q.jawaban_id ?? ''}"
                                                        >

                                                        <div class="font-semibold text-gray-800 leading-snug">
                                                            ${nomor}. ${q.pertanyaan ?? "-"}
                                                            ${q.is_required
                                                                ? `<span class="text-red-500 ml-1">*</span>`
                                                                : ''
                                                            }
                                                        </div>

                                                        ${
                                                            q.keterangan
                                                            ? `
                                                                <div class="text-xs text-gray-500 mt-1 leading-snug whitespace-pre-line">
                                                                    ${q.keterangan}
                                                                </div>
                                                            `
                                                            : ''
                                                        }

                                                        <div class="mt-3 space-y-2 text-sm text-gray-700">
                                                            ${renderInputJawaban(
                                                                q,
                                                                `keluarga[${kkIndex}][skrining_nik][${aIndex}][jawaban_list][${qIndex}]`
                                                            )}
                                                            <p class="text-red-500 text-xs mt-2 hidden error-pertanyaan"></p>
                                                        </div>

                                                    </div>
                                                `;
                                            });
                                    });

                                    anggotaHtml += `
                                            </div>
                                        </div>
                                    `;

                                    kkHtml += anggotaHtml;
                                });
                        }

                        kkHtml += `
                                </div>
                            </div>
                        `;

                        nikContainer.innerHTML += kkHtml;

                        document.querySelectorAll('.space-y-2').forEach(wrapper => {
                            const checkbox = wrapper.querySelector(
                                'input[type="checkbox"]'
                            );

                            if (checkbox) {
                                syncCheckboxValues(checkbox);
                            }

                            const radio = wrapper.querySelector(
                                'input[type="radio"]'
                            );

                            if (radio) {
                                syncRadioValues(radio);
                            }
                        });
                    });

                document.querySelectorAll('.kk-tab').forEach(tab => {
                    tab.addEventListener('click', function() {

                        const kkIndex = this.dataset.kk;

                        document.querySelectorAll('.kk-tab').forEach(btn => {

                            btn.classList.remove(
                                'bg-[#61359C]',
                                'text-white',
                                'border-[#61359C]'
                            );

                            btn.classList.add(
                                'bg-white',
                                'text-gray-700',
                                'border-gray-300'
                            );
                        });

                        this.classList.remove(
                            'bg-white',
                            'text-gray-700',
                            'border-gray-300'
                        );

                        this.classList.add(
                            'bg-[#61359C]',
                            'text-white',
                            'border-[#61359C]'
                        );

                        document.querySelectorAll('.kk-content').forEach(content => {
                            content.classList.add('hidden');
                        });

                        document
                            .querySelector(`[data-kk-content="${kkIndex}"]`)
                            .classList.remove('hidden');
                    });
                });

                document.querySelectorAll('.toggle-anggota').forEach(btn => {
                    btn.addEventListener('click', function() {

                        const wrapper = this.closest('.border.border-gray-200');

                        const content = wrapper.querySelector('.anggota-content');

                        content.classList.toggle('hidden');

                        const icon = this.querySelector('i');

                        icon.classList.toggle('fa-chevron-down');
                        icon.classList.toggle('fa-chevron-up');
                    });
                });

                document.querySelectorAll('.btn-delete-anggota').forEach(btn => {
                    btn.addEventListener('click', function () {

                        const wrapper = this.closest('.border.border-gray-200');

                        showDeleteConfirmToast(
                            'Yakin ingin menghapus skrining NIK anggota ini?',
                            () => {
                                wrapper.remove();
                            }
                        );
                    });
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

                formEdit.querySelector('[name="unit[alamat]"]').value = unit.alamat ?? ''; formEdit.querySelector('[name="unit[rt]"]').value = unit.rt ?? ''; formEdit.querySelector('[name="unit[rw]"]').value = unit.rw ?? '';

                document.getElementById('kelurahan_id').value = unit.kelurahan_id ?? ''; setDropdownLabel('kelurahanDropdown', unit.kelurahan || 'Pilih Kelurahan', 'Pilih Kelurahan');

                document.getElementById('posyandu_id').value = unit.posyandu_id ?? ''; setDropdownLabel('posyanduDropdown', unit.posyandu || 'Pilih Posyandu', 'Pilih Posyandu'); setDropdownDisabled('posyanduDropdown', !unit.posyandu_id);
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

                document.addEventListener('click', function(e) {
                    const toggle = e.target.closest('.edit-dropdown-toggle');
                    if (toggle) {
                        const wrapper = toggle.closest('.edit-custom-dropdown');

                        document.querySelectorAll('.edit-dropdown-menu').forEach(menu => {
                            if (!wrapper.contains(menu)) {
                                menu.classList.add('hidden');
                            }
                        });

                        wrapper.querySelector('.edit-dropdown-menu')
                            ?.classList.toggle('hidden');

                        return;
                    }

                    const item = e.target.closest('.edit-dropdown-item');

                    if (item) {
                        const wrapper = item.closest('.edit-custom-dropdown');

                        const value = item.dataset.value;
                        const label = item.dataset.label;

                        wrapper.querySelector('.edit-dropdown-label')
                            .textContent = value;

                        wrapper.querySelector('.edit-dropdown-value')
                            .value = value;

                        wrapper.querySelector('.edit-dropdown-other')
                            ?.classList.add('hidden');

                        wrapper.querySelector('.edit-dropdown-menu')
                            .classList.add('hidden');

                        return;
                    }

                    const otherBtn = e.target.closest('.edit-dropdown-other-btn');
                    if (otherBtn) {
                        const wrapper = otherBtn.closest('.edit-custom-dropdown');

                        wrapper.querySelector('.edit-dropdown-other')
                            ?.classList.remove('hidden');

                        wrapper.querySelector('.edit-dropdown-other-input')
                            ?.focus();

                        return;
                    }

                    document.querySelectorAll('.edit-dropdown-menu')
                        .forEach(menu => {
                            menu.classList.add('hidden');
                        });
                });

                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('edit-dropdown-other-input')) {

                        const wrapper = e.target.closest('.edit-custom-dropdown');

                        const value = e.target.value;

                        wrapper.querySelector('.edit-dropdown-label')
                            .textContent = value || 'Lainnya';

                        wrapper.querySelector('.edit-dropdown-value')
                            .value = value;
                    }
                });

                document.addEventListener('change', function (e) {
                    if (!e.target.classList.contains('luar-wilayah-toggle')) return;

                    const section = e.target
                        .closest('.kk-item')
                        ?.querySelector('.luar-wilayah-section');

                    if (!section) return;

                    if (!e.target.checked) {
                        section.classList.add('hidden');

                        section.querySelectorAll('input').forEach(input => {
                            input.value = '';
                        });
                    } else {
                        section.classList.remove('hidden');
                    }
                });
                
                loadKader();
                loadDetail();
            });
</script>