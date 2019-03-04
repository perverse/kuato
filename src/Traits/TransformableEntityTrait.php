<?php

namespace Kuato\Traits;

trait TransformableEntityTrait
{
    public function relationshipIsLoaded($relationship)
    {
        return array_key_exists($relationship, $this->relations);
    }

    public function getLoadedRelationships()
    {
        return collect($this->relations)
            ->filter(function($value){
                return $value != null;
            })->map(function($value, $key){
                return $key;
            })->toArray();
    }

    abstract public function getTransformer();

    public function pushFakeRelationship($name, $value=[])
    {
        $this->relations[$name] = $value;
        return true;
    }
}