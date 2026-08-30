<?php

namespace App\Services;

use App\Mail\ReservationOrderNotice;
use App\Models\GiftCard;
use App\Models\OccasionSpecialItems;
use App\Models\OccasionSpecialItemsCategory;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\SpecialDays;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Prompts\Output\ConsoleOutput;

class ReservationService
{
    public $token = '';

    public $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput;
    }

    public function getSettings()
    {
        $keys = [
            'use_reservation_external_link',
            'reservation_link',
            'force_reservation_downpayment',
            'downpayment_amount',
            'seating_time_en',
            'seating_time_ar',
            'booking_min_guests',
            'booking_max_guests',
            'booking_time_window',
            'sevenrooms_venue_id',
            'enable_occasions',
            'enable_occasion_items',
            'enable_occasion_items_payment',
            'add_calculated_vat',
            'vat_value',
            'occasion_items_title_en',
            'occasion_items_title_ar',
            'occasion_items_notice_en',
            'occasion_items_notice_ar',
            'enable_booking_notice',
            'booking_notice_en',
            'booking_notice_en',
            'booking_notice_ar',
            'booking_intro_en',
            'booking_intro_ar',
        ];
        $reservation_settings = $this->getCachedReservationSettings($keys);
        $occasions = json_decode(Setting::where('key', 'occasions')->first()?->value ?? '', true) ?? [];
        $allergies = json_decode(Setting::where('key', 'allergies')->first()?->value ?? '', true);
        $items = $this->getCachedOccasionItems();
        $gift_cards = $this->getCachedGiftCards();

        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => [
                'foodAllergies' => $allergies,
                'occasions' => $occasions,
                'occasionItems' => $items,
                'settings' => $reservation_settings,
                'giftCards' => $gift_cards,
            ],
        ], 200);
    }

    public function makeReservation(Request $request)
    {
        Setting::where('key', 'add_calculated_vat')->first()?->value == 'true' ? $add_vat = true : $add_vat = false;
        Log::alert('Making reservation with data: '.json_encode($request->all()));
        $this->output->writeln('Making reservation with data: '.json_encode($request->all()));
        $this->output->writeln('Request: '.json_encode($request->all('occasionSelectedItems')));
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|string|max:10',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'emailAddress' => 'required|string|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'specialRequest' => 'nullable|string|max:255',
            'guests' => 'required|integer|min:1',
            'occasion' => 'nullable|boolean',
            'occasionType' => 'nullable|string|max:255',
            'occasionSelectedItems' => 'nullable|array|max:255',
            'occasionSelectedItems.*.itemId' => 'required|integer',
            'occasionSelectedItems.*.itemName' => 'required|string',
            'occasionSelectedItems.*.variationValue' => 'nullable|string',
            'occasionSelectedItems.*.quantity' => 'nullable|integer|min:1',
            'occasionItemsPrice' => 'nullable|numeric',
            'deposite' => 'nullable|numeric',
            'cardContent' => 'nullable|string|max:255',
            'giftCard' => 'nullable|integer|exists:gift_cards,id',
            'allergic' => 'nullable|boolean',
            'allergies' => 'nullable|array',
            'allergies.*' => 'string|max:255',
            'termsAccepted' => 'required|boolean',
            'paymentPolicyAccepted' => 'required|boolean',
        ]);

        $occasionSelectedItems = $validated['occasionSelectedItems'] ?? [];
        $occasion = (bool) ($validated['occasion'] ?? false);
        $allergic = (bool) ($validated['allergic'] ?? false);

        $this->output->writeln('Validated occasion: '.json_encode($occasion));
        $this->output->writeln('Validated occasionSelectedItems: '.json_encode($occasionSelectedItems));
        $this->output->writeln('Validated allergies: '.json_encode($validated['allergies'] ?? []));
        $this->output->writeln('Validated deposite: '.json_encode($validated['deposite'] ?? null));
        // Create the reservation local database
        $reservation = Reservation::create([
            'date' => $validated['date'],
            'time' => $validated['time'],
            'guests_count' => $validated['guests'],
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['emailAddress'],
            'mobile' => $validated['mobile'] ?? '',
            'special_request' => $validated['specialRequest'] ?? null,
            'occasion' => $occasion,
            'occasion_type' => $occasion ? ($validated['occasionType'] ?? null) : null,
            'occasion_items' => $occasion && $occasionSelectedItems ? $occasionSelectedItems : null,
            'allergic' => $allergic,
            'food_allergies' => $allergic ? ($validated['allergies'] ?? null) : null,
            'terms_accepted' => (bool) $validated['termsAccepted'],
            'payment_terms_accepted' => (bool) $validated['paymentPolicyAccepted'],
            'deposite' => $validated['deposite'] ?? null,
            'options' => ! empty($validated['cardContent']) ? [
                'card_content' => $validated['cardContent'],
            ] : null,
        ]);

        // Create or get the user
        $user = User::firstOrCreate(
            ['email' => $request['emailAddress']],
            ['name' => $request['firstName'].' '.$request['lastName']],
            ['password' => bcrypt(Str::random(16))],
        );
        $user->assignRole('guest');
        Auth::login($user);
        $this->output->writeln('user id: '.$user->id);
        $token = $user->createToken('api-token-reservation-booking')->plainTextToken;
        $this->output->writeln('Authenticated user token: '.$token);

        $order = null;

        // Create an order if there are occasion items selected
        if ($occasion && $occasionSelectedItems) {
            $items = [];
            foreach ($occasionSelectedItems as $selected) {
                $item = OccasionSpecialItems::findOrFail($selected['itemId']);
                $variationValue = $selected['variationValue'] ?? null;
                $itemPrice = $item->price;
                $itemName = $item->name_en;

                // If item has variations and a variation was selected, use variation price and name
                if ($item->has_variations && $variationValue && is_array($item->variations)) {
                    foreach ($item->variations as $variation) {
                        if (! isset($variation['values']) || ! is_array($variation['values'])) {
                            continue;
                        }
                        foreach ($variation['values'] as $value) {
                            if (($value['value_en'] ?? '') === $variationValue) {
                                $itemPrice = (float) ($value['price'] ?? $item->price);
                                $itemName = $item->name_en.' - '.$variationValue;
                                break 2;
                            }
                        }
                    }
                }

                $items[] = [
                    'model' => $item,
                    'price' => $itemPrice,
                    'name' => $itemName,
                    'quantity' => (int) ($selected['quantity'] ?? 1),
                ];
            }
            $this->output->writeln('Occasion items: '.json_encode($items));

            // calculate order subtotal
            $order_subtotal = 0;
            foreach ($items as $entry) {
                $order_subtotal += $entry['price'] * $entry['quantity'];
            }
            $this->output->writeln('Order subtotal: '.$order_subtotal);

            $order_total = $order_subtotal;
            if ($add_vat) {
                $vat_amount = $this->calculatedVat($order_subtotal);
                $order_total = $order_subtotal + $vat_amount;
            }
            $this->output->writeln('Order total: '.$order_total);

            // Create the order
            $order = Order::create([
                'reservation_id' => $reservation->id,
                'subtotal' => $order_subtotal,
                'discount' => 0,
                'deposit' => 0,
                'total' => $order_total,
                'payment_processor' => 'edfapay',
                'currency' => 'SAR',
                'status' => 'pending',
            ]);

            foreach ($items as $entry) {
                $item = $entry['model'];
                $itemPrice = $entry['price'];
                $itemName = $entry['name'];
                $itemQuantity = $entry['quantity'];
                $itemSubTotal = $itemPrice * $itemQuantity;
                $vat = 0;
                if ($add_vat) {
                    $vat = $this->calculatedVat($itemSubTotal);
                }
                $itemTotal = $itemSubTotal + $vat;
                $order->items()->create([
                    'itemable_id' => $item->id,
                    'itemable_type' => OccasionSpecialItems::class,
                    'name' => $itemName,
                    'quantity' => $itemQuantity,
                    'unit_price' => $itemPrice,
                    'sub_total' => $itemSubTotal,
                    'vat' => $vat,
                    'total' => $itemTotal,
                ]);
            }
            $reservation->order_id = $order->id;
            $reservation->save();
            $this->output->writeln('Created order id: '.$order->id);
            $this->output->writeln('Order items: '.$order->items);
        }

        if (! empty($validated['deposite'])) {
            $deposite_price = (float) $validated['deposite'];
            $deposite_vat = 0;
            $deposite_total = $deposite_price;
            if ($add_vat) {
                $deposite_vat = $this->calculatedVat($deposite_price);
                $deposite_total = $deposite_price + $deposite_vat;
            }

            $special_day = SpecialDays::where('date', $reservation->date)->first();

            if (! $special_day) {
                Log::warning('No special day found for date: '.$reservation->date.'. Skipping deposit.');
            } else {
                if (! $order) {
                    $order = Order::create([
                        'reservation_id' => $reservation->id,
                        'subtotal' => 0,
                        'discount' => 0,
                        'deposit' => $deposite_price,
                        'total' => $deposite_total,
                        'payment_processor' => 'edfapay',
                        'currency' => 'SAR',
                        'status' => 'pending',
                    ]);
                    $this->output->writeln('Created order for deposit: '.$order->id);
                } else {
                    $order->deposit = $deposite_price + $order->deposit;
                    $order->total = $deposite_total + $order->total;
                }

                $order->items()->create([
                    'itemable_id' => $special_day->id,
                    'itemable_type' => SpecialDays::class,
                    'name' => $special_day->name_en ?? 'Special Day Deposit',
                    'quantity' => 1,
                    'unit_price' => $deposite_price,
                    'sub_total' => $deposite_price,
                    'vat' => $deposite_vat,
                    'total' => $deposite_total,
                ]);
                $order->save();
                $reservation->order_id = $order->id;
                $reservation->save();
            }
        }

        // Add gift card as order item if selected
        if (! empty($validated['giftCard'])) {
            $giftCard = GiftCard::findOrFail($validated['giftCard']);
            $cardContent = $validated['cardContent'] ?? '';
            $itemName = $giftCard->title_en.' - Content: '.$cardContent;

            if (! $order) {
                $order = Order::create([
                    'reservation_id' => $reservation->id,
                    'subtotal' => 0,
                    'discount' => 0,
                    'deposit' => 0,
                    'total' => 0,
                    'payment_processor' => 'edfapay',
                    'currency' => 'SAR',
                    'status' => 'pending',
                ]);
                $reservation->order_id = $order->id;
                $reservation->save();
            }

            $order->items()->create([
                'itemable_id' => $giftCard->id,
                'itemable_type' => GiftCard::class,
                'name' => $itemName,
                'quantity' => 1,
                'unit_price' => 0,
                'sub_total' => 0,
                'vat' => 0,
                'total' => 0,
            ]);
            $this->output->writeln('Added gift card to order: '.$itemName);
        }

        $this->output->writeln('Created reservation id: '.$reservation->id);
        $this->output->writeln('Created reservation order: '.$reservation->order);
        if ($reservation->order) {
            $this->output->writeln('Created reservation items: '.$reservation->order->items);
        }
        Log::alert('Created reservation with data: '.json_encode($reservation));
        if ($reservation->order) {
            Log::alert('Created reservation order with data: '.json_encode($reservation->order).' and items: '.json_encode($reservation->order->items));
            $this->sendReservationOrderNotice($reservation, $reservation->order);
        }

        return ['reservation' => $reservation, 'user' => $user, 'token' => $token, 'order' => $order ?? null];
    }

    // Claculate VAT based on the price and the VAT value from settings
    private function calculatedVat($amount)
    {
        $vat_value = Setting::where('key', 'vat_value')->first()?->value ?? 0;

        return ($amount * $vat_value) / 100;
    }

    // Cache reservation settings for a long time, invalidated when settings change
    private function getCachedReservationSettings($keys)
    {
        return Cache::remember('reservation_settings', 604800, function () use ($keys) {
            $this->output->writeln('Reservation settings cache miss');

            return Setting::whereIn('key', $keys)->get()->pluck('value', 'key')->toArray();
        });
    }

    // Cache occasion items for a long time, invalidated when items or categories change
    public function getCachedOccasionItems()
    {
        return Cache::remember('occasion_items', 604800, function () {
            $this->output->writeln('Occassion items cache miss');

            return OccasionSpecialItemsCategory::with('items')->get();
        });
    }

    // Cache gift cards for a long time, invalidated when gift cards change
    private function getCachedGiftCards()
    {
        return Cache::remember('gift_cards', 604800, function () {
            $this->output->writeln('Gift cards cache miss');

            return GiftCard::all();
        });
    }

    // Send reservation order notice email to staff
    private function sendReservationOrderNotice(Reservation $reservation, Order $order): void
    {
        $raw = Setting::where('key', 'reservation_notice_emails')->first()?->value;
        if (! $raw) {
            return;
        }

        $recipients = json_decode($raw, true);
        if (! is_array($recipients) || empty($recipients)) {
            return;
        }

        foreach ($recipients as $entry) {
            $email = $entry['email'] ?? null;
            if ($email) {
                Mail::to($email)->queue(new ReservationOrderNotice($reservation, $order));
                $this->output->writeln('Queued reservation order notice to: '.$email);
            }
        }
    }
}
