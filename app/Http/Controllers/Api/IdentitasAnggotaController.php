<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\Warga\IdentitasAnggotaHelper;
use App\Http\Requests\IdentitasAnggotaRequest;
use App\Http\Resources\Warga\IdentitasAnggotaResource;
use App\Models\AnggotaKeluargaModel;
use App\Models\KeluargaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class IdentitasAnggotaController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new IdentitasAnggotaHelper();
    }

    public function index(Request $request)
    {
        $data = $this->helper->getAll([
            'keyword' => $request->keyword,
            'kelurahan_id' => $request->kelurahan_id,
            'posyandu_id'  => $request->posyandu_id,
        ], $request->page ?? 1);

        return response()->success([
            'list' => IdentitasAnggotaResource::collection($data['data']),
            'meta' => [
                'total' => $data['data']->total(),
            ],
        ]);
    }

    public function show($id)
    {
        $data = $this->helper->getById($id);

        if (!$data['status']) {
            return response()->failed(['Data tidak ditemukan'], 404);
        }

        return response()->success(
            new IdentitasAnggotaResource($data['data'])
        );
    }

    public function store(IdentitasAnggotaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        if ($request->has('validate_only')) {
            return response()->json([
                'message' => 'Validasi berhasil'
            ]);
        }

        $result = $this->helper->create($request->validated());

        if (!$result['status']) {
            return response()->failed([$result['error']]);
        }

        return response()->success(
            $result['data'],
            'Anggota keluarga berhasil ditambahkan'
        );
    }

    public function update(IdentitasAnggotaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'keluarga_id',
            'nama',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'hubungan_keluarga',
            'status_perkawinan',
            'pendidikan_terakhir',
            'pekerjaan'
        ]);

        $result = $this->helper->update($payload, $payload['id']);

        if (!$result['status']) {
            return response()->failed($result['error']);
        }

        return response()->success(
            new IdentitasAnggotaResource($result['data']),
            'Identitas berhasil diubah'
        );
    }

    public function destroy($id)
    {
        $deleted = $this->helper->delete($id);

        if (!$deleted) {
            return response()->failed(['Gagal menghapus data']);
        }

        return response()->success(true, 'Anggota berhasil dihapus');
    }

    public function validateOnly(IdentitasAnggotaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $request->validator->errors()
            ], 422);
        }

        return response()->json([
            'message' => 'Valid'
        ]);
    }

    public function getByKeluarga($keluargaId)
    {
        $anggota = AnggotaKeluargaModel::where('keluarga_id', $keluargaId)->get();

        return response()->success([
            'data' => IdentitasAnggotaResource::collection($anggota)
        ]);
    }

    public function import_anggota(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_anggota' => ['required', 'mimes:xlsx', 'max:2048']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => ['File wajib berformat .xlsx dan maksimal 2MB']
            ], 422);
        }

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file_anggota')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true);

        $insertAnggota = [];

        $hasEmptyField      = false;
        $hasInvalidKK       = false;
        $hasInvalidGender   = false;
        $hasInvalidNIK      = false;
        $hasDuplicateNIK    = false;
        $hasHeaderMismatch  = false;

        $expectedHeader = [
            'A' => 'No KK',
            'B' => 'Nama',
            'C' => 'NIK',
            'D' => 'Tempat Lahir',
            'E' => 'Tanggal Lahir',
            'F' => 'Jenis Kelamin',
            'G' => 'Hubungan Keluarga',
            'H' => 'Status Perkawinan',
            'I' => 'Pendidikan Terakhir',
            'J' => 'Pekerjaan',
        ];

        $headerRow = $data[1] ?? [];

        foreach ($expectedHeader as $col => $expectedName) {
            if (!isset($headerRow[$col]) || strtolower(trim($headerRow[$col])) !== strtolower($expectedName)) {
                $hasHeaderMismatch = true;
            }
        }

        if ($hasHeaderMismatch) {
            return response()->json([
                'status'  => false,
                'message' => 'Import gagal',
                'errors'  => ['Format file tidak sesuai template, periksa judul kolom.']
            ], 422);
        }

        $nikList = [];

        foreach ($data as $row => $value) {
            if ($row == 1) continue;

            if (
                empty($value['A']) ||
                empty($value['B']) ||
                empty($value['C']) ||
                empty($value['D']) ||
                empty($value['E']) ||
                empty($value['F']) ||
                empty($value['G'])
            ) {
                $hasEmptyField = true;
                continue;
            }

            $keluarga = KeluargaModel::where('no_kk', trim($value['A']))->first();
            if (!$keluarga) {
                $hasInvalidKK = true;
                continue;
            }

            $nik = preg_replace('/\D/', '', $value['C']);

            if (strlen($nik) !== 16) {
                $hasInvalidNIK = true;
                continue;
            }

            if (in_array($nik, $nikList)) {
                $hasDuplicateNIK = true;
                continue;
            }

            if (AnggotaKeluargaModel::where('nik', $nik)->exists()) {
                $hasDuplicateNIK = true;
                continue;
            }

            $nikList[] = $nik;

            $jk = strtoupper(trim($value['F']));
            if (in_array($jk, ['LAKI-LAKI', 'LAKI', 'L'])) {
                $jk = 'L';
            } elseif (in_array($jk, ['PEREMPUAN', 'P'])) {
                $jk = 'P';
            } else {
                $hasInvalidGender = true;
                continue;
            }

            $tanggalRaw = $value['E'];

            if (is_numeric($tanggalRaw)) {
                try {
                    $tanggalLahir = Date::excelToDateTimeObject($tanggalRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $hasEmptyField = true;
                    continue;
                }
            } else {
                $tanggalLahir = date('Y-m-d', strtotime($tanggalRaw));
            }

            $insertAnggota[] = [
                'id'                  => Str::uuid(),
                'keluarga_id'         => $keluarga->id,
                'nama'                => trim($value['B']),
                'nik'                 => $nik,
                'tempat_lahir'        => trim($value['D']),
                'tanggal_lahir'       => $tanggalLahir,
                'jenis_kelamin'       => $jk,
                'hubungan_keluarga'   => trim($value['G']),
                'status_perkawinan'   => trim($value['H'] ?? ''),
                'pendidikan_terakhir' => trim($value['I'] ?? ''),
                'pekerjaan'           => trim($value['J'] ?? ''),
                'created_at'          => now()
            ];
        }

        $errors = [];
        if ($hasEmptyField)      $errors[] = 'Terdapat kolom yang kosong.';
        if ($hasInvalidKK)       $errors[] = 'No KK tidak ditemukan di database.';
        if ($hasInvalidNIK)      $errors[] = 'Terdapat NIK yang tidak valid (harus 16 digit).';
        if ($hasDuplicateNIK)    $errors[] = 'Terdapat NIK yang duplikat atau sudah terdaftar.';
        if ($hasInvalidGender)   $errors[] = 'Format Jenis Kelamin tidak sesuai.';

        if (!empty($errors)) {
            return response()->json([
                'status'  => false,
                'message' => 'Import gagal',
                'errors'  => $errors
            ], 422);
        }

        DB::transaction(function () use ($insertAnggota) {
            AnggotaKeluargaModel::insert($insertAnggota);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data anggota keluarga berhasil diimport'
        ]);
    }
}
