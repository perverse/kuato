<?php

namespace Kuato\Services;

use Kuato\Containers\ServiceResponse;
use Kuato\Fractal\Serializers\KuatoDataArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Resource\Collection as FractalCollection;
use Illuminate\Contracts\Container\Container as ServiceContainer;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Kuato\Exceptions\ServiceResponseException;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Kuato\Contracts\RelationshipLoaderInterface;

class ServiceResponseFormatter
{
    protected $service_response;

    public function __construct(ServiceContainer $app, Fractal $fractal, RelationshipLoaderInterface $relationship_loader)
    {
        $this->app = $app;
        $this->fractal = $fractal;
        $this->relationship_loader = $relationship_loader;

        $this->fractal->setSerializer(new KuatoDataArraySerializer);
    }

    public function make(ServiceResponse $service_response)
    {
        $formatter = $this->app->make(self::class);
        $formatter->setServiceResponse($service_response);

        return $formatter;
    }

    public function setServiceResponse(ServiceResponse $service_response)
    {
        $this->service_response = $service_response;

        return $this;
    }

    public function getServiceResponse()
    {
        return $this->service_response;
    }

    protected function loadFractalIncludes($model=null)
    {
        if (is_null($model)) {
            $model = $this->getActiveModel();
        }

        if ($model && is_object($model) && method_exists($model, 'getLoadedRelationships')) {
            $loaded_relations = $this->relationship_loader->getLoadedRelationshipsRecursive($model, $model->getLoadedRelationships());
            
            $this->fractal->parseIncludes($loaded_relations);
        }
    }

    protected function getActiveModel()
    {
        switch ($this->service_response->getDataType()) {
            case ServiceResponse::DATA_ITEM:
                $model = $this->service_response->getOriginalData();
                break;

            case ServiceResponse::DATA_COLLECTION:
                $model = $this->service_response->getOriginalData()->first();
                break;

            case ServiceResponse::DATA_PAGINATION:
                $model = $this->service_response->getOriginalData()->getCollection()->first();
                break;

            default:
                $model = null;
                break;
        }

        return $model;
    }

    protected function getTransformer()
    {
        if ($transformer = $this->service_response->getTransformer()) {
            return $transformer;
        } else {
            $full_class_name = false;

            $model = $this->getActiveModel();

            if ($model && is_object($model)) {
                $full_class_name = get_class($model);
                $transformer = $this->app->make($full_class_name)->getTransformer();

                if ($transformer) {
                    return $transformer;
                }
            }
        }

        // Couldn't find a transformer after all that, throw an error! we need one!
        // throw new ServiceResponseException('Could not find Transformer class for payload: <br><pre>' . print_r($this->service_response->getOriginalData(), true));

        // Fallback to a standard/default transformer.
        return $this->app->make("Kuato\Fractal\Transformers\FallbackTransformer");
    }

    protected function organiseFractalData($result)
    {
        $result[$this->service_response->getResultIndex()] = $result['data'];
        unset($result['data']);

        return $result;
    }

    protected function paginatorData()
    {
        $resource = new FractalCollection($this->service_response->getOriginalData(), $this->getTransformer(), 'result');
        $resource->setPaginator(new IlluminatePaginatorAdapter($this->service_response->getOriginalData()));

        $result = $this->fractal->createData($resource)->toArray();

        return $result;
    }

    /*
    protected function collectionData()
    {
        $resource = new FractalCollection($this->service_response->getOriginalData(), $this->getTransformer(), 'result');
        $result = $this->fractal->createData($resource)->toArray();

        return $result;
    }
    */

    protected function collectionData()
    {
        $ret = ['result' => []];

        foreach ($this->service_response->getOriginalData() as $record) {
            $this->loadFractalIncludes($record);

            $resource = new FractalItem($record, $this->getTransformer(), 'result');
            $result = $this->fractal->createData($resource)->toArray();

            $ret['result'][] = array_get($result, 'result', []);
        }

        return $ret;
    }

    protected function itemData()
    {
        $resource = new FractalItem($this->service_response->getOriginalData(), $this->getTransformer(), 'result');
        $result = $this->fractal->createData($resource)->toArray();

        return $result;
    }

    protected function formatErrors(array $errors)
    {
        $organise_array = [];

        foreach ($errors as $index => $value) {
            $matches = [];

            if (preg_match_all('/^([a-zA-Z0-9]*)\.([0-9]*)$/', $index, $matches)) {
                $field = array_get($matches, '1.0');
            } else {
                $field = $index;
            }

            if (!isset($organise_array[$field]))
                $organise_array[$field] = [];

            $organise_array[$field][] = $value;
        }

        return array_map(function($value, $index){
            $ret = [
                'field' => $index
            ];

            if (count($value) > 1) {
                $ret['value'] = $value;
            } else {
                $ret['value'] = head($value);
            }

            return $ret;
        }, $organise_array, array_keys($organise_array));
    }

    public function toJsonResponse()
    {
        if ($this->service_response->getSuccess()) {

            switch ($this->service_response->getDataType()) {
                case ServiceResponse::DATA_ITEM:
                    $this->loadFractalIncludes();
                    $ret = $this->itemData();
                    break;
                case ServiceResponse::DATA_COLLECTION:
                    $ret = $this->collectionData();
                    break;
                case ServiceResponse::DATA_PAGINATION:
                    $this->loadFractalIncludes();
                    $ret = $this->paginatorData();
                    break;
                default:
                    $ret = [];
                    break;
            }

            $ret['messages'] = array_values($this->service_response->getMessages());
        } else {
            $ret['errors'] = $this->formatErrors($this->service_response->getErrors());
        }

        $ret['success'] = $this->service_response->getSuccess();

        return response()->json($ret, $this->service_response->getHttpResponseCode());
    }

    public function toDownloadResponse()
    {
        if ($this->service_response->getSuccess()) {
            $filename = $this->service_response->getData('filename');
            $mime_type = $this->service_response->getData('mime_type');
            $content = $this->service_response->getData('content');

            return response()->make($content, $this->service_response->getHttpResponseCode(), [
                'Content-Type' => $mime_type,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } else {
            response()->json([
                'success' => false,
                'errors' => $this->formatErrors($this->service_response->getErrors())
            ]);
        }
    }
}