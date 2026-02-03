<form id="formEdit" class="space-y-4">
    <input type="hidden" name="role" value="admin">

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
        <label for="nik" class="block text-sm font-semibold mb-1">
            NIK
        </label>
        <input type="text" id="nik" name="nik"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan NIK">
        <p class="text-red-500 text-xs mt-1 hidden" id="error-nik"></p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="no_telepon" class="block text-sm font-semibold mb-1">
                No Telepon
            </label>
            <input type="number" id="no_telepon" name="no_telepon"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan nomor telepon">
            <p class="text-red-500 text-xs mt-1 hidden" id="error-no_telepon"></p>
        </div>

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
        id: "",
        name: "",
        username: "",
        password: "",
        nik: "",
        no_telepon: "",
        jenis_kelamin: "",
    };

    window.setFormData = (item) => {
        if (item) {
            formEdit.querySelector('[name="name"]').value = item.nama ?? '';
            formEdit.querySelector('[name="username"]').value = item.username ?? '';
            formEdit.querySelector('[name="password"]').value = '';

            formEdit.querySelector('[name="nik"]').value = item.adminDetail?.nik ?? '';
            formEdit.querySelector('[name="no_telepon"]').value = item.adminDetail?.no_telepon ?? '';
            formEdit.querySelector('[name="jenis_kelamin"]').value = item.adminDetail?.jenis_kelamin ?? '';

            document.getElementById('jenis_kelamin').value = item.adminDetail?.jenis_kelamin ?? '';

            const jenisKelaminDropdown = document.querySelector('[data-dropdown="filter"]');
            if (jenisKelaminDropdown) {
                const label = jenisKelaminDropdown.querySelector('.dropdown-selected');
                if (label) {
                    label.textContent =
                        item.adminDetail?.jenis_kelamin === 'L' ?
                        'Laki-laki' :
                        item.adminDetail?.jenis_kelamin === 'P' ?
                        'Perempuan' :
                        'Pilih Jenis Kelamin';
                }
            }
        } else {
            formModel.id = "";
            formModel.name = "";
            formModel.username = "";
            formModel.nik = "";
            formModel.no_telepon = "";
            formModel.jenis_kelamin = "";

            formEdit.reset();

            const jenisKelaminDropdown = document.querySelector('[data-dropdown="filter"]');
            if (jenisKelaminDropdown) {
                const label = jenisKelaminDropdown.querySelector('.dropdown-selected');
                if (label) label.textContent = 'Pilih Jenis Kelamin';
            }
        }
    };
</script>