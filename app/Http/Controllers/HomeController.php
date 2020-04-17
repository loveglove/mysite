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
        $building = Building::where("user_id", Auth::user()->id)->first();

        $logs = Log::orderBy('id','desc')->get();

        $this_month = Log::whereMonth('created_at', Carbon::now())->count();
        $last_month = Log::whereMonth('created_at', Carbon::now()->subMonth(1))->count();

        $today = Carbon::now();
        for ($i = 0; $i < 7; $i++) {
            $chart_dates[] = $today->format('D jS');
            $chart_values[] = Log::whereDay('created_at', $today->format('d'))->count();
            $today = $today->subDay();
        }

        $morning = Log::whereBetween(DB::raw('TIME(created_at)'), ['00:00:01', '12:00:00'])->count();
        $afternoon = Log::whereBetween(DB::raw('TIME(created_at)'), ['12:00:01', '18:00:00'])->count();
        $evening = Log::whereBetween(DB::raw('TIME(created_at)'), ['18:00:01', '24:00:00'])->count();

        return view('home', [
            "building" => $building,
            "logs" => $logs,
            "this_month_count" => $this_month,
            "last_month_count" => $last_month,
            "last_entry" => $logs->first(),
            "chart_values" => $chart_values,
            "chart_dates" => $chart_dates,
            "morning" => $morning,
            "afternoon" => $afternoon,
            "evening" => $evening,
        ]);

    }

    public function saveSettings(Request $request)
    {

        $data = Request::all();

        $building = Auth::user()->building;
        $building->entry_message = $data["message"];
        $building->entry_digit = $data["digits"];
        $building->mode = $data["mode"];
        if (Request::has('passcode')) {
            $building->entry_code = $data["passcode"];
        }   
        $building->save();

        return Response::json(['success' => true, "msg", "values" => $data]);

    }


}
