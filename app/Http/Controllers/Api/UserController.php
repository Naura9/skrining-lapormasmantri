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
        $user = $this->user->delete($id);

        if (!$user) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($user, 'User berhasil dihapus');
    }

    public function import(Request $request)
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
            'message' => 'Data kader berhasil diimport'
        ]);
    }
}
