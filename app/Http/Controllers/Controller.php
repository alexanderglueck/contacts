<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected ?string $accessEntity = null;

    protected function can($right): void
    {
        abort_unless(Auth::user()->can($right . ' ' . $this->accessEntity), 403, trans('ui.access_denied'));
    }

    protected function isImpersonating(): bool
    {
        return session()->has('impersonate');
    }
}
