<?php

namespace App\Http\Controllers\Account\Subscription;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionInvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $invoices = collect($request->user()->invoices())->map(fn ($invoice) => [
            'id' => $invoice->id,
            'date' => $invoice->date()->toFormattedDateString(),
            'total' => $invoice->total(),
        ])->all();

        return Inertia::render('UserSettings/Subscription/Invoices', [
            'invoices' => $invoices,
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
