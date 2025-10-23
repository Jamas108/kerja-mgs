<?php
// 5. Auth Controller untuk Middleware
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isDirector()) {
            return redirect()->route('director.dashboard');
        } elseif ($user->isDivisionHead()) {
            return redirect()->route('head_division.dashboard');
        } elseif ($user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        }

        return view('home');
    }
}