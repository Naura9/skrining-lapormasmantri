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
    
    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $user = $this->query();

        if (!empty($filter['nama_kategori'])) {
            $user->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        $total = $user->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $user->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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
