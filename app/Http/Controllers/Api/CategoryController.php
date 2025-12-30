<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Helpers\Category\CategoryHelper;
use App\Http\Resources\Category\CategoryResource;

class CategoryController extends Controller
{
    private $category;

    public function __construct()
    {
        $this->category = new CategoryHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'category_name' => $request->category_name ?? '',
        ];

        $categorys = $this->category->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => CategoryResource::collection($categorys['data']['data']),
            'meta' => [
                'total' => $categorys['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $category = $this->category->getById($id);

        if (!$category['status']) {
            return response()->failed(['Data kategori tidak ditemukan'], 404);
        }

        return response()->success(new CategoryResource($category['data']));
    }

    public function store(CategoryRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['category_name']);
        $category = $this->category->create($payload);

        if (!$category['status']) {
            return response()->failed($category['error']);
        }

        return response()->success(new CategoryResource($category['data']), 'Kategori berhasil ditambahkan');
    }

    public function update(CategoryRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'category_name']);
        $category = $this->category->update($payload, $payload['id']);

        if (!$category['status']) {
            return response()->failed($category['error']);
        }

        return response()->success(new CategoryResource($category['data']), 'Kategori berhasil diubah');
    }

    public function destroy($id)
    {
        $category = $this->category->delete($id);

        if (!$category) {
            return response()->failed(['Mohon maaf data soal tidak ditemukan']);
        }

        return response()->success($category, 'Kategori berhasil dihapus');
    }
}
