<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Numbers
{
    public static function next(string $modelClass, string $column, string $prefix): string
    {
        return DB::transaction(function () use ($modelClass, $column, $prefix) {
            $year = now()->year;
            $latest = $modelClass::query()
                ->where($column, 'like', $prefix.'-'.$year.'-%')
                ->lockForUpdate()
                ->orderByDesc($column)
                ->value($column);

            $sequence = 1;
            if (is_string($latest) && preg_match('/(\d+)$/', $latest, $matches)) {
                $sequence = ((int) $matches[1]) + 1;
            }

            return sprintf('%s-%d-%04d', $prefix, $year, $sequence);
        });
    }
}
