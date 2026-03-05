<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Random\Engine\Secure;

class PertanyaanModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_pertanyaan";
    protected $fillable = [
        'section_id',
        'no_urut',
        'pertanyaan',
        'keterangan',
        'is_required',
        'jenis_jawaban',
        'opsi_jawaban',
        'opsi_lain'
    ];

    public $timestamp = true;

    protected $casts = [
        'opsi_jawaban' => 'array',
        'opsi_lain' => 'boolean'
    ];

    public function section()
    {
        return $this->belongsTo(SectionModel::class, 'section_id', 'id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;

        $query = $this->query()
            ->select('m_pertanyaan.*')
            ->join('m_section', 'm_section.id', '=', 'm_pertanyaan.section_id')
            ->orderBy('m_section.no_urut', 'asc')
            ->orderBy('m_pertanyaan.no_urut', 'asc');

        if (!empty($filter['judul_section'])) {
            $query->where('m_section.judul_section', 'LIKE', '%' . $filter['judul_section'] . '%');
        }

        if (!empty($filter['kategori_id'])) {
            $query->whereHas('section', function ($q) use ($filter) {
                $q->where('kategori_id', $filter['kategori_id']);
            });
        }

        $total = $query->count();

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
