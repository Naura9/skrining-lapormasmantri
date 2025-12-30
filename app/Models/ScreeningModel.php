<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScreeningModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "t_screening";
    protected $fillable = [
        'family_id',
        'user_id',
        'screening_date',
    ];

    public $timestamp = true;

    public function family()
    {
        return $this->belongsTo(FamilyModel::class, 'family_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $screening = $this->query();

        if (!empty($filter['screening_date'])) {
            $screening->where('screening_date', 'LIKE', '%' . $filter['screening_date'] . '%');
        }

        if (!empty($filter['user_id'])) {
            $screening->where('user_id', 'LIKE', '%' . $filter['user_id'] . '%');
        }

        $total = $screening->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $screening->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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
