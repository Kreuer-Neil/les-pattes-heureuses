<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public array $statItems;
    public function __construct()
    {
        $this->statItems = [
            'saved' => 152,
            'searching' => 31,
            'adopted' => 117,
        ];
    }
    public function index()
    {
        $statItems = $this->statItems;
        return view('client/home', compact('statItems'));
    }
}
