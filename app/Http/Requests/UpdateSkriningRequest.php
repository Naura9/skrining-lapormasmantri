<?php

namespace App\Http\Requests;

use App\Models\AnggotaKeluargaModel;
use App\Models\KeluargaModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSkriningRequest extends FormRequest
{
    public $validator = null;

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
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
            "skrining_kk.*.pertanyaan_id" => "required|string|exists:m_pertanyaan,id",
            "skrining_kk.*.jawaban" => "present",
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
            "keluarga.*.identitas.no_telepon" => "required|string",

            "keluarga.*.identitas.is_luar_wilayah" => "nullable|in:0,1",
            "keluarga.*.identitas.alamat_ktp" => [
                "nullable",
                function ($attribute, $value, $fail) {
                    preg_match('/keluarga\.(\d+)\.identitas\.alamat_ktp/', $attribute, $m);
                    $index = $m[1] ?? null;

                    $isLuar = $this->input("keluarga.$index.identitas.is_luar_wilayah");

                    if ($isLuar == "1" && empty($value)) {
                        $fail("Alamat KTP wajib diisi jika KK luar wilayah.");
                    }
                }
            ],

            "keluarga.*.identitas.rt_ktp" => [
                "nullable",
                function ($attribute, $value, $fail) {
                    preg_match('/keluarga\.(\d+)\.identitas\.rt_ktp/', $attribute, $m);
                    $index = $m[1] ?? null;

                    $isLuar = $this->input("keluarga.$index.identitas.is_luar_wilayah");

                    if ($isLuar == "1" && empty($value)) {
                        $fail("RT KTP wajib diisi jika KK luar wilayah.");
                    }
                }
            ],

            "keluarga.*.identitas.rw_ktp" => [
                "nullable",
                function ($attribute, $value, $fail) {
                    preg_match('/keluarga\.(\d+)\.identitas\.rw_ktp/', $attribute, $m);
                    $index = $m[1] ?? null;

                    $isLuar = $this->input("keluarga.$index.identitas.is_luar_wilayah");

                    if ($isLuar == "1" && empty($value)) {
                        $fail("RW KTP wajib diisi jika KK luar wilayah.");
                    }
                }
            ],

            "keluarga.*.skrining_nik" => "nullable|array",

            "keluarga.*.skrining_nik.*.anggota_id" =>
            "required_with:keluarga.*.skrining_nik|uuid|exists:m_anggota_keluarga,id",

            "keluarga.*.skrining_nik.*.identitas.nama" => "required|string",
            "keluarga.*.skrining_nik.*.identitas.nik" => [
                "required",
                "digits:16",
                function ($attribute, $value, $fail) {
                    if ($value === '0000000000000000') {
                        return;
                    }

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

            "keluarga.*.skrining_nik.*.identitas.tempat_lahir" => "required|string",
            "keluarga.*.skrining_nik.*.identitas.tanggal_lahir" => "required|date",
            "keluarga.*.skrining_nik.*.identitas.jenis_kelamin" => "required|string",
            "keluarga.*.skrining_nik.*.identitas.hubungan_keluarga" => [
                "required",
                "string",
                function ($attribute, $value, $fail) {

                    if (strtolower(trim($value)) !== 'kepala keluarga') {
                        return;
                    }

                    preg_match('/keluarga\.(\d+)\./', $attribute, $matches);

                    $keluargaIndex = $matches[1] ?? null;

                    $anggotaList = $this->input("keluarga.$keluargaIndex.skrining_nik", []);

                    $jumlahKepalaKeluarga = collect($anggotaList)
                        ->filter(function ($anggota) {

                            $hubungan = strtolower(
                                trim($anggota['identitas']['hubungan_keluarga'] ?? '')
                            );

                            return $hubungan === 'kepala keluarga';
                        })
                        ->count();

                    if ($jumlahKepalaKeluarga > 1) {
                        $fail('Kepala keluarga hanya boleh 1 dalam satu KK.');
                    }
                }
            ],
            "keluarga.*.skrining_nik.*.identitas.pendidikan_terakhir" => "required|string",
            "keluarga.*.skrining_nik.*.identitas.pekerjaan" => "required|string",
            "keluarga.*.skrining_nik.*.identitas.status_perkawinan" => "required|string",

            "keluarga.*.skrining_nik.*.jawaban_list" => "nullable|array",
            "keluarga.*.skrining_nik.*.jawaban_list.*.pertanyaan_id" =>
            "required_with:keluarga.*.skrining_nik.*.jawaban_list|string|exists:m_pertanyaan,id",
            "keluarga.*.skrining_nik.*.jawaban_list.*.jawaban" => "nullable",
        ];
    }

    public function messages(): array
    {
        return [
            "tanggal_skrining.required" => "Tanggal skrining wajib diisi.",
            "tanggal_skrining.date" => "Format tanggal skrining tidak valid.",

            "user_id.required" => "User ID (kader) wajib diisi.",
            "user_id.uuid" => "User ID tidak valid.",
            "user_id.exists" => "User ID tidak ditemukan.",

            "unit.required" => "Unit wajib diisi.",
            "unit.kelurahan_id.required" => "Kelurahan wajib dipilih.",
            "unit.kelurahan_id.exists" => "Kelurahan tidak valid.",
            "unit.posyandu_id.required" => "Posyandu wajib dipilih.",
            "unit.posyandu_id.exists" => "Posyandu tidak valid.",
            "unit.alamat.required" => "Alamat unit wajib diisi.",
            "unit.rt.required" => "RT unit wajib diisi.",
            "unit.rw.required" => "RW unit wajib diisi.",

            "keluarga.required" => "Minimal harus ada 1 keluarga.",
            "keluarga.min" => "Minimal harus ada 1 keluarga.",

            "keluarga.*.keluarga_id.required" => "ID keluarga wajib diisi.",
            "keluarga.*.keluarga_id.exists" => "ID keluarga tidak ditemukan.",

            "keluarga.*.identitas.no_kk.required" => "No KK wajib diisi.",
            "keluarga.*.identitas.no_kk.string" => "No KK harus berupa teks.",
            "keluarga.*.identitas.no_kk.unique" => "No KK sudah terdaftar.",

            "keluarga.*.identitas.no_telepon.required" => "No telepon wajib diisi.",
            "keluarga.*.identitas.no_telepon.string" => "No telepon tidak valid.",

            "keluarga.*.identitas.is_luar_wilayah.boolean" => "Status luar wilayah tidak valid.",

            "keluarga.*.identitas.alamat_ktp.required_if" =>
            "Alamat KTP wajib diisi jika KK luar wilayah.",
            "keluarga.*.identitas.rt_ktp.required_if" =>
            "RT KTP wajib diisi jika KK luar wilayah.",
            "keluarga.*.identitas.rw_ktp.required_if" =>
            "RW KTP wajib diisi jika KK luar wilayah.",

            "keluarga.*.skrining_nik.required" => "Data anggota keluarga wajib diisi.",
            "keluarga.*.skrining_nik.array" => "Format data anggota tidak valid.",

            "keluarga.*.skrining_nik.*.anggota_id.required_with" =>
            "ID anggota wajib diisi.",
            "keluarga.*.skrining_nik.*.anggota_id.uuid" =>
            "ID anggota tidak valid.",
            "keluarga.*.skrining_nik.*.anggota_id.exists" =>
            "Anggota tidak ditemukan.",

            "keluarga.*.skrining_nik.*.identitas.nama.required" =>
            "Nama wajib diisi.",

            "keluarga.*.skrining_nik.*.identitas.nik.required" =>
            "NIK wajib diisi.",
            "keluarga.*.skrining_nik.*.identitas.nik.digits" =>
            "NIK harus 16 digit.",
            "keluarga.*.skrining_nik.*.identitas.nik.unique" =>
            "NIK sudah terdaftar.",

            "keluarga.*.skrining_nik.*.identitas.tempat_lahir.required" =>
            "Tempat lahir wajib diisi.",
            "keluarga.*.skrining_nik.*.identitas.tanggal_lahir.required" =>
            "Tanggal lahir wajib diisi.",
            "keluarga.*.skrining_nik.*.identitas.tanggal_lahir.date" =>
            "Tanggal lahir tidak valid.",

            "keluarga.*.skrining_nik.*.identitas.jenis_kelamin.required" =>
            "Jenis kelamin wajib diisi.",
            "keluarga.*.skrining_nik.*.identitas.hubungan_keluarga.required" =>
            "Hubungan keluarga wajib diisi.",
            "keluarga.*.skrining_nik.*.identitas.pendidikan_terakhir.required" =>
            "Pendidikan terakhir wajib diisi.",
            "keluarga.*.skrining_nik.*.identitas.pekerjaan.required" =>
            "Pekerjaan wajib diisi.",
            "keluarga.*.skrining_nik.*.identitas.status_perkawinan.required" =>
            "Status perkawinan wajib diisi.",

            "keluarga.*.skrining_nik.*.jawaban_list.array" =>
            "Format jawaban tidak valid.",
            "keluarga.*.skrining_nik.*.jawaban_list.*.pertanyaan_id.required_with" =>
            "Pertanyaan wajib diisi.",
            "keluarga.*.skrining_nik.*.jawaban_list.*.pertanyaan_id.exists" =>
            "Pertanyaan tidak valid.",
        ];
    }
}
