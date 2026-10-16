<?php

namespace App\Actions;

use App\Support\Activity;
use App\Support\StatusTransition;
use App\Exceptions\InvalidStatusTransition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class TransitionRecord
{
    /**
     * @param  array<string, array<int, string>>  $map
     */
    public static function run(Model $model, string $to, array $map, string $action): void
    {
        try {
            StatusTransition::assert((string) $model->getAttribute('status'), $to, $map);
        } catch (InvalidStatusTransition $exception) {
            throw ValidationException::withMessages(['status' => $exception->getMessage()]);
        }

        $model->setAttribute('status', $to);
        $model->save();
        Activity::record($model, $action, 'Status set to '.$to);
    }
}
