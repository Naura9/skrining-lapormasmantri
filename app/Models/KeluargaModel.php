<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeluargaModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_keluarga";
    protected $fillable = [
        'unit_rumah_id',
        'no_kk',
        'is_luar_wilayah',
        'alamat_ktp',
        'rt_ktp',
        'rw_ktp',
        'no_telepon'
    ];

    public $timestamp = true;

    public function unitRumah()
    {
        return $this->belongsTo(UnitModel::class, 'unit_rumah_id', 'id');
    }

    public function anggota()
    {
        return $this->hasMany(AnggotaKeluargaModel::class, 'keluarga_id', 'id');
    }

    public function kepalaKeluarga()
    {
        return $this->hasOne(AnggotaKeluargaModel::class, 'keluarga_id', 'id')
            ->where('hubungan_keluarga', 'Kepala Keluarga');
    }

    public function skrining()
    {
        return $this->hasMany(SkriningModel::class, 'keluarga_id', 'id');
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
