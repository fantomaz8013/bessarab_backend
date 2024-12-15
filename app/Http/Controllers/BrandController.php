<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Получить список всех брендов
     */
    public function index()
    {
        return Brand::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создать бренд
     */
    public function store(BrandRequest $request)
    {
        $data = $request->validated();
        $brand = Brand::create($data);
        return response()->json(["result" => ['brand-Id' => $brand->id]]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $create_brand_table)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $create_brand_table)
    {
        //
    }

    /**
     * Обновить бренд
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        $data = $request->validated();
        $brand->update($data);
        $brand->save();
        return response()->json(["result" => "Ok"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $create_brand_table)
    {
        //
    }
}
