<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project; // Pastikan Model Project di-import
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // public function index(){
    //     return view('dashboard');
    // }

    public function index()
    {
        // Mengambil semua project milik user yang sedang login sesuai user_id di Tabel Projects
        $projects = Project::where('user_id', Auth::user()->user_id)->get();

        // Mengirimkan variabel $projects ke view dashboard
        return view('dashboard', compact('projects'));
    }
}
