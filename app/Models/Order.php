<?php

namespace App\Models;

use App\Services\ReservationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    protected $fillable = [
        'reservation_id',
        'subtotal',
        'discount',
        'deposit',
        'total',
        'payment_processor',
        'currency',
        'status',
    ];

    protected static function booted(): void
    {
        // Whenever an order becomes paid, whatever caused the change (gateway
        // webhook, a status verification request, or a manual update): notify
        // staff and confirm the reservation.
        static::updated(function (Order $order) {
            if (! $order->wasChanged('status') || $order->status !== 'paid') {
                return;
            }

            // Orders created for a gift card only don't carry reservation_id,
            // so fall back to the reservation pointing at this order.
            $reservation = $order->reservation ?? Reservation::where('order_id', $order->id)->first();

            if (! $reservation) {
                Log::warning('Reservation not found for paid order — notice email not sent', [
                    'order_id' => $order->id,
                ]);

                return;
            }

            app(ReservationService::class)->sendReservationOrderNotice($reservation, $order);

            // A cancelled reservation is left alone — a late webhook must not resurrect it
            if ($reservation->status === 'cancelled') {
                Log::warning('Reservation left cancelled after payment', [
                    'order_id' => $order->id,
                    'reservation_id' => $reservation->id,
                ]);

                return;
            }

            if ($reservation->status !== 'confirmed') {
                $reservation->status = 'confirmed';
                $reservation->save();
            }
        });
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
