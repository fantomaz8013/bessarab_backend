<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductLineStoreRequest;
use App\Models\ProductLines;
use Illuminate\Http\Request;

class ProductLinesController extends Controller
{
    /**
     * Получить список линеек продукции
     */
    public function index()
    {
       return ProductLines::with('brand')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создать линейку продуктов
     */
    public function store(ProductLineStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $imageName = time().rand(111111, 999999).'.'.$file->extension();
            $imagePath = 'storage/img';
            $file->move(public_path($imagePath), $imageName);
            $data['img'] =  $imagePath . '/' . $imageName;
        }

        $product = ProductLines::create($data);
        return response()->json(["result" => ['product_line_id' => $product->id]]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductLines $productLines)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductLines $productLines)
    {
        //
    }

    /**
     * Обновить линейку проодуктов
     */
    public function update(ProductLineStoreRequest $request, ProductLines $productLines)
    {
        $data = $request->validated();

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $imageName = time().rand(111111, 999999).'.'.$file->extension();
            $imagePath = 'storage/img';
            $file->move(public_path($imagePath), $imageName);
            $data['img'] =  $imagePath . '/' . $imageName;
        }

        $productLines->update($data);
        $productLines->save();
        return response()->json(["result" => "Ok"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductLines $productLines)
    {
        //
    }
}
