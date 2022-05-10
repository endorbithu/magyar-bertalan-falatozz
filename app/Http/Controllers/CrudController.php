<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CrudServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class CrudController extends Controller
{
    public function show(Request $request, CrudServiceInterface $crudService)
    {

        $model = $crudService->getEntityData($request->eloquentModelClass, $request->id);

        if ($request->is('api/*')) {
            return $model;
        }

        return view('crud.show', [
            'model' => $model,
            'eloquentModelClass' => $request->eloquentModelClass,
            'title' => $crudService::getEloquentClassTitle($request->eloquentModelClass),
            'id' => $request->id
        ]);
    }

    public function list(Request $request, CrudServiceInterface $crudService)
    {
        try {

            $list = $crudService->getListData($request->eloquentModelClass);

            if ($request->is('api/*')) {
                return Response::json(['status' => 'success', 'data' => $list], 200);
            }

            return view('crud.list', [
                'eloquentModelClass' => $request->eloquentModelClass,
                'title' => $crudService::getEloquentClassTitle($request->eloquentModelClass),
                'list' => $list
            ]);
        } catch (\Throwable $e) {
            if ($request->is('api/*')) {
                return Response::json(['status' => 'error', 'data' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }


    public function save(Request $request, CrudServiceInterface $crudService)
    {
        try {
            if ($request->is('api/*')) {
                $requestData = !is_array($request->getContent()) ? json_decode($request->getContent(), true) : $request->getContent();
                $requestData = collect($requestData);
            } else {
                $requestData = $request->collect();
            }

            $id = $crudService->save($request->eloquentModelClass, $requestData);

            if ($request->is('api/*')) {
                return Response::json([], 201);
            }

            Session::flash('success', __('Entity with ID: :id has been saved!', ['id' => $id]));
            return redirect()->route('crud.list', [
                'eloquentModelClass' => $request->eloquentModelClass,
            ]);

        } catch (\Throwable $e) {
            if ($request->is('api/*')) {
                return Response::json(['status' => 'error', 'data' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    public function delete(Request $request, CrudServiceInterface $crudService)
    {
        try {
            $id = $crudService->deleteEntity($request->eloquentModelClass, (int)$request->id);

            if ($request->is('api/*')) {
                return Response::json([], 204);
            }

            Session::flash('success', __('Entity with ID: :id has been deleted!', ['id' => $id]));

            return redirect()->route('crud.list', [
                'eloquentModelClass' => $request->eloquentModelClass,
            ]);
        } catch (\Throwable $e) {
            if ($request->is('api/*')) {
                return Response::json(['status' => 'error', 'data' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }

    public function form(Request $request, CrudServiceInterface $crudService)
    {
        $formdata = $crudService->getFormData($request->eloquentModelClass, $request->id);

        return view('crud.form', [
                'formdata' => $formdata,
                'title' => $crudService::getEloquentClassTitle($request->eloquentModelClass),
                'id' => $request->id
            ]
        );
    }

}
