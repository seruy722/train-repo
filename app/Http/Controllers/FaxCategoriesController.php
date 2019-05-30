<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class FaxCategoriesController extends Controller
{
    public function index(){
        $faxCategoriesNames = Category::all('category_name', 'id');

        return response()->json(['status' => true, 'categories' => $faxCategoriesNames]);
    }
}
