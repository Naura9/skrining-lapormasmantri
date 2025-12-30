<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_family";
    protected $fillable = [
        'family_card_number',
        'head_of_family',
        'address',
        'neighborhood_rt',
        'neighborhood_rw',
        'urban_village',
        'posyandu'
    ];

    public $timestamp = true;

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $family = $this->query();

        if (!empty($filter['family_card_number'])) {
            $family->where('family_card_number', 'LIKE', '%' . $filter['family_card_number'] . '%');
        }
        if (!empty($filter['neighborhood_rt'])) {
            $family->where('neighborhood_rt', 'LIKE', '%' . $filter['neighborhood_rt'] . '%');
        }
        if (!empty($filter['neighborhood_rw'])) {
            $family->where('neighborhood_rw', 'LIKE', '%' . $filter['neighborhood_rw'] . '%');
        }
        if (!empty($filter['urban_village'])) {
            $family->where('urban_village', 'LIKE', '%' . $filter['urban_village'] . '%');
        }

        $total = $family->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $family->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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
