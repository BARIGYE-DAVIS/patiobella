<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        if (!$user->department || $user->department->name !== 'KITCHEN') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        return view('kitchen.dashboard');
    }
}
