<?php

namespace App\Models\User;

use App\Http\Traits\Uuid;
use App\Models\KelurahanModel;
use App\Models\User\UserModel;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNakesModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_user_nakes";
    protected $fillable = [
        'user_id',
        'kelurahan_id',
        'nik',
        'no_telepon',
        'jenis_kelamin',
    ];

    public $timestamp = true;

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function kelurahan()
    {
        return $this->belongsTo(KelurahanModel::class, 'kelurahan_id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $user = $this->query();

        if (!empty($filter['name'])) {
            $user->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        if (!empty($filter['email'])) {
            $user->where('email', 'LIKE', '%' . $filter['email'] . '%');
        }

        $total = $user->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $user->take($itemPerPage)->orderByRaw($sort)->get();

        return [
            'total' => $total,
            'data' => $list,
        ];
    }

    public function getById(string $id)
    {
        return $this->where('id', $id)->first(); 
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
}
