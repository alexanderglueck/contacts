<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Contracts\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        return view('activities.index', [
            'activities' => Activity::latest()->paginate(10)
        ]);
    }
}
