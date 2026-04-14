@extends('layouts.main')

@section('title', 'Data Warga (KK)')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Data Warga (KK)</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <input id="searchInput" type="text"
                placeholder="Cari berdasarkan No KK atau kepala keluarga"
                class="h-9 bg-white border border-[#00000033] rounded-lg px-3 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 w-full sm:w-70">

            <x-dropdown
                id="kelurahanFilterDropdown"
                label="Pilih Kelurahan"
                :options="[]"
                width="w-full sm:w-40 h-9"
                data-dropdown="filter" />

            <x-dropdown
                id="posyanduFilterDropdown"
                label="Pilih Posyandu"
                :options="[]"
                width="w-full sm:w-40 h-9"
                data-dropdown="filter" />

            <button id="searchBtn"
                class="h-9 flex items-center justify-center bg-[#61359C] text-white
                   border border-[#00000033] px-3 rounded-lg text-sm
                   hover:bg-[#61359C]/80 transition w-full sm:w-auto">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
            <button id="btnImport"
                class="h-9 flex items-center gap-2 bg-[#61359C] text-white
                    text-sm px-4 rounded-lg hover:bg-[#61359C]/80
                    transition w-full sm:w-auto justify-center">
                <i class="fa-solid fa-file-excel"></i>
                Import Excel
            </button>

            <button id="btnTambahKeluarga"
                class="h-9 flex items-center gap-2 bg-[#61359C] text-white
                   text-sm px-4 rounded-lg hover:bg-[#61359C]/80
                   transition w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i>
                Tambah
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">Total KK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">Luar wilayah</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[25%]">Kepala Keluarga</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Aksi</th>
                </tr>
            </thead>
            <tbody id="keluargaTableBody"></tbody>
        </table>
    </div>

    <div id="modal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50 px-4">
        <div class="bg-white rounded-xl shadow-lg 
            w-full sm:w-11/12 md:w-10/12 lg:w-7/12 xl:w-6/12 
            max-w-5xl max-h-[90vh] overflow-y-auto
            flex flex-col relative p-6">

            <div class="w-full">
                <h3 class="text-lg font-bold text-left">Detail</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-2 text-sm">
                <div class="space-y-1">
                    <div class="grid grid-cols-[100px_10px_1fr] items-start">
                        <p class="font-medium">Kelurahan</p>
                        <p class="font-medium">:</p>
                        <p id="modal-kelurahan"></p>
                    </div>

                    <div class="grid grid-cols-[100px_10px_1fr] items-start">
                        <p class="font-medium">Posyandu</p>
                        <p class="font-medium">:</p>
                        <p id="modal-posyandu"></p>
                    </div>

                    <div class="grid grid-cols-[100px_10px_1fr] items-start">
                        <p class="font-medium">Alamat</p>
                        <p class="font-medium">:</p>
                        <p id="modal-alamat"></p>
                    </div>
                </div>

                <div class="space-y-1">
                    <div class="grid grid-cols-[100px_10px_1fr] items-start">
                        <p class="font-medium">RT / RW</p>
                        <p class="font-medium">:</p>
                        <p id="modal-rt-rw"></p>
                    </div>

                    <div class="grid grid-cols-[100px_10px_1fr] items-start">
                        <p class="font-medium">Total KK</p>
                        <p class="font-medium">:</p>
                        <p id="modal-totalKK"></p>
                    </div>

                    <div class="grid grid-cols-[100px_10px_1fr] items-start">
                        <p class="font-medium">Luar Wilayah</p>
                        <p class="font-medium">:</p>
                        <p id="modal-luarWilayah"></p>
                    </div>
                </div>
            </div>

            <div id="detailKKContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>

            <div class="mt-6">
                <button
                    id="closeModalBtn"
                    class="w-full bg-[#61359C] text-white text-sm font-semibold py-2 rounded-lg hover:bg-[#61359C]/80 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</section>

<x-modal id="keluargaModalRef" size="xl">
    <x-slot name="title">
        <h3 id="keluargaModalTitle" class="text-lg font-bold">Tambah Data Warga</h3>
    </x-slot>

    @include('admin.fitur.data_warga.kk.form')

    <x-slot name="footer">
        <button type="button" id="keluargaCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="keluargaSaveBtn" form="formEdit"
            class="w-full px-6 py-2 rounded-lg bg-[#61359C] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<x-modal id="importKeluargaModal" size="md">
    <x-slot name="title">
        <h3 class="text-lg font-bold">Import Data Warga (KK)</h3>
    </x-slot>

    @include('admin.fitur.data_warga.kk.import')
</x-modal>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("keluargaTableBody");
        const modal = document.getElementById("modal");

        const modalNama = document.getElementById("modal-nama");
        const modalUsername = document.getElementById("modal-username");
        const modalKelurahan = document.getElementById("modal-kelurahan");
        const modalPosyandu = document.getElementById("modal-posyandu");
        const modalJenisKelamin = document.getElementById("modal-jenisKelamin");
        const modalTelepon = document.getElementById("modal-telepon");
        const modalAlamat = document.getElementById("modal-alamat");
        const modalRTRW = document.getElementById("modal-rt-rw");
        const modalTotalKK = document.getElementById("modal-totalKK");
        const modalLuarWilayah = document.getElementById("modal-luarWilayah");

        const keluargaModalRef = document.getElementById("keluargaModalRef");
        const keluargaModalTitle = document.getElementById("keluargaModalTitle");
        const formEdit = document.getElementById("formEdit");

        const searchInput = document.getElementById("searchInput");
        const searchBtn = document.getElementById("searchBtn");
        const filterDropdown = document.querySelector('[data-dropdown="filter"]');

        let list = [];

        async function fetchDataWarga() {
            const keyword = searchInput.value.trim();

            const params = new URLSearchParams();
            if (keyword) params.append("keyword", keyword);

            try {
                const response = await fetch(`{{ url('api/identitas_keluarga') }}?${params.toString()}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();
                if (!result || !result.data) return renderTable([]);
                renderTable(result.data.list || []);
            } catch (err) {
                console.error("Gagal memuat data:", err);
                renderTable([]);
            }
        }

        function renderTable(data) {
            list = data;
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-gray-500 py-4">
                        Tidak ada data keluarga.
                    </td>
                </tr>`;
                return;
            }

            list.forEach((item, index) => {
                const jumlahKK = item.keluarga?.length ?? 0;
                const jumlahLuarWilayah = item.keluarga?.filter(k => k.is_luar_wilayah)?.length ?? 0;
                const kepalaList = item.keluarga
                    .map(k => k.kepala_keluarga?.nama ?? "-")
                    .join(", ");

                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">${index + 1}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama_kelurahan}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama_posyandu}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${jumlahKK}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${jumlahLuarWilayah}</td>
                    <td class="border border-[#00000033] px-3 py-3">${kepalaList}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        <div class="flex justify-center gap-2">
                            <button
                                class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail"
                                data-id="${item.id}">
                                Detail
                            </button>

                            <button
                                onclick="openKeluargaModal('edit', '${item.id}')"
                                class="px-3 py-1 text-xs rounded bg-yellow-500 text-white hover:bg-yellow-600 transition">
                                Edit
                            </button>

                            <button
                                class="px-3 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 transition delete-btn"
                                data-id="${item.id}">
                                Hapus
                            </button>
                        </div>
                    </td>
                `;

                tbody.appendChild(tr);
            });

            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", async () => {
                    const id = btn.dataset.id;

                    showDeleteConfirmToast("Apakah Anda yakin ingin menghapus data ini?", async () => {
                        await fetch(`{{ url('api/identitas_keluarga') }}/${id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        });

                        showSuccessToast("Data berhasil dihapus!");
                        fetchDataWarga();
                    });
                });
            });
        }

        formEdit.addEventListener("submit", async (e) => {
            e.preventDefault();

            document.querySelectorAll('[id^="error-"]').forEach(el => {
                el.textContent = "";
                el.classList.add("hidden");
            });

            const mode = formEdit.getAttribute("data-mode");
            const id = formEdit.getAttribute("data-id");

            const formData = new FormData(formEdit);

            const kkItems = document.querySelectorAll('.kk-item');
            const keluargaArray = Array.from(kkItems).map(item => {
                const luarWilayahCheckbox = item.querySelector('input[name="is_luar_wilayah"]');
                return {
                    id: item.querySelector('[name="id"]')?.value || null,
                    no_kk: item.querySelector('[name="no_kk"]').value,
                    nik_kepala_keluarga: item.querySelector('[name="nik_kepala_keluarga"]').value,
                    nama_kepala_keluarga: item.querySelector('[name="nama_kepala_keluarga"]').value,
                    no_telepon: item.querySelector('[name="no_telepon"]').value,
                    is_luar_wilayah: luarWilayahCheckbox ? luarWilayahCheckbox.checked : false,
                    alamat_ktp: item.querySelector('[name="alamat_ktp"]')?.value || "",
                    rt_ktp: item.querySelector('[name="rt_ktp"]')?.value || "",
                    rw_ktp: item.querySelector('[name="rw_ktp"]')?.value || ""
                };
            });

            formData.set('keluarga', JSON.stringify(keluargaArray));

            if (mode === "edit" && id) {
                formData.append("id", id);
                formData.append("_method", "PUT");
            }

            try {
                const res = await fetch("{{ url('api/identitas_keluarga') }}", {
                    method: "POST",
                    body: formData
                });
                const data = await res.json();

                if (!data.errors) {
                    showSuccessToast("Data berhasil disimpan!");
                    keluargaModalRef.classList.add("hidden");
                    keluargaModalRef.classList.remove("flex");
                    fetchDataWarga();
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            if (key.startsWith('keluarga.')) {
                                const parts = key.split('.');
                                const index = parts[1];
                                const fieldName = parts[2];

                                const kkItems = document.querySelectorAll('.kk-item');
                                const targetKK = kkItems[index];

                                if (targetKK) {
                                    const el = targetKK.querySelector(`p[data-key="${fieldName}"]`);
                                    if (el) {
                                        el.textContent = data.errors[key][0];
                                        el.classList.remove("hidden");
                                    }
                                }
                            }
                            else {
                                const el = document.querySelector(`p[data-key="${key}"]`);
                                if (el) {
                                    el.textContent = data.errors[key][0];
                                    el.classList.remove("hidden");
                                }
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

        function capitalizeFirstLetter(text) {
            if (!text || text === "-") return "-";
            return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
        }

        tbody.addEventListener("click", (e) => {
            if (!e.target.classList.contains("open-detail")) return;

            const id = e.target.dataset.id;
            const item = list.find(f => f.id == id);
            if (!item) return;

            modalKelurahan.textContent = item.nama_kelurahan ?? "-";
            modalPosyandu.textContent = item.nama_posyandu ?? "-";
            modalAlamat.textContent = item.alamat ?? "-";
            modalRTRW.textContent = `${item.rt ?? "-"} / ${item.rw ?? "-"}`;

            modalTotalKK.textContent = item.keluarga?.length ?? 0;
            modalLuarWilayah.textContent = item.keluarga?.filter(k => k.is_luar_wilayah)?.length ?? 0;

            renderDetailKK(item.keluarga || []);

            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.style.overflow = "hidden";
        });

        function renderDetailKK(keluargaList) {
            const container = document.getElementById("detailKKContainer");
            container.innerHTML = "";
            container.className = "grid grid-cols-1 gap-2";

            keluargaList.forEach(k => {
                const card = document.createElement("div");
                card.className = "relative border border-gray-200 rounded bg-gray-50 shadow-sm mt-3 p-4";

                card.innerHTML = `
                    ${k.is_luar_wilayah ? `
                    <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-0.5 rounded-full 
                                static block mt-1 mb-3 sm:absolute sm:top-2 sm:right-2 sm:mt-0">
                        Luar Wilayah
                    </span>` : ""}

                    <div class="space-y-2 text-sm">
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <p class="font-medium">No KK</p>
                            <p>:</p>
                            <p>${k.no_kk}</p>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <p class="font-medium">Kepala Keluarga</p>
                            <p>:</p>
                            <p>${k.kepala_keluarga?.nama ?? "-"}</p>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <p class="font-medium">NIK Kepala Keluarga</p>
                            <p>:</p>
                            <p>${k.kepala_keluarga?.nik ?? "-"}</p>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <p class="font-medium">No Telepon</p>
                            <p>:</p>
                            <p>${k.no_telepon ?? "-"}</p>
                        </div>

                        ${k.is_luar_wilayah ? `
                        <hr class="text-gray-300">

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <p class="font-medium">Alamat KTP</p>
                            <p>:</p>
                            <p>${k.alamat_ktp ?? "-"}</p>
                        </div>

                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <p class="font-medium">RT/RW KTP</p>
                            <p>:</p>
                            <p>${k.rt_ktp ?? "-"} / ${k.rw_ktp ?? "-"}</p>
                        </div>` : ""}
                    </div>
                `;

                container.appendChild(card);
            });
        }

        const openKeluargaModal = async (mode, id = null) => {
            keluargaModalRef.classList.remove("hidden");
            keluargaModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                keluargaModalTitle.textContent = "Edit Data Keluarga";
                try {
                    const data = await fetch(`{{ url('api/identitas_keluarga') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEdit.setAttribute('data-mode', 'edit');
                    formEdit.setAttribute('data-id', id);
                } catch (err) {
                    console.error("Gagal mengambil data keluarga:", err);
                }
            } else {
                keluargaModalTitle.textContent = "Tambah Data Keluarga";
                setFormData(null);
                formEdit.removeAttribute('data-id');
                formEdit.setAttribute('data-mode', 'add');
            }
        };

        window.openKeluargaModal = openKeluargaModal;

        document.getElementById("btnTambahKeluarga").addEventListener("click", () => {
    openKeluargaModal("add");
});

        document.addEventListener("click", (e) => {
            if (e.target && e.target.textContent === "Edit") {
                const card = e.target.closest(".flex.flex-col");
                const itemName = card.querySelector("h3").textContent;
                const qty = card.querySelector("p.text-2xl").textContent;
                const img = card.querySelector("img").src;

                const id = e.target.dataset.id || null;

                openKeluargaModal("edit", id);
            }
        });

        document.getElementById("closeModalBtn").addEventListener("click", () => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow = "";
        });

        document.getElementById("keluargaCancelBtn").addEventListener("click", () => {
            keluargaModalRef.classList.add("hidden");
            keluargaModalRef.classList.remove("flex");
        });

        searchBtn.addEventListener("click", fetchDataWarga);
        searchInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") fetchDataWarga();
        });

        if (filterDropdown) {
            const selectedSpan = filterDropdown.querySelector('.dropdown-selected');
            if (selectedSpan && !selectedSpan.textContent.trim()) {
                selectedSpan.textContent = "Aktif";
            }
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

        async function fetchKkWithFilter() {
            const keyword = document.getElementById("searchInput").value.trim();
            const kelurahanId = document.getElementById("kelurahan_id").value;
            const posyanduId = document.getElementById("posyandu_id").value;

            const params = new URLSearchParams();

            if (keyword) params.append("keyword", keyword);
            if (kelurahanId) params.append("kelurahan_id", kelurahanId);
            if (posyanduId) params.append("posyandu_id", posyanduId);

            try {
                const response = await fetch(`{{ url('api/identitas_keluarga') }}?${params.toString()}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();
                renderTable(result.data?.list || []);
            } catch (err) {
                console.error("Gagal memuat data:", err);
                renderTable([]);
            }
        }

        document.getElementById("searchBtn").addEventListener("click", () => {
            fetchKkWithFilter();
        });

        const importModal = document.getElementById("importKeluargaModal");
        const btnImport = document.getElementById("btnImport");
        const importCancelBtn = document.getElementById("importCancelBtn");

        btnImport.addEventListener("click", () => {
            importModal.classList.remove("hidden");
            importModal.classList.add("flex");
            document.body.style.overflow = "hidden";
        });

        importCancelBtn.addEventListener("click", () => {
            importModal.classList.add("hidden");
            importModal.classList.remove("flex");
            document.body.style.overflow = "";
        });

        fetchDataWarga();
        loadKelurahan();
        setDropdownDisabled('posyanduFilterDropdown', true);
    });
</script>
@endsection