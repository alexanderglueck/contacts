<?php

namespace App\Http\Controllers;

use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        return view('activities.index', [
            'activities' => Activity::latest()->paginate(10)
        ]);
    }
}
