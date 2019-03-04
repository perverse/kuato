<?php

namespace Kuato\Fractal\Serializers;

use League\Fractal\Serializer\DataArraySerializer;

class KuatoDataArraySerializer extends DataArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        if (!empty($resourceKey)) {
            return [$resourceKey => $data];
        } else {
            return $data;
        }
    }

    public function item($resourceKey, array $data)
    {
        if (!empty($resourceKey)) {
            return [$resourceKey => $data];
        } else {
            return $data;
        }
    }
}