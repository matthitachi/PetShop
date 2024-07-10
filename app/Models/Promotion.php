<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Promotion extends Model
{
    /**
     * App\Models\Promotion.
     *
     * @property string $metadata
     * @property int $id
     * @property string $uuid
     * @property string $title
     * @property string $content
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     **/
    use HasFactory;
}
