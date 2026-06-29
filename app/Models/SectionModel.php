<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_section";
    protected $fillable = [
        'kategori_id',
        'judul_section',
        'no_urut'
    ];

    public $timestamp = true;

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'id');
    }

    public function pertanyaan()
    {
        return $this->hasMany(PertanyaanModel::class, 'section_id', 'id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $section = $this->query();

        if (!empty($filter['judul_section'])) {
            $section->where('name', 'LIKE', '%' . $filter['name'] . '%');
        }

        $total = $section->count();
        $sort = $sort ?: 'judul_section ASC';
        $query = $section->orderByRaw($sort);

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
