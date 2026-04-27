<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnggotaKeluargaModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_anggota_keluarga";
    protected $fillable = [
        'id',
        'keluarga_id',
        'nama',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_kk_asal',
        'hubungan_keluarga',
        'status_perkawinan',
        'pendidikan_terakhir',
        'pekerjaan'
    ];

    public $timestamp = true;

    public function keluarga()
    {
        return $this->belongsTo(KeluargaModel::class, 'keluarga_id', 'id');
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanModel::class, 'anggota_keluarga_id', 'id');
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
