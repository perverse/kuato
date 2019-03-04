<?php

namespace Kuato\Services;

use Kuato\Contracts\RelationshipLoaderInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class RelationshipLoader extends RelationshipLoaderInterface
{
    public function dotNotation($relationship, $parent_nesting)
    {
        $ret = '';

        if (!empty($parent_nesting)) { 
            $ret = $parent_nesting . '.' . $relationship;
        } else {
            $ret = $relationship;
        }

        return $ret;
    }

    public function getRelatedModel($parent_model, $relationship)
    {
        $related_model = $parent_model->$relationship;

        if ($related_model instanceof EloquentCollection) {
            $related_model = $related_model->first();
        }

        return $related_model;
    }

    public function removeJunkRelationships($nested_relationships)
    {
        $delete_index = array_search('pivot', $nested_relationships);

        if ($delete_index !== false) {
            unset($nested_relationships[$delete_index]);
        }

        return $nested_relationships;
    }

    public function getLoadedRelationshipsRecursive($model, $loaded_relations, $return_array=[], $parent_nesting='')
    {
        foreach ($loaded_relations as $relationship) {
            $related_model = $this->getRelatedModel($model, $relationship);

            if ($related_model && $related_model instanceof EloquentModel) {
                $nested_relationships = $related_model->getLoadedRelationships();
                $nested_relationships = $this->removeJunkRelationships($nested_relationships);

                if (!empty($nested_relationships)) {
                    $new_nesting = $this->dotNotation($relationship, $parent_nesting);

                    $return_array = $this->getLoadedRelationshipsRecursive($related_model, $nested_relationships, $return_array, $new_nesting);
                } else {
                    $return_array[] = $this->dotNotation($relationship, $parent_nesting);
                }
            } else {
                $return_array[] = $this->dotNotation($relationship, $parent_nesting);
            }
        }

        return $return_array;
    }
}