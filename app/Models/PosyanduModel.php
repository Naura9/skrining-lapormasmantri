<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosyanduModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_posyandu";
    protected $fillable = [
        'kelurahan_id',
        'nama_posyandu'
    ];

    public $timestamp = true;

    public function kelurahan()
    {
        return $this->belongsTo(KelurahanModel::class, 'kelurahan_id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $posyandus = $this->query();

        if (!empty($filter['nama_posyandu'])) {
            $posyandus->where('nama_posyandu', 'LIKE', '%' . $filter['nama_posyandu'] . '%');
        }

        $total = $posyandus->count();
        $sort = $sort ?: 'nama_posyandu ASC';
        $list = $posyandus->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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

    public function dropByKelurahanId(string $kelurahanId)
    {
        return $this->where('kelurahan_id', $kelurahanId)->delete();
    }
}
