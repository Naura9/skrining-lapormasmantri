<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyMemberModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "m_family_member";
    protected $fillable = [
        'family_id',
        'full_name',
        'national_id_number',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'relationship',
        'marital_status',
        'last_education',
        'occupation'
    ];

    public $timestamp = true;

    public function family()
    {
        return $this->belongsTo(FamilyModel::class, 'family_id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $familyMember = $this->query();

        if (!empty($filter['full_name'])) {
            $familyMember->where('full_name', 'LIKE', '%' . $filter['full_name'] . '%');
        }

        $total = $familyMember->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $familyMember->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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
