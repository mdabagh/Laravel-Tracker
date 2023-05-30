<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use YourVendor\LaravelTracker\Tracker;

class LogRequests
{
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('log_token')) {
            $logToken = Str::random(40);
            $request->session()->flash('log_token', $logToken);
        } else {
            $logToken = $request->session()->get('log_token');
        }
      
        Tracker::logRequest([
            'ip_address' => $request->ip(),
            'country' => geoip()->getLocation($request->ip())->country,
            'browser_name' => $request->header('User-Agent'),
            'language' => $request->header('Accept-Language'),
            'os' => $this->getOperatingSystem($request->header('User-Agent')),
            'is_guest' => Auth::guest(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'log_token' => $logToken,
            'current_route' => $request->route()->getName(),
            'previous_route' => $request->session()->get('previous_route_name'),
            'login_time' => $previousLog ? $previousLog->created_at : now(),
            'logout_time' => null, // This will be updated on next request
        ]);

         // Set the previous route name in the session
        if ($request->route()) {
            $request->session()->put('previous_route_name', $request->route()->getName());
        }

        $previousLog = Tracker::getLastRequestLog($logToken);
        if ($previousLog) {
            $previousLog->update(['time_out' => now()]);
        }
    }
}
