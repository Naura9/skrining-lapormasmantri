<form id="formEdit" class="space-y-4">
    <input type="hidden" name="role" value="kader">

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

    <div class="text-left w-full">
        <label for="no_telepon" class="block text-sm font-semibold mb-1">
            No Telepon
        </label>
        <input type="text" id="no_telepon" name="no_telepon"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan No Telepon">
        <p class="text-red-500 text-xs mt-1 hidden" id="error-no_telepon"></p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="kelurahan_id" class="block text-sm font-semibold mb-1">
                Kelurahan
            </label>
            <x-dropdown
                id="kelurahanDropdown"
                label="Pilih Kelurahan"
                :options="[]"
                width="w-full sm:w-56"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" id="error-kelurahan_id"></p>
            <input type="hidden" name="kelurahan_id" id="kelurahan_id">
        </div>

        <div class="text-left">
            <label for="posyandu_id" class="block text-sm font-semibold mb-1">
                Posyandu
            </label>
            <x-dropdown
                id="posyanduDropdown"
                label="Pilih Posyandu"
                :options="[]"
                width="w-full sm:w-56"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" id="error-posyandu_id"></p>
            <input type="hidden" name="posyandu_id" id="posyandu_id">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="jenis_kelamin" class="block text-sm font-semibold mb-1">
                Jenis Kelamin
            </label>
            <x-dropdown
                id="jenisKelaminDropdown"
                label="Pilih Jenis Kelamin"
                :options="['Laki-laki', 'Perempuan']"
                width="w-full sm:w-56"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" id="error-jenis_kelamin"></p>
            <input type="hidden" name="jenis_kelamin" id="jenis_kelamin">
        </div>

        <div class="text-left">
            <label for="status" class="block text-sm font-semibold mb-1">
                Status
            </label>
            <x-dropdown
                id="statusDropdown"
                label="Pilih Status"
                :options="['Aktif', 'Nonaktif']"
                width="w-full sm:w-56"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" id="error-status"></p>
            <input type="hidden" name="status" id="status">
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

    const formModel = {
        id: "",
        name: "",
        username: "",
        password: "",
        no_telepon: "",
        kelurahan: "",
        posyandu: "",
        jenis_kelamin: "",
        status: "",
    };

    window.setFormData = async (item) => {
        if (!item) {
            formEdit.reset();

            document.getElementById('jenis_kelamin').value = '';
            document.getElementById('status').value = '';
            document.getElementById('kelurahan_id').value = '';
            document.getElementById('posyandu_id').value = '';

            setDropdownLabel('jenisKelaminDropdown', null, 'Pilih Jenis Kelamin');
            setDropdownLabel('statusDropdown', null, 'Pilih Status');
            setDropdownLabel('kelurahanDropdown', null, 'Pilih Kelurahan');
            setDropdownLabel('posyanduDropdown', null, 'Pilih Posyandu');

            setDropdownDisabled('posyanduDropdown', true);

            return;
        }

        const d = item.kaderDetail ?? {};

        formEdit.querySelector('[name="name"]').value = item.nama ?? '';
        formEdit.querySelector('[name="username"]').value = item.username ?? '';
        formEdit.querySelector('[name="password"]').value = '';
        formEdit.querySelector('[name="no_telepon"]').value = d.no_telepon ?? '';

        setDropdownLabel(
            'jenisKelaminDropdown',
            d.jenis_kelamin === 'L' ? 'Laki-laki' :
            d.jenis_kelamin === 'P' ? 'Perempuan' : null,
            'Pilih Jenis Kelamin'
        );
        document.getElementById('jenis_kelamin').value = d.jenis_kelamin ?? '';

        setDropdownLabel(
            'statusDropdown',
            d.status === 'aktif' ? 'Aktif' :
            d.status === 'nonaktif' ? 'Nonaktif' : null,
            'Pilih Status'
        );
        document.getElementById('status').value = d.status ?? '';

        if (!kelurahanData.length) {
            await loadKelurahan();
        }

        if (!d.posyandu_id) return;

        const kelurahan = kelurahanData.find(k =>
            k.posyandu?.some(p => p.id == d.posyandu_id)
        );

        if (kelurahan) {
            setDropdownLabel('kelurahanDropdown', kelurahan.nama_kelurahan, 'Pilih Kelurahan');
            document.getElementById('kelurahan_id').value = kelurahan.id;

            setDropdownDisabled('posyanduDropdown', false);
            renderPosyanduDropdown(kelurahan.posyandu);

            const pos = kelurahan.posyandu.find(p => p.id == d.posyandu_id);
            if (pos) {
                setDropdownLabel('posyanduDropdown', pos.nama_posyandu, 'Pilih Posyandu');
                document.getElementById('posyandu_id').value = pos.id;
            }
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
        document
            .querySelectorAll('#statusDropdown .dropdown-menu button')
            .forEach(btn => {
                btn.onclick = () => {
                    const text = btn.textContent.trim();

                    setDropdownLabel('statusDropdown', text, 'Pilih Status');
                    document.getElementById('status').value =
                        text === 'Aktif' ? 'aktif' : 'nonaktif';
                };
            });
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadKelurahan();
        setDropdownDisabled('posyanduDropdown', true);
    });
</script>