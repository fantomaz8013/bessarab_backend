<?php


namespace App\Filters;


use App\Http\Requests\OrderRequest;
use App\Http\Requests\ProductRequest;

class OrderFilter extends QueryFilter
{
    public function __construct(OrderRequest $request)
    {
        parent::__construct($request);
    }

    public function firstName($title)
    {
        return $this->builder->where('first_name','LIKE','%'.$title.'%');
    }

    public function lastName($title)
    {
        return $this->builder->where('last_name','LIKE','%'.$title.'%');
    }

    public function email($title)
    {
        return $this->builder->where('email','LIKE','%'.$title.'%');
    }

    public function phone($title)
    {
        return $this->builder->where('phone','LIKE','%'.$title.'%');
    }

    public function city($title)
    {
        return $this->builder->where('city','LIKE','%'.$title.'%');
    }

    public function address($title)
    {
        return $this->builder->where('address','LIKE','%'.$title.'%');
    }
}
