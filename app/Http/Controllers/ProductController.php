<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @bodyParam title string Название продукта. Example: Шампунь
     * @bodyParam category int Id категории.
     * @bodyParam size string[] Нужные объемы. Example: ["200", "1000"]
     * @bodyParam page int Страница. Example: 1
     * @bodyParam limit int Сколько выдать записей. Example: 10.
     */
    public function index(ProductFilter $filters)
    {
        return Product::filter($filters)
            ->with('category','sizes')
            ->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @header Content-Type multipart/form-data
     * @header Accept multipart/form-data
     * @param ProductStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $imageName = time().rand(111111, 999999).'.'.$file->extension();
            $imagePath = 'storage/img';
            $file->move(public_path($imagePath), $imageName);
            $data['avatar_url'] =  $imagePath . '/' . $imageName;
        }

        $product = Product::create($data);

        $product->save();
        if (isset($data['sizes']))
        {
            foreach ($data['sizes'] as $size)
            {
                ProductSize::create([
                    'value' => $size['value'],
                    'unit' => $size['unit'],
                    'product_id' => $product->id,
                ]);
            }
        }

        if ($request->hasFile('images'))
        {
            $images = $request->file('images');
            foreach ($images as $file)
            {
                $imageName = time().rand(111111, 999999).'.'.$file->extension();
                $imagePath = 'storage/img';
                $file->move(public_path($imagePath), $imageName);
                $image = ProductImage::create([
                    'product_id' =>  $product->id,
                    'url' => $imagePath . '/' . $imageName
                ]);
            }
        }

        return response()->json(["result" => "Ok"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
