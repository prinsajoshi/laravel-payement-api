<?php

namespace App\Http\Controllers;

use App\Models\Carts;
use App\Models\order_product;
use App\Models\Orders;
use App\Models\Products;
use App\Models\discount;
use App\Models\shipping_address;
use Illuminate\Http\Request;
use App\Mail\OrderConfirmationMail;
use App\Models\Customer;
use App\Models\discount as ModelsDiscount;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersController extends Controller
{
    //
    public function createOrder(Request $request)
    {
        DB::beginTransaction();

        try {
            // Step 1: Validate the request and extract required data
            $customerId = $request->input('customer_id');
            $discountToken = $request->input('discount_token');
            $shippingAddressData = $request->input('shipping_address');

            // Step 2: Get cart items
            $cartItems = $this->getCartItems($customerId);
            if ($cartItems->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Cart is empty'], 400);
            }

            // Step 3: Calculate the total amount
            $totalAmount = $this->calculateTotalAmount($cartItems);

            // Step 4: Apply discount
            [$totalAmountAfterDiscount, $discount_percentage] = $this->applyDiscount($totalAmount, $discountToken);

            // Step 5: Charge the customer via Stripe
            $charge = $this->chargeCustomer($customerId, $totalAmountAfterDiscount);

            // Step 6: Create the order
            $order = $this->createOrderRecord($customerId, $totalAmount, $totalAmountAfterDiscount, $discountToken);

            // Step 7: Store order products
            $this->addProductsToOrder($order->id, $cartItems);

            // Step 8: Store shipping address
            $this->storeShippingAddress($order->id, $customerId, $shippingAddressData);

            // Step 9: Send order confirmation email
            $this->sendOrderConfirmationEmail($customerId, $order);

            // Step 10: Clear the cart
            $this->clearCart($customerId);

            // Commit the transaction
            DB::commit();

            // Step 11: Return the response
            return $this->createSuccessResponse($order, $charge, $totalAmountAfterDiscount, $discount_percentage);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $ex->getMessage()], 500);
        }
    }

    private function getCartItems($customerId)
    {
        return Carts::where('user_id', $customerId)->get();
    }

    private function calculateTotalAmount($cartItems)
    {
        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            $product = Products::find($cartItem->product_id);
            $totalAmount += $product->price * $cartItem->quantity;
        }
        return $totalAmount;
    }

    private function applyDiscount($totalAmount, $discountToken)
    {
        $discountInfo = discount::where('discount_token', $discountToken)->first();
        if (!$discountInfo) {
            throw new Exception("Invalid discount token");
        }
        $discount_percentage = $discountInfo->discount_percentage;
        $totalAmountAfterDiscount = $totalAmount - (($discount_percentage / 100) * $totalAmount);
        return [$totalAmountAfterDiscount, $discount_percentage];
    }

    private function chargeCustomer($customerId, $totalAmountAfterDiscount)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        return $stripe->charges->create([
            'amount' => $totalAmountAfterDiscount * 100,
            'currency' => 'usd',
            'source' => 'tok_visa',
            'description' => 'Order for user ' . $customerId,
        ]);
    }

    private function createOrderRecord($customerId, $totalAmount, $totalAmountAfterDiscount, $discountToken)
    {
        $order = new Orders();
        $order->user_id = $customerId;
        $order->total_amount = $totalAmount;
        $order->total_amount_after_discount = $totalAmountAfterDiscount;
        $order->discount_token = $discountToken;
        $order->status = 'completed';
        $order->order_number = Str::random(8);
        $order->save();

        return $order;
    }


    private function addProductsToOrder($orderId, $cartItems)
    {
        foreach ($cartItems as $cartItem) {
            order_product::create([
                'order_id' => $orderId,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity
            ]);
        }
    }

    private function storeShippingAddress($orderId, $customerId, $shippingAddressData)
    {
        shipping_address::create([
            'user_id' => $customerId,
            'order_id' => $orderId,
            'shipping_address' => $shippingAddressData['shipping_address'],
            'city' => $shippingAddressData['city'],
            'state' => $shippingAddressData['state'],
            'zip_code' => $shippingAddressData['zip_code'],
            'country' => $shippingAddressData['country']
        ]);
    }

    private function sendOrderConfirmationEmail($customerId, $order)
    {
        $customer = Customer::find($customerId);
        if ($customer && $customer->email) {
            Mail::to($customer->email)->send(new OrderConfirmationMail($order));
        } else {
            throw new Exception("Customer or customer email not found.");
        }
    }

    private function clearCart($customerId)
    {
        Carts::where('user_id', $customerId)->delete();
    }

    private function createSuccessResponse($order, $charge, $totalAmountAfterDiscount, $discount_percentage)
    {
        return response()->json([
            'status' => 'success',
            'payload' => [
                'message' => 'Order created successfully',
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'discount_percentage' => $discount_percentage,
                'total_amount_after_discount' => $totalAmountAfterDiscount,
                'order_id' => $order->id,
                'charge_id' => $charge->id
            ]
        ], 201);
    }
    
    public function fetch($customer_id)
    {
        // Step 1: Fetch orders with their associated products and categories
        $orders = $this->getOrdersWithProducts($customer_id);

        // Step 2: Validate that orders exist
        $this->validateOrders($orders);

        // Step 3: Build structured response for orders
        $response = $this->buildOrdersResponse($orders);

        // Step 4: Return success response with all order details
        return $this->createSuccessResponse4($response);
    }
    private function getOrdersWithProducts($customer_id)
    {
        return Orders::with(['order_products.products.category'])
            ->where('user_id', $customer_id)
            ->get();
    }
    private function validateOrders($orders)
    {
        if ($orders->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No orders found for this customer.'
            ], 404)->send();
        }
    }
    private function buildOrdersResponse($orders)
    {
        $response = [];

        foreach ($orders as $order) {
            $products = $this->getProductsForOrder($order);

            // Prepare the order response
            $response[] = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'status' => $order->status,
                'products' => $products,
            ];
        }

        return $response;
    }

    private function getProductsForOrder($order)
    {
        $products = [];

        foreach ($order->order_products as $orderProduct) {
            $products[] = [
                'product_id' => $orderProduct->products->id,
                'product_name' => $orderProduct->products->title,
                'quantity' => $orderProduct->quantity,
                'price' => $orderProduct->products->price,
                'category' => $orderProduct->products->category->category_name
            ];
        }

        return $products;
    }

    private function createSuccessResponse4($response)
    {
        return response()->json([
            'status' => 'success',
            'orders' => $response
        ], 200);
    }


    public function fetchSingleOrder($order_id)
    {
        // Step 1: Fetch the order with related data
        $order = $this->getOrderWithRelations($order_id);

        // Step 2: Validate the order
        $this->validateOrder($order);

        // Step 3: Build structured response with order details
        $orderDetails = $this->buildOrderDetails($order);

        // Step 4: Return success response with order details
        return $this->createSuccessResponse2($orderDetails);
    }
    private function getOrderWithRelations($order_id)
    {
        return Orders::with(['user', 'order_products.products.category'])
            ->where('id', $order_id)
            ->first();
    }

    private function validateOrder($order)
    {
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.'
            ], 404)->send();
        }
    }

    private function buildOrderDetails($order)
    {
        $orderDetails = [
            'customer_id' => $order->user->id,
            'customer_name' => $order->user->email,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'products' => []
        ];

        // Add products information related to this order
        foreach ($order->order_products as $orderProduct) {
            $orderDetails['products'][] = [
                'product_id' => $orderProduct->products->id,
                'product_name' => $orderProduct->products->title,
                'quantity' => $orderProduct->quantity,
                'price' => $orderProduct->products->price,
                'category' => $orderProduct->products->category->category_name
            ];
        }

        return $orderDetails;
    }

    private function createSuccessResponse2($orderDetails)
    {
        return response()->json([
            'status' => 'success',
            'order' => $orderDetails
        ], 200);
    }



    public function fetchShippingAddress($order_id)
    {
        // Step 1: Retrieve the order with shipping address
        $order = $this->getOrderWithShippingAddress($order_id);

        // Step 2: Validate the order and shipping address
        $this->validateOrderAndShippingAddress($order);

        // Step 3: Prepare shipping address details
        $shippingAddressDetails = $this->prepareShippingAddressDetails($order->shippingAddress);

        // Step 4: Return success response with shipping address details
        return $this->createSuccessResponse1($shippingAddressDetails);
    }

    private function getOrderWithShippingAddress($order_id)
    {
        return Orders::with('shippingAddress')
            ->where('id', $order_id)
            ->first();
    }

    private function validateOrderAndShippingAddress($order)
    {
        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404)->send();
        }

        if (!$order->shippingAddress) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shipping address not found for this order'
            ], 404)->send();
        }
    }

    private function prepareShippingAddressDetails($shippingAddress)
    {
        return [
            'shipping_address' => $shippingAddress->shipping_address,
            'city' => $shippingAddress->city,
            'state' => $shippingAddress->state,
            'zip_code' => $shippingAddress->zip_code,
            'country' => $shippingAddress->country,
        ];
    }

    private function createSuccessResponse1($shippingAddressDetails)
    {
        return response()->json([
            'status' => 'success',
            'shipping_address' => $shippingAddressDetails
        ]);
    }
}
