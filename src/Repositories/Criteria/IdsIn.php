<?php

namespace Kuato\Repositories\Criteria;

use Bosnadev\Repositories\Criteria\Criteria;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;

class IdsIn extends Criteria
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function apply($model, Repository $repo)
    {
        if (empty($this->ids)) {
            app('Illuminate\Contracts\Logging\Log')->warning("Kuato\Repositories\Criteria\IdsIn received an empty id array");
        }

        $model = $model->whereIn('id', $this->ids);
        return $model;
    }
}