<form id="formEdit" class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="keluarga_id" class="block text-sm font-semibold mb-1">
                No KK
            </label>
            <x-dropdown
                id="kkDropdown"
                label="Pilih Nomor KK"
                :options="[]"
                searchable="true"
                width="w-full"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" data-key="kelurga_id"></p>
            <input type="hidden" name="keluarga_id" id="keluarga_id">
        </div>

        <div class="text-left">
            <label class="block text-sm font-semibold mb-1">
                Kepala Keluarga
            </label>
            <input type="text" id="kepala_keluarga"
                class="w-full border border-gray-300 px-3 py-2 text-sm rounded rounded-lg bg-gray-50" disabled>
        </div>
    </div>
    <div class="text-left w-full">
        <label for="nama" class="block text-sm font-semibold mb-1">
            Nama Lengkap
        </label>
        <input type="text" id="nama" name="nama"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan nama lengkap">
        <p class="text-red-500 text-xs mt-1 hidden" data-key="nama"></p>
    </div>

    <div class="text-left w-full">
        <label for="nik" class="block text-sm font-semibold mb-1">
            NIK
        </label>
        <input type="text" id="nik" name="nik"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan NIK">
        <p class="text-red-500 text-xs mt-1 hidden" data-key="nik"></p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="tempat_lahir" class="block text-sm font-semibold mb-1">
                Tempat Lahir
            </label>
            <input type="text" id="tempat_lahir" name="tempat_lahir"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan tempat lahir">
            <p class="text-red-500 text-xs mt-1 hidden" data-key="tempat_lahir"></p>
        </div>
        <div class="text-left">
            <label for="tanggal_lahir" class="block text-sm font-semibold mb-1">
                Tanggal Lahir
            </label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan tanggal lahir">
            <p class="text-red-500 text-xs mt-1 hidden" data-key="tanggal_lahir"></p>
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
                width="w-full"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" data-key="jenis_kelamin"></p>
            <input type="hidden" name="jenis_kelamin" id="jenis_kelamin">
        </div>
        <div class="text-left">
            <label for="hubungan_keluarga" class="block text-sm font-semibold mb-1">
                Hubungan Keluarga
            </label>
            <x-dropdown
                id="hubunganDropdown"
                label="Pilih Hubungan Keluarga"
                :options="[
                    'Kepala Keluarga', 'Istri',
                    'Anak', 'Menantu', 'Cucu',
                    'Orang Tua', 'Famili Lain', 'Pembantu / Asisten'
                ]"
                allowOther="true"
                otherPlaceholder="Ketik hubungan keluarga..."
                width="w-full"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" data-key="hubungan_keluarga"></p>
            <input type="hidden" name="hubungan_keluarga" id="hubungan_keluarga">
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="text-left">
            <label for="status_perkawinan" class="block text-sm font-semibold mb-1">
                Status Perkawinan
            </label>
            <x-dropdown
                id="statusDropdown"
                label="Pilih Status Perkawinan"
                :options="[ 'Kawin', 'Belum Kawin', 'Cerai Hidup', 'Cerai Mati' ]"
                width="w-full"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" data-key="status_perkawinan"></p>
            <input type="hidden" name="status_perkawinan" id="status_perkawinan">
        </div>
        <div class="text-left">
            <label for="pendidikan_terakhir" class="block text-sm font-semibold mb-1">
                Pendidikan Terakhir
            </label>
            <x-dropdown
                id="pendidikanDropdown"
                label="Pilih Pendidikan"
                :options="[
                    'S1 / S2 / S3 (PT)', 'D1 / D2 / D3',
                    'SMA atau sederajat', 'SMP atau sederajat', 'SD atau sederajat',
                    'Tidak pernah sekolah', 'Belum sekolah'
                ]"
                width="w-full"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" data-key="pendidikan_terakhir"></p>
            <input type="hidden" name="pendidikan_terakhir" id="pendidikan_terakhir">
        </div>
    </div>
    <div class="text-left w-full">
        <label for="pekerjaan" class="block text-sm font-semibold mb-1">
            Pekerjaan
        </label>
        <x-dropdown
            id="pekerjaanDropdown"
            label="Pilih Pekerjaan"
            :options="[ 
                'Tidak Bekerja', 'Pelajar / Mahasiswa', 
                'PNS / TNI / POLRI / BUMN / BUMD', 'Pegawai Swasta',
                'Wiraswasta', 'Petani / Nelayan', 'Pedagang',
                'Pengusaha', 'Ibu Rumah Tangga' 
            ]"
            allowOther="true"
            otherPlaceholder="Ketik pekerjaan..."
            width="w-full"
            data-dropdown="filter" />
        <p class="text-red-500 text-xs mt-1 hidden" data-key="pekerjaan"></p>
        <input type="hidden" name="pekerjaan" id="pekerjaan">
    </div>

