<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\UpdateCategoryResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CategoryController extends Controller

{
    public function save(CategoryRequest $request)
    {
        if (Category::insert($request->all())) {
            return true;
        }
        return false;
    }

    public function getCategory($id)
    {
        return new UpdateCategoryResource(Category::find($id));
    }

    public function store($id, UpdateCategoryRequest $request)
    {
        return Category::query()->find($id)->update($request->Validated());
    }

    public function delete($id)
    {
        if (Product::query()->where('category_id', $id)->exists()) {
            $products = Product::query()->where('category_id', $id)->get();
            foreach ($products as $product) {
                $product['category_id'] = 0;
                $product->save();
            }
            if (Category::destroy($id)) {
                return true;
            }
            return false;
        }

        if (Category::destroy($id)) {
            return true;
        }
        return false;
    }

    public function index(Request $request)
    {
        $category = Category::query()->when($request->keyword,function(Builder $query, string $keyword){
            $query->where('name',$keyword);
        })->get();
        return new CategoriesResource($category);
    }
}
