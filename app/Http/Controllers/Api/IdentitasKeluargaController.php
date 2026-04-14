<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\Warga\IdentitasKeluargaHelper;
use App\Http\Requests\IdentitasKeluargaRequest;
use App\Http\Resources\Warga\IdentitasKeluargaResource;
use App\Models\AnggotaKeluargaModel;
use App\Models\KeluargaModel;
use App\Models\KelurahanModel;
use App\Models\PosyanduModel;
use App\Models\UnitModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class IdentitasKeluargaController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new IdentitasKeluargaHelper();
    }

    public function index(Request $request)
    {
        $data = $this->helper->getAll([
            'kelurahan_id' => $request->kelurahan_id,
            'posyandu_id'  => $request->posyandu_id,
            'keyword'      => $request->keyword,
        ], $request->page ?? 1);

        return response()->success([
            'list' => IdentitasKeluargaResource::collection($data['data']),
            'meta' => [
                'total' => $data['data']['total'],
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
            new IdentitasKeluargaResource($data['data'])
        );
    }

    public function store(IdentitasKeluargaRequest $request)
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
            return response()->failed($result['error']);
        }

        return response()->success(
            new IdentitasKeluargaResource($result['data']),
            'Identitas berhasil ditambahkan'
        );
    }

    public function update(IdentitasKeluargaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'kelurahan_id',
            'posyandu_id',
            'alamat',
            'rt',
            'rw',
        ]);

        $keluargaInput = $request->input('keluarga');

        $keluarga = is_string($keluargaInput)
            ? json_decode($keluargaInput, true)
            : $keluargaInput;

        $payload['keluarga'] = collect($keluarga)->map(function ($item) {
            return [
                'id' => $item['id'] ?? null,
                'no_kk' => $item['no_kk'] ?? null,
                'nik_kepala_keluarga' => $item['nik_kepala_keluarga'] ?? null,
                'nama_kepala_keluarga' => $item['nama_kepala_keluarga'] ?? null,
                'no_telepon' => $item['no_telepon'] ?? null,
                'is_luar_wilayah' => $item['is_luar_wilayah'] ?? false,
                'alamat_ktp' => $item['alamat_ktp'] ?? null,
                'rt_ktp' => $item['rt_ktp'] ?? null,
                'rw_ktp' => $item['rw_ktp'] ?? null,
            ];
        })->toArray();

        $result = $this->helper->update($payload, $payload['id']);

        if (!$result['status']) {
            return response()->failed($result['error']);
        }

        return response()->success(
            new IdentitasKeluargaResource($result['data']),
            'Identitas berhasil diubah'
        );
    }

    public function destroy($id)
    {
        $deleted = $this->helper->delete($id);

        if (!$deleted) {
            return response()->failed(['Gagal menghapus data']);
        }

        return response()->success(true, 'Identitas berhasil dihapus');
    }

    public function validateOnly(IdentitasKeluargaRequest $request)
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

    public function import_data_keluarga(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_keluarga' => ['required', 'mimes:xlsx', 'max:2048']
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
        $spreadsheet = $reader->load($request->file('file_keluarga')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true);

        $insertKeluarga = [];
        $insertAnggota  = [];

        $expectedHeader = [
            'A' => 'Kelurahan',
            'B' => 'Posyandu',
            'C' => 'Unit Rumah',
            'D' => 'Alamat',
            'E' => 'RT',
            'F' => 'RW',
            'G' => 'No KK',
            'H' => 'Nama Kepala Keluarga',
            'I' => 'NIK Kepala Keluarga',
            'J' => 'No Telepon',
            'K' => 'Luar Wilayah',
            'L' => 'Alamat KTP',
            'M' => 'RT KTP',
            'N' => 'RW KTP',
        ];

        $headerRow = $data[1] ?? [];
        foreach ($expectedHeader as $col => $expectedName) {
            if (!isset($headerRow[$col]) || strtolower(trim($headerRow[$col])) !== strtolower($expectedName)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Import gagal',
                    'errors'  => ['Format file tidak sesuai template']
                ], 422);
            }
        }

        $unitCache = [];

        $errors = [];
        $rowNumber = 1;
        $validRows = [];
        $kkSeen  = [];
        $nikSeen = [];
        foreach ($data as $row => $value) {
            if ($row == 1) continue;
            $rowNumber++;

            $kelurahanName = trim($value['A']);
            $posyanduName  = trim($value['B']);
            $unitCode      = trim($value['C']);

            $alamat   = trim($value['D']);
            $rt       = trim($value['E']);
            $rw       = trim($value['F']);
            $noKK     = trim($value['G']);
            $namaKK   = trim($value['H']);
            $nikKK    = trim($value['I']);
            $noTelp   = trim($value['J']);
            $kolomK = strtolower(trim($value['K']));
            if (!in_array($kolomK, ['ya', 'tidak', ''])) {
                $errors[] = "Baris $rowNumber: Kolom 'Luar Wilayah' harus diisi 'Ya' atau 'Tidak'.";
                continue;
            }
            $isLuar = $kolomK === 'ya';

            $alamatKtp = trim($value['L']);
            $rtKtp     = trim($value['M']);
            $rwKtp     = trim($value['N']);

            if (empty($alamat) || empty($rt) || empty($rw) || empty($noKK) || empty($namaKK) || empty($nikKK)) {
                $errors[] = "Baris $rowNumber: Terdapat kolom yang kosong.";
                continue;
            }

            $kelurahan = KelurahanModel::where('nama_kelurahan', $kelurahanName)->first();
            if (!$kelurahan) {
                $errors[] = "Baris $rowNumber: Kelurahan '$kelurahanName' tidak ditemukan.";
                continue;
            }

            $posyandu = PosyanduModel::where('nama_posyandu', $posyanduName)
                ->where('kelurahan_id', $kelurahan->id)
                ->first();

            if (!$posyandu) {
                $errors[] = "Baris $rowNumber: Posyandu '$posyanduName' tidak berada di Kelurahan '$kelurahanName'.";
                continue;
            }

            if (KeluargaModel::where('no_kk', $noKK)->exists()) {
                $errors[] = "Baris $rowNumber: No KK '$noKK' sudah terdaftar.";
                continue;
            }

            if (AnggotaKeluargaModel::where('nik', $nikKK)->exists()) {
                $errors[] = "Baris $rowNumber: NIK '$nikKK' sudah terdaftar.";
                continue;
            }

            if (isset($kkSeen[$noKK])) {
                $errors[] = "Baris $rowNumber: No KK '$noKK' duplikat dalam file.";
                continue;
            }
            $kkSeen[$noKK] = true;

            if (isset($nikSeen[$nikKK])) {
                $errors[] = "Baris $rowNumber: NIK '$nikKK' duplikat dalam file.";
                continue;
            }

            $nikSeen[$nikKK] = true;
            $validRows[] = [
                'kelurahan' => $kelurahan,
                'posyandu'  => $posyandu,
                'unitCode'  => $unitCode,
                'alamat'    => $alamat,
                'rt'        => $rt,
                'rw'        => $rw,
                'isLuar'    => $isLuar,
                'noKK'      => $noKK,
                'namaKK'    => $namaKK,
                'nikKK'     => $nikKK,
                'noTelp'    => $noTelp,
                'alamatKtp' => $alamatKtp,
                'rtKtp'     => $rtKtp,
                'rwKtp'     => $rwKtp,
            ];

            $unitKey = $kelurahan->id . '-' . $posyandu->id . '-' . $unitCode;

            if (!isset($unitCache[$unitKey])) {
                $unit = UnitModel::firstOrCreate(
                    [
                        'kelurahan_id' => $kelurahan->id,
                        'posyandu_id'  => $posyandu->id,
                        'alamat'       => $alamat,
                    ],
                    [
                        'rt' => $rt,
                        'rw' => $rw,
                    ]
                );
                $unitCache[$unitKey] = $unit->id;
            }

            $unitId = $unitCache[$unitKey];
            $keluargaId = Str::uuid();

            $insertKeluarga[] = [
                'id'              => $keluargaId,
                'unit_rumah_id'   => $unitId,
                'no_kk'           => $noKK,
                'is_luar_wilayah' => $isLuar,
                'alamat_ktp'      => $isLuar ? $alamatKtp : null,
                'rt_ktp'          => $isLuar ? $rtKtp : null,
                'rw_ktp'          => $isLuar ? $rwKtp : null,
                'no_telepon'      => $noTelp,
                'created_at'      => now()
            ];

            $insertAnggota[] = [
                'id'                 => Str::uuid(),
                'keluarga_id'        => $keluargaId,
                'nama'               => $namaKK,
                'nik'                => $nikKK,
                'hubungan_keluarga'  => 'Kepala Keluarga',
                'created_at'         => now()
            ];
        }

        if (!empty($errors)) {
            return response()->json([
                'status'  => false,
                'message' => 'Import gagal',
                'errors'  => $errors
            ], 422);
        }

        DB::transaction(function () use ($insertKeluarga, $insertAnggota) {
            KeluargaModel::insert($insertKeluarga);
            AnggotaKeluargaModel::insert($insertAnggota);
        });
        return response()->json([
            'status'  => true,
            'message' => 'Data warga berhasil diimport'
        ]);
    }
}
