<?php


namespace App\Filters;


use App\Http\Requests\ProductRequest;

class ProductFilter extends QueryFilter
{
    public function __construct(ProductRequest $request)
    {
        parent::__construct($request);
    }

    public function title($title)
    {
        return $this->builder->where('title','LIKE','%'.$title.'%');
    }

    public function category($category_id)
    {
        return $this->builder
            ->where('product_category_id', $category_id)
            ->orWhere('additional_product_category_id', $category_id);
    }

    public function line($line_id)
    {
        return $this->builder->where('product_line_id', $line_id);
    }

    public function hide($hide)
    {
        return $this->builder->where('is_hide', $hide);
    }

    public function size($category)
    {
        if (!is_array($category)) {
            return $this->builder->whereIn('sizes.value', [$category]);
        }
        return $this->builder->whereIn('sizes.value', $category);
    }
}
