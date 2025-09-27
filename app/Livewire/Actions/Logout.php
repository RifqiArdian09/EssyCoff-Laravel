<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        // Clear the user's active session ID
        if (Auth::check()) {
            $user = Auth::user();
            try {
                $user->active_session_id = null;
                $user->save();
            } catch (\Throwable $e) {
                // swallow
            }
        }

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route('login');
    }
}
