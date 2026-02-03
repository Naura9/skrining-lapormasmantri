@extends('layouts.main')

@section('title', 'Data Wilayah')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Data Wilayah</h2>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-3">
        <div class="flex flex-col sm:flex-row items-stretch gap-4 mb-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-56">
                <x-dropdown
                    label="Urutkan Jumlah Posyandu"
                    :options="['Terbanyak → Terkecil', 'Terkecil → Terbanyak']"
                    width="w-full sm:w-56"
                    data-dropdown="filter" />
            </div>
        </div>

        <div class="flex justify-end w-full sm:w-auto">
            <button
                id="btnTambahWilayah"
                class="flex items-center gap-2 bg-[#61359C] text-white text-sm px-4 py-2 rounded-lg
                   hover:bg-[#61359C]/80 transition w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i>
                Tambah
            </button>
        </div>

    </div>


    <div class="overflow-x-auto px-25">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%] text-center">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[45%]">Nama Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[25%] text-center">Jumlah Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[25%] text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="kelurahanTableBody"></tbody>
        </table>
    </div>

    <div id="modal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-11/12 sm:w-8/12 md:w-6/12 lg:w-4/12 xl:w-3/12 relative py-2">
            <div class="px-4 py-2">
                <h2 id="modal-title" class="text-lg font-bold text-left">
                    Kelurahan
                </h2>
            </div>

            <div class="px-4 py-2">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-2 py-1 border border-[#00000033] w-[20%] text-center">No</th>
                            <th class="px-2 py-1 border border-[#00000033]">Posyandu</th>
                        </tr>
                    </thead>
                    <tbody id="modal-posyandu-body">
                    </tbody>
                </table>

                <div class="flex justify-center mt-5">
                    <button id="closeModalBtn"
                        class="bg-[#61359C] text-white text-sm font-semibold px-33 py-1 rounded hover:bg-[#61359C]/60 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<x-modal id="kelurahanModalRef" size="md">
    <x-slot name="title">
        <h3 id="kelurahanModalTitle" class="text-lg font-bold">Tambah Data Wilayah</h3>
    </x-slot>

    @include('admin.fitur.data_wilayah.form')

    <x-slot name="footer">
        <button type="button" id="kelurahanCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-[#E71D1D] text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="kelurahanSaveBtn" form="formEdit"
            class="w-full px-6 py-2 rounded-lg bg-[#0B6CF4] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("kelurahanTableBody");
        const modal = document.getElementById("modal");
        const closeModalBtn = document.getElementById("closeModalBtn");

        const kelurahanModalRef = document.getElementById("kelurahanModalRef");
        const kelurahanModalTitle = document.getElementById("kelurahanModalTitle");
        const formEdit = document.getElementById("formEdit");

        let currentFilter = null;
        let kelurahanData = [];

        async function fetchKelurahan() {
            try {
                const response = await fetch(`{{ url('api/kelurahan') }}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const data = await response.json();
                if (!data) return;

                kelurahanData = data.data.list || [];

                let items = [...kelurahanData];

                if (currentFilter === 'Terbanyak → Terkecil') {
                    items.sort((a, b) => b.jumlah_posyandu - a.jumlah_posyandu);
                } else if (currentFilter === 'Terkecil → Terbanyak') {
                    items.sort((a, b) => a.jumlah_posyandu - b.jumlah_posyandu);
                }

                renderTable(items);

            } catch (error) {
                console.error("Gagal memuat data wilayah:", error);
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-red-500 py-4">
                        Gagal memuat data
                    </td>
                </tr>`;
            }
        }


        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data wilayah.</td></tr>`;
                return;
            }

            list.reverse();

            list.forEach((item, index) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">${index + 1}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama_kelurahan}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${item.jumlah_posyandu}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        <div class="flex justify-center gap-2">
                            <button
                                class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail"
                                data-id="${item.id}">
                                Detail
                            </button>
                            
                            <button
                                onclick="openKelurahanModal('edit', '${item.id}')"
                                class="px-3 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700 transition">
                                Edit
                            </button>

                            <button
                                class="px-3 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 transition delete-btn"
                                data-id="${item.id}"
                                data-name="${item.nama_kelurahan}">
                                Hapus
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });


            document.querySelectorAll(".open-detail").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.dataset.id;

                    const item = list.find(f => f.id === id);
                    if (!item) return;

                    document.getElementById("modal-title").textContent =
                        `Kelurahan ${item.nama_kelurahan}`;

                    const tbody = document.getElementById("modal-posyandu-body");
                    tbody.innerHTML = "";

                    if (item.posyandu && item.posyandu.length > 0) {
                        item.posyandu.forEach((p, index) => {
                            tbody.innerHTML += `
                                <tr>
                                    <td class="px-3 py-2 border border-[#00000033] text-center text-gray-700">${index + 1}</td>
                                    <td class="px-3 py-2 border border-[#00000033] text-gray-700">${p.nama_posyandu}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="2" class="px-3 py-4 border border-[#00000033] text-center text-gray-500">
                                    Tidak ada data posyandu
                                </td>
                            </tr>
                        `;
                    }

                    document.getElementById("modal").classList.remove("hidden");
                    document.getElementById("modal").classList.add("flex");
                });
            });


            document.querySelectorAll(".open-modal-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    modalImg.src = btn.dataset.img;
                    modalDate.textContent = btn.dataset.date;
                    modalType.textContent = btn.dataset.type;
                    modalDesc.textContent = btn.dataset.desc;
                    modal.classList.remove("hidden");
                    modal.classList.add("flex");
                    document.body.style.overflow = "hidden";
                });
            });


            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", async () => {
                    const id = btn.dataset.id;

                    showDeleteConfirmToast("Apakah Anda yakin ingin menghapus data ini?", async () => {
                        try {
                            const data = await fetch(`{{ url('api/kelurahan') }}/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            });
                            if (!data) return;

                            showSuccessToast("Data berhasil dihapus!");
                            await fetchKelurahan();
                        } catch (error) {
                            console.error("Gagal menghapus data:", error);
                            showErrorToast("Terjadi kesalahan pada server!");
                        }
                    });
                });
            });
        }

        closeModalBtn.addEventListener("click", () => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow = "auto";
        });

        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
                document.body.style.overflow = "auto";
            }
        });

        formEdit.addEventListener("submit", async (e) => {
            e.preventDefault();

            ['nama_kelurahan'].forEach(name => {
                const el = document.getElementById("error-" + name);
                if (el) {
                    el.textContent = "";
                    el.classList.add("hidden");
                }
            });

            const mode = formEdit.getAttribute("data-mode");
            const id = formEdit.getAttribute("data-id");
            const formData = new FormData(formEdit);

            deletedPosyanduIds.forEach((id, i) => {
                formData.append(`posyandu_deleted[${i}]`, id);
            });


            if (mode === "edit" && id) {
                formData.append("id", id);
            }

            try {
                let url = "{{ url('api/kelurahan') }}";
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
                    kelurahanModalRef.classList.add("hidden");
                    kelurahanModalRef.classList.remove("flex");
                    fetchKelurahan();
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


        const openKelurahanModal = async (mode, id = null) => {
            kelurahanModalRef.classList.remove("hidden");
            kelurahanModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                kelurahanModalTitle.textContent = "Edit Data Wilayah";
                try {
                    const data = await fetch(`{{ url('api/kelurahan') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEdit.setAttribute('data-mode', 'edit');
                    formEdit.setAttribute('data-id', id);
                } catch (err) {
                    console.error("Gagal mengambil data wilayah:", err);
                }
            } else {
                kelurahanModalTitle.textContent = "Tambah Data Wilayah";
                setFormData(null);
                formEdit.removeAttribute('data-id');
                formEdit.setAttribute('data-mode', 'add');
            }
        };

        window.openKelurahanModal = openKelurahanModal;

        document.getElementById("btnTambahWilayah")
            .addEventListener("click", () => openKelurahanModal("add"));


        document.addEventListener("click", (e) => {
            if (e.target && e.target.textContent === "Edit") {
                const card = e.target.closest(".flex.flex-col");
                const itemName = card.querySelector("h3").textContent;
                const qty = card.querySelector("p.text-2xl").textContent;
                const img = card.querySelector("img").src;

                const id = e.target.dataset.id || null;

                openKelurahanModal("edit", id);
            }
        });

        document.getElementById("kelurahanCancelBtn").addEventListener("click", () => {
            kelurahanModalRef.classList.add("hidden");
            kelurahanModalRef.classList.remove("flex");
        });

        const dropdownFilter = document.querySelector('[data-dropdown="filter"]');

        if (dropdownFilter) {
            dropdownFilter.addEventListener('dropdown-changed', (e) => {
                currentFilter = e.detail.value;
                fetchKelurahan();
            });
        }

        initPosyanduForm();
        fetchKelurahan();
    });
</script>
@endsection