<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class FaxCategoriesController extends Controller
{
    public function index(){
        $faxCategoriesNames = Category::pluck('category_name');

        return response()->json(['status' => true, 'categoriesNames' => $faxCategoriesNames]);
    }
}
