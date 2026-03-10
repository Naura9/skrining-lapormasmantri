    <form id="formEditPertanyaan" class="space-y-4">
        <div class="text-left">
            <label for="section_id" class="block text-sm font-semibold mb-1">
                Section
            </label>
            <x-dropdown
                id="sectionDropdown"
                label="Pilih Section"
                :options="[]"
                width="w-full"
                data-dropdown="filter" />
            <p class="text-red-500 text-xs mt-1 hidden" id="error-section_id"></p>
            <input type="hidden" name="section_id" id="section_id">
            <div class="text-right mt-1">
                <button type="button"
                    id="btnAddSection"
                    class="text-sm text-[#61359C] hover:underline hidden">
                    + Tambah Section
                </button>
            </div>
        </div>

        <div id="addSectionForm" class="hidden mt-3 border border-gray-400 p-3 rounded-lg bg-gray-50">
            <label class="block text-sm font-semibold mb-1">
                Judul Section
            </label>
            <input type="text"
                id="new_judul_section"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan judul section">

            <input type="hidden" id="new_target_skrining" value="kk">

            <div class="text-right mt-2">
                <button type="button"
                    id="saveSectionBtn"
                    class="bg-[#61359C] text-white px-3 py-1 rounded text-sm">
                    Simpan
                </button>
            </div>
        </div>

        <div class="mt-3">
            <div class="flex items-center gap-2">
                <input type="checkbox"
                    id="is_required"
                    name="is_required"
                    value="1"
                    class="w-4 h-4 accent-[#61359C]">

                <label for="is_required" class="text-sm text-gray-500 font-medium">
                    Centang jika pertanyaan bersifat wajib diisi.
                </label>
            </div>
            </p>
        </div>

        <div class="text-left w-full">
            <label for="pertanyaan" class="block text-sm font-semibold mb-1">
                Pertanyaan
            </label>
            <input type="text" id="pertanyaan" name="pertanyaan" disabled
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                    focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan pertanyaan skrining KK">
            <p class="text-red-500 text-xs mt-1 hidden" id="error-pertanyaan"></p>
        </div>

        <div class="text-left w-full">
            <label for="keterangan" class="block text-sm font-semibold mb-1">
                Keterangan
            </label>
            <textarea id="keterangan" name="keterangan" disabled
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan keterangan (opsional)"></textarea>
            <p class="text-red-500 text-xs mt-1 hidden" id="error-keterangan"></p>
        </div>

        <div class="text-left">
            <label class="block text-sm font-semibold mb-1">
                Jenis Jawaban
            </label>
            <x-dropdown
                id="jenisDropdown"
                label="Pilih Jenis Jawaban"
                :options="['Radio', 'Checkbox', 'Dropdown', 'Jawaban Pendek', 'Jawaban Panjang', 'Date']"
                width="w-full"
                disabled />
            <input type="hidden" name="jenis_jawaban" id="jenis_jawaban">
            <p class="text-red-500 text-xs mt-1 hidden" id="error-jenis_jawaban"></p>
        </div>

        <div id="opsiContainer" class="hidden">
            <div class="overflow-hidden">
                <table class="w-full border border-[#00000033] text-sm text-left text-gray-700">
                    <thead class="bg-gray-100 text-center">
                        <tr>
                            <th class="px-3 py-2 border border-[#00000033] text-left w-[80%]">Opsi Jawaban</th>
                            <th class="px-3 py-2 border border-[#00000033] text-center w-[20%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="opsiTableBody"></tbody>
                </table>
            </div>
            <div id="allowOtherContainer" class="hidden mt-2">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="opsi_lain" value="0">
                    <input type="checkbox"
                        id="opsi_lain"
                        name="opsi_lain"
                        value="1"
                        class="w-4 h-4 accent-[#61359C]">
                    <label for="opsi_lain" class="text-sm text-gray-600">
                        Tambahkan opsi "Lainnya"
                    </label>
                </div>
            </div>
        </div>

    </form>

    <script>
        const pertanyaanInput = document.getElementById("pertanyaan");
        const keteranganInput = document.getElementById("keterangan");
        const isRequiredInput = document.getElementById("is_required");
        const opsiLainInput = document.getElementById("opsi_lain");
        const jenisDropdownEl = document.getElementById("jenisDropdown");
        const btnAddSection = document.getElementById("btnAddSection");
        const addSectionForm = document.getElementById("addSectionForm");

        function disableFormFields() {
            pertanyaanInput.disabled = true;
            keteranganInput.disabled = true;
            isRequiredInput.disabled = true;

            pertanyaanInput.classList.add("opacity-50", "cursor-not-allowed");
            keteranganInput.classList.add("opacity-50", "cursor-not-allowed");

            isRequiredInput.classList.add("opacity-50", "cursor-not-allowed");

            jenisDropdownEl.classList.add("pointer-events-none", "opacity-50");

            isRequiredInput.checked = false;

            document.getElementById('jenis_jawaban').value = "";
            setDropdownLabel('jenisDropdown', null, 'Pilih Jenis Jawaban');

            opsiContainer.classList.add("hidden");
            opsiTableBody.innerHTML = "";
        }

        function enableFormFields() {
            pertanyaanInput.disabled = false;
            keteranganInput.disabled = false;
            isRequiredInput.disabled = false;

            pertanyaanInput.classList.remove("opacity-50", "cursor-not-allowed");
            keteranganInput.classList.remove("opacity-50", "cursor-not-allowed");
            isRequiredInput.classList.remove("opacity-50", "cursor-not-allowed");

            jenisDropdownEl.classList.remove("pointer-events-none", "opacity-50");
        }

        const jenisDropdown = document.getElementById("jenisDropdown");
        const jenisInput = document.getElementById("jenis_jawaban");

        const opsiContainer = document.getElementById("opsiContainer");
        const opsiTableBody = document.getElementById("opsiTableBody");

        if (jenisDropdown) {
            jenisDropdown.addEventListener("dropdown-changed", (e) => {

                const label = e.detail.value;
                let value = "";

                switch (label) {
                    case "Radio":
                        value = "radio";
                        break;
                    case "Checkbox":
                        value = "checkbox";
                        break;
                    case "Dropdown":
                        value = "select";
                        break;
                    case "Jawaban Pendek":
                        value = "text";
                        break;
                    case "Jawaban Panjang":
                        value = "textarea";
                        break;
                    case "Date":
                        value = "date";
                        break;
                }

                jenisInput.value = value;

                const allowOtherContainer = document.getElementById("allowOtherContainer");

                if (value === "text" || value === "textarea" || value === "date") {
                    opsiContainer.classList.add("hidden");
                    allowOtherContainer.classList.add("hidden");

                    opsiTableBody.innerHTML = "";
                } else {
                    opsiContainer.classList.remove("hidden");

                    if (["radio", "checkbox"].includes(value)) {
                        allowOtherContainer.classList.remove("hidden");
                    } else {
                        allowOtherContainer.classList.add("hidden");
                    }

                    if (opsiTableBody.children.length === 0) {
                        addOpsiRow();
                    }
                }
            });
        }

        function addOpsiRow(value = "") {
            const tr = document.createElement("tr");

            tr.innerHTML = `
                <td class="border border-[#00000033] px-3 py-2">
                    <input type="text" name="opsi_jawaban[]"
                        value="${value}"
                        class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                        placeholder="Masukkan opsi">
                </td>

                <td class="border border-[#00000033] px-3 py-2 text-center">
                    <div class="flex items-center justify-center gap-2 action-buttons">
                    </div>
                </td>
            `;

            opsiTableBody.appendChild(tr);

            renderActionButtons();
        }

        function renderActionButtons() {
            const rows = opsiTableBody.querySelectorAll("tr");

            rows.forEach((row, index) => {

                const actionCell = row.querySelector(".action-buttons");

                actionCell.innerHTML = `
                ${rows.length > 1 ? `
                    <button type="button" onclick="removeRow(this)" 
                        class="text-red-600 hover:text-red-800">
                        <i class="fa-solid fa-square-minus text-xl"></i>
                    </button>
                    ` : ''}

                    <button type="button" onclick="addOpsiRow()" 
                        class="text-green-600 hover:text-green-800">
                        <i class="fa-solid fa-square-plus text-xl"></i>
                    </button>
                `;
            });
        }

        function removeRow(btn) {
            btn.closest("tr").remove();
            renderActionButtons();
        }

        let sectionData = [];

        async function loadKategori() {
            const res = await fetch(`{{ url('api/kategori') }}`);
            const json = await res.json();

            const list = json.data.list || [];

            const kategoriKk = list.find(k => k.target_skrining === 'kk');

            if (kategoriKk) {
                kategoriKkId = kategoriKk.id;
            }
        }

        async function loadSection() {
            const res = await fetch(`{{ url('api/section') }}`);
            const json = await res.json();

            const allSection = json.data.list || [];

            sectionData = allSection.filter(sec =>
                sec.target_skrining === 'kk'
            );

            renderSectionDropdown();

            btnAddSection.classList.remove("hidden");

            disableFormFields();
        }

        function renderSectionDropdown() {
            const dropdown = document
                .getElementById('sectionDropdown')
                .querySelector('.dropdown-menu');

            dropdown.innerHTML = '';

            if (!sectionData.length) {
                const empty = document.createElement("div");
                empty.className = "px-4 py-2 text-center text-sm text-gray-500 italic";
                empty.textContent = "Tidak ada section";

                dropdown.appendChild(empty);

                document.getElementById("section_id").value = "";
                setDropdownLabel("sectionDropdown", null, "Pilih Section");

                disableFormFields();

                return;
            }

            sectionData.forEach(sec => {
                const wrapper = document.createElement('div');
                wrapper.className = "flex items-center justify-between px-4 py-1 hover:bg-gray-100";

                const selectBtn = document.createElement('button');
                selectBtn.type = 'button';
                selectBtn.className = "text-sm text-left w-full";
                selectBtn.textContent = sec.judul_section;

                selectBtn.onclick = (e) => {
                    const dropdownEl = e.target.closest('.relative');
                    dropdownEl.querySelector('.dropdown-menu').classList.add('hidden');

                    setDropdownLabel('sectionDropdown', sec.judul_section, 'Pilih Section');
                    document.getElementById('section_id').value = sec.id;

                    enableFormFields();
                };

                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = "text-red-500 hover:text-red-700 ml-2";

                deleteBtn.innerHTML = `<i class="fa-regular fa-trash-can text-sm"></i>`;

                deleteBtn.onclick = async (e) => {
                    e.stopPropagation();

                    try {
                        const res = await fetch(`{{ url('api/section') }}/${sec.id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        });

                        const json = await res.json();

                        if (!res.ok) {
                            showErrorToast(
                                "Gagal Menghapus",
                                json.errors ? json.errors[0] : "Terjadi kesalahan"
                            );
                            return;
                        }

                        showSuccessToast("Section berhasil dihapus!");

                        await loadSection();

                    } catch (error) {
                        showErrorToast("Error", "Terjadi kesalahan sistem");
                    }
                };

                wrapper.appendChild(selectBtn);
                wrapper.appendChild(deleteBtn);

                dropdown.appendChild(wrapper);
            });
        }

        btnAddSection.addEventListener("click", () => {
            addSectionForm.classList.remove("hidden");
        });

        let kategoriKkId = null;

        document.getElementById("saveSectionBtn").addEventListener("click", async () => {

            const judul = document.getElementById("new_judul_section").value;

            if (!judul) {
                showErrorToast("Judul section wajib diisi");
                return;
            }

            await fetch(`{{ url('api/section') }}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },

                body: JSON.stringify({
                    kategori_id: kategoriKkId,
                    judul_section: judul
                })
            });

            addSectionForm.classList.add("hidden");
            document.getElementById("new_judul_section").value = "";

            await loadSection();
        });

        function setDropdownLabel(dropdownId, value, defaultLabel) {

            const dropdown = document.getElementById(dropdownId);
            if (!dropdown) return;

            const label = dropdown.querySelector('.dropdown-selected');
            if (!label) return;

            label.textContent = value ?? defaultLabel;
        }

        const formModel = {
            id: "",
            section_id: "",
            pertanyaan: "",
            keterangan: "",
            is_required: "",
            jenis_jawaban: "",
            opsi_jawaban: "",
            opsi_lain: ""
        };

        window.setFormData = async (item) => {
            if (item) {
                document.getElementById('pertanyaan').value = item.pertanyaan ?? '';
                document.getElementById('keterangan').value = item.keterangan ?? '';
                isRequiredInput.checked = item.is_required ? true : false;
                opsiLainInput.checked = item.opsi_lain ? true : false;
                document.getElementById('section_id').value = item.section_id ?? '';

                if (!sectionData.length) {
                    await loadSection();
                }

                const sec = sectionData.find(k => k.id == item.section_id);

                if (sec) {
                    setDropdownLabel('sectionDropdown', sec.judul_section, 'Pilih Section');
                    enableFormFields();
                }

                const jenisMap = {
                    radio: "Radio",
                    checkbox: "Checkbox",
                    select: "Dropdown",
                    text: "Jawaban Pendek",
                    textarea: "Jawaban Panjang",
                    date: "Date"
                };

                const jenisLabel = jenisMap[item.jenis_jawaban];

                if (jenisLabel) {
                    setDropdownLabel('jenisDropdown', jenisLabel, 'Pilih Jenis Jawaban');
                    document.getElementById('jenis_jawaban').value = item.jenis_jawaban;
                }

                opsiTableBody.innerHTML = "";

                const allowOtherContainer = document.getElementById("allowOtherContainer");

                if (["radio", "checkbox", "select"].includes(item.jenis_jawaban)) {
                    opsiContainer.classList.remove("hidden");

                    if (["radio", "checkbox"].includes(item.jenis_jawaban)) {
                        allowOtherContainer.classList.remove("hidden");
                    } else {
                        allowOtherContainer.classList.add("hidden");
                    }

                    if (item.opsi_jawaban && item.opsi_jawaban.length) {
                        item.opsi_jawaban.forEach(opt => {
                            addOpsiRow(opt);
                        });
                    } else {
                        addOpsiRow();
                    }
                } else {
                    opsiContainer.classList.add("hidden");
                    allowOtherContainer.classList.add("hidden");
                }
            } else {
                formEditPertanyaan.reset();

                formModel.id = "";
                formModel.section_id = "";
                formModel.pertanyaan = "";
                formModel.keterangan = "";
                formModel.is_required = "";
                formModel.jenis_jawaban = "";
                formModel.opsi_jawaban = "";
                formModel.opsi_lain = "";

                disableFormFields();

                setDropdownLabel('sectionDropdown', null, 'Pilih Section');
                document.getElementById('section_id').value = '';
            }
        };

        document.addEventListener('DOMContentLoaded', async () => {
            await loadKategori();
            await loadSection();
        });
    </script>