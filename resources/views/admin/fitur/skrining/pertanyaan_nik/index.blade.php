@extends('layouts.main')

@section('title', 'Pertanyaan Skrining NIK')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Pertanyaan Skrining NIK</h2>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <x-dropdown
                id="kategoriFilterDropdown"
                label="Pilih Siklus"
                :options="[]"
                width="sm:w-64 h-9"
                data-dropdown="filter" />
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                <button
                    id="btnTambahPertanyaan"
                    class="flex items-center gap-2 bg-[#61359C] text-white text-sm px-4 py-2 rounded-lg
                        hover:bg-[#61359C]/80 transition w-full sm:w-auto justify-center">
                    <i class="fa-solid fa-plus"></i>
                    Tambah
                </button>

                <button
                    id="btnToggleEditMode"
                    class="hidden flex items-center gap-2 bg-yellow-500 text-white text-sm px-4 py-2 rounded-lg
                        hover:bg-yellow-600 transition w-full sm:w-auto justify-center">
                    <i class="fa-solid fa-pen"></i>
                    <span>Edit</span>
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[95%]">Pertanyaan</th>
                    <th id="aksiHeader"
                        class="px-3 py-2 border border-[#00000033] w-[15%] hidden">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody id="pertanyaanTableBody"></tbody>
        </table>
    </div>
</section>

<x-modal id="pertanyaanModalRef" size="md">
    <x-slot name="title">
        <h3 id="pertanyaanModalTitle" class="text-lg font-bold">Tambah Pertanyaan Skrining NIK</h3>
    </x-slot>

    @include('admin.fitur.skrining.pertanyaan_nik.form')

    <x-slot name="footer">
        <button type="button" id="pertanyaanCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="pertanyaanSaveBtn" form="formEditPertanyaan"
            class="w-full px-6 py-2 rounded-lg bg-[#0B6CF4] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<x-modal id="sectionModalRef" size="md">
    <x-slot name="title">
        <h3 id="sectionModalTitle" class="text-lg font-bold">Tambah Section</h3>
    </x-slot>

    @include('admin.fitur.skrining.pertanyaan_nik.form_section')

    <x-slot name="footer">
        <button type="button" id="sectionCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="sectionSaveBtn" form="formEditSection"
            class="w-full px-6 py-2 rounded-lg bg-[#0B6CF4] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("pertanyaanTableBody");

        const pertanyaanModalRef = document.getElementById("pertanyaanModalRef");
        const pertanyaanModalTitle = document.getElementById("pertanyaanModalTitle");
        const formEditPertanyaan = document.getElementById("formEditPertanyaan");

        const sectionModalRef = document.getElementById("sectionModalRef");
        const sectionModalTitle = document.getElementById("sectionModalTitle");
        const formEditSection = document.getElementById("formEditSection");

        let editMode = false;
        const btnToggleEditMode = document.getElementById("btnToggleEditMode");
        const aksiHeader = document.getElementById("aksiHeader");
        const btnTambahPertanyaan = document.getElementById("btnTambahPertanyaan");

        let selectedKategoriId = null;
        let kategoriList = [];

        function renderDefaultMessage() {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 italic">
                        Pilih siklus terlebih dahulu
                    </td>
                </tr>
            `;
        }

        renderDefaultMessage();

        async function loadKategoriFilter() {
            try {
                const res = await fetch(`{{ url('api/kategori') }}`);
                const json = await res.json();

                const list = json.data.list || [];

                kategoriList = list.filter(k =>
                    k.target_skrining?.toLowerCase() === 'nik'
                );

                renderKategoriDropdown();

            } catch (err) {
                console.error("Gagal load kategori:", err);
            }
        }

        function renderKategoriDropdown() {
            const dropdown = document
                .getElementById("kategoriFilterDropdown")
                .querySelector(".dropdown-menu");

            dropdown.innerHTML = "";

            kategoriList.forEach(kat => {

                const btn = document.createElement("button");
                btn.type = "button";
                btn.className = "block w-full text-left px-4 py-2 text-sm hover:bg-gray-100";
                btn.textContent = kat.nama_kategori;

                btn.onclick = async () => {
                    selectedKategoriId = kat.id;

                    setDropdownLabel(
                        "kategoriFilterDropdown",
                        kat.nama_kategori,
                        "Pilih Siklus"
                    );

                    const parent = document.getElementById("kategoriFilterDropdown");
                    const menu = parent.querySelector(".dropdown-menu");
                    menu.classList.add("hidden");

                    await fetchPertanyaan();
                };
                dropdown.appendChild(btn);
            });
        }

        async function fetchPertanyaan() {
            if (!selectedKategoriId) {
                renderDefaultMessage();
                return;
            }

            try {
                const response = await fetch(`{{ url('api/pertanyaan') }}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();
                if (!result || !result.data) return;

                const list = result.data.list || [];

                const filtered = list.filter(item =>
                    item.target_skrining?.toLowerCase() === 'nik' &&
                    item.kategori_id === selectedKategoriId
                );

                const btnEdit = document.getElementById("btnToggleEditMode");

                if (filtered.length > 0) {
                    btnEdit.classList.remove("hidden");
                } else {
                    btnEdit.classList.add("hidden");

                    editMode = false;
                    aksiHeader.classList.add("hidden");

                    btnToggleEditMode.innerHTML = `
                        <i class="fa-solid fa-pen"></i>
                        <span>Edit</span>
                    `;

                    btnToggleEditMode.classList.remove("bg-blue-600", "hover:bg-blue-700");
                    btnToggleEditMode.classList.add("bg-yellow-500", "hover:bg-yellow-600");
                }
                renderTable(filtered);

            } catch (error) {
                console.error("Gagal memuat data:", error);
            }
        }

        async function moveSection(id, direction) {
            const allRows = [...tbody.querySelectorAll("tr")];

            const currentIndex = allRows.findIndex(r =>
                r.classList.contains("section-row") &&
                r.dataset.sectionId === id
            );

            if (currentIndex === -1) return;

            let group = [allRows[currentIndex]];

            let i = currentIndex + 1;
            while (i < allRows.length && allRows[i].dataset.parentSection === id) {
                group.push(allRows[i]);
                i++;
            }

            const sectionRows = allRows.filter(r => r.classList.contains("section-row"));
            const sectionIndex = sectionRows.findIndex(r => r.dataset.sectionId === id);

            const targetIndex = direction === "up" ?
                sectionIndex - 1 :
                sectionIndex + 1;

            if (targetIndex < 0 || targetIndex >= sectionRows.length) return;

            const targetSectionId = sectionRows[targetIndex].dataset.sectionId;

            const targetSectionRow = allRows.find(r =>
                r.classList.contains("section-row") &&
                r.dataset.sectionId === targetSectionId
            );

            group.forEach(row => {
                row.classList.add(direction === "up" ? "move-up" : "move-down");
            });

            if (direction === "up") {
                group.forEach(row => {
                    tbody.insertBefore(row, targetSectionRow);
                });
            } else {
                let targetGroupEnd = targetSectionRow;
                let j = allRows.indexOf(targetSectionRow) + 1;

                while (j < allRows.length && allRows[j].dataset.parentSection === targetSectionId) {
                    targetGroupEnd = allRows[j];
                    j++;
                }

                const fragment = document.createDocumentFragment();

                group.forEach(row => {
                    fragment.appendChild(row);
                });

                tbody.insertBefore(fragment, targetGroupEnd.nextSibling);
            }

            setTimeout(() => {
                group.forEach(row => {
                    row.classList.remove("move-up", "move-down");
                });
            }, 300);

            try {
                const res = await fetch(`/api/section/${id}/move`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        direction
                    })
                });

                const json = await res.json();

                if (!res.ok || !json.status) {
                    showErrorToast(json.message || "Tidak bisa dipindahkan");
                    await fetchPertanyaan();
                    return;
                }

                await fetchPertanyaan();

            } catch (error) {
                showErrorToast("Terjadi kesalahan server");
                fetchPertanyaan();
            }
        }
        window.moveSection = moveSection;

        async function movePertanyaan(id, sectionId, direction) {
            const rows = [...tbody.querySelectorAll("tr")]
                .filter(r => r.dataset.parentSection === sectionId);

            const currentMainRow = rows.find(r => r.dataset.pertanyaanId === id);
            if (!currentMainRow) return;

            const currentIndex = rows.indexOf(currentMainRow);

            const currentGroup = [
                currentMainRow,
                rows[currentIndex + 1]
            ];

            let targetMainRow;

            if (direction === "up") {
                if (currentIndex < 2) return;

                targetMainRow = rows[currentIndex - 2];

                const fragment = document.createDocumentFragment();
                currentGroup.forEach(r => fragment.appendChild(r));

                tbody.insertBefore(fragment, targetMainRow);

            } else {

                if (currentIndex + 2 >= rows.length) return;

                targetMainRow = rows[currentIndex + 2];

                const targetGroupEnd = rows[currentIndex + 3];

                const fragment = document.createDocumentFragment();
                currentGroup.forEach(r => fragment.appendChild(r));

                if (targetGroupEnd) {
                    tbody.insertBefore(fragment, targetGroupEnd.nextSibling);
                } else {
                    tbody.appendChild(fragment);
                }
            }

            currentGroup.forEach(row => {
                row.classList.add(direction === "up" ? "move-up" : "move-down");
            });

            setTimeout(() => {
                currentGroup.forEach(row => {
                    row.classList.remove("move-up", "move-down");
                });
            }, 300);

            try {
                const res = await fetch(`/api/pertanyaan/${id}/move`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        direction
                    })
                });

                const json = await res.json();

                if (!res.ok || !json.status) {
                    showErrorToast(json.message || "Tidak bisa dipindahkan");
                    await fetchPertanyaan();
                    return;
                }

                await fetchPertanyaan();

            } catch (error) {
                showErrorToast("Terjadi kesalahan server");
                fetchPertanyaan();
            }
        }
        window.movePertanyaan = movePertanyaan;

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 italic">
                            Tidak ada pertanyaan untuk siklus ini
                        </td>
                    </tr>
                `;
                return;
            }

            const grouped = list.reduce((acc, item) => {
                if (!acc[item.section_id]) {
                    acc[item.section_id] = {
                        judul_section: item.judul_section,
                        items: []
                    };
                }
                acc[item.section_id].items.push(item);
                return acc;
            }, {});

            const sectionEntries = Object.entries(grouped);

            sectionEntries.forEach(([sectionId, section], index) => {

                const sectionRow = document.createElement("tr");
                sectionRow.classList.add("section-row");
                sectionRow.dataset.sectionId = sectionId;

                sectionRow.innerHTML = `
                    <td class="bg-gray-100 border border-[#00000033] border-r-0"></td>
                    <td class="px-3 py-3 bg-gray-100 font-bold 
                        border border-[#00000033] border-l-0">
                        ${section.judul_section}
                    </td>

                    ${editMode ? `
                        <td class="border border-[#00000033] text-center align-middle px-3 py-3">
                            <div class="flex items-center justify-center gap-4">
                                <div class="flex flex-col gap-2">
                                    <button 
                                        class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 transition 
                                        ${index === 0 ? 'opacity-40 cursor-not-allowed' : ''}"
                                        ${index === 0 ? '' : `onclick="moveSection('${sectionId}','up')"`}>
                                        <i class="fa-solid fa-circle-arrow-up text-xl text-gray-600"></i>
                                    </button>

                                    <button 
                                        class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 transition 
                                        ${index === sectionEntries.length - 1 ? 'opacity-40 cursor-not-allowed' : ''}"
                                        ${index === sectionEntries.length - 1 ? '' : `onclick="moveSection('${sectionId}','down')"`}>
                                        <i class="fa-solid fa-circle-arrow-down text-xl text-gray-600"></i>
                                    </button>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button 
                                        class="px-3 py-1 text-xs rounded bg-yellow-500 text-white hover:bg-yellow-600 transition"
                                        onclick="openSectionModal('edit','${sectionId}')">
                                        Edit            
                                    </button>

                                    <button 
                                        class="px-3 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 transition delete-section-btn"
                                        data-id="${sectionId}">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </td>
                    ` : ''}
                `;

                tbody.appendChild(sectionRow);

                let no = 1;

                section.items
                    .sort((a, b) => a.no_urut - b.no_urut)
                    .forEach((item, index, arr) => {

                        const isFirst = index === 0;
                        const isLast = index === arr.length - 1;

                        const tr1 = document.createElement("tr");
                        tr1.dataset.parentSection = sectionId;
                        tr1.dataset.pertanyaanId = item.id;

                        tr1.innerHTML = `
                            <td rowspan="2"
                                class="border border-[#00000033] text-center align-middle px-3 py-3 font-semibold">
                                ${no++}
                            </td>

                            <td class="border border-[#00000033] px-3 py-3">
                                <div class="font-semibold">
                                    ${item.pertanyaan}
                                    ${
                                        item.is_required
                                        ? `<span class="text-red-500 ml-1">*</span>`
                                        : ''
                                    }
                                </div>
                                ${
                                    item.keterangan 
                                    ? `<div class="text-xs text-gray-500 mt-1 leading-snug whitespace-pre-line">${item.keterangan}</div>`
                                    : ''
                                }
                            </td>

                            ${editMode ? `
                                <td rowspan="2" class="border border-[#00000033] text-center align-middle px-3 py-3">
                                    <div class="flex items-center justify-center gap-4">

                                        <div class="flex flex-col gap-2">
                                            <button
                                                ${isFirst ? "disabled" : ""}
                                                class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 transition
                                                    ${isFirst ? 'opacity-40 cursor-not-allowed hover:bg-transparent' : ''}"
                                                onclick="movePertanyaan('${item.id}','${sectionId}','up')">
                                                <i class="fa-solid fa-circle-arrow-up text-xl text-gray-600"></i>
                                            </button>

                                            <button
                                                ${isLast ? "disabled" : ""}
                                                class="w-6 h-6 flex items-center justify-center hover:bg-gray-100 transition
                                                    ${isLast ? 'opacity-40 cursor-not-allowed hover:bg-transparent' : ''}"
                                                onclick="movePertanyaan('${item.id}','${sectionId}','down')">
                                                <i class="fa-solid fa-circle-arrow-down text-xl text-gray-600"></i>
                                            </button>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button 
                                                class="px-3 py-1 text-xs rounded bg-yellow-500 text-white hover:bg-yellow-600 transition"
                                                onclick="openPertanyaanModal('edit','${item.id}')">
                                                Edit            
                                            </button>

                                            <button 
                                                class="px-3 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 transition delete-pertanyaan-btn"
                                                data-id="${item.id}">
                                                Hapus
                                            </button>
                                        </div>

                                    </div>
                                </td>
                            ` : ''}
                        `;

                        tbody.appendChild(tr1);

                        const tr2 = document.createElement("tr");
                        tr2.dataset.parentSection = sectionId;
                        tr2.innerHTML = `
                            <td class="border border-[#00000033] px-3 py-2 text-sm text-gray-600">
                                ${renderOpsi(item)}
                            </td>
                        `;

                        tbody.appendChild(tr2);
                    });

                document.querySelectorAll(".delete-section-btn").forEach(btn => {
                    btn.addEventListener("click", async (e) => {
                        e.stopPropagation();

                        const id = btn.dataset.id;

                        try {
                            const res = await fetch(`{{ url('api/section') }}/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            });

                            const json = await res.json();

                            if (!res.ok || !json.status) {
                                showErrorToast(
                                    "Gagal Menghapus",
                                    json.errors ? json.errors[0] : json.message || "Terjadi kesalahan"
                                );
                                return;
                            }

                            showSuccessToast("Section berhasil dihapus!");
                            await fetchPertanyaan();

                        } catch (error) {
                            console.error("Gagal menghapus section:", error);
                            showErrorToast("Terjadi kesalahan pada server!");
                        }
                    });
                });

                document.querySelectorAll(".delete-pertanyaan-btn").forEach(btn => {
                    btn.addEventListener("click", async () => {
                        const id = btn.dataset.id;

                        showDeleteConfirmToast("Apakah Anda yakin ingin menghapus pertanyaan ini?", async () => {
                            try {
                                const data = await fetch(`{{ url('api/pertanyaan') }}/${id}`, {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                    }
                                });
                                if (!data) return;

                                showSuccessToast("Data berhasil dihapus!");
                                await fetchPertanyaan();
                            } catch (error) {
                                console.error("Gagal menghapus data:", error);
                                showErrorToast("Terjadi kesalahan pada server!");
                            }
                        });
                    });
                });
            });

            function renderOpsi(item) {
                switch (item.jenis_jawaban) {
                    case 'radio':
                        if (!item.opsi_jawaban?.length && !item.opsi_lain) return '-';

                        let radioOptions = item.opsi_jawaban?.map(opt => `
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-circle-dot text-gray-500"></i>
                                <span>${opt}</span>
                            </div>
                        `).join('') || '';

                        if (item.opsi_lain) {
                            radioOptions += `
                                <div class="flex items-end gap-2">
                                    <i class="fa-regular fa-circle-dot text-gray-500"></i>
                                    <span>Lainnya :</span>
                                    <span class="border-b border-gray-400 w-32 inline-block"></span>
                                </div>
                            `;
                        }
                        return radioOptions;

                    case 'checkbox':
                        if (!item.opsi_jawaban?.length && !item.opsi_lain) return '-';

                        let checkboxOptions = item.opsi_jawaban?.map(opt => `
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-square-check text-gray-500"></i>
                                <span>${opt}</span>
                            </div>
                        `).join('') || '';

                        if (item.opsi_lain) {
                            checkboxOptions += `
                                <div class="flex items-end gap-2">
                                    <i class="fa-regular fa-square-check text-gray-500"></i>
                                    <span>Lainnya :</span>
                                    <span class="border-b border-gray-400 w-32 inline-block"></span>
                                </div>
                            `;
                        }
                        return checkboxOptions;

                    case 'select':
                        let opsi = item.opsi_jawaban?.length ?
                            item.opsi_jawaban.join(', ') :
                            '-';

                        if (item.opsi_lain) {
                            opsi += ', Lainnya';
                        }
                        return `
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-caret-down text-gray-500"></i>
                                <span>Dropdown:</span>
                            </div>
                            <div class="ml-6">
                                ${opsi}
                            </div>
                        `;

                    case 'text':
                        return `
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fa-solid fa-i-cursor"></i>
                                Jawaban pendek
                            </div>
                        `;

                    case 'textarea':
                        return `
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fa-solid fa-align-left"></i>
                                Jawaban Panjang
                            </div>
                        `;

                    case 'date':
                        return `
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fa-solid fa-calendar"></i>
                                Date
                            </div>
                        `;

                    default:
                        return '-';
                }
            }
        }

        formEditPertanyaan.addEventListener("submit", async (e) => {
            e.preventDefault();

            ['pertanyaan', 'jenis_jawaban', 'opsi_jawaban'].forEach(name => {
                const el = document.getElementById("error-" + name);
                if (el) {
                    el.textContent = "";
                    el.classList.add("hidden");
                }
            });

            const mode = formEditPertanyaan.getAttribute("data-mode");
            const id = formEditPertanyaan.getAttribute("data-id");
            const formData = new FormData(formEditPertanyaan);

            if (mode === "edit" && id) {
                formData.append("id", id);
            }

            try {
                let url = "{{ url('api/pertanyaan') }}";
                let method = "POST";

                if (mode === "edit" && id) {
                    formData.append("_method", "PUT");
                }

                const res = await fetch(url, {
                    method: method,
                    body: formData
                });
                const data = await res.json();

                if (!data.errors) {
                    showSuccessToast("Data berhasil disimpan!");
                    pertanyaanModalRef.classList.add("hidden");
                    pertanyaanModalRef.classList.remove("flex");
                    fetchPertanyaan();
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const el = document.getElementById("error-" + key);
                            if (el) {
                                el.textContent = data.errors[key][0];
                                el.classList.remove("hidden");
                            }
                        });
                    } else {
                        showErrorToast("Gagal menyimpan data!");
                    }
                }
            } catch (err) {
                console.error("Error:", err);
                alert("Terjadi kesalahan pada server!");
            }
        });

        const openPertanyaanModal = async (mode, id = null) => {
            pertanyaanModalRef.classList.remove("hidden");
            pertanyaanModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                pertanyaanModalTitle.textContent = "Edit Pertanyaan Skrining NIK";
                try {
                    const data = await fetch(`{{ url('api/pertanyaan') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEditPertanyaan.setAttribute('data-mode', 'edit');
                    formEditPertanyaan.setAttribute('data-id', id);
                } catch (err) {
                    console.error("Gagal mengambil data pertanyaan:", err);
                }
            } else {
                pertanyaanModalTitle.textContent = "Tambah Pertanyaan Skrining NIK";
                setFormData(null);
                formEditPertanyaan.removeAttribute('data-id');
                formEditPertanyaan.setAttribute('data-mode', 'add');
            }
        };

        btnToggleEditMode.addEventListener("click", () => {
            const wasEditMode = editMode;
            editMode = !editMode;

            if (editMode) {

                btnTambahPertanyaan.classList.add("invisible");

                btnToggleEditMode.innerHTML = `
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan</span>
                `;

                btnToggleEditMode.classList.remove(
                    "bg-yellow-500",
                    "hover:bg-yellow-600"
                );

                btnToggleEditMode.classList.add(
                    "bg-blue-600",
                    "hover:bg-blue-700"
                );

                aksiHeader.classList.remove("hidden");

            } else {

                showSuccessToast("Perubahan berhasil disimpan!");

                btnTambahPertanyaan.classList.remove("invisible");

                btnToggleEditMode.innerHTML = `
                    <i class="fa-solid fa-pen"></i>
                    <span>Edit</span>
                `;

                btnToggleEditMode.classList.remove(
                    "bg-blue-600",
                    "hover:bg-blue-700"
                );

                btnToggleEditMode.classList.add(
                    "bg-yellow-500",
                    "hover:bg-yellow-600"
                );

                aksiHeader.classList.add("hidden");
            }

            fetchPertanyaan();
        });

        window.openPertanyaanModal = openPertanyaanModal;

        document.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes("Tambah")) {
                btn.addEventListener("click", () => openPertanyaanModal("add"));
            }
        });

        document.getElementById("pertanyaanCancelBtn").addEventListener("click", () => {
            pertanyaanModalRef.classList.add("hidden");
            pertanyaanModalRef.classList.remove("flex");
        });

        formEditSection.addEventListener("submit", async (e) => {
            e.preventDefault();

            const mode = formEditSection.getAttribute("data-mode");
            const id = formEditSection.getAttribute("data-id");

            const formData = new FormData(formEditSection);

            if (mode === "edit" && id) {
                formData.append("id", id);
            }

            try {
                let url = "{{ url('api/section') }}";
                let method = "POST";

                if (mode === "edit" && id) {
                    formData.append("_method", "PUT");
                }

                const res = await fetch(url, {
                    method: method,
                    body: formData
                });
                const data = await res.json();

                if (!data.errors) {
                    showSuccessToast("Section berhasil diperbarui!");
                    sectionModalRef.classList.add("hidden");
                    sectionModalRef.classList.remove("flex");
                    fetchPertanyaan();
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const el = document.getElementById("error-" + key);
                            if (el) {
                                el.textContent = data.errors[key][0];
                                el.classList.remove("hidden");
                            }
                        });
                    } else {
                        showErrorToast("Gagal menyimpan data!");
                    }
                }
            } catch (err) {
                console.error("Error:", err);
                alert("Terjadi kesalahan pada server!");
            }
        });

        const openSectionModal = async (mode, id = null) => {
            sectionModalRef.classList.remove("hidden");
            sectionModalRef.classList.add("flex");

            if (mode === "edit" && id) {

                sectionModalTitle.textContent = "Edit Section";

                try {
                    const res = await fetch(`{{ url('api/section') }}/${id}`);
                    const json = await res.json();

                    const item = json.data;

                    setFormSectionData(item);

                    formEditSection.setAttribute('data-mode', 'edit');
                    formEditSection.setAttribute('data-id', id);

                } catch (err) {
                    console.error("Gagal mengambil data section:", err);
                }
            }
        };

        window.openSectionModal = openSectionModal;

        document.getElementById("sectionCancelBtn").addEventListener("click", () => {
            sectionModalRef.classList.add("hidden");
            sectionModalRef.classList.remove("flex");
        });

        loadKategoriFilter();
    });
</script>

<style>
    .section-row {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    .move-up {
        transform: translateY(-15px);
    }

    .move-down {
        transform: translateY(15px);
    }

    .fade {
        opacity: 0.5;
    }

    tbody tr {
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
</style>

@endsection