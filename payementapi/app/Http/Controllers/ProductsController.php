<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    public function fetch()
    {
        $products = Products::with('category:id,category_name')->select('id', 'title', 'category_id')->get();
        return response()->json($products);
    }

    public function fetchID($id)
    {
        $products = Products::with('category:id,category_name')->select('id', 'title', 'category_id')->where('id', $id)->first();
        return response()->json($products);
    }

    public function create(Request $request)
    {
        // Step 1: Validate the request
        $this->validateRequest($request);

        // Step 2: Check if the category exists
        $category = $this->findCategory($request->category_name);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category does not exist'
            ], 400);
        }

        // Step 3: Create the product
        $product = $this->createProduct($request, $category->id);

        // Step 4: Return success response
        return $this->createSuccessResponse($product);
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category_name' => 'required|string',
        ]);
    }

    private function findCategory($categoryName)
    {
        return Categories::where('category_name', $categoryName)->first();
    }

    private function createProduct(Request $request, $categoryId)
    {
        return Products::create([
            'title' => $request->product_name,
            'description' => $request->description,
            'category_id' => $categoryId,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);
    }

    private function createSuccessResponse(Products $product)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Created a new product successfully.',
            'data' => $product
        ], 201);  // 201 Created
    }



    public function update(Request $request, $id)
    {
        // Step 1: Validate the request
        $this->validateRequest1($request);

        // Step 2: Find the product by ID or fail
        $product = $this->findProductById($id);

        // Step 3: Update category if provided
        $this->updateCategoryIfProvided($request, $product);

        // Step 4: Update the product details
        $this->updateProductDetails($request, $product);

        // Step 5: Return a success response
        return $this->createSuccessResponse1($product, $request->product_name);
    }

    private function validateRequest1(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|integer',
            'stock' => 'sometimes|integer',
            'category_name' => 'sometimes|string',
        ]);
    }

    private function findProductById($id)
    {
        return Products::findOrFail($id);
    }

    private function updateCategoryIfProvided(Request $request, Products $product)
    {
        if ($request->has('category_name')) {
            $category = Categories::where('category_name', $request->category_name)->first();

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category does not exist',
                ], 400)->send();  // 400 Bad Request
            }

            // Update the category_id if the category exists
            $product->category_id = $category->id;
        }
    }

    private function updateProductDetails(Request $request, Products $product)
    {
        $product->update([
            'title' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);
    }

    private function createSuccessResponse1(Products $product, $productName)
    {
        return response()->json([
            'status' => 'success',
            'message' => $productName . ' updated successfully.',
            'data' => $product,
        ], 200);  // 200 OK
    }


    public function delete($id)
    {
        $product = Products::findOrFail($id);
        $product_name = $product->title;
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => $product_name . ' Deleted product successfully.',
            'data' => $product
        ], 201);
    }
}
