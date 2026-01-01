<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * Invokable action for logging users out.
 *
 * Handles session cleanup and redirects to the home page.
 * Used by Livewire components to handle logout.
 */
class Logout
{
    /**
     * Log the current user out of the application.
     *
     * Invalidates the session and regenerates the CSRF token for security.
     */
    public function __invoke(): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}
