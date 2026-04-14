<?php

namespace App\Http\Requests;

use App\Models\KeluargaModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IdentitasKeluargaRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    private function createRules(): array
    {
        return [
            'kelurahan_id' => 'required|exists:m_kelurahan,id',
            'posyandu_id'  => 'required|exists:m_posyandu,id',
            'alamat'       => 'required|string|max:255',
            'rt'           => 'required|string|max:5',
            'rw'           => 'required|string|max:5',

            'keluarga' => 'required|array|min:1',
            'keluarga.*.no_kk' => [
                'required',
                'string',
                'digits:16',
                'distinct',
                Rule::unique('m_keluarga', 'no_kk')
            ],
            'keluarga.*.no_telepon' => 'required|string|max:20',

            'keluarga.*.is_luar_wilayah' => 'required|boolean',
            'keluarga.*.alamat_ktp' => [
                'nullable',
                'required_if:keluarga.*.is_luar_wilayah,1',
                'string'
            ],

            'keluarga.*.rt_ktp' => [
                'nullable',
                'required_if:keluarga.*.is_luar_wilayah,1',
                'string',
                'max:5'
            ],

            'keluarga.*.rw_ktp' => [
                'nullable',
                'required_if:keluarga.*.is_luar_wilayah,1',
                'string',
                'max:5'
            ],

            'keluarga.*.nik_kepala_keluarga' => [
                'required',
                'string',
                'digits:16',
                'distinct',
                Rule::unique('m_anggota_keluarga', 'nik')
            ],

            'keluarga.*.nama_kepala_keluarga' => 'required|string|max:150',
        ];
    }
    protected function prepareForValidation()
    {
        if ($this->has('keluarga') && is_string($this->keluarga)) {
            $this->merge([
                'keluarga' => json_decode($this->keluarga, true)
            ]);
        }
    }

    private function updateRules(): array
    {
        return [
            'kelurahan_id' => 'required|exists:m_kelurahan,id',
            'posyandu_id'  => 'required|exists:m_posyandu,id',
            'alamat'       => 'required|string|max:255',
            'rt'           => 'required|string|max:5',
            'rw'           => 'required|string|max:5',

            'keluarga' => 'required|array|min:1',

            'keluarga.*.id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !KeluargaModel::where('id', $value)->exists()) {
                        $fail("Data keluarga dengan ID $value tidak ditemukan.");
                    }
                }
            ],

            'keluarga.*.no_kk' => [
                'required',
                'string',
                'digits:16',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $id = $this->input("keluarga.$index.id");

                    $query = KeluargaModel::where('no_kk', $value);

                    if ($id) {
                        $query->where('id', '!=', $id);
                    }

                    if ($query->exists()) {
                        $fail('No KK sudah terdaftar.');
                    }
                }
            ],

            'keluarga.*.nik_kepala_keluarga' => 'required|string|digits:16',
            'keluarga.*.nama_kepala_keluarga' => 'required|string|max:150',
            'keluarga.*.no_telepon' => 'required|string|max:20',

            'keluarga.*.is_luar_wilayah' => 'required|boolean',

            'keluarga.*.alamat_ktp' => [
                'nullable',
                'required_if:keluarga.*.is_luar_wilayah,1',
                'string'
            ],

            'keluarga.*.rt_ktp' => [
                'nullable',
                'required_if:keluarga.*.is_luar_wilayah,1',
                'string',
                'max:5'
            ],

            'keluarga.*.rw_ktp' => [
                'nullable',
                'required_if:keluarga.*.is_luar_wilayah,1',
                'string',
                'max:5'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.exists'   => 'Kelurahan tidak ditemukan.',

            'posyandu_id.required' => 'Posyandu wajib dipilih.',
            'posyandu_id.exists'   => 'Posyandu tidak ditemukan.',

            'alamat.required' => 'Alamat domisili wajib diisi.',
            'rt.required' => 'RT domisili wajib diisi.',
            'rw.required' => 'RW domisili wajib diisi.',

            'keluarga.required' => 'Minimal harus ada 1 keluarga.',
            'keluarga.*.no_kk.required' => 'No KK wajib diisi.',
            'keluarga.*.no_kk.distinct' => 'No KK tidak boleh duplikat dalam satu input.',
            'keluarga.*.no_kk.digits' => 'No KK harus 16 digit angka.',
            'keluarga.*.no_kk.unique'   => 'No KK sudah terdaftar.',

            'keluarga.*.nik_kepala_keluarga.digits' => 'NIK harus 16 digit angka.',
            'keluarga.*.nik_kepala_keluarga.distinct' => 'NIK tidak boleh duplikat dalam satu input.',
            'keluarga.*.nik_kepala_keluarga.unique'   => 'NIK sudah terdaftar.',

            'keluarga.*.nik_kepala_keluarga.required' => 'NIK kepala keluarga wajib diisi.',
            'keluarga.*.nama_kepala_keluarga.required' => 'Nama kepala keluarga wajib diisi.',

            'keluarga.*.no_telepon.required' => 'No telepon wajib diisi.',
            'keluarga.*.no_telepon.regex'    => 'Format no telepon tidak valid.',
            'keluarga.*.no_telepon.max'      => 'No telepon maksimal 20 karakter.',

            'keluarga.*.alamat_ktp.required_if' => 'Alamat (KTP) wajib diisi jika KK luar wilayah.',
            'keluarga.*.rt_ktp.required_if'     => 'RT (KTP) wajib diisi jika KK luar wilayah.',
            'keluarga.*.rw_ktp.required_if'     => 'RW (KTP) wajib diisi jika KK luar wilayah.',
        ];
    }
}
