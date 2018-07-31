<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class Navigation
{
    public static function isActiveRoute($routes, $output = 'active', $data = [])
    {
        if ( ! is_array($routes)) {
            $routes = [$routes];
        }

        foreach ($routes as $route) {
            if (empty($data)) {
                if (Route::currentRouteName() == $route) {
                    return $output;
                }
            } else {
                foreach ($data as $key => $value) {
                    if (Route::currentRouteName() == $route && Route::current()->parameter($key)->slug == $value) {
                        return $output;
                    }
                }
            }
        }
    }
}
