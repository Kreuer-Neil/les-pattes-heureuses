<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminAttentionCollection;
use App\Models\AdoptionRequest;
use App\Services\AdminAttentionFeed;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AdoptionRequest::class);

        return Inertia::render('notifications/index', [
            'items' => (new AdminAttentionCollection(AdminAttentionFeed::items()))->toArray($request),
            'unreadMessageCount' => AdminAttentionFeed::unreadMessageCount(),
            'defaultSignature' => $request->user()->defaultSignature(),
        ]);
    }
}
