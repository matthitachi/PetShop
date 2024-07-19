<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Brand extends Model
{
    /**
     * App\Models\Brand.
     *
     * @property int $id
     * @property string $uuid
     * @property string $title
     * @property string $slug
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     **/
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
        self::initUuid();

        self::creating(function (self $model) {
            $model->setAttribute('slug', Str::slug($model->title));
        });

        self::updating(function (self $model) {
            $model->setAttribute('slug', Str::slug($model->title));
        });
    }
}
