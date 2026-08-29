{{-- Example: Reservation confirmation email using the base layout --}}
@extends('emails.layout')

@php
    $subject = 'Welcome to Le Maschou';
    $preheader = 'Thank you for joining us at Le Maschou.';
@endphp

@section('content')
    {{-- Greeting --}}
    <h1 style="margin: 0 0 20px 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 28px; font-weight: normal; color: #422a2c; line-height: 1.3;">
        Bonjour, {{ $name ?? 'Guest' }}
    </h1>

    {{-- Decorative divider --}}
    <table width="60" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 25px;">
        <tr>
            <td style="border-bottom: 2px solid #422a2c; font-size: 0; line-height: 0;">&nbsp;</td>
        </tr>
    </table>

    {{-- Body text --}}
    <p style="margin: 0 0 18px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #4A3728; line-height: 1.7;">
        Thank you for choosing Le Maschou. We are delighted to welcome you to our restaurant, where the elegance of French Mediterranean cuisine meets the warmth of Riyadh hospitality.
    </p>

    <p style="margin: 0 0 25px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #4A3728; line-height: 1.7;">
        From our signature wood-fired grills to our curated selection of premium cuts and fresh seafood, every dish at Le Maschou is crafted with passion and authenticity.
    </p>

    {{-- CTA Button --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding: 10px 0 30px 0;">
                <a href="https://lemaschou.gafystudio.com/en/menu" target="_blank" style="display: inline-block; padding: 14px 40px; background-color: #422a2c; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 14px; color: #E9DED7; text-decoration: none; letter-spacing: 2px; text-transform: uppercase;">
                    Explore Our Menu
                </a>
            </td>
        </tr>
    </table>

    {{-- Closing --}}
    <p style="margin: 0 0 5px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #4A3728; line-height: 1.7;">
        We look forward to serving you.
    </p>
    <p style="margin: 0; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 16px; color: #422a2c; font-style: italic;">
        — Le Maschou Team
    </p>
@endsection
