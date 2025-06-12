<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class GuestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $guest = User::create([
            'name' => 'Guest-'. Str::random(10),
            'email' => null,
            'password' => null,
            'is_guest' => true,
        ]);
        Auth::login($guest, true);
        $request->session()->regenerate();
        return redirect('/');
    }
}
