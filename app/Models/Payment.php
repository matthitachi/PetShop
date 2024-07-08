<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Payment extends Model
{
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
