<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionInvoiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('user_settings.subscription.invoice.index', [
            'invoices' => $request->user()->invoices()
        ]);
    }

    public function show(Request $request, $invoice): RedirectResponse
    {
        return $request->user()->downloadInvoice($invoice, [
            'vendor' => config('app.name'),
            'product' => config('app.name') . ' membership',
        ]);
    }
}
