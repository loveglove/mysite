<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $logs = Log::orderBy('created_at','desc')->get();

        $this_month = Log::whereMonth('created_at', '=', Carbon::now());
        $last_month = Log::whereMonth('created_at', '=', Carbon::now()->subMonth(1));

        $this_month_array = Log::whereMonth('created_at', '=', Carbon::now())
                            ->orderBy('created_at')
                            ->get()
                            ->groupBy(function ($val) {
                                return Carbon::parse($val->created_at)->format('d');
                            });

        return view('home', [
            "logs" => $logs,
            "this_month_count" => $this_month->count(),
            "last_month_count" => $last_month->count(),
        ]);

    }


}
