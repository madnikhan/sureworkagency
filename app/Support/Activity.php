<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Activity
{
    public static function record(Model $subject, string $action, string $summary): void
    {
        ActivityLog::query()->create([
            'user_id' => Auth::id(),
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'action' => $action,
            'summary' => $summary,
        ]);
    }
}
