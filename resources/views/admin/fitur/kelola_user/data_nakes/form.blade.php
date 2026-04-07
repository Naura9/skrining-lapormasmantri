<form id="formEdit" class="space-y-4">
    <input type="hidden" name="role" value="nakes">

    <div class="text-left w-full">
        <label for="name" class="block text-sm font-semibold mb-1">
            Nama Lengkap
        </label>
        <input type="text" id="name" name="name"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan nama lengkap">
        <p class="text-red-500 text-xs mt-1 hidden" id="error-name"></p>
    </div>

    <div class="text-left w-full">
        <label for="username" class="block text-sm font-semibold mb-1">
            Username
        </label>
        <input type="text" id="username" name="username"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan username">
        <p class="text-red-500 text-xs mt-1 hidden" id="error-username"></p>
    </div>

    <div class="text-left w-full relative">
        <label for="password" class="block text-sm font-semibold mb-1">
            Password
        </label>
        <input id="password" name="password" type="password"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm
               focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan password">

        <button type="button"
            onclick="togglePassword()"
            class="absolute right-3 top-[30px] text-gray-500 hover:text-gray-700">
            <i id="eye-icon" class="fa-solid fa-eye-slash"></i>
        </button>
        <p class="text-red-500 text-xs mt-1 hidden" id="error-password"></p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="nik" class="block text-sm font-semibold mb-1">
                NIK
            </label>
            <input type="number" id="nik" name="nik"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 sm:w-60"
                placeholder="Masukkan NIK">
            <p class="text-red-500 text-xs mt-1 hidden" id="error-nik"></p>
        </div>

        <div class="text-left">
            <label for="no_telepon" class="block text-sm font-semibold mb-1">
                No Telepon
            </label>
            <input type="number" id="no_telepon" name="no_telepon"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 sm:w-60"
                placeholder="Masukkan nomor telepon">
            <p class="text-red-500 text-xs mt-1 hidden" id="error-no_telepon"></p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="kelurahan_id" class="block text-sm font-semibold mb-1">
                Kelurahan (Wilayah)
            </label>
            <x-dropdown
                id="kelurahanDropdown"
                label="Pilih Kelurahan"
                :options="[]"
                width="w-full sm:w-60"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" id="error-kelurahan_id"></p>
            <input type="hidden" name="kelurahan_id" id="kelurahan_id">
        </div>

        <div class="text-left">
            <label for="jenis_kelamin" class="block text-sm font-semibold mb-1">
                Jenis Kelamin
            </label>
            <x-dropdown
                id="jenisKelaminDropdown"
                label="Pilih Jenis Kelamin"
                :options="['Laki-laki', 'Perempuan']"
                width="w-full sm:w-60"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" id="error-jenis_kelamin"></p>
            <input type="hidden" name="jenis_kelamin" id="jenis_kelamin">
        </div>
    </div>
</form>

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
            };

            dropdown.appendChild(btn);
        });
    }

    const formModel = {
        id: "",
        name: "",
        username: "",
        password: "",
        nik: "",
        no_telepon: "",
        kelurahan: "",
        jenis_kelamin: "",
    };

    window.setFormData = async (item) => {
        if (!item) {
            formEdit.reset();

            document.getElementById('jenis_kelamin').value = '';
            document.getElementById('kelurahan_id').value = '';

            setDropdownLabel('jenisKelaminDropdown', null, 'Pilih Jenis Kelamin');
            setDropdownLabel('kelurahanDropdown', null, 'Pilih Kelurahan');

            return;
        }

        const d = item.nakesDetail ?? {};

        formEdit.querySelector('[name="name"]').value = item.nama ?? '';
        formEdit.querySelector('[name="username"]').value = item.username ?? '';
        formEdit.querySelector('[name="password"]').value = '';
        formEdit.querySelector('[name="nik"]').value = d.nik ?? '';
        formEdit.querySelector('[name="no_telepon"]').value = d.no_telepon ?? '';

        setDropdownLabel(
            'jenisKelaminDropdown',
            d.jenis_kelamin === 'L' ? 'Laki-laki' :
            d.jenis_kelamin === 'P' ? 'Perempuan' : null,
            'Pilih Jenis Kelamin'
        );
        document.getElementById('jenis_kelamin').value = d.jenis_kelamin ?? '';

        if (!kelurahanData.length) {
            await loadKelurahan();
        }

        const kel = kelurahanData.find(k => k.id == d.kelurahan_id);

        if (kel) {
            setDropdownLabel('kelurahanDropdown', kel.nama_kelurahan, 'Pilih Kelurahan');
            document.getElementById('kelurahan_id').value = kel.id;
        } else {
            setDropdownLabel('kelurahanDropdown', null, 'Pilih Kelurahan');
            document.getElementById('kelurahan_id').value = '';
        }
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

    document.addEventListener('DOMContentLoaded', () => {
        loadKelurahan();
    });
</script>