<?php

namespace App\Http\Controllers;

use App\Services\ShelterStatistics;

class HomeController extends Controller
{
    public function index()
    {
        $statItems = ShelterStatistics::allTime();

        return view('client/home', compact('statItems'));
    }
}
