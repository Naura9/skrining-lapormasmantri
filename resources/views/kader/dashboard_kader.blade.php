@extends('layouts.main')

@section('title', 'Dashboard Kader')

@section('content')
<div class="flex flex-col gap-4 w-full max-w-4xl mx-auto text-base py-5">
    <div id="kaderCards" class="grid grid-cols-1 md:grid-cols-2 gap-6"></div>

    <div id="detailTableContainer" class="mt-3 hidden overflow-x-auto">
        <table id="detailTable" class="min-w-[700px] w-full border-collapse table-auto text-left">
            <thead class="bg-gray-100" id="detailTableHead"></thead>
            <tbody id="detailTableBody"></tbody>
        </table>
    </div>
</div>

<div id="skriningDetailModal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50 px-4">
    <div class="bg-white rounded-xl shadow-lg 
                w-full sm:w-11/12 md:w-10/12 lg:w-5/12 xl:w-4/12 
                max-w-3xl flex flex-col relative max-h-[90vh]">
        <div class="w-full py-3 px-4">
            <h2 class="text-lg font-semibold">Detail</h2>
        </div>

        <div id="skriningDetailBody" class="overflow-auto flex-1"></div>

        <div class="flex justify-center mt-4 mb-2 sm:mt-6 px-4">
            <button
                id="closeModalBtn"
                class="w-full bg-[#61359C] text-white text-sm font-semibold py-2 rounded-lg hover:bg-[#61359C]/80 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById('kaderCards');
        const tableContainer = document.getElementById('detailTableContainer');
        const tableHead = document.getElementById('detailTableHead');
        const tableBody = document.getElementById('detailTableBody');

        async function fetchKader() {
            try {
                const result = await fetchWithAuth("{{ url('api/monitoring/kader') }}");

                if (!result.status) {
                    container.innerHTML = '<p class="text-red-500">Gagal memuat data kader.</p>';
                    return;
                }

                const data = result.data;
                container.innerHTML = '';

                data.forEach(kader => {
                    const cardKK = document.createElement('div');
                    cardKK.className = `bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 flex flex-col relative overflow-hidden mb-4`;
                    cardKK.innerHTML = `
                        <div class="absolute left-1/2 top-1/5 h-3/5 transform -translate-x-1/2 border-l-2 border-gray-300"></div>
                        <div class="flex w-full z-10 p-4">
                            <div class="w-1/2 flex items-center justify-center">
                                <div class="bg-blue-100 text-blue-600 rounded-full p-4 text-5xl shadow-inner">
                                    <i class="fas fa-people-roof"></i>
                                </div>
                            </div>
                            <div class="w-1/2 flex flex-col items-center justify-center">
                                <div class="text-4xl font-bold text-blue-700">${kader.jumlah_skrining_kk}</div>
                                <div class="text-gray-500 font-semibold mb-3 text-lg">Skrining KK</div>
                                <button class="detailBtn inline-flex items-center text-sm text-blue-600 hover:text-white hover:bg-blue-600 rounded-full px-3 py-1 transition-colors">
                                    Detail <i class="fas fa-chevron-down ml-2"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    container.appendChild(cardKK);

                    const cardNIK = document.createElement('div');
                    cardNIK.className = `bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 flex flex-col relative overflow-hidden mb-4`;
                    cardNIK.innerHTML = `
                        <div class="absolute left-1/2 top-1/5 h-3/5 transform -translate-x-1/2 border-l-2 border-gray-300"></div>
                        <div class="flex w-full z-10 p-4">
                            <div class="w-1/2 flex items-center justify-center">
                                <div class="bg-yellow-100 text-yellow-400 rounded-full p-4 text-5xl shadow-inner">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="w-1/2 flex flex-col items-center justify-center">
                                <div class="text-4xl font-bold text-yellow-600">${kader.jumlah_skrining_nik}</div>
                                <div class="text-gray-500 font-semibold mb-3 text-lg">Skrining NIK</div>
                                <button class="detailBtn inline-flex items-center text-sm text-yellow-600 hover:text-white hover:bg-yellow-600 rounded-full px-3 py-1 transition-colors">
                                    Detail <i class="fas fa-chevron-down ml-2"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    container.appendChild(cardNIK);

                    cardKK.querySelector('.detailBtn').addEventListener('click', () => {
                        renderTable('kk', kader);
                    });

                    cardNIK.querySelector('.detailBtn').addEventListener('click', () => {
                        renderTable('nik', kader);
                    });
                });

            } catch (error) {
                console.error(error);
                container.innerHTML = '<p class="text-red-500">Terjadi kesalahan saat memuat data kader.</p>';
            }
        }

        function renderTable(type, kader) {
            tableContainer.classList.remove('hidden');
            tableBody.innerHTML = '';

            tableContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            if (type === 'kk') {
                tableHead.innerHTML = `
                    <tr class="bg-blue-700 text-white text-center text-sm">
                        <th class="p-2 border border-[#00000033] w-[15%]">Tanggal</th>
                        <th class="p-2 border border-[#00000033] w-[35%]">Alamat</th>
                        <th class="p-2 border border-[#00000033] w-[10%]">RT/RW</th>
                        <th class="p-2 border border-[#00000033] w-[30%]">Kepala Keluarga</th>
                        <th class="p-2 border border-[#00000033] w-[10%]">Aksi</th>
                    </tr>
                `;

                if (!kader.detail.length) {
                    const row = document.createElement('tr');
                    row.className = "bg-white";
                    row.innerHTML = `<td class="p-2 border border-[#00000033] text-center text-sm" colspan="6">Data tidak tersedia</td>`;
                    tableBody.appendChild(row);
                } else {
                    kader.detail.forEach((kel, idx) => {
                        const row = document.createElement('tr');
                        row.className = "bg-white";
                        row.innerHTML = `
                            <td class="p-2 border border-[#00000033] text-sm text-center">${kel.tanggal_skrining_terakhir ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">${kel.alamat ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">${kel.rt ?? '-'}/${kel.rw ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">${kel.kepala_keluarga ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">
                                <button class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail">Detail</button>
                            </td>
                        `;
                        tableBody.appendChild(row);

                        row.querySelector('.open-detail').addEventListener('click', () => {
                            const htmlContent = `
                            <div class="px-4 py-2 w-full space-y-1 text-sm">
                                <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                    <p class="font-medium">Tanggal Skrining</p>
                                    <p class="font-medium text-left">:</p>
                                    <p class="text-left">${kel.tanggal_skrining_terakhir ?? '-'}</p>
                                </div>

                                <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                    <p class="font-medium">No KK</p>
                                    <p class="font-medium text-left">:</p>
                                    <p class="text-left">${kel.no_kk ?? '-'}</p>
                                </div>

                                <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                    <p class="font-medium">Kepala Keluarga</p>
                                    <p class="font-medium text-left">:</p>
                                    <p class="text-left">${kel.kepala_keluarga ?? '-'}</p>
                                </div>

                                <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                    <p class="font-medium">Alamat</p>
                                    <p class="font-medium text-left">:</p>
                                    <p class="text-left">${kel.alamat ?? '-'}</p>
                                </div>

                                <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                    <p class="font-medium">RT / RW</p>
                                    <p class="font-medium text-left">:</p>
                                    <p class="text-left">${kel.rt ?? '-'} / ${kel.rw ?? '-'}</p>
                                </div>
                            </div>
                        `;

                            openSkriningModal(`Detail KK ${kel.no_kk ?? '-'}`, htmlContent);
                        });
                    });
                }

            } else if (type === 'nik') {
                tableHead.innerHTML = `
                    <tr class="bg-yellow-500 text-white text-center text-sm">
                        <th class="p-2 border border-[#00000033] w-[15%]">Tanggal</th>
                        <th class="p-2 border border-[#00000033] w-[20%]">NIK</th>
                        <th class="p-2 border border-[#00000033] w-[30%]">Nama Lengkap</th>
                        <th class="p-2 border border-[#00000033] w-[15%]">Siklus</th>
                        <th class="p-2 border border-[#00000033] w-[20%]">Aksi</th>
                    </tr>
                `;

                const anggotaSorted = kader.detail
                    .flatMap(kel => kel.anggota.map(agt => ({
                        ...agt,
                        tanggal: kel.tanggal_skrining_terakhir
                    })))
                    .sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

                if (!anggotaSorted.length) {
                    const row = document.createElement('tr');
                    row.className = "bg-white";
                    row.innerHTML = `<td class="p-2 border border-[#00000033] text-center text-sm" colspan="5">Data tidak tersedia</td>`;
                    tableBody.appendChild(row);
                } else {
                    anggotaSorted.forEach((agt, idx) => {
                        const row = document.createElement('tr');
                        row.className = "bg-white";
                        row.innerHTML = `
                            <td class="p-2 border border-[#00000033] text-sm text-center">${agt.tanggal ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">${agt.nik ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">${agt.nama ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">${agt.siklus ?? '-'}</td>
                            <td class="p-2 border border-[#00000033] text-sm text-center">
                                <button class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail">Detail</button>
                            </td>
                        `;
                        tableBody.appendChild(row);

                        row.querySelector('.open-detail').addEventListener('click', () => {
                            const htmlContent = `
                                <div class="px-4 py-2 w-full space-y-1 text-sm">
                                    <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                        <p class="font-medium">Siklus</p>
                                        <p class="font-medium text-left">:</p>
                                        <p class="text-left">${agt.siklus ?? '-'}</p>
                                    </div>
                                    <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                        <p class="font-medium">NIK</p>
                                        <p class="font-medium text-left">:</p>
                                        <p class="text-left">${agt.nik ?? '-'}</p>
                                    </div>
                                    <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                        <p class="font-medium">Nama Lengkap</p>
                                        <p class="font-medium text-left">:</p>
                                        <p class="text-left">${agt.nama ?? '-'}</p>
                                    </div>
                                    <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                        <p class="font-medium">Jenis Kelamin</p>
                                        <p class="font-medium text-left">:</p>
                                        <p class="text-left">${agt.jenis_kelamin ?? '-'}</p>
                                    </div>
                                    <div class="grid grid-cols-[130px_10px_1fr] items-start">
                                        <p class="font-medium">Hubungan Keluarga</p>
                                        <p class="font-medium text-left">:</p>
                                        <p class="text-left">${agt.hubungan_keluarga ?? '-'}</p>
                                    </div>
                                </div>
                            `;
                            openSkriningModal(`Detail ${agt.nama ?? '-'}`, htmlContent);
                        });
                    });
                }
            }
        }

        const skriningModal = document.getElementById('skriningDetailModal');
        const skriningModalBody = document.getElementById('skriningDetailBody');
        const closeModalBtn = document.getElementById('closeModalBtn');

        closeModalBtn.addEventListener("click", () => {
            skriningModal.classList.add("hidden");
            skriningModal.classList.remove("flex");
            document.body.style.overflow = "";
        });

        function openSkriningModal(title, htmlContent) {
            skriningModalBody.innerHTML = htmlContent;
            skriningModal.classList.remove('hidden');
            skriningModal.classList.add('flex');
        }

        fetchKader();
    });
</script>
@endsection