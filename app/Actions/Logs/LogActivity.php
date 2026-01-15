<?php

namespace App\Actions\Logs;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public static function run(
        string $module,
        string $change,
        ?int $objectId = null,
        ?int $userId = null
    ): void {
        $userId = $userId ?? Auth::id();

        Log::create([
            'user_id'    => $userId,
            'module'     => $module,
            'object_id'  => $objectId,
            'change'     => $change,
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
