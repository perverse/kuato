<?php

namespace Kuato\Repositories\Criteria\User;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;


class SearchForName extends Criteria
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function apply($model, Repository $repo)
    {
        $model = $model->where(function($query){
            $query->where('first_name', 'LIKE', '%' . $this->name . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $this->name . '%'); 
        });

        return $model;
    }
}