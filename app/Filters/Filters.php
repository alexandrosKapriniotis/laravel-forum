<?php


namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    /**
     * @var Request
     */
    protected $request, $builder;

    protected $filters = [];

    /**
     * ThreadFilters constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $builder
     */
    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value){
            if(method_exists($this,$filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    /**
     * Get filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->request->only($this->filters);
    }

}
