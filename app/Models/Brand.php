<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


final class Brand extends Model
{
    use HasFactory;
    use UuidTrait;

    protected $fillable = ['title'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        UuidTrait::initUuid();

        self::creating(function (self $model) {
            $model->setAttribute('slug', Str::slug($model->title));
        });

        self::updating(function (self $model) {
            $model->setAttribute('slug', Str::slug($model->title));
        });
    }
}
