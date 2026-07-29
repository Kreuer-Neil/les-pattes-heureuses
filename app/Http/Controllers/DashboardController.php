<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminAttentionCollection;
use App\Models\AdoptionRequest;
use App\Services\AdminAttentionFeed;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function render(Request $request)
    {
        $needsAttention = [];
        $unreadMessageCount = 0;

        if (Gate::allows('viewAny', AdoptionRequest::class)) {
            $items = AdminAttentionFeed::items()->take(5);
            $needsAttention = (new AdminAttentionCollection($items))->toArray($request);
            $unreadMessageCount = AdminAttentionFeed::unreadMessageCount();
        }

        return Inertia::render('dashboard', [
            'needsAttention' => $needsAttention,
            'unreadMessageCount' => $unreadMessageCount,
        ]);
    }
}
