<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JawabanModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "t_jawaban";
    protected $fillable = [
        'skrining_id',
        'pertanyaan_id',
        'anggota_keluarga_id',
        'value_jawaban'
    ];

    public $timestamp = true;

    public function skrining()
    {
        return $this->belongsTo(SkriningModel::class, 'skrining_id', 'id');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(PertanyaanModel::class, 'pertanyaan_id', 'id');
    }

    public function anggota()
    {
        return $this->belongsTo(AnggotaKeluargaModel::class, 'anggota_keluarga_id', 'id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $jawaban = $this->query();

        if (!empty($filter['skrining_id'])) {
            $jawaban->where('skrining_id', $filter['skrining_id']);
        }

        $total = $jawaban->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $jawaban->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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
