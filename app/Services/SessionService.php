<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Jenssegers\Agent\Agent;

class SessionService
{
    /**
     * Get all sessions for the currently authenticated user.
     *
     * @return array<int, object>
     */
    public static function getUserSessions(): array
    {
        $driver = config('session.driver');

        return match ($driver) {
            'database' => self::getDatabaseSessions(),
            'redis'    => self::getRedisSessions(),
            default    => [],
        };
    }

    /**
     * Logout other sessions for the currently authenticated user.
     */
    public static function logoutOtherSessions(string $password): bool
    {
        if (! Hash::check($password, Auth::user()->password)) {
            return false;
        }

        Auth::guard()->logoutOtherDevices($password);

        $driver = config('session.driver');

        if ($driver === 'database') {
            self::deleteDatabaseSessions();
        } elseif ($driver === 'redis') {
            self::deleteRedisSessions();
        }

        request()->session()->put([
            'password_hash_' . Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
        ]);

        return true;
    }

    /**
     * Get sessions stored in database.
     *
     * @return array<int, object>
     */
    protected static function getDatabaseSessions(): array
    {
        $sessions = DB::connection(config('session.connection'))->table(config('session.table'))
            ->where('user_id', Auth::id())
            ->latest('last_activity')
            ->get();

        return $sessions->map(fn ($session) => self::formatSession($session))->toArray();
    }

    /**
     * Get sessions stored in Redis.
     *
     * @return array<int, object>
     */
    protected static function getRedisSessions(): array
    {
        $prefix   = config('session.prefix', 'laravel:session:');
        $keys     = Redis::keys($prefix . '*');
        $sessions = [];

        foreach ($keys as $key) {
            $dataRaw = Redis::get($key);
            $data    = [];
            try {
                $data = unserialize($dataRaw);
            } catch (\Throwable $e) {
                logger()->error('Redis session error', ['e' => $e]);

                continue;
            }
            if (! is_array($data) || ($data['login_web'] ?? null) !== Auth::id()) {
                continue;
            }

            $session = (object) [
                'id'            => $key,
                'user_agent'    => $data['user_agent'] ?? '',
                'ip_address'    => $data['_ip'] ?? 'Unknown',
                'last_activity' => $data['_last_activity'] ?? null,
            ];

            $sessions[] = self::formatSession($session);
        }

        return $sessions;
    }

    /**
     * Format session object for display.
     */
    protected static function formatSession(object $session): object
    {
        $agent = self::createAgent($session->user_agent ?? '');

        return (object) [
            'device' => [
                'browser'  => $agent->browser(),
                'desktop'  => $agent->isDesktop(),
                'mobile'   => $agent->isMobile(),
                'tablet'   => $agent->isTablet(),
                'platform' => $agent->platform(),
            ],
            'ip_address'        => $session->ip_address ?? 'Unknown',
            'is_current_device' => ($session->id ?? '') === request()->session()->getId(),
            'last_active'       => isset($session->last_activity)
                ? Carbon::createFromTimestamp($session->last_activity)->diffForHumans()
                : 'Unknown',
        ];
    }

    /**
     * Create an Agent instance from user agent string.
     */
    protected static function createAgent(string $userAgent): Agent
    {
        return tap(new Agent(), fn ($agent) => $agent->setUserAgent($userAgent));
    }

    /**
     * Delete other sessions from database.
     */
    protected static function deleteDatabaseSessions(): void
    {
        DB::connection(config('session.connection'))->table(config('session.table'))
            ->where('user_id', Auth::id())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }

    /**
     * Delete other sessions from Redis.
     */
    protected static function deleteRedisSessions(): void
    {
        $prefix = config('session.prefix', 'laravel:session:');
        $keys   = Redis::keys($prefix . '*');

        foreach ($keys as $key) {
            $dataRaw = Redis::get($key);
            $data    = [];
            try {
                $data = unserialize($dataRaw);
            } catch (\Throwable $e) {
                logger()->error('Redis session error', ['e' => $e]);

                continue;
            }
            if (($data['login_web'] ?? null) === Auth::id() && $key !== request()->session()->getId()) {
                Redis::del($key);
            }
        }
    }
}
