<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Payment extends Model
{
    /**
     * App\Models\Payment.
     *
     * @property string $details
     * @property int $id
     * @property string $uuid
     * @property string $type
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     **/
    use HasFactory;

    use UuidTrait;

    protected $fillable = ['type', 'details'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();
        self::initUuid();
    }
}
