<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionInvoiceController extends Controller
{
    public function index(Request $request)
    {
        return view('user_settings.subscription.invoice.index', [
            'invoices' => $request->user()->invoices()
        ]);
    }

    public function show(Request $request, $invoice)
    {
        return $request->user()->downloadInvoice($invoice, [
            'vendor' => config('app.name'),
            'product' => config('app.name') . ' membership',
        ]);
    }
}
