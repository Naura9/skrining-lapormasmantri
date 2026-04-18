<?php

namespace App\Models\User;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements CrudInterface, JWTSubject
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_user";
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    public $timestamp = true;

    public function adminDetail()
    {
        return $this->hasOne(UserAdminModel::class, 'user_id', 'id');
    }

    public function nakesDetail()
    {
        return $this->hasOne(UserNakesModel::class, 'user_id', 'id');
    }

    public function kaderDetail()
    {
        return $this->hasOne(UserKaderModel::class, 'user_id', 'id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;

        $user = $this->query()->with([
            'adminDetail',
            'nakesDetail',
            'kaderDetail'
        ]);

        if (!empty($filter['name'])) {
            $user->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if (!empty($filter['role'])) {

            if ($filter['role'] === 'kader') {
                if (!empty($filter['kelurahan'])) {
                    $user->whereHas('kaderDetail.posyandu.kelurahan', function ($q) use ($filter) {
                        $q->where('nama_kelurahan', 'LIKE', '%' . $filter['kelurahan'] . '%');
                    });
                }

                if (!empty($filter['posyandu'])) {
                    $user->whereHas('kaderDetail.posyandu', function ($q) use ($filter) {
                        $q->where('nama_posyandu', 'LIKE', '%' . $filter['posyandu'] . '%');
                    });
                }

                if (!empty($filter['status'])) {
                    $user->whereRelation('kaderDetail', 'status', $filter['status']);
                }
            }

            if ($filter['role'] === 'nakes') {
                if (!empty($filter['no_telepon'])) {
                    $user->whereHas('nakesDetail', function ($q) use ($filter) {
                        $q->where('no_telepon', 'LIKE', '%' . $filter['no_telepon'] . '%');
                    });
                }

                if (!empty($filter['kelurahan'])) {
                    $user->whereHas('nakesDetail.kelurahan', function ($q) use ($filter) {
                        $q->where('nama_kelurahan', 'LIKE', '%' . $filter['kelurahan'] . '%');
                    });
                }
            }
        }

        $total = $user->count();
        $sort = $sort ?: 'name DESC';
        $list = $user->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

        return [
            'total' => $total,
            'data' => $list,
        ];
    }

    public function getById(string $id)
    {
        return $this->with([
            'adminDetail',
            'nakesDetail',
            'kaderDetail.posyandu.kelurahan'
        ])->where('id', $id)->first();
    }

    public function store(array $payload)
    {
        return $this->create($payload);
    }

    public function edit(array $payload, string $id)
    {
        return $this->find($id)->update($payload);
    }

    public function drop(string $id)
    {
        return $this->find($id)->delete();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,
                'username' => $this->username,
                'updated_security' => $this->updated_security,
            ],
        ];
    }
}
