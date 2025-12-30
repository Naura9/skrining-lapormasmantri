<?php
namespace App\Helpers\FamilyMember;

use App\Helpers\Helper;
use App\Models\FamilyMemberModel;
use Throwable;

class FamilyMemberHelper extends Helper
{
    private $familyMemberModel;

    public function __construct()
    {
        $this->familyMemberModel = new FamilyMemberModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $dfamilyMembers = $this->familyMemberModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $dfamilyMembers
        ];
    }

    public function getById(string $id): array
    {
        $familyMember = $this->familyMemberModel->getById($id);
        if (!$familyMember) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $familyMember
        ];
    }

    public function create(array $payload): array
    {
        try {
            $familyMember = $this->familyMemberModel->store($payload);

            return [
                'status' => true,
                'data' => $familyMember
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function update(array $payload, string $id): array
    {
        try {
            $this->familyMemberModel->edit($payload, $id);

            $familyMember = $this->getById($id);
            return [
                'status' => true,
                'data' => $familyMember['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $id): bool
    {
        try {
            $this->familyMemberModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
