<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use DB;
use Request;
use Response;
use Auth;
use Input;
use Carbon\Carbon;

use App\User;
use App\Building;
use App\Contact;
use App\Log;


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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('home');

    }


}
