<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KelurahanModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_kelurahan";
    protected $fillable = [
        'nama_kelurahan',
    ];

    public $timestamp = true;

    public function posyandu()
    {
        return $this->hasMany(PosyanduModel::class, 'kelurahan_id', 'id')
                    ->orderBy('nama_posyandu', 'ASC');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $kelurahans = $this->query();

        if (!empty($filter['nama_kelurahan'])) {
            $kelurahans->where('nama_kelurahan', 'LIKE', '%' . $filter['nama_kelurahan'] . '%');
        }

        if (!empty($filter['nama_posyandu'])) {
            $kelurahans->whereHas('posyandu', function ($q) use ($filter) {
                $q->where('nama_posyandu', 'LIKE', '%' . $filter['nama_posyandu'] . '%');
            });
        }

        $sort = $sort ?: 'nama_kelurahan DESC';
        $kelurahans->orderByRaw($sort);
        $itemPerPage = ($itemPerPage > 0) ? $itemPerPage : false;

        return $kelurahans->paginate($itemPerPage)->appends('sort', $sort);
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
