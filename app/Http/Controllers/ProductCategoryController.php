<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Получить список категорий
     */
    public function index()
    {
        return ProductCategory::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создать категорию
     */
    public function store(CreateCategoryRequest $request)
    {
        $data = $request->validated();
        $category = ProductCategory::create($data);
        return response()->json(["result" => "Ok", "categoryId" => $category->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Обновить категорию
     */
    public function update(CreateCategoryRequest $request, ProductCategory $productCategory)
    {
        $data = $request->validated();
        $category = ProductCategory::find($productCategory->id);
        $category->update($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        //
    }
}
