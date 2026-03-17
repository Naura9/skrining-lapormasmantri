<?php

namespace App\Helpers\Monitoring;

use App\Helpers\Helper;
use App\Models\SkriningModel;
use App\Models\User\UserModel;

class MonitoringHelper extends Helper
{
    private $userModel;
    private $skriningModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->skriningModel = new SkriningModel();
    }

    public function monitoringKader(array $filter = [])
    {
        $query = $this->userModel
            ->with(['kaderDetail.posyandu.kelurahan'])
            ->where('role', 'kader');

        if (!empty($filter['search'])) {
            $search = $filter['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (!empty($filter['kelurahan_id'])) {
            $query->whereHas('kaderDetail.posyandu.kelurahan', function ($q) use ($filter) {
                $q->where('id', $filter['kelurahan_id']);
            });
        }

        if (!empty($filter['posyandu_id'])) {
            $query->whereHas('kaderDetail.posyandu', function ($q) use ($filter) {
                $q->where('id', $filter['posyandu_id']);
            });
        }

        $kaders = $query->get();

        $data = $kaders->map(function ($kader) {
            $skrining = $this->skriningModel
                ->with([
                    'keluarga.kepalaKeluarga',
                    'keluarga.anggota',
                    'jawaban.pertanyaan.section.kategori'
                ])
                ->where('user_id', $kader->id)
                ->get()
                ->groupBy('keluarga_id');

            $totalNik = 0;

            $keluarga = $skrining->map(function ($items) use (&$totalNik) {
                $skr = $items->first();

                $kategori = $items
                    ->flatMap(function ($s) {
                        return $s->jawaban
                            ->filter(function ($jawaban) {
                                return optional($jawaban->pertanyaan->section->kategori)->target_skrining === 'nik';
                            })
                            ->pluck('pertanyaan.section.kategori.nama_kategori');
                    })
                    ->filter()
                    ->unique()
                    ->values();

                $anggota = $skr->keluarga->anggota->map(function ($agt) use ($items, &$totalNik) {

                    $skriningNik = $items
                        ->flatMap(fn($s) => $s->jawaban)
                        ->firstWhere('anggota_keluarga_id', $agt->id);

                    $sudahSkrining = $skriningNik ? true : false;

                    if (!$sudahSkrining) {
                        return null;
                    }

                    $totalNik++;

                    return [
                        'nik' => $agt->nik,
                        'nama' => $agt->nama,
                        'siklus' => optional($skriningNik->pertanyaan->section->kategori)->nama_kategori,
                        'sudah_skrining' => true
                    ];
                })->filter()->values();

                return [
                    'no_kk' => $skr->keluarga->no_kk,
                    'kepala_keluarga' => optional($skr->keluarga->kepalaKeluarga)->nama,
                    'jumlah_skrining' => $items->count(),
                    'tanggal_skrining_terakhir' => $items->max('tanggal_skrining'),
                    'anggota' => $anggota
                ];
            })->values();

            return [
                'id' => $kader->id,
                'nama_kader' => $kader->name,
                'kelurahan' => optional($kader->kaderDetail->posyandu->kelurahan)->nama_kelurahan,
                'posyandu' => optional($kader->kaderDetail->posyandu)->nama_posyandu,

                'jumlah_skrining_kk' => $skrining->count(),
                'jumlah_skrining_nik' => $totalNik,

                'detail' => $keluarga
            ];
        });

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function monitoringNikPerKk(array $filter = [])
    {
        $query = $this->skriningModel
            ->with([
                'keluarga.unitRumah.posyandu.kelurahan',
                'keluarga.kepalaKeluarga',
                'keluarga.anggota',
                'jawaban.pertanyaan.section.kategori'
            ]);

        if (!empty($filter['kelurahan_id'])) {
            $query->whereHas('keluarga.unitRumah.posyandu', function ($q) use ($filter) {
                $q->where('kelurahan_id', $filter['kelurahan_id']);
            });
        }

        if (!empty($filter['posyandu_id'])) {
            $query->whereHas('keluarga.unitRumah', function ($q) use ($filter) {
                $q->where('posyandu_id', $filter['posyandu_id']);
            });
        }

        $skrining = $query->get()->groupBy(function ($item) {
            return optional($item->keluarga)->unit_rumah_id ?? 'luar_wilayah';
        });

        $data = $skrining->map(function ($unitItems) {

            $unit = optional($unitItems->first()->keluarga->unitRumah);

            $keluargaGroup = $unitItems->groupBy('keluarga_id');

            $keluargaList = $keluargaGroup->map(function ($items) {

                $skr = $items->first();
                $keluarga = $skr->keluarga;

                $anggota = $keluarga->anggota->map(function ($agt) use ($items) {
                    $skriningNik = $items->flatMap(fn($s) => $s->jawaban)
                        ->firstWhere('anggota_keluarga_id', $agt->id);

                    if (!$skriningNik) {
                        return null;
                    }

                    $kategori = optional($skriningNik->pertanyaan->section->kategori)->nama_kategori ?? '-';

                    return [
                        'siklus' => $kategori,
                        'no_nik' => $agt->nik,
                        'nama_lengkap' => $agt->nama,
                        'tempat_lahir' => $agt->tempat_lahir,
                        'tanggal_lahir' => $agt->tanggal_lahir,
                        'jenis_kelamin' => $agt->jenis_kelamin,
                        'pekerjaan' => $agt->pekerjaan,
                        'pendidikan_terakhir' => $agt->pendidikan_terakhir,
                        'hubungan_keluarga' => $agt->hubungan_keluarga,
                        'status_perkawinan' => $agt->status_perkawinan,
                        'sudah_skrining' => true
                    ];
                })->filter()->values();

                $alamat = $keluarga->is_luar_wilayah
                    ? $keluarga->alamat_ktp
                    : optional($keluarga->unitRumah)->alamat;

                $rt = $keluarga->is_luar_wilayah
                    ? $keluarga->rt_ktp
                    : optional($keluarga->unitRumah)->rt;

                $rw = $keluarga->is_luar_wilayah
                    ? $keluarga->rw_ktp
                    : optional($keluarga->unitRumah)->rw;

                return [
                    'no_kk' => $keluarga->no_kk,
                    'kepala_keluarga' => optional($keluarga->kepalaKeluarga)->nama,
                    'jumlah_nik' => $anggota->count(),
                    'alamat' => $alamat,
                    'rt' => $rt,
                    'rw' => $rw,
                    'is_luar_wilayah' => $keluarga->is_luar_wilayah,
                    'anggota' => $anggota
                ];
            })->values();

            return [
                'kelurahan' => optional($unit->posyandu->kelurahan)->nama_kelurahan,
                'posyandu' => optional($unit->posyandu)->nama_posyandu,
                'alamat_unit' => $unit->alamat,
                'rt_unit' => $unit->rt,
                'rw_unit' => $unit->rw,
                'keluarga' => $keluargaList
            ];
        })->values();

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function monitoringNikPerSiklus(array $filter = [])
    {
        $query = $this->skriningModel
            ->selectRaw("
            kelr.nama_kelurahan,
            pos.nama_posyandu,
            kat.nama_kategori as siklus,
            COUNT(DISTINCT agt.nik) as jumlah_nik
        ")
            ->join('m_keluarga as kel', 'kel.id', '=', 't_skrining.keluarga_id')
            ->join('m_unit_rumah as unit', 'unit.id', '=', 'kel.unit_rumah_id')
            ->join('m_posyandu as pos', 'pos.id', '=', 'unit.posyandu_id')
            ->join('m_kelurahan as kelr', 'kelr.id', '=', 'pos.kelurahan_id')
            ->join('t_jawaban as jw', 'jw.skrining_id', '=', 't_skrining.id')
            ->join('m_anggota_keluarga as agt', 'agt.id', '=', 'jw.anggota_keluarga_id')
            ->join('m_pertanyaan as prt', 'prt.id', '=', 'jw.pertanyaan_id')
            ->join('m_section as sec', 'sec.id', '=', 'prt.section_id')
            ->join('m_kategori as kat', 'kat.id', '=', 'sec.kategori_id')
            ->where('kat.target_skrining', 'nik');

        if (!empty($filter['kelurahan_id'])) {
            $query->where('pos.kelurahan_id', $filter['kelurahan_id']);
        }

        if (!empty($filter['posyandu_id'])) {
            $query->where('unit.posyandu_id', $filter['posyandu_id']);
        }

        if (!empty($filter['siklus_id'])) {
            $query->where('kat.id', $filter['siklus_id']);
        }

        $result = $query
            ->groupBy(
                'kelr.nama_kelurahan',
                'pos.nama_posyandu',
                'kat.nama_kategori'
            )
            ->get();

        $data = $result
            ->groupBy(function ($item) {
                return $item->nama_kelurahan . '|' . $item->nama_posyandu;
            })
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'kelurahan' => $first->nama_kelurahan,
                    'posyandu' => $first->nama_posyandu,
                    'siklus' => $items->map(function ($row) {
                        return [
                            'nama_siklus' => $row->siklus,
                            'jumlah_nik' => (int) $row->jumlah_nik
                        ];
                    })->values()
                ];
            })
            ->values();

        if (!empty($filter['sort'])) {
            if ($filter['sort'] === 'Terbanyak → Terkecil') {
                $data = $data->sortByDesc(function ($item) {
                    return collect($item['siklus'])->sum('jumlah_nik');
                })->values();
            } elseif ($filter['sort'] === 'Terkecil → Terbanyak') {
                $data = $data->sortBy(function ($item) {
                    return collect($item['siklus'])->sum('jumlah_nik');
                })->values();
            }
        }

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function monitoringHasilSkrining(array $filter = [])
    {
        $query = $this->skriningModel
            ->with([
                'kader',
                'keluarga.unitRumah.posyandu.kelurahan',
                'keluarga.kepalaKeluarga',
                'keluarga.anggota',
                'jawaban.pertanyaan.section.kategori',
                'jawaban.anggota'
            ]);

        if (!empty($filter['kelurahan_id'])) {
            $query->whereHas('keluarga.unitRumah.posyandu', function ($q) use ($filter) {
                $q->where('kelurahan_id', $filter['kelurahan_id']);
            });
        }

        if (!empty($filter['posyandu_id'])) {
            $query->whereHas('keluarga.unitRumah', function ($q) use ($filter) {
                $q->where('posyandu_id', $filter['posyandu_id']);
            });
        }

        if (!empty($filter['siklus_id'])) {
            $query->whereHas('jawaban.pertanyaan.section.kategori', function ($q) use ($filter) {
                $q->where('id', $filter['siklus_id']);
            });
        }

        $skrining = $query->get();

        $data = $skrining
            ->groupBy('user_id')
            ->map(function ($kaderItems) {
                $kader = optional($kaderItems->first()->kader)->name;

                $unitGroup = $kaderItems->groupBy(function ($item) {
                    return optional($item->keluarga)->unit_rumah_id;
                });

                $unitList = $unitGroup->map(function ($unitItems) {
                    $unit = optional($unitItems->first()->keluarga->unitRumah);

                    $tanggalKK = null;

                    foreach ($unitItems as $skr) {
                        foreach ($skr->jawaban as $jawaban) {
                            $target = optional($jawaban->pertanyaan->section->kategori)->target_skrining;

                            if ($target === 'kk') {
                                $tanggalKK = $skr->tanggal_skrining;
                                break 2;
                            }
                        }
                    }

                    $keluargaGroup = $unitItems->groupBy('keluarga_id');

                    $keluarga = $keluargaGroup->map(function ($kkItems) {
                        $skriningList = $kkItems
                            ->flatMap(function ($skr) {
                                return $skr->jawaban->map(function ($jawaban) use ($skr) {
                                    $kategori = optional($jawaban->pertanyaan->section->kategori);

                                    return [
                                        'siklus' => $kategori->nama_kategori,
                                        'target' => $kategori->target_skrining,
                                        'jawaban' => $jawaban,
                                        'skrining' => $skr
                                    ];
                                });
                            })
                            ->groupBy(function ($item) {
                                return $item['siklus'] . '|' . $item['target'];
                            })
                            ->map(function ($groupItems) {
                                $siklusName = $groupItems->first()['siklus'];
                                $target = $groupItems->first()['target'];

                                if ($target === 'nik') {
                                    $anggotaGroup = collect($groupItems)
                                        ->groupBy(function ($item) {
                                            return optional($item['jawaban']->anggota)->id;
                                        })
                                        ->map(function ($items) {

                                            $first = $items->first();
                                            $agt = $first['jawaban']->anggota;
                                            $tanggal = optional($first['skrining'])->tanggal_skrining;

                                            return [
                                                'id' => $agt->id,
                                                'nama' => $agt->nama,
                                                'tanggal_skrining_nik' => $tanggal,
                                                'pertanyaan' => collect($items)->map(function ($i) {
                                                    return [
                                                        'section' => optional($i['jawaban']->pertanyaan->section)->judul_section,
                                                        'pertanyaan' => optional($i['jawaban']->pertanyaan)->pertanyaan,
                                                        'jawaban' => $i['jawaban']->value_jawaban
                                                    ];
                                                })->values()
                                            ];
                                        })
                                        ->values();

                                    return [
                                        'siklus' => $siklusName,
                                        'target_skrining' => $target,
                                        'anggota' => $anggotaGroup
                                    ];
                                }

                                return [
                                    'siklus' => $siklusName,
                                    'target_skrining' => $target,
                                    'pertanyaan' => collect($groupItems)->map(function ($item) {
                                        return [
                                            'section' => optional($item['jawaban']->pertanyaan->section)->judul_section,
                                            'pertanyaan' => optional($item['jawaban']->pertanyaan)->pertanyaan,
                                            'jawaban' => $item['jawaban']->value_jawaban
                                        ];
                                    })->values()
                                ];
                            })
                            ->values();

                        $keluarga = $kkItems->first()->keluarga;
                        $kepala = optional($keluarga->kepalaKeluarga);

                        $anggota = $keluarga->anggota->map(function ($agt) {
                            return [
                                'id' => $agt->id,
                                'nama' => $agt->nama,
                                'nik' => $agt->nik,
                                'tempat_lahir' => $agt->tempat_lahir,
                                'tanggal_lahir' => $agt->tanggal_lahir,
                                'jenis_kelamin' => $agt->jenis_kelamin,
                                'hubungan_keluarga' => $agt->hubungan_keluarga,
                                'status_perkawinan' => $agt->status_perkawinan,
                                'pendidikan_terakhir' => $agt->pendidikan_terakhir,
                                'pekerjaan' => $agt->pekerjaan,
                            ];
                        });

                        return [
                            'no_kk' => $keluarga->no_kk,
                            'kepala_keluarga' => $kepala->nama,
                            'nik_kepala_keluarga' => $kepala->nik,
                            'no_telepon' => $keluarga->no_telepon,
                            'jumlah_anggota' => $keluarga->anggota->count(),
                            'anggota' => $anggota->values(),
                            'skrining' => $skriningList
                        ];
                    });

                    return [
                        'tanggal_skrining_kk' => $tanggalKK,
                        'kelurahan' => optional($unit->posyandu->kelurahan)->nama_kelurahan,
                        'posyandu' => optional($unit->posyandu)->nama_posyandu,
                        'alamat_unit' => $unit->alamat,
                        'rt_unit' => $unit->rt,
                        'rw_unit' => $unit->rw,
                        'jumlah_kk' => $keluarga->count(),
                        'keluarga' => $keluarga->values()
                    ];
                });

                return [
                    'id' => $kaderItems->first()->user_id,
                    'nama_kader' => $kader,
                    'unit_rumah' => $unitList->values()
                ];
            })->values();
        return [
            'status' => true,
            'data' => $data
        ];
    }
}
