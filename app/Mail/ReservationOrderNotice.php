<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationOrderNotice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    public Order $order;

    public function __construct(Reservation $reservation, Order $order)
    {
        $this->reservation = $reservation;
        $this->order = $order;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Reservation Order #'.$this->order->id.' — '.$this->reservation->first_name.' '.$this->reservation->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-order-notice',
        );
    }
}
