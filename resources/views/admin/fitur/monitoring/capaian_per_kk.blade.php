@extends('layouts.main')

@section('title', 'Capaian per KK')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-11 text-center sm:text-left">Capaian per KK</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[25%]">Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Input KK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Target</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[20%]">Persen Capaian</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[15%]">Aksi</th>
                </tr>
            </thead>
            <tbody id="kkTableBody"></tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold">
                    <td class="border border-[#00000033] px-3 py-2">
                        Grand Total
                    </td>
                    <td id="grandJumlahKk" class="border border-[#00000033] px-3 py-2 text-center">
                        0
                    </td>
                    <td id="grandTarget" class="border border-[#00000033] px-3 py-2 text-center">
                        0
                    </td>
                    <td id="grandPersentase" class="border border-[#00000033] px-3 py-2 text-center">
                        0%
                    </td>
                    <td class="border border-[#00000033] px-3 py-2 text-center"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <x-modal id="targetModalRef" size="md">
        <x-slot name="title">
            <h3 class="text-lg font-bold">Edit Target KK</h3>
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
        const tbody = document.getElementById("kkTableBody");

        const kaderModalRef = document.getElementById("kaderModalRef");
        const kaderModalTitle = document.getElementById("kaderModalTitle");

        async function fetchKK() {
            const result = await fetchWithAuth(`{{ url('api/monitoring/capaian-per-kk') }}`, {
                method: "GET",
                headers: {
                    "Accept": "application/json"
                }
            });

            if (!result || result.status_code) return;

            renderTable(result.data || []);

            if (result.grand_total) {
                document.getElementById('grandJumlahKk').textContent =
                    result.grand_total.jumlah_kk ?? 0;

                document.getElementById('grandTarget').textContent =
                    result.grand_total.target ?? 0;

                document.getElementById('grandPersentase').textContent =
                    (result.grand_total.persentase ?? 0) + '%';
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-gray-500 py-4">
                        Tidak ada data.
                    </td>
                </tr>`;
                return;
            }

            list.forEach((item, index) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] px-3 py-2">${item.nama_kelurahan ?? '-'}</td>
                    <td class="border border-[#00000033] px-3 py-2 text-center">${item.jumlah_kk}</td>
                    <td class="border border-[#00000033] px-3 py-2 text-center">${item.target}</td>
                    <td class="border border-[#00000033] px-3 py-2 text-center">
                        ${item.persentase}%
                    </td>
                    <td class="border border-[#00000033] px-2 py-2 text-center">
                        <button
                            class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded bg-yellow-500 text-white hover:bg-yellow-600"
                            onclick="openTargetModal('${item.kelurahan_id}', '${item.nama_kelurahan}')">

                            <i class="fa fa-edit"></i>
                            <span>Target</span>
                        </button>
                    </td>
                `;

                tbody.appendChild(tr);
            });
        }

        let KATEGORI_ID = null;

        async function loadKategoriKK() {
            const res = await fetchWithAuth("{{ url('api/kategori') }}");

            const list = res?.data?.list || [];

            const kk = list.find(item => item.target_skrining === 'kk');
            if (!kk) {
                console.error("Kategori KK tidak ditemukan");
                return;
            }

            KATEGORI_ID = kk.id;
        }

        const targetModalRef = document.getElementById('targetModalRef');

        window.openTargetModal = async function(kelurahanId, namaKelurahan) {

            if (!KATEGORI_ID) {
                console.error("Kategori belum siap, tunggu sebentar atau refresh");
                return;
            }

            document.getElementById('kategori_id').value = KATEGORI_ID;
            document.getElementById('kelurahan_id').value = kelurahanId;
            document.getElementById('nama_kelurahan').value = namaKelurahan;

            const result = await fetchWithAuth(
                `{{ url('api/target/kelurahan') }}/${kelurahanId}/${KATEGORI_ID}`
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
                    KATEGORI_ID
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

                fetchKK();
            });

        document.getElementById("closeTargetModal").addEventListener("click", () => {
            targetModalRef.classList.add("hidden");
            targetModalRef.classList.remove("flex");
        });

        fetchKK();
        loadKategoriKK();
    });
</script>
@endsection