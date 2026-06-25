@extends('layouts.main')

@section('title', 'Capaian per NIK')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Capaian per NIK</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-center gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <x-dropdown
                id="siklusDropdown"
                label="Pilih Siklus"
                :options="[]"
                width="w-full sm:w-70 h-9"
                data-dropdown="filter" />

            <input type="hidden" id="siklus_id" value="">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700 table-fixed">
            <thead id="tableHead" class="bg-[#61359C] text-white text-center"></thead>
            <tbody id="nikTableBody"></tbody>
            <tfoot id="tableFoot"></tfoot>
        </table>
    </div>

    <x-modal id="targetModalRef" size="md">
        <x-slot name="title">
            <h3 class="text-lg font-bold">Edit Target NIK</h3>
        </x-slot>

        <form id="targetForm" class="space-y-4">
            <input type="hidden" id="target_id">
            <input type="hidden" id="kelurahan_id">
            <input type="hidden" id="kategori_id">

            <div>
                <label class="lock text-sm font-semibold mb-1">Kelurahan</label>
                <input
                    type="text"
                    id="nama_kelurahan"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                    readonly>
            </div>

            <div>
                <label class="lock text-sm font-semibold mb-1">Siklus</label>
                <input
                    type="text"
                    id="nama_kategori"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                    readonly>
            </div>

            <div>
                <label class="lock text-sm font-semibold mb-1">Target</label>
                <input
                    type="number"
                    id="target"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                        focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
            </div>
        </form>

        <x-slot name="footer">
            <button
                type="button"
                id="closeTargetModal"
                class="w-full px-4 py-2 bg-gray-400 text-white rounded-lg">
                Batal
            </button>

            <button
                type="submit"
                form="targetForm"
                class="w-full px-4 py-2 bg-[#61359C] text-white rounded-lg">
                Simpan
            </button>
        </x-slot>
    </x-modal>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let siklusData = [];

        async function loadSiklus() {
            try {
                const res = await fetchWithAuth("{{ url('api/kategori') }}", {
                    method: "GET",
                    headers: {
                        "Accept": "application/json"
                    }
                });

                const json = await res.json?.() ?? res;

                const allData = json.data.list || [];

                siklusData = allData
                    .filter(item => item.target_skrining?.toLowerCase() === 'nik')
                    .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                renderSiklusDropdown();

            } catch (error) {
                console.error('Gagal load siklus:', error);
            }
        }

        function renderSiklusDropdown() {
            const dropdown = document
                .getElementById('siklusDropdown')
                .querySelector('.dropdown-menu');

            dropdown.innerHTML = '';

            if (!siklusData.length) {
                dropdown.innerHTML = `
                <div class="px-4 py-2 text-sm text-gray-400 text-center">
                    Tidak ada data siklus
                </div>
            `;
                return;
            }

            siklusData.forEach(siklus => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item block w-full text-center px-4 py-1 text-sm hover:bg-gray-100';
                btn.textContent = siklus.nama_kategori;

                btn.onclick = () => {
                    setDropdownLabel('siklusDropdown', siklus.nama_kategori, 'Pilih Siklus');
                    document.getElementById('siklus_id').value = siklus.id;
                    document.getElementById('siklus_id').dataset.nama = siklus.nama_kategori;

                    setDropdownDisabled('kelurahanFilterDropdown', false);
                    setDropdownDisabled('urutDropdown', false);

                    resetTable()
                    fetchNIK();
                };

                dropdown.appendChild(btn);
            });
        }

        const tbody = document.getElementById("nikTableBody");

        const kaderModalRef = document.getElementById("kaderModalRef");
        const kaderModalTitle = document.getElementById("kaderModalTitle");

        async function fetchNIK() {
            const kategoriId = document.getElementById('siklus_id').value;

            if (!kategoriId) {
                resetTable();
                return;
            }

            const result = await fetchWithAuth(
                `{{ url('api/monitoring/capaian-per-nik') }}?kategori_id=${kategoriId}`
            );

            if (!result || result.status_code) return;

            renderHeader(result.data);
            renderTable(result.data);
            renderFooter(result.data);
        }

        function renderHeader(list) {
            const thead = document.getElementById('tableHead');

            if (!list.length) return;

            const kategoriList = list[0].kategori || [];
            let row1 = `
                <tr>
                    <th rowspan="2" class="border border-[#00000033] px-3 py-2 w-[180px] max-w-[180px]">
                        Kelurahan
                    </th>
            `;

            kategoriList.forEach(k => {
                row1 += `
                    <th colspan="3" class="border border-[#00000033] px-3 py-2">
                        ${k.nama_kategori}
                    </th>
                `;
            });

            row1 += `
                <th rowspan="2" class="border border-[#00000033] px-3 py-2 w-[100px]">
                    Aksi
                </th>
            `;

            row1 += `</tr>`;

            let row2 = `<tr>`;

            kategoriList.forEach(() => {
                row2 += `
                    <th class="border border-[#00000033] px-3 py-2 w-[120px]">Input NIK</th>
                    <th class="border border-[#00000033] px-3 py-2 w-[120px]">Target</th>
                    <th class="border border-[#00000033] px-3 py-2 w-[120px]">Persen Capaian</th>
                `;
            });

            row2 += `</tr>`;

            thead.innerHTML = row1 + row2;
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="100%" class="text-center py-10 text-gray-400">
                            Data tidak ditemukan
                        </td>
                    </tr>
                `;
                return;
            }


            list.forEach(item => {

                let html = `
                    <td class="border border-[#00000033] px-3 py-2">
                        ${item.nama_kelurahan}
                    </td>
                `;

                item.kategori.forEach(k => {
                    html += `
                        <td class="border border-[#00000033] px-3 py-2 text-center">
                            ${k.jumlah_nik}
                        </td>

                        <td class="border border-[#00000033] px-3 py-2 text-center">
                            ${k.target}
                        </td>

                        <td class="border border-[#00000033] px-3 py-2 text-center">
                            ${k.persentase}%
                        </td>

                        <td class="border border-[#00000033] px-2 py-2 text-center">
                            <button
                                class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded bg-yellow-500 text-white hover:bg-yellow-600"
                                onclick="openTargetModal(
                                    '${item.kelurahan_id}',
                                    '${k.kategori_id}',
                                    '${item.nama_kelurahan}',
                                    '${k.nama_kategori}'
                                )">

                                <i class="fa fa-edit"></i>
                                Target
                            </button>
                        </td>
                    `;
                });

                const tr = document.createElement("tr");
                tr.innerHTML = html;
                tbody.appendChild(tr);
            });
        }

        function renderFooter(list) {
            const tfoot = document.getElementById('tableFoot');
            if (!list.length) return;

            const kategoriList = list[0].kategori || [];

            let totalNIK = {};
            let totalTarget = {};

            kategoriList.forEach(k => {
                totalNIK[k.kategori_id] = 0;
                totalTarget[k.kategori_id] = 0;
            });

            list.forEach(item => {
                item.kategori.forEach(k => {
                    totalNIK[k.kategori_id] += Number(k.jumlah_nik);
                    totalTarget[k.kategori_id] += Number(k.target);
                });
            });

            let row = `
                <tr class="bg-gray-100 font-bold">
                    <td class="border border border-[#00000033] px-3 py-2">Grand Total</td>
            `;

            kategoriList.forEach(k => {
                const nik = totalNIK[k.kategori_id] || 0;
                const target = totalTarget[k.kategori_id] || 0;

                const persen = target > 0 ?
                    ((nik / target) * 100).toFixed(2) :
                    0;

                row += `
                    <td class="border border border-[#00000033] px-3 py-2 text-center">${nik}</td>
                    <td class="border border border-[#00000033] px-3 py-2 text-center">${target}</td>
                    <td class="border border border-[#00000033] px-3 py-2 text-center">${persen}%</td>
                    <td class="border border border-[#00000033] px-3 py-2 text-center"></td>
                `;
            });

            row += `</tr>`;

            tfoot.innerHTML = row;
        }

        const targetModalRef = document.getElementById('targetModalRef');

        window.openTargetModal = async function(kelurahanId, kategoriId, namaKelurahan, namaKategori) {
            document.getElementById('kategori_id').value = kategoriId;
            document.getElementById('kelurahan_id').value = kelurahanId;
            document.getElementById('nama_kelurahan').value = namaKelurahan;
            document.getElementById('nama_kategori').value = namaKategori;

            const result = await fetchWithAuth(
                `{{ url('api/target/kelurahan') }}/${kelurahanId}/${kategoriId}`
            );

            const data = result?.data ?? null;

            document.getElementById('target_id').value = data?.id ?? '';
            document.getElementById('target').value = data?.target ?? 0;

            targetModalRef.classList.remove('hidden');
            targetModalRef.classList.add('flex');
        };

        document.getElementById('targetForm')
            .addEventListener('submit', async function(e) {
                e.preventDefault();

                const id = document.getElementById('target_id').value;

                const formData = new FormData();

                formData.append(
                    'kelurahan_id',
                    document.getElementById('kelurahan_id').value
                );

                formData.append(
                    'target',
                    document.getElementById('target').value
                );

                formData.append(
                    'kategori_id',
                    document.getElementById('kategori_id').value
                );

                let url = "{{ url('api/target') }}";

                if (id) {
                    formData.append('id', id);
                    formData.append('_method', 'PUT');
                }

                const result = await fetchWithAuth(url, {
                    method: 'POST',
                    body: formData
                });

                if (result.status_code && result.status_code !== 200) {
                    showErrorToast(result.message || 'Gagal menyimpan');
                    return;
                }

                showSuccessToast('Target berhasil disimpan');

                targetModalRef.classList.add('hidden');
                targetModalRef.classList.remove('flex');

                fetchNIK();
            });

        document.getElementById("closeTargetModal").addEventListener("click", () => {
            targetModalRef.classList.add("hidden");
            targetModalRef.classList.remove("flex");
        });

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

        function setDropdownLabel(id, text, fallback) {
            const el = document.getElementById(id);
            if (!el) return;

            const label = el.querySelector('.dropdown-selected');
            if (label) label.textContent = text || fallback;
        }

        function resetTable() {
            document.getElementById('tableHead').innerHTML = '';
            document.getElementById('tableFoot').innerHTML = '';
            tbody.innerHTML = `
                <tr id="emptyState">
                    <td colspan="100%" class="text-center py-10 text-gray-600 italic border border-[#FAFAFA]">
                        Silakan pilih siklus terlebih dahulu
                    </td>
                </tr>
            `;
        }

        resetTable()
        loadSiklus();
    });
</script>
@endsection