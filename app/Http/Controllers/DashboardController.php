<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:wali');
    }

    public function index()
    {
        return view('dasborwali.index');
    }

    public function show()
    {
        return 'info';
    }
}
