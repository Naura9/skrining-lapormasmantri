@extends('layouts.main')

@section('title', 'Riwayat Skrining')

@section('content')
<section class="p-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Riwayat Skrining</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-center gap-4 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <input id="searchInput" type="text"
                placeholder="Cari berdasarkan nama, No KK, atau NIK..."
                class="h-9 bg-white border border-[#00000033] rounded-lg px-3 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 w-full sm:w-75">

            <button id="searchBtn"
                class="h-9 flex items-center justify-center bg-[#61359C] text-white
                   border border-[#00000033] px-3 rounded-lg text-sm
                   hover:bg-[#61359C]/80 transition w-full sm:w-auto">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[12%]">Tanggal</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[38%] break-words">Alamat</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%]">RT/RW</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%]">Jumlah KK</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%]">Luar Wilayah</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[10%]">Aksi</th>
                </tr>
            </thead>
            <tbody id="hasilTableBody"></tbody>
        </table>
    </div>

    <div id="modal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-11/12 sm:w-10/12 md:w-10/12 lg:w-9/12 xl:w-8/12 relative py-2">
            <div class="px-4 py-2">
                <h2 class="text-lg font-bold">
                    Detail Hasil Skrining
                </h2>
            </div>

            <div class="px-4 py-3 max-h-[70vh] overflow-y-auto">
                <div id="modal-detail-body" class="space-y-3 text-sm"></div>
            </div>

            <div class="flex justify-center p-3">
                <button id="closeModalBtn"
                    class="bg-[#61359C] text-white text-sm font-semibold w-full py-1 rounded hover:bg-[#61359C]/80 transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("hasilTableBody");

        const hasilModalRef = document.getElementById("hasilModalRef");
        const hasilModalTitle = document.getElementById("hasilModalTitle");

        const formEdit = document.getElementById("formEdit");

        async function fetchHasil() {
            try {
                const result = await fetchWithAuth(`{{ url('api/monitoring/hasil-skrining') }}`);

                if (result?.status_code && result.status_code !== 200) return;

                const hasil = result.data || [];
                renderTable(hasil);

            } catch (error) {
                console.error("Gagal memuat data hasil skrining:", error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-gray-500 py-4">Tidak ada data hasil skrining.</td></tr>`;
                return;
            }

            list.reverse();

            list.forEach((item, i) => {
                item.unit_rumah?.forEach((unit, index) => {
                    const tanggal = unit.tanggal_skrining ?? "-";
                    const jumlahKK = unit.jumlah_kk ?? 0;

                    let jumlahNIK = 0;

                    unit.keluarga?.forEach(kk => {
                        kk.skrining?.forEach(skr => {
                            if (skr.target_skrining === "nik") {
                                jumlahNIK += skr.anggota?.length || 0;
                            }
                        });
                    });

                    const tr = document.createElement("tr");

                    tr.innerHTML = `
                        <td class="border border-[#00000033] px-3 py-2 text-center">${formatTanggal(tanggal)}</td>
                        <td class="border border-[#00000033] px-3 py-2 max-w-xs break-words">${unit.alamat_unit ?? "-"}</td>
                        <td class="border border-[#00000033] px-3 py-2 text-center">${unit.rt_unit ?? "-"}/${unit.rw_unit ?? "-"}</td>
                        <td class="border border-[#00000033] px-3 py-2 text-center">${unit.jumlah_kk ?? "-"}</td>
                        <td class="border border-[#00000033] px-3 py-2 text-center">${unit.jumlah_kk_luar_wilayah ?? 0}</td>
                        <td class="border border-[#00000033] px-3 py-2 text-center">
                            <div class="flex justify-center gap-1">
                                <button
                                    class="px-2 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail"
                                    data-index="${i}" data-unit="${index}">
                                    Detail
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                })
            });

            document.querySelectorAll(".open-detail").forEach(btn => {
                btn.addEventListener("click", () => {
                    const kaderIndex = btn.dataset.index;
                    const unitIndex = btn.dataset.unit;

                    const item = list[kaderIndex];
                    const unit = item.unit_rumah[unitIndex];
                    if (!item) return;

                    const detailBody = document.getElementById("modal-detail-body");

                    const tanggal = unit.tanggal_skrining ?? '-';

                    detailBody.innerHTML = `
                        <div class="space-y-2 text-sm">
                            <div class="grid grid-cols-[120px_1fr]">
                                <span class="font-semibold">Tanggal Skrining</span>
                                <span>: ${formatTanggal(tanggal)}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 mt-3 gap-y-1">
                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Nama Kader</span>
                                    <span>: ${item.nama_kader}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Jumlah KK</span>
                                    <span>: ${unit.keluarga?.length ?? 0}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Kelurahan</span>
                                    <span>: ${unit.kelurahan ?? '-'}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Alamat</span>
                                    <span>: ${unit.alamat_unit ?? '-'}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">Posyandu</span>
                                    <span>: ${unit.posyandu ?? '-'}</span>
                                </div>

                                <div class="grid grid-cols-[120px_1fr]">
                                    <span class="font-semibold">RT / RW</span>
                                    <span>: ${unit.rt_unit ?? '-'} / ${unit.rw_unit ?? '-'}</span>
                                </div>
                            </div>
                            <div class="border-t border-gray-300 my-2"></div>
                        </div>
                    `;
                    const firstKK = unit.keluarga?.[0];
                    let kkTableRows = "";

                    if (!firstKK?.skrining || !firstKK.skrining.some(skr => skr.target_skrining === "kk")) {
                        kkTableRows = `
                            <tr>
                                <td colspan="3" class="text-center px-3 py-2 border border-[#00000033]">
                                    Belum melakukan skrining KK
                                </td>
                            </tr>
                        `;
                    } else {
                        firstKK.skrining.forEach(skr => {
                            if (skr.target_skrining !== "kk") return;

                            let lastSection = null;

                            skr.pertanyaan?.forEach((p, i) => {

                                if (p.section !== lastSection) {
                                    kkTableRows += `
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-3 py-2 font-semibold border-t">
                                            ${p.section ?? "-"}
                                        </td>
                                    </tr>
                                `;
                                    lastSection = p.section;
                                }

                                kkTableRows += `
                                <tr>
                                    <td class="border border-[#00000033] px-3 py-2 text-center w-[40px]">${i + 1}</td>
                                    <td class="border border-[#00000033] px-3 py-2">${p.pertanyaan ?? "-"}</td>
                                    <td class="border border-[#00000033] px-3 py-2">${p.jawaban ?? "-"}</td>
                                </tr>
                            `;
                            });
                        });
                    };
                    detailBody.innerHTML += `
                        <div class="overflow-hidden mt-3">
                            <div class="flex items-center justify-between cursor-pointer toggle-rumah
                                font-semibold text-[#61359C] bg-[#61359C]/22 px-2 py-1 rounded-lg"
                                data-target="skrining-rumah">
                                <span>Hasil Skrining KK</span>
                                <i class="fa-solid fa-chevron-down transition-transform duration-200"></i>
                            </div>

                            <div id="skrining-rumah" class="hidden mt-2">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm border border-[#00000033] rounded-lg">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 border border-[#00000033] w-[40px]">No</th>
                                                <th class="px-3 py-2 border border-[#00000033]">Pertanyaan</th>
                                                <th class="px-3 py-2 border border-[#00000033]">Jawaban</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${kkTableRows}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        `;

                    unit.keluarga?.forEach((kk, index) => {

                        let skriningHtml = "";

                        kk.skrining?.forEach((skr, sIndex) => {
                            if (skr.target_skrining === "nik") {

                                let anggotaHtml = "";

                                skr.anggota?.forEach((agt, aIndex) => {

                                    let nikRows = "";
                                    let lastSection = null;

                                    agt.pertanyaan?.forEach((p, i) => {

                                        if (p.section !== lastSection) {
                                            nikRows += `
                                                <tr class="bg-gray-50">
                                                    <td colspan="3" class="px-3 py-2 font-semibold border-t">
                                                        ${p.section ?? "-"}
                                                    </td>
                                                </tr>
                                            `;
                                            lastSection = p.section;
                                        }

                                        nikRows += `
                                        <tr>
                                            <td class="border border-[#00000033] px-3 py-2 text-center w-[40px]">${i + 1}</td>
                                            <td class="border border-[#00000033] px-3 py-2">${p.pertanyaan ?? "-"}</td>
                                            <td class="border border-[#00000033] px-3 py-2">${p.jawaban ?? "-"}</td>
                                        </tr>`;
                                    });

                                    const detailAgt = kk.anggota?.find(a => a.id === agt.id);
                                    anggotaHtml += `
                                        <div class="px-2 mt-2">
                                            <div class="flex items-center justify-between cursor-pointer
                                                        font-semibold text-[#61359C] bg-[#61359C]/5 px-2 py-1 rounded-lg"
                                                data-target="agt-${index}-${sIndex}-${aIndex}">
                                                ${agt.nama}
                                                <i class="fa-solid fa-chevron-down transition-transform duration-200"></i>
                                            </div>
                                                    
                                            <div id="agt-${index}-${sIndex}-${aIndex}" class="hidden">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 text-sm mt-2 mb-2 bg-gray-50 p-2 rounded">
                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">NIK</span>
                                                        <span>: ${detailAgt?.nik ?? '-'}</span>
                                                    </div>

                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">Jenis Kelamin</span>
                                                        <span>: ${detailAgt?.jenis_kelamin ?? '-'}</span>
                                                    </div>

                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">Tanggal Lahir</span>
                                                        <span>: ${detailAgt?.tanggal_lahir ?? '-'}</span>
                                                    </div>

                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">Pekerjaan</span>
                                                        <span>: ${detailAgt?.pekerjaan ?? '-'}</span>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">Tempat Lahir</span>
                                                        <span>: ${detailAgt?.tempat_lahir ?? '-'}</span>
                                                    </div>

                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">Status Perkawinan</span>
                                                        <span>: ${detailAgt?.status_perkawinan ?? '-'}</span>
                                                    </div>

                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">Hubungan Keluarga</span>
                                                        <span>: ${detailAgt?.hubungan_keluarga ?? '-'}</span>
                                                    </div>

                                                    <div class="grid grid-cols-[125px_1fr]">
                                                        <span class="font-semibold">Pendidikan Terakhir</span>
                                                        <span>: ${detailAgt?.pendidikan_terakhir ?? '-'}</span>
                                                    </div>
                                                </div>
                                        
                                                <div class="overflow-x-auto mt-2">
                                                    <table class="min-w-full text-sm border border-[#00000033] rounded-lg">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-3 py-2 border border-[#00000033] w-[40px]">No</th>
                                                                <th class="px-3 py-2 border border-[#00000033]">Pertanyaan</th>
                                                                <th class="px-3 py-2 border border-[#00000033]">Jawaban</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            ${nikRows}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>`;
                                });

                                skriningHtml += `
                                    <div class="mt-3">
                                        <div class="flex items-center justify-between cursor-pointer
                                                        font-semibold text-[#61359C] bg-[#61359C]/12 px-2 py-1 rounded-lg"
                                            data-target="siklus-${index}-${sIndex}">
                                            ${skr.siklus}
                                            <i class="fa-solid fa-chevron-down transition-transform duration-200"></i>
                                        </div>

                                        <div id="siklus-${index}-${sIndex}" class="hidden">
                                            ${anggotaHtml}
                                        </div>

                                    </div>`;
                            }
                        });

                        detailBody.innerHTML += `
                            <div class="overflow-hidden mt-2">
                                <div class="flex items-center justify-between cursor-pointer
                                    font-semibold text-[#61359C] bg-[#61359C]/22 px-2 py-1 rounded-lg"
                                    data-target="kk-${index}">
                                    
                                    <div class="flex items-center justify-between w-full">
                                        <span>KK ${index + 1}</span>

                                        ${kk.is_luar_wilayah 
                                            ? `<span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-0.5 rounded-full mr-3">
                                                Luar Wilayah
                                            </span>` 
                                            : ''}
                                    </div>

                                    <i class="fa-solid fa-chevron-down transition-transform duration-200"></i>
                                </div>

                                <div id="kk-${index}" class="hidden mt-2 space-y-3 px-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 mt-2 gap-y-1">
                                        <div class="grid grid-cols-[120px_1fr]">
                                            <span class="font-semibold">No KK</span>
                                            <span>: ${kk.no_kk}</span>
                                        </div>

                                        <div class="grid grid-cols-[120px_1fr]">
                                            <span class="font-semibold">Total Anggota</span>
                                            <span>: ${kk.anggota?.length ?? 0}</span>
                                        </div>

                                        <div class="grid grid-cols-[120px_1fr]">
                                            <span class="font-semibold">Kepala Keluarga</span>
                                            <span>: ${kk.kepala_keluarga}</span>
                                        </div>

                                        <div class="grid grid-cols-[120px_1fr]">
                                            <span class="font-semibold">No Telepon</span>
                                            <span>: ${kk.no_telepon ?? '-'}</span>
                                        </div>

                                        ${kk.is_luar_wilayah ? `
                                            <div class="grid grid-cols-[120px_1fr]">
                                                <span class="font-semibold">Alamat KTP</span>
                                                <span>: ${kk.alamat ?? '-'}</span>
                                            </div>

                                            <div class="grid grid-cols-[120px_1fr]">
                                                <span class="font-semibold">RT / RW KTP</span>
                                                <span>: ${kk.rt ?? '-'} / ${kk.rw ?? '-'}</span>
                                            </div>
                                        ` : ''}
                                    </div>
                                    ${skriningHtml}
                                </div>
                            </div>
                        `;
                    });

                    setTimeout(() => {
                        document.querySelectorAll("[data-target]").forEach(btn => {
                            btn.onclick = () => {
                                const el = document.getElementById(btn.dataset.target);
                                const icon = btn.querySelector("i");

                                if (el) el.classList.toggle("hidden");
                                if (icon) icon.classList.toggle("rotate-180");
                            };
                        });
                    }, 50);

                    document.getElementById("modal").classList.remove("hidden");
                    document.getElementById("modal").classList.add("flex");
                });
            });
        }

        function formatTanggal(tgl) {
            if (!tgl) return "-";

            const d = new Date(tgl);

            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();

            return `${day}/${month}/${year}`;
        }

        document.getElementById("closeModalBtn").onclick = () => {
            document.getElementById("modal").classList.add("hidden");
        };

        function setDropdownLabel(id, text, fallback) {
            const el = document.getElementById(id);
            if (!el) return;

            const label = el.querySelector('.dropdown-selected');
            if (label) label.textContent = text || fallback;
        }

        document.getElementById("searchBtn").addEventListener("click", () => {
            fetchHasilWithFilter();
        });

        document.getElementById("searchInput").addEventListener("keyup", (e) => {
            if (e.key === "Enter") fetchHasilWithFilter();
        });

        async function fetchHasilWithFilter() {
            const search = document.getElementById("searchInput").value || "";

            try {
                const url = new URL("{{ url('api/monitoring/hasil-skrining') }}", window.location.origin);

                if (search) url.searchParams.append("search", search);

                const result = await fetchWithAuth(url.toString());

                if (result?.status_code && result.status_code !== 200) return;

                renderTable(result.data || []);

            } catch (err) {
                console.error("Gagal memuat data:", err);
            }
        }

        fetchHasil();
    });
</script>
@endsection