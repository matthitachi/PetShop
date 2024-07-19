<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    /**
     * App\Models\User.
     *
     * @property int $id
     * @property string $uuid
     * @property string $first_name
     * @property string $last_name
     * @property int $is_admin
     * @property string $email
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $avatar
     * @property string $address
     * @property string $phone_number
     * @property int $is_marketing
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $last_login
     **/
    use HasFactory;

    use Notifiable;
    use UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'uuid',
        'first_name',
        'last_name',
        'is_admin',
        'email',
        'password',
        'address',
        'phone_number',
        'avatar',
        'is_marketing',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setIsMarketingAttribute($value)
    {
        $this->attributes['is_marketing'] = $value ?? 0;
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(JwtToken::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    public function scopeHasToken(Builder $query, int $tokenId): void
    {
        $query->whereHas('tokens', function ($q) use ($tokenId) {
            $q->where('unique_id', '=', $tokenId);
        });
    }

    protected static function boot()
    {
        parent::boot();
        self::initUuid();
    }
}
