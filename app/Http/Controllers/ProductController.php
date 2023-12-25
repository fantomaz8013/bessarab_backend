<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Получить список продуктов
     * @queryParam  title string Название продукта. Example: Шампунь
     * @queryParam  category int Id категории.
     * @queryParam  size string[] Нужные объемы. Example: ["200", "1000"]
     * @queryParam  page int Страница. Example: 1
     * @queryParam  limit int Сколько выдать записей. Example: 10.
     * @queryParam  orderByAsc string сортировка по возрастанию
     * @queryParam  orderByDesc string сортировка по убыванию
     */
    public function index(ProductFilter $filters)
    {
        $data =  Product::filter($filters)
            ->with('category','sizes')
            ->get();
        $pages = $filters->countPages;

        return response()->json(["result" => $data, "pages" => $pages]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Создать продукт
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
                    'price' => $size['price'],
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
     * Получить продукт по ID
     */
    public function show(Product $Product)
    {
        return Product::where('id', $Product->id)
            ->with('category', 'images', 'sizes')
            ->first();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Изменить продукт
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            File::delete($product->avatar_url);
            $imageName = time().rand(111111, 999999).'.'.$file->extension();
            $imagePath = 'storage/img';
            $file->move(public_path($imagePath), $imageName);
            $data['avatar_url'] =  $imagePath . '/' . $imageName;
        }

        $product->update($data);

        $product->save();

        if (isset($data['sizes']))
        {
            foreach ($data['sizes'] as $size)
            {
                ProductSize::create([
                    'value' => $size['value'],
                    'unit' => $size['unit'],
                    'price' => $size['price'],
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

        if (isset($data['delete_images'])) {
            foreach ($data['delete_images'] as $delete_image) {
                $productImage = ProductImage::find($delete_image);
                if (isset($productImage)) {
                    File::delete($productImage->url);
                    $productImage->delete();
                }
            }
        }

        if (isset($data['delete_sizes'])) {
            foreach ($data['delete_sizes'] as $delete_image) {
                $productSize = ProductSize::find($delete_image);
                if (isset($productSize)) {
                    $productSize->delete();
                }
            }
        }

        return response()->json(["result" => "Ok"]);
    }

    /**
     * Удалить продукт
     */
    public function destroy(Product $product)
    {
        $product->is_delete = true;
        $product->save();
        return response()->json(["result" => "Ok"]);
    }
}
