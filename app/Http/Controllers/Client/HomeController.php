<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

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

    // TODO move to ContactMessageController, persist $contactMessage and notify the admin
    /* Technically a store for ContactMessage */
    public function contact()
    {
        $contactMessage = new ContactMessage;

        return redirect()
            ->back()
            ->with('status', 'message-sent');
    }
}
