<?php

namespace App\Contracts\Services;

use Illuminate\Support\Collection;

interface CrudServiceInterface
{
    public static function getEloquentClassTitle(string $eloquentModelClass): string;

    public function getEntityData(string $eloquentModelClass, int $id): array;

    public function getFormData(string $eloquentModelClass, ?int $id): array;

    public function save(string $eloquentModelClass, Collection $formData): int;

    public function deleteEntity(string $eloquentModelClass, int $id): int;

    public function getListData(string $eloquentModelClass): array;

}
