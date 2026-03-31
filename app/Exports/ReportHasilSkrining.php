<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportHasilSkrining implements FromView
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

public function view(): View
{
    $rows = [];
    $questions = [];

    // 🔹 ambil semua pertanyaan unik
    foreach ($this->data as $kader) {
        foreach ($kader['unit_rumah'] as $unit) {
            foreach ($unit['keluarga'] as $kk) {
                foreach ($kk['skrining'] as $skr) {

                    if ($skr['target_skrining'] === 'nik') {
                        foreach ($skr['anggota'] as $agt) {
                            foreach ($agt['pertanyaan'] as $p) {
                                $questions[$p['pertanyaan']] = true;
                            }
                        }
                    } else {
                        foreach ($skr['pertanyaan'] as $p) {
                            $questions[$p['pertanyaan']] = true;
                        }
                    }

                }
            }
        }
    }

    $questions = array_keys($questions); // jadi array header

    // 🔹 mapping data
    foreach ($this->data as $kader) {
        foreach ($kader['unit_rumah'] as $unit) {
            foreach ($unit['keluarga'] as $kk) {
                foreach ($kk['skrining'] as $skr) {

                    if ($skr['target_skrining'] === 'nik') {
                        foreach ($skr['anggota'] as $agt) {

                            $row = [
                                'kader' => $kader['nama_kader'],
                                'no_kk' => $kk['no_kk'],
                                'kepala_keluarga' => $kk['kepala_keluarga'],
                                'nama_anggota' => $agt['nama'],
                                'siklus' => $skr['siklus'],
                            ];

                            // default semua "-"
                            foreach ($questions as $q) {
                                $row[$q] = '-';
                            }

                            // isi jawaban
                            foreach ($agt['pertanyaan'] as $p) {
                                $row[$p['pertanyaan']] = $p['jawaban'] ?? '-';
                            }

                            $rows[] = $row;
                        }
                    }

                }
            }
        }
    }

    return view('excel.report-hasil-skrining', [
        'rows' => $rows,
        'questions' => $questions
    ]);
}}