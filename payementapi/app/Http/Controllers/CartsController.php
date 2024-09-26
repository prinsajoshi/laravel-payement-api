<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    // Fetch all items in a customer's cart
    public function fetch($customer_id)
    {
        $cartItems = Carts::where('user_id', $customer_id)
            ->with('products:id,title,price')
            ->get(['product_id', 'quantity']);

        return response()->json([
            'status' => 'success',
            'data' => $cartItems
        ]);
    }

    // Add a product to the customer's cart
    public function create(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Check if the item already exists in the cart
        $cartItem = Carts::updateOrCreate(
            ['user_id' => $request->customer_id, 'product_id' => $request->product_id],
            ['quantity' => $request->quantity]
        );

        $cartItemDetails = Carts::with(['products.category:id,category_name'])
            ->where('id', $cartItem->id)
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully.',
            'data' => [
                'cart_item' => $cartItemDetails,

            ]
        ], 201);
    }

    public function update(Request $request)
    {
        // Step 1: Validate the request
        $this->validateRequest1($request);

        // Step 2: Find the cart item
        $cartItem = $this->findCartItem1($request->customer_id, $request->product_id);

        // Step 3: Update the quantity
        $this->updateCartItemQuantity($cartItem, $request->quantity);

        // Step 4: Fetch detailed information about the updated cart item
        $cartItemDetails = $this->fetchCartItemDetails($cartItem->id);

        // Step 5: Return the response
        return $this->createSuccessResponse1($cartItemDetails);
    }

    private function validateRequest1(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id', // Corrected to match 'customers' table
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
    }

    private function findCartItem1($customerId, $productId)
    {
        return Carts::where('user_id', $customerId)
            ->where('product_id', $productId)
            ->firstOrFail();
    }

    private function updateCartItemQuantity($cartItem, $quantity)
    {
        $cartItem->update(['quantity' => $quantity]);
    }

    private function fetchCartItemDetails($cartItemId)
    {
        return Carts::with(['products.category:id,category_name'])
            ->where('id', $cartItemId)
            ->first();
    }

    private function createSuccessResponse1($cartItemDetails)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated successfully.',
            'data' => [
                'cart_item' => $cartItemDetails,
                'product' => [
                    'title' => $cartItemDetails->products->title,
                    'description' => $cartItemDetails->products->description,
                    'price' => $cartItemDetails->products->price,
                    'category' => $cartItemDetails->products->category->category_name,
                ],
            ]
        ]);
    }

    // Remove a product from the customer's cart
    public function delete(Request $request)
    {
        // Step 1: Validate the request
        $this->validateRequest($request);

        // Step 2: Find the cart item
        $cartItem = $this->findCartItem($request->customer_id, $request->product_id);

        // Step 3: Delete the cart item
        $this->deleteCartItem($cartItem);

        // Step 4: Return success response
        return $this->createSuccessResponse();
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id'
        ]);
    }

    private function findCartItem($customerId, $productId)
    {
        return Carts::where('user_id', $customerId)
            ->where('product_id', $productId)
            ->firstOrFail();
    }

    private function deleteCartItem($cartItem)
    {
        $cartItem->delete();
    }

    private function createSuccessResponse()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Cart item removed successfully.'
        ]);
    }
}
