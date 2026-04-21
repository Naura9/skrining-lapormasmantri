@extends('layouts.main')

@section('title', 'Riwayat Skrining')

@section('content')

<section class="max-w-4xl mx-auto mt-3">
    <div class="bg-white border border-[#00000020] rounded-2xl shadow-sm p-6">
        <div class="mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-gray-800">Profil</h2>
            <p class="text-sm text-gray-500">Perbarui informasi akun Anda</p>
        </div>

        <form id="formEdit" class="space-y-6">
            <input type="hidden" name="role" value="nakes">
            <div>
                <div class="mt-4 relative">
                    <label class="text-sm font-medium">Nama Lengkap</label>
                    <input type="text" id="name" name="name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 mt-1"
                        placeholder="Masukkan nama lengkap">
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-name"></p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="text-sm font-medium">Username</label>
                        <input type="text" id="username" name="username"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 mt-1"
                            placeholder="Masukkan username">
                        <p class="text-red-500 text-xs mt-1 hidden" id="error-username"></p>
                    </div>
                    <div class="relative">
                        <label class="text-sm font-medium">Password</label>
                        <input id="password" name="password" type="password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 mt-1"
                            placeholder="Kosongkan jika tidak diubah">

                        <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-[34px] text-gray-500 hover:text-gray-700">
                            <i id="eye-icon" class="fa-solid fa-eye-slash"></i>
                        </button>
                        <p class="text-red-500 text-xs mt-1 hidden" id="error-password"></p>
                    </div>
                </div>
                <div class="mt-4 relative">
                    <label class="text-sm font-medium">NIK</label>
                    <input type="number" id="nik" name="nik"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 mt-1"
                        placeholder="Masukkan NIK">
                    <p class="text-red-500 text-xs mt-1 hidden" id="error-nik"></p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="text-sm font-medium">No Telepon</label>
                        <input type="text" id="no_telepon" name="no_telepon"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 mt-1"
                            placeholder="Masukkan No Telepon">
                        <p class="text-red-500 text-xs mt-1 hidden" id="error-no_telepon"></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Jenis Kelamin</label>
                        <x-dropdown id="jenisKelaminDropdown" label="Pilih Jenis Kelamin" class="mt-1"
                            :options="['Laki-laki', 'Perempuan']" width="w-full" data-dropdown="filter" />
                        <p class="text-red-500 text-xs mt-1 hidden" id="error-jenis_kelamin"></p>
                        <input type="hidden" id="jenis_kelamin">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t mt-8">
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-[#61359C] text-white hover:bg-[#512c82] transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</section>

<script>
    const jenisKelaminDropdown = document.getElementById('jenisKelaminDropdown');

    function togglePassword() {
        const input = document.getElementById("password");
        const icon = document.getElementById("eye-icon");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        }
    }

    function setDropdownLabel(id, text, fallback) {
        const el = document.getElementById(id);
        if (!el) return;

        const label = el.querySelector('.dropdown-selected');
        if (label) label.textContent = text || fallback;
    }

    async function loadProfile() {
        try {
            const res = await fetchWithAuth('/api/auth/profile', {
                method: 'GET',
                headers: {
                    "Accept": "application/json"
                }
            });

            if (!res || !res.data) {
                showErrorToast("Gagal memuat profil");
                return;
            }

            renderProfile(res.data);

        } catch (error) {
            console.error(error);
            showErrorToast("Terjadi kesalahan server");
        }
    }

    function renderProfile(user) {
        const detail = user.nakesDetail ?? {};

        document.getElementById('name').value = user.nama ?? '';
        document.getElementById('username').value = user.username ?? '';
        document.getElementById('password').value = '';
        document.getElementById('nik').value = detail.nik ?? '';
        document.getElementById('no_telepon').value = detail.no_telepon ?? '';
        document.getElementById('jenis_kelamin').value = detail.jenis_kelamin ?? '';

        setDropdownLabel(
            'jenisKelaminDropdown',
            detail.jenis_kelamin === 'L' ? 'Laki-laki' :
            detail.jenis_kelamin === 'P' ? 'Perempuan' : null,
            'Pilih Jenis Kelamin'
        );

        formModel.id = user.id;
    }

    const formModel = {
        id: "",
        name: "",
        username: "",
        password: "",
        no_telepon: "",
        kelurahan: "",
        jenis_kelamin: "",
    };

    window.setFormData = (item) => {
        if (!item) {
            formEdit.reset();
            document.getElementById('jenis_kelamin').value = '';
            setDropdownLabel('jenisKelaminDropdown', null, 'Pilih Jenis Kelamin');
            return;
        }

        const d = item.nakesDetail ?? {};

        document.getElementById('name').value = item.nama ?? '';
        document.getElementById('username').value = item.username ?? '';
        document.getElementById('password').value = '';
        document.getElementById('no_telepon').value = d.no_telepon ?? '';

        document.getElementById('jenis_kelamin').value = d.jenis_kelamin ?? '';

        setDropdownLabel(
            'jenisKelaminDropdown',
            d.jenis_kelamin === 'L' ? 'Laki-laki' :
            d.jenis_kelamin === 'P' ? 'Perempuan' : null,
            'Pilih Jenis Kelamin'
        );
    };

    document.addEventListener('DOMContentLoaded', () => {
        document
            .querySelectorAll('#jenisKelaminDropdown .dropdown-menu button')
            .forEach(btn => {
                btn.onclick = () => {
                    const text = btn.textContent.trim();

                    setDropdownLabel('jenisKelaminDropdown', text, 'Pilih Jenis Kelamin');
                    document.getElementById('jenis_kelamin').value =
                        text === 'Laki-laki' ? 'L' : 'P';
                };
            });
    });

    document.getElementById('formEdit').addEventListener('submit', async function(e) {
        e.preventDefault();

        document.querySelectorAll('[id^="error-"]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });

        const payload = {
            name: document.getElementById('name').value,
            username: document.getElementById('username').value,
            password: document.getElementById('password').value,
            nik: document.getElementById('nik').value,
            no_telepon: document.getElementById('no_telepon').value,
            jenis_kelamin: document.getElementById('jenis_kelamin').value,
        };

        try {
            const res = await fetchWithAuth('/api/auth/update_profile', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            });

            if (res?.status_code === 422 || res?.status === 422) {
                Object.keys(res.errors).forEach(key => {
                    const el = document.getElementById("error-" + key);
                    if (el) {
                        el.textContent = res.errors[key][0];
                        el.classList.remove("hidden");
                    }
                });
                return;
            }

            if (!res.status) {
                showErrorToast(res.message ?? "Gagal update profil");
                return;
            }

            showSuccessToast("Profil berhasil diperbarui");
            await loadProfile();

        } catch (err) {
            console.error(err);
            showErrorToast("Terjadi kesalahan server");
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        await loadProfile();
    });
</script>
@endsection