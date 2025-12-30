<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use App\Repository\CrudInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnswerModel extends Model implements CrudInterface
{
    use HasFactory;
    use Uuid;
    use SoftDeletes;

    protected $table = "t_answer";
    protected $fillable = [
        'question_id',
        'family_id',
        'family_member_id',
        'screening_id',
        'answer_value',
    ];

    public $timestamp = true;

    public function question()
    {
        return $this->belongsTo(QuestionModel::class, 'question_id');
    }

    public function family()
    {
        return $this->belongsTo(FamilyModel::class, 'family_id');
    }

    public function familyMember()
    {
        return $this->belongsTo(FamilyMemberModel::class, 'family_member_id');
    }

    public function screening()
    {
        return $this->belongsTo(ScreeningModel::class, 'screening_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skip = ($page * $itemPerPage) - $itemPerPage;
        $answer = $this->query();

        if (!empty($filter['question_type'])) {
            $answer->where('question_type', 'LIKE', '%' . $filter['question_type'] . '%');
        }

        $total = $answer->count();
        $sort = $sort ?: 'created_at ASC';
        $list = $answer->skip($skip)->take($itemPerPage)->orderByRaw($sort)->get();

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
