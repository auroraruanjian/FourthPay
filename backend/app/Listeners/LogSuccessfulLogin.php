<?php

namespace App\Listeners;

use App\Models\AdminLoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;
use itbdw\Ip\IpLocation;
use apanly\BrowserDetector\Browser;
use apanly\BrowserDetector\Os;
use apanly\BrowserDetector\Device;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        //
        $user = $event->user;

        $user->last_session = request()->cookie(session()->getName())??'';
        $user->last_ip      = request()->ip();
        $user->last_time    = Carbon::now();

        $user->save();

        $request = request();
        $browser = new Browser();
        $os = new Os();
        $device = new Device();
        $ip = $request->ip();

        $location = IpLocation::getLocation($ip);
        $admin_login_log = new AdminLoginLog();
        $request_data = request()->all();
        unset($request_data['password']);  // 密码不保存
        $admin_login_log->user_id = $user->id;
        $admin_login_log->province = !empty($location['province']) ? $location['province'] :
            (!empty($location['country']) ? $location['country'] : '');
        $admin_login_log->domain = request()->getHost();
        $admin_login_log->browser = $browser->getName();
        $admin_login_log->browser_version = $browser->getVersion();
        $admin_login_log->os = $os->getName();
        $admin_login_log->device = $device->getName();
        $admin_login_log->ip = $ip;
        $admin_login_log->request = json_encode($request_data);
        $admin_login_log->created_at = (string) now();

        try {
            $admin_login_log->save();
        } catch (\Exception $e) {
            \Log::error($e);
        }

    }
}
