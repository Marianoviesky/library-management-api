<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends GenericController
{
    public function __construct()
    {
        $this->model = Category::class;
        $this->resource = CategoryResource::class;
    }
}
