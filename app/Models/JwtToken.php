<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class JwtToken extends Model
{
    /**
     * App\Models\JwtToken.
     *
     * @property string|null $restrictions
     * @property string|null $permissions
     * @property int $id
     * @property int $user_id
     * @property string $unique_id
     * @property string $token_title
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $expires_at
     * @property string|null $last_used_at
     * @property string|null $refreshed_at
     *
     **/
    use HasFactory;

    protected $fillable = ['unique_id', 'token_title', 'expires_at'];
}
