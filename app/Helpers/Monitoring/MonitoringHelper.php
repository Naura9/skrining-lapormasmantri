<?php

namespace App\Helpers\Monitoring;

use App\Helpers\Helper;
use App\Models\AnggotaKeluargaModel;
use App\Models\JawabanModel;
use App\Models\KeluargaModel;
use App\Models\SkriningModel;
use App\Models\UnitModel;
use App\Models\User\UserModel;
use Illuminate\Support\Facades\DB;

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
        $user = auth()->user();

        $query = $this->userModel
            ->with(['kaderDetail.posyandu.kelurahan'])
            ->where('role', 'kader');

        if ($user->role === 'kader') {
            $query->where('id', $user->id);
        }

        if ($user->role === 'nakes') {
            $query->whereHas('kaderDetail.posyandu.kelurahan', function ($q) use ($user) {
                $q->where('id', $user->nakesDetail->kelurahan_id);
            });
        }

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

        $kaders = $query->orderBy('name', 'desc')->get();

        $data = $kaders->map(function ($kader) {
            $skrining = $this->skriningModel
                ->with([
                    'keluarga.kepalaKeluarga',
                    'keluarga.anggota',
                    'jawaban.pertanyaan.section.kategori'
                ])
                ->where('user_id', $kader->id)
                ->orderBy('tanggal_skrining', 'desc')
                ->get()
                ->groupBy('keluarga_id');

            $totalNik = 0;

            $keluarga = $skrining->map(function ($items) use (&$totalNik) {
                $skr = $items->first();
                $keluarga = $skr->keluarga;

                $alamat = $keluarga->is_luar_wilayah
                    ? $keluarga->alamat_ktp
                    : optional($keluarga->unitRumah)->alamat;

                $rt = $keluarga->is_luar_wilayah
                    ? $keluarga->rt_ktp
                    : optional($keluarga->unitRumah)->rt;

                $rw = $keluarga->is_luar_wilayah
                    ? $keluarga->rw_ktp
                    : optional($keluarga->unitRumah)->rw;

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
                        'jenis_kelamin' => $agt->jenis_kelamin,
                        'hubungan_keluarga' => $agt->hubungan_keluarga,
                        'siklus' => optional($skriningNik->pertanyaan->section->kategori)->nama_kategori,
                        'sudah_skrining' => true
                    ];
                })->filter()->values();

                return [
                    'no_kk' => $skr->keluarga->no_kk,
                    'kepala_keluarga' => optional($skr->keluarga->kepalaKeluarga)->nama,
                    'jumlah_skrining' => $items->count(),
                    'tanggal_skrining_terakhir' => $items->max('tanggal_skrining'),
                    'is_luar_wilayah' => $keluarga->is_luar_wilayah,
                    'alamat' => $alamat,
                    'rt' => $rt,
                    'rw' => $rw,
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
        $user = auth()->user();

        $query = $this->skriningModel
            ->with([
                'keluarga.unitRumah.posyandu.kelurahan',
                'keluarga.kepalaKeluarga',
                'keluarga.anggota',
                'jawaban.pertanyaan.section.kategori'
            ]);

        if ($user->role === 'nakes') {
            $query->whereHas('keluarga.unitRumah.posyandu', function ($q) use ($user) {
                $q->where('kelurahan_id', $user->nakesDetail->kelurahan_id);
            });
        }

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

        if (!empty($filter['search'])) {
            $search = $filter['search'];

            $query->where(function ($q) use ($search) {
                $q->whereHas('keluarga', function ($k) use ($search) {
                    $k->where('no_kk', 'like', "%{$search}%")
                        ->orWhereHas('anggota', function ($a) use ($search) {
                            $a->where('nik', 'like', "%{$search}%");
                        });
                });
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
                    'alamat_unit' => optional($keluarga->unitRumah)->alamat,
                    'rt_unit' => optional($keluarga->unitRumah)->rt,
                    'rw_unit' => optional($keluarga->unitRumah)->rw,

                    'alamat_ktp' => $keluarga->alamat_ktp,
                    'rt_ktp' => $keluarga->rt_ktp,
                    'rw_ktp' => $keluarga->rw_ktp,
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
        $user = auth()->user();

        $query = $this->skriningModel
            ->with([
                'kader',
                'keluarga.unitRumah.posyandu.kelurahan',
                'keluarga.kepalaKeluarga',
                'keluarga.anggota',
                'jawaban.pertanyaan.section.kategori',
                'jawaban.anggota'
            ]);

        if ($user && $user->role === 'kader') {
            $query->where('user_id', $user->id);
        }

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

        if (!empty($filter['search'])) {
            $search = $filter['search'];

            $query->where(function ($q) use ($search) {

                $q->whereHas('keluarga', function ($q2) use ($search) {
                    $q2->where('no_kk', 'like', "%{$search}%")
                        ->orWhereHas('kepalaKeluarga', function ($q3) use ($search) {
                            $q3->where('nama', 'like', "%{$search}%");
                        });
                })

                    ->orWhereHas('keluarga.anggota', function ($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
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
                        $keluarga = $kkItems->first()->keluarga;
                        $kepala = optional($keluarga->kepalaKeluarga);

                        $alamat = $keluarga->is_luar_wilayah
                            ? $keluarga->alamat_ktp
                            : optional($keluarga->unitRumah)->alamat;

                        $rt = $keluarga->is_luar_wilayah
                            ? $keluarga->rt_ktp
                            : optional($keluarga->unitRumah)->rt;

                        $rw = $keluarga->is_luar_wilayah
                            ? $keluarga->rw_ktp
                            : optional($keluarga->unitRumah)->rw;

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
                            'alamat' => $alamat,

                            'rt' => $rt,
                            'rw' => $rw,
                            'is_luar_wilayah' => $keluarga->is_luar_wilayah,

                            'jumlah_anggota' => $keluarga->anggota->count(),
                            'anggota' => $anggota->values(),
                            'skrining' => $skriningList
                        ];
                    });

                    return [
                        'unit_rumah_id' => $unit->id,
                        'tanggal_skrining' => $tanggalKK,
                        'kelurahan' => optional($unit->posyandu->kelurahan)->nama_kelurahan,
                        'posyandu' => optional($unit->posyandu)->nama_posyandu,
                        'alamat_unit' => $unit->alamat,
                        'rt_unit' => $unit->rt,
                        'rw_unit' => $unit->rw,
                        'jumlah_kk' => $keluarga->count(),
                        'jumlah_kk_luar_wilayah' => $keluarga->where('is_luar_wilayah', 1)->count(),
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

    public function getHasilSkriningById($unitId)
    {
        $query = $this->skriningModel
            ->with([
                'kader',
                'keluarga.unitRumah.posyandu.kelurahan',
                'keluarga.kepalaKeluarga',
                'keluarga.anggota',
                'jawaban.pertanyaan.section.kategori',
                'jawaban.anggota'
            ])
            ->whereHas('keluarga.unitRumah', function ($q) use ($unitId) {
                $q->where('id', $unitId);
            });

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

                        $keluarga = $kkItems->first()->keluarga;
                        $kepala = optional($keluarga->kepalaKeluarga);

                        $alamat = $keluarga->is_luar_wilayah
                            ? $keluarga->alamat_ktp
                            : optional($keluarga->unitRumah)->alamat;

                        $rt = $keluarga->is_luar_wilayah
                            ? $keluarga->rt_ktp
                            : optional($keluarga->unitRumah)->rt;

                        $rw = $keluarga->is_luar_wilayah
                            ? $keluarga->rw_ktp
                            : optional($keluarga->unitRumah)->rw;

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

                            'is_luar_wilayah' => $keluarga->is_luar_wilayah,

                            'alamat_ktp' => $keluarga->is_luar_wilayah ? $keluarga->alamat_ktp : null,
                            'rt_ktp' => $keluarga->is_luar_wilayah ? $keluarga->rt_ktp : null,
                            'rw_ktp' => $keluarga->is_luar_wilayah ? $keluarga->rw_ktp : null,

                            'jumlah_anggota' => $keluarga->anggota->count(),
                            'anggota' => $anggota->values(),
                            'skrining' => $skriningList
                        ];
                    });

                    return [
                        'unit_rumah_id' => $unit->id,
                        'tanggal_skrining' => $tanggalKK,
                        'kelurahan_id' => optional($unit->posyandu->kelurahan)->id,
                        'kelurahan' => optional($unit->posyandu->kelurahan)->nama_kelurahan,

                        'posyandu_id' => optional($unit->posyandu)->id,
                        'posyandu' => optional($unit->posyandu)->nama_posyandu,

                        'alamat' => $unit->alamat,
                        'rt' => $unit->rt,
                        'rw' => $unit->rw,
                        'jumlah_kk' => $keluarga->count(),
                        'keluarga' => $keluarga->values()
                    ];
                });

                return [
                    'user_id' => $kaderItems->first()->user_id,
                    'nama_kader' => $kader,
                    'unit_rumah' => $unitList->values()
                ];
            })->values();

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function updateSkrining($unitId, $data)
    {
        DB::beginTransaction();

        try {
            SkriningModel::whereHas('keluarga', function ($q) use ($unitId) {
                $q->where('unit_rumah_id', $unitId);
            })->update([
                'tanggal_skrining' => $data['tanggal_skrining'],
                'user_id' => $data['user_id']
            ]);

            if (!empty($data['skrining_kk'])) {
                foreach ($data['skrining_kk'] as $kk) {

                    JawabanModel::whereNull('anggota_keluarga_id')
                        ->whereHas('skrining', function ($q) use ($unitId) {
                            $q->whereHas('keluarga', function ($q2) use ($unitId) {
                                $q2->where('unit_rumah_id', $unitId);
                            });
                        })
                        ->where('pertanyaan_id', $kk['pertanyaan_id'])
                        ->update([
                            'value_jawaban' => $kk['jawaban']
                        ]);
                }
            }

            if (!empty($data['unit'])) {
                $unitData = $data['unit'];
                UnitModel::where('id', $unitId)->update([
                    'kelurahan_id' => $unitData['kelurahan_id'] ?? null,
                    'posyandu_id'  => $unitData['posyandu_id'] ?? null,
                    'alamat' => $unitData['alamat'] ?? null,
                    'rt'     => $unitData['rt'] ?? null,
                    'rw'     => $unitData['rw'] ?? null,
                ]);
            }

            $existingKeluarga = KeluargaModel::where('unit_rumah_id', $unitId)
                ->pluck('id')
                ->toArray();

            $requestKeluargaIds = array_column($data['keluarga'], 'keluarga_id');

            $keluargaToReset = array_diff($existingKeluarga, $requestKeluargaIds);

            if (!empty($keluargaToReset)) {
                $skriningIds = SkriningModel::whereIn('keluarga_id', $keluargaToReset)
                    ->pluck('id')
                    ->toArray();

                if (!empty($skriningIds)) {
                    JawabanModel::whereIn('skrining_id', $skriningIds)
                        ->whereNotNull('anggota_keluarga_id')
                        ->delete();
                }

                SkriningModel::whereIn('id', $skriningIds)->delete();
            }

            foreach ($data['keluarga'] as $kel) {
                $identitas = $kel['identitas'] ?? [];

                $isLuarWilayah = !empty($identitas['alamat']) && !empty($identitas['rt']) && !empty($identitas['rw']);

                KeluargaModel::where('id', $kel['keluarga_id'])->update([
                    'no_kk' => $identitas['no_kk'] ?? null,
                    'alamat_ktp' => $identitas['alamat'] ?? null,
                    'rt_ktp' => $identitas['rt'] ?? null,
                    'rw_ktp' => $identitas['rw'] ?? null,
                    'no_telepon' => $identitas['no_telepon'] ?? null,
                    'is_luar_wilayah' => $isLuarWilayah
                ]);

                if (!empty($kel['skrining_nik'])) {
                    if (!empty($kel['keluarga_id'])) {
                        $existingAnggota = AnggotaKeluargaModel::where('keluarga_id', $kel['keluarga_id'])
                            ->pluck('id')
                            ->toArray();

                        $requestAnggotaIds = [];
                        if (!empty($kel['skrining_nik'])) {
                            $requestAnggotaIds = array_column($kel['skrining_nik'], 'anggota_id');
                        }

                        $anggotaToReset = array_diff($existingAnggota, $requestAnggotaIds);

                        if (!empty($anggotaToReset)) {
                            $skriningIds = JawabanModel::whereIn('anggota_keluarga_id', $anggotaToReset)
                                ->pluck('skrining_id')
                                ->unique()
                                ->toArray();

                            if (!empty($skriningIds)) {

                                JawabanModel::whereIn('skrining_id', $skriningIds)->delete();

                                SkriningModel::whereIn('id', $skriningIds)->delete();
                            }
                        }
                    }
                    foreach ($kel['skrining_nik'] as $nikData) {
                        if (!empty($nikData['identitas'])) {
                            AnggotaKeluargaModel::where('id', $nikData['anggota_id'])
                                ->update([
                                    'nama'              => $nikData['identitas']['nama'] ?? null,
                                    'nik'               => $nikData['identitas']['nik'] ?? null,
                                    'tempat_lahir'      => $nikData['identitas']['tempat_lahir'] ?? null,
                                    'tanggal_lahir'     => $nikData['identitas']['tanggal_lahir'] ?? null,
                                    'jenis_kelamin'     => $nikData['identitas']['jenis_kelamin'] ?? null,
                                    'hubungan_keluarga' => $nikData['identitas']['hubungan_keluarga'] ?? null,
                                    'pendidikan_terakhir' => $nikData['identitas']['pendidikan_terakhir'] ?? null,
                                    'pekerjaan'         => $nikData['identitas']['pekerjaan'] ?? null,
                                    'status_perkawinan' => $nikData['identitas']['status_perkawinan'] ?? null,
                                ]);
                        }

                        foreach ($nikData['jawaban_list'] as $n) {
                            JawabanModel::whereHas('skrining', function ($q) use ($kel) {
                                $q->where('keluarga_id', $kel['keluarga_id']);
                            })
                                ->where('anggota_keluarga_id', $nikData['anggota_id'])
                                ->where('pertanyaan_id', $n['pertanyaan_id'])
                                ->update([
                                    'value_jawaban' => $n['jawaban']
                                ]);
                        }
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function chartHasilSkrining(array $filter = [])
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

        $skrining = $query->get();

        $groupByUnit = $skrining->groupBy(function ($item) {
            return optional($item->keluarga->unitRumah)->id;
        });

        $result = $groupByUnit->map(function ($items, $unitId) {
            $first = $items->first();

            $unit = $first->keluarga->unitRumah;
            $kelurahan = optional($unit->posyandu->kelurahan)->nama_kelurahan;
            $posyandu = optional($unit->posyandu)->nama_posyandu;

            $listKK = $unit->keluarga->map(function ($keluarga) use ($first) {
                $kepala = optional($keluarga->kepalaKeluarga);

                return [
                    'no_kk' => $keluarga->no_kk,
                    'kepala_keluarga' => $kepala->nama,
                    'nik_kepala_keluarga' => $kepala->nik,
                    'no_telepon' => $keluarga->no_telepon,
                    'alamat_ktp' => $keluarga->alamat_ktp,
                    'rt_ktp' => $keluarga->rt_ktp,
                    'rw_ktp' => $keluarga->rw_ktp,
                    'tanggal_skrining' => $first->tanggal_skrining,
                ];
            });

            $jawabanKK = [];

            foreach ($items as $skr) {
                foreach ($skr->jawaban as $jwb) {
                    $target = $jwb->pertanyaan->section->kategori->target_skrining
                        ?? $jwb->pertanyaan->target_skrining
                        ?? 'kk';

                    if ($target === 'kk') {
                        $jawabanKK[$jwb->pertanyaan_id] = [
                            'pertanyaan_id' => $jwb->pertanyaan_id,
                            'pertanyaan' => $jwb->pertanyaan->pertanyaan,
                            'jawaban' => $jwb->value_jawaban
                        ];
                    }
                }
            }

            $jawabanNIK = [];

            foreach ($items as $skr) {
                foreach ($skr->jawaban as $jwb) {
                    $kategori = $jwb->pertanyaan->section->kategori;

                    if (!$kategori || $kategori->target_skrining !== 'nik') {
                        continue;
                    }

                    $anggotaId = $jwb->anggota_keluarga_id;

                    if (!isset($jawabanNIK[$anggotaId])) {
                        $jawabanNIK[$anggotaId] = [
                            'siklus' => $kategori->nama_kategori,
                            'jawaban' => []
                        ];
                    }

                    $jawabanNIK[$anggotaId]['jawaban'][$jwb->pertanyaan_id] = [
                        'pertanyaan_id' => $jwb->pertanyaan_id,
                        'pertanyaan' => $jwb->pertanyaan->pertanyaan,
                        'jawaban' => $jwb->value_jawaban
                    ];
                }
            }

            $jawabanNIK = array_values($jawabanNIK);

            $dataNIK = $unit->keluarga->flatMap(function ($keluarga) use ($items) {
                return $keluarga->anggota->map(function ($anggota) use ($items) {
                    $jawabanAnggota = [];

                    foreach ($items as $skr) {
                        foreach ($skr->jawaban as $jwb) {

                            $target = $jwb->pertanyaan->section->kategori->target_skrining
                                ?? $jwb->pertanyaan->target_skrining
                                ?? 'kk';

                            if ($target === 'nik' && $jwb->anggota_keluarga_id === $anggota->id) {

                                $kategori = optional($jwb->pertanyaan->section->kategori)->nama_kategori;

                                $jawabanAnggota[] = [
                                    'pertanyaan_id' => $jwb->pertanyaan_id,
                                    'pertanyaan' => $jwb->pertanyaan->pertanyaan,
                                    'jawaban' => $jwb->value_jawaban,
                                    'kategori' => $kategori,
                                ];
                            }
                        }
                    }

                    $siklusNama = null;
                    if (!empty($jawabanAnggota)) {
                        $siklusNama = $jawabanAnggota[0]['kategori'];
                    }

                    return [
                        'nama' => $anggota->nama,
                        'nik' => $anggota->nik,
                        'tempat_lahir' => $anggota->tempat_lahir,
                        'tanggal_lahir' => $anggota->tanggal_lahir,
                        'jenis_kelamin' => $anggota->jenis_kelamin,
                        'pendidikan_terakhir' => $anggota->pendidikan_terakhir,
                        'hubungan_keluarga' => $anggota->hubungan_keluarga,
                        'tanggal_skrining' => optional($items->first())->tanggal_skrining,
                        'siklus' => $siklusNama,
                        'jawaban' => $jawabanAnggota
                    ];
                });
            });

            return [
                'unit_rumah' => $unitId,
                'kelurahan' => $kelurahan,
                'posyandu' => $posyandu,
                'tanggal_skrining' => $first->tanggal_skrining,
                'alamat_unit' => $unit->alamat,
                'rt_unit' => $unit->rt,
                'rw_unit' => $unit->rw,
                'kk_di_unit' => $listKK,
                'skrining_kk' => $jawabanKK,
                'skrining_nik' => $dataNIK
            ];
        })->values();

        return [
            'status' => true,
            'data' => $result
        ];
    }

    public function chartMonitoringNikPerSiklus(array $filter = [])
    {
        $allSiklus = DB::table('m_kategori')
            ->where('target_skrining', 'nik')
            ->orderBy('created_at', 'asc')
            ->select('id', 'nama_kategori')
            ->get();

        $query = $this->skriningModel
            ->selectRaw("
            kat.id as siklus_id,
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

        $raw = $query
            ->groupBy('kat.id')
            ->pluck('jumlah_nik', 'siklus_id');

        $data = $allSiklus->map(function ($s) use ($raw) {
            return [
                'siklus_id' => $s->id,
                'siklus' => $s->nama_kategori,
                'jumlah_nik' => (int) ($raw[$s->id] ?? 0)
            ];
        });

        return [
            'status' => true,
            'data' => $data
        ];
    }
}
