<form id="formEdit" class="space-y-4">
    <div id="kkContainer" class="space-y-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="text-left">
                <label for="kelurahan_id" class="block text-sm font-semibold mb-1">
                    Kelurahan
                </label>
                <x-dropdown
                    id="kelurahanDropdown"
                    label="Pilih Kelurahan"
                    :options="[]"
                    width="w-full"
                    data-dropdown="filter" />
                <p class="text-red-500 text-xs mt-1 hidden" data-key="kelurahan_id"></p>
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
                    width="w-full"
                    data-dropdown="filter" />
                <p class="text-red-500 text-xs mt-1 hidden" data-key="posyandu_id"></p>
                <input type="hidden" name="posyandu_id" id="posyandu_id">
            </div>
        </div>
        <div class="text-left w-full">
            <label for="alamat" class="block text-sm font-semibold mb-1">
                Alamat domisili
            </label>
            <input type="text" id="alamat" name="alamat"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan alamat domisili">
            <p class="text-red-500 text-xs mt-1 hidden" data-key="alamat"></p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="text-left">
                <label for="rt" class="block text-sm font-semibold mb-1">
                    RT Domisili
                </label>
                <input type="number" id="rt" name="rt"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                <p class="text-red-500 text-xs mt-1 hidden" data-key="rt"></p>
            </div>

            <div class="text-left">
                <label for="rw" class="block text-sm font-semibold mb-1">
                    RW Domisili
                </label>
                <input type="number" id="rw" name="rw"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                <p class="text-red-500 text-xs mt-1 hidden" data-key="rw"></p>
            </div>
        </div>

        <div class="border-t-2 border-[#00000033] my-6"></div>

        <template id="kkTemplate">
            <div class="kk-item border border-gray-300 rounded-xl p-6 relative bg-white">
                <input type="hidden" name="id">
                <div class="mb-4">
                    <div class="flex items-start gap-2">
                        <input type="checkbox" name="is_luar_wilayah" class="kk-luar-wilayah mt-1 w-4 h-4 accent-[#61359C]">
                        <div>
                            <span class="font-semibold text-sm">
                                KK Luar Wilayah
                            </span>
                            <p class="text-xs text-gray-500 mt-1">
                                *Centang jika KK berasal dari luar wilayah.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nomor Kartu Keluarga</label>
                        <input type="text"
                            name="no_kk"
                            inputmode="numeric"
                            maxlength="16"
                            pattern="[0-9]{16}"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                        <p class="text-red-500 text-xs mt-1 hidden" data-key="no_kk"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">NIK Kepala Keluarga</label>
                        <input type="text"
                            name="nik_kepala_keluarga"
                            inputmode="numeric"
                            maxlength="16"
                            pattern="[0-9]{16}"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,16)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                        <p class="text-red-500 text-xs mt-1 hidden" data-key="nik_kepala_keluarga"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Kepala Keluarga</label>
                        <input type="text" name="nama_kepala_keluarga"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                            focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                        <p class="text-red-500 text-xs mt-1 hidden" data-key="nama_kepala_keluarga"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">No Telepon</label>
                        <input type="text"
                            name="no_telepon"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="20"
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                        <p class="text-red-500 text-xs mt-1 hidden" data-key="no_telepon"></p>
                    </div>
                </div>
                <div class="luar-wilayah-field hidden mt-6">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold mb-1">Alamat (KTP)</label>
                            <textarea name="alamat_ktp"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"></textarea>
                            <p class="text-red-500 text-xs mt-1 hidden" data-key="alamat_ktp"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">RT (KTP)</label>
                            <input type="text"
                                name="rt_ktp"
                                inputmode="numeric"
                                maxlength="3"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                            <p class="text-red-500 text-xs mt-1 hidden" data-key="rt_ktp"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">RW (KTP)</label>
                            <input type="text"
                                name="rw_ktp"
                                inputmode="numeric"
                                maxlength="3"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
                            <p class="text-red-500 text-xs mt-1 hidden" data-key="rw_ktp"></p>
                        </div>
                    </div>
                </div>
                <button type="button"
                    class="btn-remove hidden absolute top-4 right-4
                            flex items-center gap-1
                            bg-red-50 text-red-600
                            px-3 py-1.5 rounded-lg
                            hover:bg-red-100
                            transition text-sm font-semibold">
                    <i class="fa-solid fa-trash"></i>
                    Hapus
                </button>
            </div>
        </template>
    </div>
    <div class="mt-4">
        <button type="button" id="btnAddKK"
            class="border border-[#61359C] text-[#61359C]
                    text-sm font-semibold px-3 py-1.5 rounded-lg
                    hover:bg-[#61359C] hover:text-white
                    transition duration-200">
            Tambah KK
        </button>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const kkContainer = document.getElementById('kkContainer');
        const btnAddKK = document.getElementById('btnAddKK');

        const template = document.getElementById('kkTemplate');
        kkContainer.appendChild(template.content.cloneNode(true));
        updateRemoveButtons();

        kkContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('kk-luar-wilayah')) {

                const kkItem = e.target.closest('.kk-item');
                const luarField = kkItem.querySelector('.luar-wilayah-field');

                if (e.target.checked) {
                    luarField.classList.remove('hidden');
                } else {
                    luarField.classList.add('hidden');
                }
            }
        });

        function updateRemoveButtons() {
            const items = document.querySelectorAll('.kk-item');
            items.forEach((item, index) => {
                const removeBtn = item.querySelector('.btn-remove');
                if (items.length > 1) {
                    removeBtn.classList.remove('hidden');
                } else {
                    removeBtn.classList.add('hidden');
                }
            });
        }

        btnAddKK.addEventListener('click', () => {
            const template = document.getElementById('kkTemplate');
            const clone = template.content.cloneNode(true);
            kkContainer.appendChild(clone);

            updateRemoveButtons();
        });

        kkContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove')) {
                e.target.closest('.kk-item').remove();
                updateRemoveButtons();
            }
        });

        updateRemoveButtons();

        function setDropdownLabel(id, text, fallback) {
            const el = document.getElementById(id);
            if (!el) return;

            const label = el.querySelector('.dropdown-selected');
            if (label) label.textContent = text || fallback;
        }

        let kelurahanData = [];

        async function loadKelurahan() {
            const json = await fetchWithAuth(`{{ url('api/kelurahan') }}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            });

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
            kelurahan_id: "",
            posyandu_id: "",
            alamat: "",
            rt: "",
            rw: "",
            keluarga: [{
                no_kk: "",
                no_telepon: "",
                is_luar_wilayah: false,
                alamat_ktp: "",
                rt_ktp: "",
                rw_ktp: "",
                nik_kepala_keluarga: "",
                nama_kepala_keluarga: ""
            }]
        };

        window.setFormData = async (data = null) => {
            const formEdit = document.getElementById('formEdit');
            const kkContainer = document.getElementById('kkContainer');
            const template = document.getElementById('kkTemplate');

            formEdit.reset();

            setDropdownLabel('kelurahanDropdown', null, 'Pilih Kelurahan');
            setDropdownLabel('posyanduDropdown', null, 'Pilih Posyandu');
            document.getElementById('kelurahan_id').value = '';
            document.getElementById('posyandu_id').value = '';
            setDropdownDisabled('posyanduDropdown', true);

            kkContainer.querySelectorAll('.kk-item').forEach(item => item.remove());

            if (!data) {
                const firstKK = template.content.cloneNode(true);
                kkContainer.appendChild(firstKK);

                updateRemoveButtons();
                return;
            }

            if (!kelurahanData.length) await loadKelurahan();

            const kelurahan = kelurahanData.find(k =>
                k.posyandu?.some(p => p.id == data.posyandu_id)
            );

            if (kelurahan) {
                setDropdownLabel('kelurahanDropdown', kelurahan.nama_kelurahan, 'Pilih Kelurahan');
                document.getElementById('kelurahan_id').value = kelurahan.id;

                setDropdownDisabled('posyanduDropdown', false);
                renderPosyanduDropdown(kelurahan.posyandu);

                const pos = kelurahan.posyandu.find(p => p.id == data.posyandu_id);
                if (pos) {
                    setDropdownLabel('posyanduDropdown', pos.nama_posyandu, 'Pilih Posyandu');
                    document.getElementById('posyandu_id').value = pos.id;
                }
            }

            formEdit.querySelector('[name="alamat"]').value = data.alamat ?? '';
            formEdit.querySelector('[name="rt"]').value = data.rt ?? '';
            formEdit.querySelector('[name="rw"]').value = data.rw ?? '';

            if (Array.isArray(data.keluarga) && data.keluarga.length) {
                data.keluarga.forEach((kel, index) => {
                    const kkItem = template.content.cloneNode(true);
                    kkContainer.appendChild(kkItem);

                    const currentKK = kkContainer.querySelectorAll('.kk-item')[index];
                    currentKK.querySelector('[name="id"]').value = kel.id ?? '';
                    currentKK.querySelector('[name="no_kk"]').value = kel.no_kk ?? '';
                    currentKK.querySelector('[name="nik_kepala_keluarga"]').value =
                        kel.kepala_keluarga?.nik ?? '';
                    currentKK.querySelector('[name="nama_kepala_keluarga"]').value =
                        kel.kepala_keluarga?.nama ?? '';
                    currentKK.querySelector('[name="no_telepon"]').value = kel.no_telepon ?? '';
                    currentKK.querySelector('[name="alamat_ktp"]').value = kel.alamat_ktp ?? '';
                    currentKK.querySelector('[name="rt_ktp"]').value = kel.rt_ktp ?? '';
                    currentKK.querySelector('[name="rw_ktp"]').value = kel.rw_ktp ?? '';

                    const checkbox = currentKK.querySelector('[name="is_luar_wilayah"]');
                    checkbox.checked = kel.is_luar_wilayah ?? false;

                    const luarField = currentKK.querySelector('.luar-wilayah-field');
                    luarField.classList.toggle('hidden', !checkbox.checked);
                });
            } else {
                kkContainer.appendChild(template.content.cloneNode(true));
            }

            updateRemoveButtons();
        };
        loadKelurahan();
        setDropdownDisabled('posyanduDropdown', true);
    });
</script>