<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Product extends Model
{
    /**
     * App\Models\Product.
     *
     * @property string $metadata
     * @property int $id
     * @property string $uuid
     * @property string $title
     * @property float $price
     * @property string $description
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property string $category_uuid
     **/
    use HasFactory;

    use UuidTrait;

    protected $fillable = ['uuid', 'title', 'price', 'description', 'metadata', 'category_uuid'];

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
