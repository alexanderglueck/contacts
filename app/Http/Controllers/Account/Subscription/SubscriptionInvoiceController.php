<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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
