<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Helpers\User\UserHelper;
use App\Http\Resources\User\UserResource;
use App\Models\KelurahanModel;
use App\Models\PosyanduModel;
use App\Models\User\UserKaderModel;
use App\Models\User\UserModel;
use App\Models\User\UserNakesModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new UserHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name'       => $request->input('name', ''),
            'role'       => $request->input('role', ''),
            'kelurahan'  => $request->input('kelurahan', ''),
            'posyandu'   => $request->input('posyandu', ''),
            'status'   => $request->input('status', ''),
            'no_telepon' => $request->input('no_telepon', ''),
        ];

        $users = $this->user->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => UserResource::collection($users['data']['data']),
            'meta' => [
                'total' => $users['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $user = $this->user->getById($id);

        if (!$user['status']) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->success(new UserResource($user['data']));
    }

    public function store(UserRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'username',
            'name',
            'password',
            'role',
            'nik',
            'no_telepon',
            'jenis_kelamin',
            'kelurahan_id',
            'posyandu_id',
            'status'
        ]);

        $user = $this->user->create($payload);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(new UserResource($user['data']), 'User berhasil ditambahkan');
    }

    public function update(UserRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'username',
            'name',
            'password',
            'role',
            'nik',
            'no_telepon',
            'jenis_kelamin',
            'kelurahan_id',
            'posyandu_id',
            'status'
        ]);

        $user = $this->user->update($payload, $payload['id']);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(
            new UserResource($user['data']),
            'User berhasil diubah'
        );
    }

    public function destroy($id)
    {
        $result = $this->user->delete($id);

        if (!$result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message']
        ]);
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6'
        ]);

        $result = $this->user->resetPassword($id, $request->password);

        if (!$result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message']
        ]);
    }

    public function import_kader(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_kader' => ['required', 'mimes:xlsx', 'max:2048']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => ['File wajib berformat .xlsx dan maksimal 2MB']
            ], 422);
        }

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file_kader')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true);

        $insertUser  = [];
        $insertKader = [];

        $hasEmptyField        = false;
        $hasDuplicateUsername = false;
        $hasInvalidKelurahan  = false;
        $hasInvalidPosyandu   = false;
        $hasMismatchWilayah   = false;

        $expectedHeader = [
            'A' => 'Nama',
            'B' => 'Username',
            'C' => 'Password',
            'D' => 'Nama Kelurahan',
            'E' => 'Nama Posyandu',
            'F' => 'No Telepon',
            'G' => 'Jenis Kelamin',
        ];

        $headerRow = $data[1] ?? [];

        foreach ($expectedHeader as $col => $expectedName) {
            if (
                !isset($headerRow[$col]) ||
                strtolower(trim($headerRow[$col])) !== strtolower($expectedName)
            ) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Import gagal',
                    'errors'  => ['Format file tidak sesuai template']
                ], 422);
            }
        }

        foreach ($data as $row => $value) {
            if ($row == 1) continue;

            if (
                empty($value['A']) ||
                empty($value['B']) ||
                empty($value['C']) ||
                empty($value['D']) ||
                empty($value['E'])
            ) {
                $hasEmptyField = true;
                continue;
            }

            if (UserModel::where('username', $value['B'])->exists()) {
                $hasDuplicateUsername = true;
                continue;
            }

            $kelurahan = KelurahanModel::where('nama_kelurahan', $value['D'])->first();
            if (!$kelurahan) {
                $hasInvalidKelurahan = true;
                continue;
            }

            $posyandu = PosyanduModel::where('nama_posyandu', $value['E'])->first();
            if (!$posyandu) {
                $hasInvalidPosyandu = true;
                continue;
            }

            if ($posyandu->kelurahan_id !== $kelurahan->id) {
                $hasMismatchWilayah = true;
                continue;
            }

            $userId  = Str::uuid();
            $kaderId = Str::uuid();

            $insertUser[] = [
                'id'         => $userId,
                'name'       => $value['A'],
                'username'   => $value['B'],
                'password'   => Hash::make($value['C']),
                'role'       => 'kader',
                'created_at' => now()
            ];

            $insertKader[] = [
                'id'          => $kaderId,
                'user_id'     => $userId,
                'posyandu_id' => $posyandu->id,
                'no_telepon'  => $value['F'],
                'status'      => 'aktif',
                'created_at'  => now()
            ];
        }

        $errors = [];

        if ($hasEmptyField) {
            $errors[] = 'Terdapat kolom yang kosong';
        }
        if ($hasDuplicateUsername) {
            $errors[] = 'Username ada yang sudah terdaftar';
        }
        if ($hasInvalidKelurahan) {
            $errors[] = 'Kelurahan tidak ditemukan';
        }
        if ($hasInvalidPosyandu) {
            $errors[] = 'Posyandu tidak ditemukan';
        }
        if ($hasMismatchWilayah) {
            $errors[] = 'Posyandu tidak sesuai dengan kelurahan';
        }

        if (!empty($errors)) {
            return response()->json([
                'status'  => false,
                'message' => 'Import gagal',
                'errors'  => $errors
            ], 422);
        }

        DB::transaction(function () use ($insertUser, $insertKader) {
            UserModel::insert($insertUser);
            UserKaderModel::insert($insertKader);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil diimport'
        ]);
    }

    public function import_nakes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_nakes' => ['required', 'mimes:xlsx', 'max:2048']
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
        $spreadsheet = $reader->load($request->file('file_nakes')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, false, true, true);

        $insertUser  = [];
        $insertNakes = [];

        $hasEmptyField        = false;
        $hasDuplicateUsername = false;
        $hasInvalidKelurahan  = false;

        $expectedHeader = [
            'A' => 'Nama',
            'B' => 'Username',
            'C' => 'Password',
            'D' => 'NIK',
            'E' => 'No Telepon',
            'F' => 'Nama Kelurahan',
            'G' => 'Jenis Kelamin',
        ];

        $headerRow = $data[1] ?? [];

        foreach ($expectedHeader as $col => $expectedName) {
            if (
                !isset($headerRow[$col]) ||
                strtolower(trim($headerRow[$col])) !== strtolower($expectedName)
            ) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Import gagal',
                    'errors'  => ['Format file tidak sesuai template']
                ], 422);
            }
        }

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

            if (UserModel::where('username', $value['B'])->exists()) {
                $hasDuplicateUsername = true;
                continue;
            }

            $kelurahan = KelurahanModel::where('nama_kelurahan', $value['F'])->first();
            if (!$kelurahan) {
                $hasInvalidKelurahan = true;
                continue;
            }

            $nik = preg_replace('/\D/', '', $value['D']);
            if (strlen($nik) !== 16) {
                $hasEmptyField = true;
                continue;
            }

            $jk = strtoupper(trim($value['G']));
            if (in_array($jk, ['LAKI-LAKI', 'LAKI', 'L'])) {
                $jk = 'L';
            } elseif (in_array($jk, ['PEREMPUAN', 'P'])) {
                $jk = 'P';
            } else {
                $hasEmptyField = true;
                continue;
            }

            $userId  = Str::uuid();
            $nakesId = Str::uuid();

            $insertUser[] = [
                'id'         => $userId,
                'name'       => trim($value['A']),
                'username'   => trim($value['B']),
                'password'   => Hash::make($value['C']),
                'role'       => 'nakes',
                'created_at' => now()
            ];

            $insertNakes[] = [
                'id'            => $nakesId,
                'user_id'       => $userId,
                'nik'           => $nik,
                'no_telepon'    => trim($value['E']),
                'kelurahan_id'  => $kelurahan->id,
                'jenis_kelamin' => $jk,
                'created_at'    => now()
            ];
        }

        $errors = [];

        if ($hasEmptyField) {
            $errors[] = 'Terdapat kolom yang kosong';
        }
        if ($hasDuplicateUsername) {
            $errors[] = 'Username ada yang sudah terdaftar';
        }
        if ($hasInvalidKelurahan) {
            $errors[] = 'Kelurahan tidak ditemukan';
        }

        if (!empty($errors)) {
            return response()->json([
                'status'  => false,
                'message' => 'Import gagal',
                'errors'  => $errors
            ], 422);
        }

        DB::transaction(function () use ($insertUser, $insertNakes) {
            UserModel::insert($insertUser);
            UserNakesModel::insert($insertNakes);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil diimport'
        ]);
    }
}
