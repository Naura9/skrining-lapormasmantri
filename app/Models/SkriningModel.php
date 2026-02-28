<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkriningModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "t_skrining";
    protected $fillable = [
        'keluarga_id',
        'user_id',
        'tanggal_skrining'
    ];

    public $timestamp = true;

    public function jawaban()
    {
        return $this->hasMany(JawabanModel::class, 'skrining_id', 'id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $skrining = $this->query();

        if (!empty($filter['keluarga_id'])) {
            $skrining->where('keluarga_id', $filter['keluarga_id']);
        }

        $total = $skrining->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $skrining->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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
