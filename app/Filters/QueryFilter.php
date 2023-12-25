<?php


namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    public $request;

    protected $builder;

    protected $delimiter = ',';

    public $countPages = 0 ;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        $filters = $this->filters();

        foreach ($this->filters() as $name => $value) {
            if (method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }

        $page = $filters['page'] ?? 1;
        $limit = $filters['limit'] ?? 10;
        $b = clone $this->builder;
        $count = count($b->get());
        $this->countPages = ceil($count / $limit);
        $this->builder->skip(($page - 1) * 10)->take($limit);

        return $this->builder;
    }

    public function filters()
    {
        return $this->request->query();
    }

    public function orderByAsc($order)
    {
        return $this->builder->orderBy($order, 'ASC');
    }

    public function orderByDesc($order)
    {
        return $this->builder->orderBy($order, 'DESC');
    }

    protected function paramToArray($param)
    {
        return explode($this->delimiter, $param);
    }
}
