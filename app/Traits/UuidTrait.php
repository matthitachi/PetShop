<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait UuidTrait
{
    public static function initUuid(): void
    {
        static::creating(function (Model $model) {
            $model->setAttribute('uuid', Str::uuid()->toString());
        });
    }
}
