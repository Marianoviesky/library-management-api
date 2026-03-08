<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class GenericController extends Controller
{
    protected string $model;
    protected string $resource;

    public function index()
    {
        return $this->resource::collection($this->model::paginate(10));
    }

    public function show($id)
    {
        $item = $this->model::findOrFail($id);
        return new $this->resource($item);
    }
}
