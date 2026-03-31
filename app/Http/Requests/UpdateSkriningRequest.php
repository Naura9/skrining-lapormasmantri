<?php

namespace App\Http\Requests;

use App\Models\AnggotaKeluargaModel;
use App\Models\KeluargaModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkriningRequest extends FormRequest
{
    public $validator = null;

    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function rules(): array
    {
        return [
            "tanggal_skrining" => "required|date",
            "user_id" => "required|uuid|exists:m_user,id",

            "unit" => "required|array",
            "unit.kelurahan_id" => "required|uuid|exists:m_kelurahan,id",
            "unit.posyandu_id" => "required|uuid|exists:m_posyandu,id",
            "unit.alamat" => "required|string",
            "unit.rt" => "required|string",
            "unit.rw" => "required|string",

            "skrining_kk" => "nullable|array",
            "skrining_kk.*.pertanyaan_id" => "required_with:skrining_kk|string|exists:m_pertanyaan,id",
            "skrining_kk.*.jawaban" => "required_with:skrining_kk|string",
            "keluarga" => "required|array|min:1",
            "keluarga.*.keluarga_id" => "required|uuid|exists:m_keluarga,id",

            "keluarga.*.identitas.no_kk" => [
                "required",
                "string",
                function ($attribute, $value, $fail) {
                    preg_match('/keluarga\.(\d+)\.identitas\.no_kk/', $attribute, $matches);
                    $index = $matches[1] ?? null;
                    $keluargaId = $this->input("keluarga.$index.keluarga_id");

                    $exists = KeluargaModel::where('no_kk', $value)
                        ->where('id', '!=', $keluargaId)
                        ->exists();
                    if ($exists) {
                        $fail('No KK sudah terdaftar.');
                    }
                }
            ],
            "keluarga.*.identitas.alamat" => "nullable|string",
            "keluarga.*.identitas.rt" => "nullable|string",
            "keluarga.*.identitas.rw" => "nullable|string",
            "keluarga.*.identitas.no_telepon" => "nullable|string",

            "keluarga.*.skrining_nik" => "nullable|array",

            "keluarga.*.skrining_nik.*.anggota_id" =>
            "required_with:keluarga.*.skrining_nik|uuid|exists:m_anggota_keluarga,id",

            "keluarga.*.skrining_nik.*.identitas.nama" => "nullable|string",
            "keluarga.*.skrining_nik.*.identitas.nik" => [
                "required",
                "digits:16",
                function ($attribute, $value, $fail) {
                    preg_match('/keluarga\.(\d+)\.skrining_nik\.(\d+)\.identitas\.nik/', $attribute, $matches);
                    $kelIndex = $matches[1] ?? null;
                    $anggotaIndex = $matches[2] ?? null;
                    $anggotaId = $this->input("keluarga.$kelIndex.skrining_nik.$anggotaIndex.anggota_id");

                    $exists = AnggotaKeluargaModel::where('nik', $value)
                        ->where('id', '!=', $anggotaId)
                        ->exists();
                    if ($exists) {
                        $fail('NIK sudah terdaftar.');
                    }
                }
            ],
            "keluarga.*.skrining_nik.*.identitas.tempat_lahir" => "nullable|string",
            "keluarga.*.skrining_nik.*.identitas.tanggal_lahir" => "nullable|date",
            "keluarga.*.skrining_nik.*.identitas.jenis_kelamin" => "nullable|string",
            "keluarga.*.skrining_nik.*.identitas.hubungan_keluarga" => "nullable|string",
            "keluarga.*.skrining_nik.*.identitas.pendidikan_terakhir" => "nullable|string",
            "keluarga.*.skrining_nik.*.identitas.pekerjaan" => "nullable|string",
            "keluarga.*.skrining_nik.*.identitas.status_perkawinan" => "nullable|string",

            "keluarga.*.skrining_nik.*.jawaban_list" => "nullable|array",
            "keluarga.*.skrining_nik.*.jawaban_list.*.pertanyaan_id" =>
            "required_with:keluarga.*.skrining_nik.*.jawaban_list|string|exists:m_pertanyaan,id",
            "keluarga.*.skrining_nik.*.jawaban_list.*.jawaban" =>
            "required_with:keluarga.*.skrining_nik.*.jawaban_list|string",
        ];
    }

    public function messages(): array
    {
        return [
            "tanggal_skrining.required" => "Tanggal skrining wajib diisi.",
            "user_id.required" => "User ID (kader) wajib diisi.",

            "unit.required" => "Unit wajib diisi.",
            "unit.kelurahan_id.required" => "Kelurahan wajib dipilih.",
            "unit.kelurahan_id.exists" => "Kelurahan tidak valid.",
            "unit.posyandu_id.required" => "Posyandu wajib dipilih.",
            "unit.posyandu_id.exists" => "Posyandu tidak valid.",
            "unit.alamat.required" => "Alamat unit wajib diisi.",
            "unit.rt.required" => "RT unit wajib diisi.",
            "unit.rw.required" => "RW unit wajib diisi.",

            "skrining_kk.*.pertanyaan_id.required_with" => "Pertanyaan skrining KK wajib.",
            "skrining_kk.*.jawaban.required_with" => "Jawaban skrining KK wajib.",

            "keluarga.required" => "Minimal harus ada 1 keluarga.",
            "keluarga.*.keluarga_id.required" => "ID keluarga wajib.",
            "keluarga.*.keluarga_id.exists" => "ID keluarga tidak ditemukan.",
            "keluarga.*.identitas.no_kk.unique" => "No KK sudah terdaftar.",

            "keluarga.*.skrining_nik.*.anggota_id.required_with" =>
            "ID anggota wajib diisi untuk skrining NIK.",
            "keluarga.*.skrining_nik.*.identitas.nik.unique" => "NIK sudah terdaftar.",
            "keluarga.*.skrining_nik.*.jawaban_list.*.pertanyaan_id.required_with" =>
            "Pertanyaan ID wajib pada jawaban anggota.",
            "keluarga.*.skrining_nik.*.jawaban_list.*.jawaban.required_with" =>
            "Jawaban wajib pada skrining anggota.",
        ];
    }
}
