<form id="formEdit" class="space-y-4">
    <div class="text-left">
        <label for="nama_kelurahan" class="block text-sm font-semibold mb-1">
            Nama Kelurahan
        </label>
        <input type="text" id="nama_kelurahan" name="nama_kelurahan"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan nama kelurahan">
        <p class="text-red-500 text-xs mt-1 hidden" id="error-nama_kelurahan"></p>
    </div>

    <div class="text-left">
        <label class="block text-sm font-semibold mb-2">
            Posyandu
        </label>

        <div id="posyanduWrapper" class="space-y-2"></div>

        <div class="flex justify-end mt-2">
            <button
                type="button" id="addPosyanduBtn" class="inline-flex items-center gap-2 text-sm text-[#61359C] hover:underline">
                <span class="text-lg font-bold">+</span>
                Tambah Posyandu
            </button>
        </div>
    </div>
</form>

<script>
    let deletedPosyanduIds = [];

    let posyanduInitialized = false;

    function initPosyanduForm() {
        if (posyanduInitialized) return;
        posyanduInitialized = true;

        const posyanduWrapper = document.getElementById("posyanduWrapper");
        const addPosyanduBtn = document.getElementById("addPosyanduBtn");
        const formEdit = document.getElementById("formEdit");

        let posyanduIndex = 0;

        function updateRemoveButtons() {
            const items = posyanduWrapper.querySelectorAll(".remove-posyandu");

            items.forEach(btn => btn.classList.remove("hidden"));

            if (items.length === 1) {
                items[0].classList.add("hidden");
            }
        }

        function createPosyanduInput(data = {}) {
            const div = document.createElement("div");
            div.className = "flex items-center gap-2";

            const index = posyanduIndex++;

            div.innerHTML = `
                <input type="hidden"
                    name="posyandu[${index}][id]"
                    value="${data.id ?? ''}"
                    class="posyandu-id">

                <input type="hidden"
                    name="posyandu[${index}][is_added]"
                    value="${data.id ? 0 : 1}"
                    class="posyandu-added">

                <input type="hidden"
                    name="posyandu[${index}][is_updated]"
                    value="0"
                    class="posyandu-updated">

                <input type="text"
                    name="posyandu[${index}][nama_posyandu]"
                    class="posyandu-name flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                    placeholder="Nama posyandu"
                    value="${data.nama_posyandu ?? ''}">

                <button type="button"
                    class="remove-posyandu text-red-600 hover:text-red-800 font-bold text-lg">
                    −
                </button>
            `;

            const nameInput = div.querySelector(".posyandu-name");
            const idInput = div.querySelector(".posyandu-id");
            const updatedInput = div.querySelector(".posyandu-updated");

            nameInput.addEventListener("input", () => {
                if (idInput.value) {
                    updatedInput.value = "1";
                }
            });

            div.querySelector(".remove-posyandu").onclick = () => {
                const idInput = div.querySelector(".posyandu-id");
                const id = idInput?.value;

                if (id) {
                    deletedPosyanduIds.push(id);
                }

                div.remove();
                updateRemoveButtons();
            };

            return div;
        }

        addPosyanduBtn.onclick = () => {
            posyanduWrapper.appendChild(createPosyanduInput());
            updateRemoveButtons();
        };

        window.setFormData = (item) => {
            posyanduWrapper.innerHTML = "";
            posyanduIndex = 0;

            if (item) {
                formEdit.querySelector('[name="nama_kelurahan"]').value = item.nama_kelurahan;
                (item.posyandu || []).forEach(p => {
                    posyanduWrapper.appendChild(createPosyanduInput(p));
                });
            } else {
                formEdit.reset();
                posyanduWrapper.appendChild(createPosyanduInput());
            }

            updateRemoveButtons();
        };
    }
</script>