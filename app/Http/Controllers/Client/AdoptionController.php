<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdoptionController extends Controller
{
    public function index()
    {
        $animals = [];
        return view('client.adoption', compact(['animals']));
    }
}
