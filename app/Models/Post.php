<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Post extends Model
{
    /**
     * App\Models\Post.
     *
     * @property string $metadata
     * @property int $id
     * @property string $uuid
     * @property string $title
     * @property string $slug
     * @property string $content
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     **/
    use HasFactory;
    use UuidTrait;


    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        self::initUuid();
    }
}
