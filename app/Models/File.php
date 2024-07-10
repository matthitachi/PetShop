<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class File extends Model
{
    /**
     * App\Models\File.
     *
     * @property int $id
     * @property string $uuid
     * @property string $name
     * @property string $path
     * @property string $size
     * @property string $type
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     **/

    use HasFactory;

    protected $fillable = ['uuid', 'name', 'path', 'size', 'type'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
