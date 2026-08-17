<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordHasBeenChanged
{
    /**
     * Redirect a user still on their temporary password to the password settings
     * page before they can reach anything else on the admin dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password) {
            return redirect()->route('user-password.edit');
        }

        return $next($request);
    }
}
