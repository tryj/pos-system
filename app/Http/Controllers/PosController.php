<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();
        $members = Member::all();
        return view('pos.index', compact('products', 'members'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array|min:1',
            'payment_method' => 'required|in:cash,qr_promptpay',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $items = [];

            foreach ($request->cart as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);
                if ($product->stock < $item['qty']) {
                    return response()->json(['error' => "สินค้า {$product->name} มีไม่พอ"], 422);
                }

                $product->decrement('stock', $item['qty']);
                $subtotal = $product->price * $item['qty'];
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            $discount = $request->discount ?? 0;
            $netTotal = max(0, $totalAmount - $discount);
            $cashReceived = $request->payment_method === 'cash' ? ($request->cash_received ?? $netTotal) : $netTotal;
            $changeAmount = max(0, $cashReceived - $netTotal);

            $order = Order::create([
                'invoice_no' => 'INV-' . date('YmdHis'),
                'user_id' => Auth::id(),
                'member_id' => $request->member_id,
                'subtotal' => $totalAmount,
                'discount' => $discount,
                'total_amount' => $netTotal,
                'cash_received' => $cashReceived,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            if ($request->member_id) {
                $earnedPoints = floor($netTotal / 100);
                Member::where('id', $request->member_id)->increment('points', $earnedPoints);
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'receipt_url' => route('pos.receipt', $order->id)
            ]);
        });
    }

    public function receipt(Order $order)
    {
        $order->load(['items.product', 'user', 'member']);
        return view('pos.receipt', compact('order'));
    }
}
