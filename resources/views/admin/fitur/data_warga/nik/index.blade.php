@extends('layouts.main')

@section('title', 'Data Anggota Keluarga (NIK)')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-10 text-center sm:text-left">Data Anggota Keluarga (NIK)</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <input id="searchInput" type="text"
                placeholder="Cari berdasarkan No KK, NIK, atau Nama"
                class="h-9 bg-white border border-[#00000033] rounded-lg px-3 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 w-full sm:w-73">

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
            <input type="hidden" id="kelurahan_id">
            <input type="hidden" id="posyandu_id">

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
                Import
            </button>

            <button
                class="h-9 flex items-center gap-2 bg-[#61359C] text-white
                   text-sm px-4 rounded-lg hover:bg-[#61359C]/80
                   transition w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i>
                Tambah
            </button>
        </div>
    </div>

    <div class="overflow-x-auto w-full">
        <table class="min-w-[650px] border border-[#00000033] text-xs sm:text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] sm:w-[5%]">No</th>
                    <th class="px-3 py-2 border border-[#00000033] sm:w-[20%]">Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033] sm:w-[20%]">Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033] sm:w-[15%] whitespace-nowrap">NIK</th>
                    <th class="px-3 py-2 border border-[#00000033] sm:w-[25%] whitespace-nowrap">Nama</th>
                    <th class="px-3 py-2 border border-[#00000033] sm:w-[15%]">Aksi</th>
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

            <div class="w-full mb-2">
                <h3 class="text-lg font-bold text-left">Detail Anggota Keluarga</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-2 text-sm">
                <div class="space-y-1">

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Kelurahan</p>
                        <p>:</p>
                        <p id="modal-kelurahan"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Posyandu</p>
                        <p>:</p>
                        <p id="modal-posyandu"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">No KK</p>
                        <p>:</p>
                        <p id="modal-no-kk"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">NIK</p>
                        <p>:</p>
                        <p id="modal-nik"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Nama Lengkap</p>
                        <p>:</p>
                        <p id="modal-nama"></p>
                    </div>

                </div>

                <div class="space-y-1">
                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Tempat Lahir</p>
                        <p>:</p>
                        <p id="modal-tempat-lahir"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Tanggal Lahir</p>
                        <p>:</p>
                        <p id="modal-tanggal-lahir"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Jenis Kelamin</p>
                        <p>:</p>
                        <p id="modal-jenis-kelamin"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Hubungan Keluarga</p>
                        <p>:</p>
                        <p id="modal-hubungan"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Status Perkawinan</p>
                        <p>:</p>
                        <p id="modal-status-perkawinan"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Pendidikan Terakhir</p>
                        <p>:</p>
                        <p id="modal-pendidikan"></p>
                    </div>

                    <div class="grid grid-cols-[140px_10px_1fr] items-start">
                        <p class="font-medium">Pekerjaan</p>
                        <p>:</p>
                        <p id="modal-pekerjaan"></p>
                    </div>
                </div>
            </div>

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

<x-modal id="keluargaModalRef" size="lg">
    <x-slot name="title">
        <h3 id="keluargaModalTitle" class="text-lg font-bold">Tambah Data Anggota Keluarga</h3>
    </x-slot>

    @include('admin.fitur.data_warga.nik.form')

    <x-slot name="footer">
        <button type="button" id="keluargaCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="keluargaSaveBtn" form="formEdit"
            class="w-full px-6 py-2 rounded-lg bg-[#61359C] text-white hover:bg-[#61359C]/80 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<x-modal id="importKeluargaModal" size="md">
    <x-slot name="title">
        <h3 class="text-lg font-bold">Import Data Anggota Keluarga</h3>
    </x-slot>

    @include('admin.fitur.data_warga.nik.import')
