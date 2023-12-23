<?php


namespace App\Filters;


use App\Http\Requests\ContactListRequest;
use App\Http\Requests\ProductRequest;

class ContactListFilter extends QueryFilter
{
    public function __construct(ContactListRequest $request)
    {
        parent::__construct($request);
    }

    public function name($title)
    {
        return $this->builder->where('name','LIKE','%'.$title.'%');
    }

    public function phone($title)
    {
        return $this->builder->where('phone','LIKE','%'.$title.'%');
    }

    public function email($title)
    {
        return $this->builder->where('email','LIKE','%'.$title.'%');
    }

    public function type($title)
    {
        return $this->builder->where('type_id', $title);
    }
}
