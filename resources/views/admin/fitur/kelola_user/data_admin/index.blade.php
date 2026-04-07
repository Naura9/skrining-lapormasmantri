@extends('layouts.main')

@section('title', 'Data Admin')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Data Admin</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-center sm:justify-end gap-4 flex-wrap">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-5 flex-wrap">
            <div class="flex items-center gap-3 w-full lg:w-auto justify-center sm:justify-end">
                <button
                    class="flex items-center gap-2 bg-[#61359C] text-white text-sm px-4 py-2 rounded-lg
                       hover:bg-[#61359C]/80 transition sm:w-auto justify-center">
                    <i class="fa-solid fa-plus"></i>
                    Tambah
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full table-fixed border border-[#00000033] text-sm text-left text-gray-700  whitespace-nowrap">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[35%]">Nama</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[15%]">Username</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[15%]">NIK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[15%]">No Telepon</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">Jenis Kelamin</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%]">Aksi</th>
                </tr>
            </thead>
            <tbody id="adminTableBody"></tbody>
        </table>
    </div>
</section>

<x-modal id="adminModalRef" size="md">
    <x-slot name="title">
        <h3 id="adminModalTitle" class="text-lg font-bold">Tambah Data Admin</h3>
    </x-slot>

    @include('admin.fitur.kelola_user.data_admin.form')

    <x-slot name="footer">
        <button type="button" id="adminCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="adminSaveBtn" form="formEdit"
            class="w-full px-6 py-2 rounded-lg bg-[#61359C] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("adminTableBody");

        const adminModalRef = document.getElementById("adminModalRef");
        const adminModalTitle = document.getElementById("adminModalTitle");
        const formEdit = document.getElementById("formEdit");

        async function fetchAdmin() {
            try {
                const response = await fetch(`{{ url('api/users') }}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();
                if (!result || !result.data) return;

                const users = result.data.list || [];

                const adminUsers = users.filter(user => user.role === 'admin');

                renderTable(adminUsers);
            } catch (error) {
                showErrorToast.error("Gagal memuat data admin:", error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data admin.</td></tr>`;
                return;
            }

            list.reverse();

            list.forEach((item, index) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">${index + 1}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.username}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${item.adminDetail?.nik ?? '-'}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${item.adminDetail?.no_telepon ?? '-'}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${item.adminDetail?.jenis_kelamin}</td>
                    
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        <div class="flex justify-center gap-2">
                            <button
                                onclick="openAdminModal('edit', '${item.id}')"
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
                        try {
                            const data = await fetch(`{{ url('api/users') }}/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            });
                            if (!data) return;

                            showSuccessToast("Data berhasil dihapus!");
                            await fetchAdmin();
                        } catch (error) {
                            showErrorToast.error("Gagal menghapus data:", error);
                            showErrorToast("Terjadi kesalahan pada server!");
                        }
                    });
                });
            });
        }

        formEdit.addEventListener("submit", async (e) => {
            e.preventDefault();

            ['name', 'username', 'password', 'nik', 'no_telepon', 'jenis_kelamin'].forEach(name => {
                const el = document.getElementById("error-" + name);
                if (el) {
                    el.textContent = "";
                    el.classList.add("hidden");
                }
            });

            const mode = formEdit.getAttribute("data-mode");
            const id = formEdit.getAttribute("data-id");
            const formData = new FormData(formEdit);

            if (mode === "edit" && id) {
                formData.append("id", id);
            }

            try {
                let url = "{{ url('api/users') }}";
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
                    adminModalRef.classList.add("hidden");
                    adminModalRef.classList.remove("flex");
                    fetchAdmin();
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
                showErrorToast.error("Error:", err);
                alert("Terjadi kesalahan pada server!");
            }
        });

        const openAdminModal = async (mode, id = null) => {
            adminModalRef.classList.remove("hidden");
            adminModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                adminModalTitle.textContent = "Edit Data Admin";
                try {
                    const data = await fetch(`{{ url('api/users') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEdit.setAttribute('data-mode', 'edit');
                    formEdit.setAttribute('data-id', id);
                } catch (err) {
                    showErrorToast.error("Gagal mengambil data admin:", err);
                }
            } else {
                adminModalTitle.textContent = "Tambah Data Admin";
                setFormData(null);
                formEdit.removeAttribute('data-id');
                formEdit.setAttribute('data-mode', 'add');
            }
        };

        window.openAdminModal = openAdminModal;

        document.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes("Tambah")) {
                btn.addEventListener("click", () => openAdminModal("add"));
            }
        });

        document.addEventListener("click", (e) => {
            if (e.target && e.target.textContent === "Edit") {
                const card = e.target.closest(".flex.flex-col");
                const itemName = card.querySelector("h3").textContent;
                const qty = card.querySelector("p.text-2xl").textContent;
                const img = card.querySelector("img").src;

                const id = e.target.dataset.id || null;

                openAdminModal("edit", id);
            }
        });

        document.getElementById("adminCancelBtn").addEventListener("click", () => {
            adminModalRef.classList.add("hidden");
            adminModalRef.classList.remove("flex");
        });

        fetchAdmin();
    });
</script>
@endsection