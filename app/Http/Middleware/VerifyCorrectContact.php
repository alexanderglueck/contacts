<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use Session;

class VerifyCorrectContact
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * Get the parameter names used in this route (contacts, contactAddress)
         */
        $parameterNames = $request->route()->parameterNames();

        /**
         * Get the parameters (using route model binding)
         */
        $parameters = $request->route()->parameters;

        /**
         * If the route has a parent contact and additional parameters
         */
        if ($request->route()->hasParameter('contact') && count($parameters) > 1) {
            /**
             * Contact is always the first parameter in our routes
             */
            $contact = $parameters[$parameterNames[0]];

            /**
             * Model (may be of instance contactAddress, contactDate, contactUrl or contactNumber
             */
            $child = $parameters[$parameterNames[1]];

            /**
             * Check if the child belongs to the given parent contact
             */
            if ($contact->id != $child->contact->id) {
                Session::flash('alert-danger', 'Fehler! Operation nicht erlaubt!');
                return redirect('home');
            }
        }


        return $next($request);
    }
}
