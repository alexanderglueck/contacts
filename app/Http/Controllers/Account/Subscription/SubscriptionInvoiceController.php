<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SubscriptionInvoiceController extends Controller
{
    public function index()
    {
        return view('user_settings.subscription.invoice.index', [
            'invoices' => $invoices = Auth::user()->invoices()
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
