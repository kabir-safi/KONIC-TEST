<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Carbon;
use App\Models\Order;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        $from = $request->from;
        $to = $request->to;
        $orders = Order::with('affiliate')->get()->whereBetween('created_at', [$request->from, $request->to]);
        $affiliate = Order::get()->where('affiliate_id', null)->whereBetween('created_at', [$request->from, $request->to]);
        $count = $orders->count('*');
        $revenue = $orders->sum('subtotal');
        $commissions_owed = $orders->sum('commission_owed') - $affiliate->sum('commission_owed');
        return response()->json(compact('from', 'to','count','revenue','commissions_owed'));
        // TODO: Complete this method
    }
}