</x-modal>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("keluargaTableBody");
        const modal = document.getElementById("modal");

        const modalKelurahan = document.getElementById("modal-kelurahan");
        const modalPosyandu = document.getElementById("modal-posyandu");
        const modalNoKK = document.getElementById("modal-no-kk");
        const modalNIK = document.getElementById("modal-nik");
        const modalNama = document.getElementById("modal-nama");
        const modalTempatLahir = document.getElementById("modal-tempat-lahir");
        const modalTanggalLahir = document.getElementById("modal-tanggal-lahir");
        const modalJenisKelamin = document.getElementById("modal-jenis-kelamin");
        const modalHubungan = document.getElementById("modal-hubungan");
        const modalStatusKawin = document.getElementById("modal-status-perkawinan");
        const modalPendidikan = document.getElementById("modal-pendidikan");
        const modalPekerjaan = document.getElementById("modal-pekerjaan");

        const keluargaModalRef = document.getElementById("keluargaModalRef");
        const keluargaModalTitle = document.getElementById("keluargaModalTitle");
        const formEdit = document.getElementById("formEdit");

        const searchInput = document.getElementById("searchInput");
        const searchBtn = document.getElementById("searchBtn");
        const filterDropdown = document.querySelector('[data-dropdown="filter"]');

        let list = [];

        async function fetchDataWarga() {
            const keyword = document.getElementById("searchInput").value.trim();
            const kelurahanId = document.getElementById("kelurahan_id").value;
            const posyanduId = document.getElementById("posyandu_id").value;

            const params = new URLSearchParams();

            if (keyword) params.append("keyword", keyword);
            if (kelurahanId) params.append("kelurahan_id", kelurahanId);
            if (posyanduId) params.append("posyandu_id", posyanduId);

            try {
                const response = await fetch(`{{ url('api/identitas_anggota') }}?${params.toString()}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();

                renderTable(result.data?.list || []);
            } catch (err) {
                showErrorToast.error("Gagal memuat data:", err);
                renderTable([]);
            }
        }

        function renderTable(data) {
            list = data;
            tbody.innerHTML = "";
            if (!list.length) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-4">
                        Tidak ada data anggota keluarga.
                    </td>
                </tr>`;
                return;
            }

            list.forEach((item, index) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">${index + 1}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama_kelurahan ?? '-'}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama_posyandu ?? '-'}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${item.nik ?? '-'}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama ?? '-'}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        <div class="flex justify-center gap-2">
                            <button
                                class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail"
                                data-id="${item.id}">
                                Detail
                            </button>

                            <button
                                onclick="openAnggotaModal('edit', '${item.id}')"
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
                        await fetch(`{{ url('api/identitas_anggota') }}/${id}`, {
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

            if (mode === "edit" && id) {
                formData.append("id", id);
            }

            try {
                let url = "{{ url('api/identitas_anggota') }}";
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
                    keluargaModalRef.classList.add("hidden");
                    keluargaModalRef.classList.remove("flex");
                    fetchDataWarga();
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const fieldName = key.split('.').pop();
                            const el = document.querySelector(`p[data-key="${fieldName}"]`);
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
                showErrorToast("Terjadi kesalahan pada server!");
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
            modalNoKK.textContent = item.no_kk ?? "-";
            modalNIK.textContent = item.nik ?? "-";
            modalNama.textContent = item.nama ?? "-";

            modalTempatLahir.textContent = item.tempat_lahir ?? "-";
            modalTanggalLahir.textContent = item.tanggal_lahir ?? "-";
            modalJenisKelamin.textContent = item.jenis_kelamin === "L" ? "Laki-Laki" : "Perempuan";
            modalHubungan.textContent = item.hubungan_keluarga ?? "-";
            modalStatusKawin.textContent = item.status_perkawinan ?? "-";
            modalPendidikan.textContent = item.pendidikan_terakhir ?? "-";
            modalPekerjaan.textContent = item.pekerjaan ?? "-";

            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.style.overflow = "hidden";
        });

        const openAnggotaModal = async (mode, id = null) => {
            keluargaModalRef.classList.remove("hidden");
            keluargaModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                keluargaModalTitle.textContent = "Edit Anggota Keluarga";
                try {
                    const data = await fetch(`{{ url('api/identitas_anggota') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEdit.setAttribute('data-mode', 'edit');
                    formEdit.setAttribute('data-id', id);
                } catch (err) {
                    showErrorToast.error("Gagal mengambil data anggota keluarga:", err);
                }
            } else {
                keluargaModalTitle.textContent = "Tambah Anggota Keluarga";
                setFormData(null);
                formEdit.removeAttribute('data-id');
                formEdit.setAttribute('data-mode', 'add');
            }
        };

        window.openAnggotaModal = openAnggotaModal;

        document.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes("Tambah")) {
                btn.addEventListener("click", () => openAnggotaModal("add"));
            }
        });

        document.addEventListener("click", (e) => {
            if (e.target && e.target.textContent === "Edit") {
                const card = e.target.closest(".flex.flex-col");
                const itemName = card.querySelector("h3").textContent;
                const qty = card.querySelector("p.text-2xl").textContent;
                const img = card.querySelector("img").src;

                const id = e.target.dataset.id || null;

                openAnggotaModal("edit", id);
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
                    document.getElementById('posyandu_id').value = "";

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

        document.getElementById("searchBtn").addEventListener("click", () => {
            fetchDataWarga();
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

        window.fetchDataWarga = fetchDataWarga;
        fetchDataWarga();
        loadKelurahan();
        setDropdownDisabled('posyanduFilterDropdown', true);
    });
</script>
@endsection