</form>

<script>
    function filterDropdownInput(inputEl) {
        const keyword = inputEl.value.toLowerCase();
        const menu = inputEl.closest('.dropdown-menu');
        const items = menu.querySelectorAll('.dropdown-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(keyword) ? 'block' : 'none';
        });
    }

    function setDropdownLabel(id, text, fallback) {
        const el = document.getElementById(id);
        if (!el) return;

        const label = el.querySelector('.dropdown-selected');
        if (label) label.textContent = text || fallback;
    }

    async function loadKk() {
        try {
            const result = await fetchWithAuth(`{{ url('api/identitas_keluarga') }}`);

            const raw = result.data?.list || [];

            window.kkData = [];

            raw.forEach(item => {
                (item.keluarga || []).forEach(k => {
                    window.kkData.push({
                        id: k.id,
                        no_kk: k.no_kk,
                        nama_kepala_keluarga: k.kepala_keluarga?.nama ?? '-'
                    });
                });
            });

            renderKKDropdown();

        } catch (err) {
            console.error("Gagal memuat data KK:", err);
            window.kkData = [];
            renderKKDropdown();
        }
    }

    function renderKKDropdown() {
        const dropdown = document
            .getElementById('kkDropdown')
            .querySelector('.dropdown-menu');

        dropdown.innerHTML = `
        <input type="text"
            placeholder="Cari Nomor KK..."
            class="dropdown-search w-full px-3 py-2 mb-2 text-sm border border-gray-300 rounded-lg
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            onkeyup="filterDropdownInput(this)">
    `;

        kkData.forEach(k => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className =
                'dropdown-item block w-full px-4 py-1 text-sm hover:bg-gray-100';

            btn.textContent = `${k.no_kk}`;

            btn.onclick = () => {
                setDropdownLabel('kkDropdown', btn.textContent, 'Pilih No KK');

                document.getElementById('keluarga_id').value = k.id;
                document.getElementById('kepala_keluarga').value = k.nama_kepala_keluarga;
            };

            dropdown.appendChild(btn);
        });
    }

    const formModel = {
        id: "",
        keluarga_id: "",
        nama: "",
        nik: "",
        tempat_lahir: "",
        tanggal_lahir: "",
        jenis_kelamin: "",
        hubungan_keluarga: "",
        status_perkawinan: "",
        pendidikan_terakhir: "",
        pekerjaan: "",
    };

    window.setFormData = async (item) => {
        if (!item) {
            formEdit.reset();

            document.getElementById('keluarga_id').value = '';
            document.getElementById('nama').value = '';
            document.getElementById('nik').value = '';
            document.getElementById('tempat_lahir').value = '';
            document.getElementById('tanggal_lahir').value = '';
            document.getElementById('jenis_kelamin').value = '';
            document.getElementById('hubungan_keluarga').value = '';
            document.getElementById('status_perkawinan').value = '';
            document.getElementById('pendidikan_terakhir').value = '';
            document.getElementById('pekerjaan').value = '';

            setDropdownLabel('kkDropdown', null, 'Pilih Nomor KK');
            setDropdownLabel('jenisKelaminDropdown', null, 'Pilih Jenis Kelamin');
            setDropdownLabel('hubunganDropdown', null, 'Pilih Hubungan Keluarga');
            setDropdownLabel('statusDropdown', null, 'Pilih Status Perkawinan');
            setDropdownLabel('pendidikanDropdown', null, 'Pilih Pendidikan Terakhir');
            setDropdownLabel('pekerjaanDropdown', null, 'Pilih Pekerjaan');

            return;
        }

        formEdit.querySelector('[name="keluarga_id"]').value = item.keluarga_id ?? '';
        formEdit.querySelector('[name="nama"]').value = item.nama ?? '';
        formEdit.querySelector('[name="nik"]').value = item.nik ?? '';
        formEdit.querySelector('[name="tempat_lahir"]').value = item.tempat_lahir ?? '';
        formEdit.querySelector('[name="tanggal_lahir"]').value = item.tanggal_lahir ?? '';
        formEdit.querySelector('[name="hubungan_keluarga"]').value = item.hubungan_keluarga ?? '';
        formEdit.querySelector('[name="status_perkawinan"]').value = item.status_perkawinan ?? '';
        formEdit.querySelector('[name="pendidikan_terakhir"]').value = item.pendidikan_terakhir ?? '';
        formEdit.querySelector('[name="pekerjaan"]').value = item.pekerjaan ?? '';

        let jkLabel = null;
        if (item.jenis_kelamin === "L") jkLabel = "Laki-laki";
        if (item.jenis_kelamin === "P") jkLabel = "Perempuan";

        setDropdownLabel('jenisKelaminDropdown', jkLabel, 'Pilih Jenis Kelamin');
        document.getElementById('jenis_kelamin').value = item.jenis_kelamin ?? '';

        setDropdownLabel('hubunganDropdown', item.hubungan_keluarga, 'Pilih Hubungan Keluarga');
        document.getElementById('hubungan_keluarga').value = item.hubungan_keluarga ?? '';

        setDropdownLabel('statusDropdown', item.status_perkawinan, 'Pilih Status Perkawinan');
        document.getElementById('status_perkawinan').value = item.status_perkawinan ?? '';

        setDropdownLabel('pendidikanDropdown', item.pendidikan_terakhir, 'Pilih Pendidikan');
        document.getElementById('pendidikan_terakhir').value = item.pendidikan_terakhir ?? '';

        setDropdownLabel('pekerjaanDropdown', item.pekerjaan, 'Pilih Pekerjaan');
        document.getElementById('pekerjaan').value = item.pekerjaan ?? '';

        if (!window.kkData) {
            window.kkData = [];
        }

        if (window.kkData.length === 0) {
            await loadKk();
        }

        const kk = window.kkData.find(k => k.id == item.keluarga_id);

        if (kk) {
            setDropdownLabel('kkDropdown', kk.no_kk, 'Pilih Nomor KK');
            document.getElementById('keluarga_id').value = kk.id;
            document.getElementById('kepala_keluarga').value = kk.nama_kepala_keluarga;
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

    document.getElementById('hubunganDropdown')
        .addEventListener('dropdown-changed', function(e) {
            document.getElementById('hubungan_keluarga').value = e.detail.value;
        });

    document.getElementById('statusDropdown')
        .addEventListener('dropdown-changed', function(e) {
            document.getElementById('status_perkawinan').value = e.detail.value;
        });

    document.getElementById('pendidikanDropdown')
        .addEventListener('dropdown-changed', function(e) {
            document.getElementById('pendidikan_terakhir').value = e.detail.value;
        });

    document.getElementById('pekerjaanDropdown')
        .addEventListener('dropdown-changed', function(e) {
            document.getElementById('pekerjaan').value = e.detail.value;
        });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('dropdown-other')) {
            const dropdown = e.target.closest('.relative');
            const id = dropdown.getAttribute('id');
            const value = e.target.value;

            if (id === 'hubunganDropdown')
                document.getElementById('hubungan_keluarga').value = value;

            if (id === 'pekerjaanDropdown')
                document.getElementById('pekerjaan').value = value;
        }
    });

    loadKk();
</script>