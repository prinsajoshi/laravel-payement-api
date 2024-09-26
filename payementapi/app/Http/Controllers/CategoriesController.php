<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //
    public function fetch(){
        $categories=Categories::select('id','category_name')->get();
        return response()->json($categories);
    }
    public function fetchID($id){
        $categories=Categories::select('id','category_name')->where('id',$id)->first();
        return response()->json($categories);
    }

    public function create(Request $request){
        $category=Categories::create(['category_name' => $request->category_name]);

        return response()->json([
            'status' => 'success',  
            'message' => 'Created a new category successfully.',
            'data' => $category  
        ], 201);  
    }

    public function update(Request $request,$id){
        $category=Categories::findOrFail($id);
        $category->update(['category_name'=>$request->category_name]);

        return response()->json([
            'status' => 'success',  
            'message' => $request->category_name.'Updated category successfully.',
            'data' => $category  
        ], 201);  
    }

    public function delete($id){
        $category=Categories::findOrFail($id);
        $category_name=$category->category_name;
        $category->delete();

        return response()->json([
            'status' => 'success',  
            'message' => $category_name.' Deleted category successfully.',
            'data' => $category  
        ], 201);  
    }
}
