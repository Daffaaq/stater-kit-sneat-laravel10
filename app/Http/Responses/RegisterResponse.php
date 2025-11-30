<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // Logout user setelah register, agar tidak langsung login
        auth()->logout();

        return redirect()->route('register.thanks');
    }
}
