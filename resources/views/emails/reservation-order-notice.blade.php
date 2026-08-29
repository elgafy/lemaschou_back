@extends('emails.layout')

@php
    $subject = 'New Reservation Order #' . $order->id;
    $preheader = 'New order from ' . $reservation->first_name . ' ' . $reservation->last_name . ' for ' . $reservation->date;
@endphp

@section('content')
    {{-- Title --}}
    <h1 style="margin: 0 0 20px 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 26px; font-weight: normal; color: #422a2c; line-height: 1.3;">
        New Reservation Order
    </h1>

    {{-- Decorative divider --}}
    <table width="60" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
        <tr>
            <td style="border-bottom: 2px solid #422a2c; font-size: 0; line-height: 0;">&nbsp;</td>
        </tr>
    </table>

    {{-- Reservation Details --}}
    <h2 style="margin: 0 0 15px 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 18px; color: #422a2c; text-transform: uppercase; letter-spacing: 1px;">
        Reservation Details
    </h2>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Sevenrooms ID</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->sevenrooms_reservation_id ?? '—' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Guest Name</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->first_name }} {{ $reservation->last_name }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Email</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->email }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Phone</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->mobile ?: '—' }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Date</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->date }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Time</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->time }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Guests</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->guests_count }}
            </td>
        </tr>
        @if($reservation->occasion)
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Occasion</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->occasion_type ?? '—' }}
            </td>
        </tr>
        @endif
        @if($reservation->special_request)
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Special Request</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ $reservation->special_request }}
            </td>
        </tr>
        @endif
        @if($reservation->allergic && $reservation->food_allergies)
        <tr>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD;">
                <strong>Food Allergies</strong>
            </td>
            <td style="padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; border-bottom: 1px solid #E5CBBD; text-align: right;">
                {{ implode(', ', $reservation->food_allergies) }}
            </td>
        </tr>
        @endif
    </table>

    {{-- Order Details --}}
    <h2 style="margin: 0 0 15px 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 18px; color: #422a2c; text-transform: uppercase; letter-spacing: 1px;">
        Order #{{ $order->id }}
    </h2>

    {{-- Items Table --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
        <tr style="background-color: #422a2c;">
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #E9DED7; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;">
                Item
            </td>
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #E9DED7; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; text-align: center;">
                Qty
            </td>
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #E9DED7; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; text-align: right;">
                Price
            </td>
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #E9DED7; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; text-align: right;">
                Total
            </td>
        </tr>
        @foreach($order->items as $index => $item)
        <tr style="background-color: {{ $index % 2 === 0 ? '#EFE8E1' : '#E9DED7' }};">
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728;">
                {{ $item->name }}
            </td>
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; text-align: center;">
                {{ $item->quantity }}
            </td>
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; text-align: right;">
                {{ number_format($item->unit_price, 2) }} SAR
            </td>
            <td style="padding: 10px 12px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; text-align: right;">
                {{ number_format($item->total, 2) }} SAR
            </td>
        </tr>
        @endforeach
    </table>

    {{-- Order Totals --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
        @if($order->subtotal > 0)
        <tr>
            <td style="padding: 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728;">
                Subtotal
            </td>
            <td style="padding: 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; text-align: right;">
                {{ number_format($order->subtotal, 2) }} SAR
            </td>
        </tr>
        @endif
        @if($order->discount > 0)
        <tr>
            <td style="padding: 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728;">
                Discount
            </td>
            <td style="padding: 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; text-align: right;">
                -{{ number_format($order->discount, 2) }} SAR
            </td>
        </tr>
        @endif
        @if($order->deposit > 0)
        <tr>
            <td style="padding: 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728;">
                Deposit
            </td>
            <td style="padding: 6px 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #4A3728; text-align: right;">
                {{ number_format($order->deposit, 2) }} SAR
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding: 12px 0 6px 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 18px; color: #422a2c; border-top: 2px solid #422a2c; font-weight: bold;">
                Total
            </td>
            <td style="padding: 12px 0 6px 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 18px; color: #422a2c; border-top: 2px solid #422a2c; text-align: right; font-weight: bold;">
                {{ number_format($order->total, 2) }} SAR
            </td>
        </tr>
    </table>

    {{-- Status --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
        <tr>
            <td style="padding: 12px 16px; background-color: #E9DED7; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4A3728;">
                <strong>Payment Status:</strong> {{ ucfirst($order->status) }} &nbsp;|&nbsp; <strong>Payment Method:</strong> {{ ucfirst($order->payment_processor) }}
            </td>
        </tr>
    </table>

    {{-- Closing --}}
    <p style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #4A3728; line-height: 1.6;">
        Please prepare the above order for the reservation date and time.
    </p>
@endsection
