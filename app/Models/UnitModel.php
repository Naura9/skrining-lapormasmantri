<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_unit_rumah";
    protected $fillable = [
        'kelurahan_id',
        'posyandu_id',
        'alamat',
        'rt',
        'rw'
    ];

    public $timestamp = true;

    public function kelurahan()
    {
        return $this->belongsTo(KelurahanModel::class, 'kelurahan_id', 'id');
    }

    public function posyandu()
    {
        return $this->belongsTo(PosyanduModel::class, 'posyandu_id', 'id');
    }

    public function keluarga()
    {
        return $this->hasMany(KeluargaModel::class, 'unit_rumah_id', 'id');
    }
    
    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $unit = $this->query();

        if (!empty($filter['nama_kategori'])) {
            $unit->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        $total = $unit->count();
        $sort = $sort ?: 'created_at ASC';
        $query = $unit->orderByRaw($sort);

        if ($itemPerPage > 0) {
            $query->skip($skip)->take($itemPerPage);
        }

        $list = $query->get();
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
