<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class UnreadMessagesMiddleware {

    public function handle(Request $request, Closure $next) {
        if (Auth::check()) {
            $unreadMessages = Message::where('ReceiverID', Auth::id())
                    ->where('is_read', false)
                    ->count();
            view()->share('unreadMessages', $unreadMessages);
        } else {
            view()->share('unreadMessages', 0);
        }

        return $next($request);
    }
}
