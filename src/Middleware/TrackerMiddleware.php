<?php

namespace YourVendorName\LaravelTracker\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use YourVendorName\LaravelTracker\Models\Tracker;

class TrackerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Get the previous log entry for the user
        $previousLog = Tracker::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->first();

        // Log the request
        Tracker::create([
            'ip_address' => $request->ip(),
            'country' => geoip()->getLocation($request->ip())->country,
            'browser_name' => $request->header('User-Agent'),
            'language' => $request->header('Accept-Language'),
            'os' => $this->getOperatingSystem($request->header('User-Agent')),
            'is_guest' => Auth::guest(),
            'user_id' => Auth::check() ? Auth::id() : null,
            'log_token' => $this->generateLogToken(),
            'current_route' => $request->route() ? $request->route()->getName() : null,
            'previous_route' => $request->session()->get('previous_route_name'),
            'login_time' => $previousLog ? $previousLog->created_at : now(),
            'logout_time' => null,
        ]);

        // Set the previous route name in the session
        if ($request->route()) {
            $request->session()->put('previous_route_name', $request->route()->getName());
        }

        // Continue to the next middleware
        return $next($request);
    }

    /**
     * Generate a unique log token
     *
     * @return string
     */
    private function generateLogToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get the operating system from the user agent string
     *
     * @param string $userAgent
     *
     * @return string|null
     */
    private function getOperatingSystem($userAgent)
    {
        $osPlatforms = [
            'Windows NT 10.0' => 'Windows 10',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.1' => 'Windows 7',
            'Windows NT 6.0' => 'Windows Vista',
            'Windows NT 5.2' => 'Windows Server 2003/XP x64',
            'Windows NT 5.1' => 'Windows XP',
            'Windows XP' => 'Windows XP',
            'Windows NT 5.0' => 'Windows 2000',
            'Windows ME' => 'Windows ME',
            'Win98' => 'Windows 98',
            'Win95' => 'Windows 95',
            'Win16' => 'Windows 3.11',
            'Mac OS X' => 'Mac OS X',
            'MacOS' => 'Mac OS Classic',
            'Linux' => 'Linux',
            'Ubuntu' => 'Ubuntu',
            'iPhone' => 'iOS',
            'iPad' => 'iOS',
            'Android' => 'Android',
        ];

        foreach ($osPlatforms as $platform => $os) {
            if (strpos($userAgent, $platform) !== false) {
                return $os;
            }
        }

        return null;
    }
}
