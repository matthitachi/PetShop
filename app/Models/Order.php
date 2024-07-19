<?php

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Order extends Model
{
    /**
     * App\Models\Order.
     *
     * @property string $metadata
     * @property string $address
     * @property string $products
     * @property int $id
     * @property string $uuid
     * @property float|null $delivery_fee
     * @property float $amount
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $shipped_at
     * @property int $user_id
     * @property int $order_status_id
     * @property int $payment_id
     **/
    use HasFactory;

    use UuidTrait;

    protected $fillable = ['order_status_id', 'payment_id', 'products', 'address', 'amount'];

    protected $casts = [
        'products' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): HasOne
    {
        return $this->hasOne(OrderStatus::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

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
