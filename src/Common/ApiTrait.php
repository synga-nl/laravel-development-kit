<?php
namespace Synga\LaravelDevelopment\Common;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

/**
 * Trait MethodHelper
 * @package App\Api
 */
trait ApiTrait
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param array $validationRules
     * @param Model|Builder $model
     * @param callable|array $existenceCheck
     * @param callable|null $refactorData
     * @return \Illuminate\Http\Resources\Json\Resource|JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function post(
        Request $request,
        array $validationRules,
        $model,
        $existenceCheck,
        callable $refactorData = null
    )
    {
        $this->isValidModel($model);
        $this->hasValidResource();

        \Validator::validate($request->json()->all(), $validationRules);

        $data = $this->getValidatedData($request->json()->all(), $validationRules);

        if (!empty($refactorData)) {
            $data = $refactorData($data);
        }

        $exception = new \LogicException('Data already exist in data source');
        if (is_callable($existenceCheck)) {
            if (true === $existenceCheck($model, $data)) {
                return $this->handleError($exception, 'already_exists');
            }
        } else {
            $dataCopy = [];

            foreach ($existenceCheck as $check) {
                if (isset($data[$check])) {
                    $dataCopy[$check] = $data[$check];
                }
            }

            if ($model->where($dataCopy)->count() > 0) {
                return $this->handleError($exception, 'already_exists');
            }
        }

        return new $this->resourceClass($model->create($data));
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Model|Builder $model
     * @return Resource
     */
    protected function all(Request $request, $model, bool $paginate = true)
    {
        $this->isValidModel($model);
        $this->hasValidResource();

        $model->with($this->with($request));

        $result = forward_static_call([$this->resourceClass, 'collection'],
            (false === $paginate) ? $model->get() : $model->paginate($request->get('per_page', null))
        );

        $result->appends($request->query());

        return $result;
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param string $id
     * @param $model
     * @return \Illuminate\Http\Resources\Json\Resource|JsonResponse
     * @throws \Exception
     */
    protected function get(Request $request, $id, $model)
    {
        $this->isValidModel($model);
        $this->hasValidResource();

        /* @var $query \Illuminate\Database\Eloquent\Builder */
        $query = $model->with($this->with($request));

        if (is_callable($id)) {
            $query = $id($query);
        } else {
            $query->where([
                'uuid' => $id
            ]);
        }

        $recipe = $query->first();

        if (empty($recipe)) {
            return $this->handleError(
                new \Exception(
                    sprintf('The %s does not exist', (new \ReflectionClass($query))->getShortName()),
                    404
                ), 'not_found'
            );
        }

        return new $this->resourceClass($recipe);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @param  Model|Builder $model
     * @return \Illuminate\Http\Response|JsonResponse
     */
    protected function patch(Request $request, $id, array $validationRules, $model)
    {
        $this->hasValidResource();
        $this->isValidModel($model);

        $request->validate($validationRules);

        $data = $this->getValidatedData($request->json()->all(), array_keys($validationRules));

        $foundModel = $model->where('uuid', $id)->first();

        if (!empty($foundModel)) {
            foreach ($data as $column => $value) {
                $foundModel->{$column} = $value;
            }

            $foundModel->save();

            return new $this->resourceClass($foundModel);
        }

        $message = sprintf('The row with id %s could not be found.', $id);

        return $this->handleError(new \Exception($message, 404), 'not_found');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param Model|Builder $model
     * @return JsonResponse
     */
    protected function delete(Request $request, $id, $model)
    {
        $this->isValidModel($model);

        return new JsonResponse(['affected_rows' => $model->where('uuid', $id)->delete()]);
    }

    /**
     * Checks if the given resource is valid
     */
    protected function hasValidResource()
    {
        if (!isset($this->resourceClass) || !class_exists($this->resourceClass)) {
            $this->handleError(new \InvalidArgumentException('$resourceClass is not a valid class'), 'code_error');
        }
    }

    /**
     * Checks if the given model is valid
     *
     * @param $model
     */
    protected function isValidModel($model)
    {
        if (!($model instanceof Model || $model instanceof Builder)) {
            $this->handleError(new \InvalidArgumentException('$model is not a Model or Builder'), 'code_error');
        }
    }

    /**
     * Handle the given error.
     *
     * @param \Exception $exception
     * @param array $additionalData
     * @return JsonResponse
     */
    protected function handleError(\Exception $exception, $key, $additionalData = [])
    {
        $reflectionClass = new \ReflectionClass($exception);

        $data = array_merge([
            'error' => $exception->getMessage(),
            'key' => $key,
            'error_type' => $reflectionClass->getShortName()
        ], $additionalData);

        if (app()->environment('local')) {
            $data = array_merge($data, [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'code' => $exception->getCode(),
                'id' => Uuid::uuid4()
            ]);
        }

        ksort($data);

        $response = new JsonResponse($data);
        $response->setStatusCode((empty($exception->getCode())) ? 500 : $exception->getCode());

        return $response;
    }

    /**
     * Returns the valid data
     *
     * @param $data
     * @param $validationRules
     * @return array|null
     */
    protected function getValidatedData($data, $validationRules)
    {
        $result = [];

        $keys = (array_intersect(
            array_keys($data),
            array_keys($validationRules)
        ));

        foreach ($keys as $key) {
            $result[$key] = $data[$key];
        }

        return $result;
    }

    /**
     * Includes all relations
     *
     * @param Request $request
     * @return array
     */
    protected function with(Request $request)
    {
        $with = [];

        if (property_exists($this, 'relations')) {
            $requestWith = explode(',', $request->query('with'));

            foreach ($requestWith as $requestWithOne) {
                foreach ($this->relations as $relation) {
                    $requestWithOneTemp = $requestWithOne;
                    if(substr_count($requestWithOne, '.') != substr_count($relation, '.')){
                        $requestWithOneTemp .= '.';
                    }
                    if (starts_with($relation, $requestWithOneTemp)) {
                        $with[] = $requestWithOne;
                    }
                }
            }
        }

        return $with;
    }

    /**
     * Convert comma seperated list to array
     *
     * @param $ids
     * @return array
     */
    protected function ids($ids){
        $ids = explode(',', $ids);

        return $ids;
    }
}