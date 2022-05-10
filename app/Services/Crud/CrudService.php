<?php

namespace App\Services\Crud;

use App\Contracts\Services\CrudModelInterface;
use App\Contracts\Services\CrudServiceInterface;
use App\Exceptions\StatusBarException;
use Illuminate\Support\Collection;

class CrudService implements CrudServiceInterface
{
    /**
     * @param $eloquentModelClass
     * @param $id
     * @return array
     * @throws StatusBarException
     */
    public function getEntityData(string $eloquentModelClass, int $id): array
    {
        $modelClass = 'App\\Models\\' . $eloquentModelClass;
        $this->checkEloquentClass($modelClass);

        /** @var CrudModelInterface $model */
        $model = $modelClass::findOrFail($id);

        $out = [];
        foreach ($model::getAttributesInfo() as $meta => $fieldName) {
            $metas = explode('|', $meta);
            $attr = $metas[0];
            $out[] = ['attribute_name' => $fieldName, 'attribute' => $attr, 'value' => $model->{$attr}];
        }

        return $out;
    }

    /**
     * @param string $eloquentModelClass
     * @param int|null $id
     * @return array
     * @throws StatusBarException
     */
    public function getFormData(string $eloquentModelClass, ?int $id): array
    {
        $modelClass = 'App\\Models\\' . $eloquentModelClass;
        $this->checkEloquentClass($modelClass);

        $model = $id ? $modelClass::findOrFail($id) : new $modelClass();

        $outs = [];
        foreach ($model::getAttributesInfo() as $meta => $fieldName) {
            $metas = explode('|', $meta);
            $attr = array_shift($metas);

            $out = [
                'label' => $fieldName,
                'name' => $attr,
                'value' => $model->{$attr},
                'required' => (in_array('required', $metas) ? 'required' : '')];

            if (in_array('hidden', $metas)) {
                $out['type'] = 'hidden';
            } elseif (in_array('textarea', $metas)) {
                $out['type'] = 'textarea';
            } elseif
            (in_array('number', $metas)) {
                $out['type'] = 'number';
            } else {
                $out['type'] = 'text';
            }

            $outs[] = $out;
        }

        return $outs;
    }

    /**
     * @param $eloquentModelClass
     * @param Collection $formData
     * @return void
     * @throws StatusBarException
     */
    public function save(string $eloquentModelClass, Collection $formData): int
    {
        $modelClass = 'App\\Models\\' . $eloquentModelClass;
        $this->checkEloquentClass($modelClass);

        $model = $formData->get('id') ? $modelClass::findOrFail($formData->get('id')) : new $modelClass();
        $errors = [];
        foreach ($model::getAttributesInfo() as $meta => $fieldName) {
            $metas = explode('|', $meta);
            $attr = $metas[0];

            $val = $formData->get($attr);


            if (in_array('required', $metas) && blank($formData->get($attr))) {
                $errors[] = __('Field :fieldName cannot be empty!', ['fieldName' => $fieldName]);
                continue;
            }

            if (in_array('number', $metas) && !is_numeric($formData->get($attr))) {
                $errors[] = __('Field :fieldName has to be number!', ['fieldName' => $fieldName]);
            }

            if (!blank($val)) {
                $model->{$attr} = $val;
            }
        }

        if (empty($errors)) {
            $model->save();
            return $model->id;
        } else {
            throw new StatusBarException(implode('<br>', $errors));
        }
    }

    public function deleteEntity(string $eloquentModelClass, int $id): int
    {
        $modelClass = 'App\\Models\\' . $eloquentModelClass;
        $this->checkEloquentClass($modelClass);
        if ($entiy = $modelClass::find($id)) {
            $entiy->delete();
        }
        return $entiy->id;
    }

    /**
     * @param string $eloquentModelClass
     * @return array
     * @throws StatusBarException
     */
    public function getListData(string $eloquentModelClass): array
    {
        $modelClass = 'App\\Models\\' . $eloquentModelClass;
        $this->checkEloquentClass($modelClass);

        $headerDatas = [];
        foreach ($modelClass::getAttributesInfo() as $fieldName) {
            $headerDatas[] = $fieldName;
        }

        $datas = [];
        foreach ($modelClass::all() as $item) {
            $data = [];
            foreach ($modelClass::getAttributesInfo() as $meta => $fieldName) {
                $metas = explode('|', $meta);
                $attr = $metas[0];
                $data[$attr] = strval($item->{$attr});
            }
            $datas[$item->id] = $data;
        }


        return [
            'header' => $headerDatas,
            'data' => $datas
        ];

    }

    public static function getEloquentClassTitle(string $eloquentClassName): string
    {
        $modelClass = 'App\\Models\\' . $eloquentClassName;

        return ($modelClass::$title ?? $eloquentClassName);
    }

    /**
     * @param $modelClass
     * @return void
     * @throws StatusBarException
     */
    protected function checkEloquentClass($modelClass): void
    {
        if (!class_exists($modelClass) || !is_subclass_of($modelClass, CrudModelInterface::class)) {
            throw new StatusBarException(__('Not Valid Eloquent Model Class: ' . $modelClass));
        }
    }


}
