<?php

namespace Domain\Order\Models;

use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Support\Casts\PriceCast;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'delivery_type_id',
        'payment_method_id',
        'amount'
    ];

    protected $casts = [
        'amount' => PriceCast::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class);
    }
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function orderCustomer(): HasOne
    {
        return $this->hasOne(OrderCustomer::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
