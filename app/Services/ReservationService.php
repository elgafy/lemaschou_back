<?php

namespace App\Services;

use App\Models\OccasionSpecialItems;
use App\Models\OccasionSpecialItemsCategory;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\SpecialDays;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Prompts\Output\ConsoleOutput;

class ReservationService
{
    public $token = '';
    public $output;

    public function __construct() {
        $this->output = new ConsoleOutput();
    }
    public function getSettings () {
        $keys = [
            'use_reservation_external_link',
            'reservation_link',
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
        $reservation_settings = Setting::whereIn('key', $keys)->get()->pluck('value', 'key')->toArray();
        $occasions = json_decode(Setting::where('key', 'occasions')->first()?->value ?? '', true) ?? [];
        $allergies = json_decode(Setting::where('key', 'allergies')->first()?->value ?? '', true);
        $items = OccasionSpecialItemsCategory::with('items')->get();
        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => [
                "foodAllergies" => $allergies,
                "occasions" => $occasions,
                "occasionItems" => $items,
                "settings" => $reservation_settings
            ],
        ], 200);
    }
    public function makeReservation(Request $request) {
        Setting::where('key', 'add_calculated_vat')->first()?->value == 'true' ? $add_vat = true : $add_vat = false;
        Log::alert("Making reservation with data: " . json_encode($request->all()));
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|string|max:10',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'emailAddress' => 'required|string|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'specialRequest' => 'nullable|string|max:255',
            'guests' => 'required|integer|min:1',
            'occasion' => 'nullable|string|max:5',
            'occasionType' => 'nullable|string|max:255',
            'occasionSelectedItems' => 'nullable|string|max:255',
            'occasionItemsPrice' => 'nullable|numeric',
            'deposite' => 'nullable|numeric',
            'cardContent' => 'nullable|string|max:255',
            'allergic' => 'nullable|string|max:5',
            'allergies' => 'nullable|string|max:255',
            'termsAccepted' => 'required|string|max:5',
            'paymentPolicyAccepted' => 'required|string|max:5',
        ]);
        $this->output->writeln("Validated occasion: " . $validated['occasion']);
        $this->output->writeln("Validated occasionSelectedItems: " . json_encode($validated['occasionSelectedItems']));
        $this->output->writeln("Validated allergies: " . $validated['allergies']);
        $this->output->writeln("Validated deposite: " . $validated['deposite']);
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
            'occasion' => $validated['occasion'] ?? false,
            'occasion_type' => $validated['occasion'] == true ? $validated['occasionType'] : null,
            'occasion_items' => $validated['occasion'] == true ? (  isset($validated['occasionSelectedItems']) && $validated['occasionSelectedItems'] != '' ? explode(',', $validated['occasionSelectedItems']) : null ) : null,
            'allergic' => $validated['allergic'] ?? false,
            'food_allergies' => $validated['allergic'] == true ? (isset($validated['allergies']) ? explode(',', $validated['allergies']) : null) : null,
            'terms_accepted' => $validated['termsAccepted'] === 'true' ? true : false,
            'payment_terms_accepted' => $validated['paymentPolicyAccepted'] === 'true' ? true : false,
            'deposite' => $validated['deposite'],
            'options' => $validated['cardContent'] ? [
                'card_content' => $validated['cardContent'],
            ] : null,
        ]);

        // Create or get the user
        $user = User::firstOrCreate(
            ['email' => $request['emailAddress']],
            ['name' => $request['firstName'] . ' ' . $request['lastName']],
            ['password' => bcrypt(Str::random(16))],
        );
        $user->assignRole('guest');
        Auth::login($user);
        $this->output->writeln("user id: " . $user->id);
        $token = $user->createToken('api-token-reservation-booking')->plainTextToken;
        $this->output->writeln("Authenticated user token: " . $token);
        // if special day, add special day deposite to order

        // Create an order if there are occasion items selected
        if (($validated['occasion']  == true && isset($validated['occasionSelectedItems']) && $validated['occasionSelectedItems'] != '') ) {
            $items_ids = explode(',', $validated['occasionSelectedItems']);
            $items = [];
            foreach ($items_ids as $item_id) {
                $item = OccasionSpecialItems::findOrFail($item_id);
                $items[] = $item;
            }
            $this->output->writeln("Occasion items: " . json_encode($items));

            // calculate order price and total
            $order_price = 0;
            $order_total = 0;
            $calculated_vat = 0;
            foreach ($items as $item) {
                $order_price += $item->price;
            }
            $this->output->writeln("Order total: " . $order_price);

            if ($add_vat) {
                $calculated_vat = $this->calculatedVat($order_price);
                $order_total = $order_price + $calculated_vat;
            } else {
                $order_total = $order_price;
            }
            $this->output->writeln("Order total with VAT: " . $order_total);

            // Create the order
            $order = Order::create([
                "user_id" => $user->id,
                "user_email" => $user->email,
                "status" => "pending",
                "price" => $order_price ?? 0,
                "vat" => $calculated_vat,
                "total" => $order_total ?? 0,
                "payment_processor" => "sevenrooms",
                "payment_reference" => "",
            ]);

            foreach ($items as $item) {
                $price = $item->price;
                $item_vat = 0;
                $item_total = $price;
                if ($add_vat) {
                    $item_vat = $this->calculatedVat($price);
                    $item_total = $price + $item_vat;
                }
                $order->items()->create([
                    'itemable_id'   => $item->id,
                    'itemable_type' => OccasionSpecialItems::class,
                    'quantity'      => 1,
                    'price'         => $item->price,
                    'vat'           => $item_vat,
                    'total_price'   => $item_total,
                ]);
            }
            $reservation->order_id = $order->id;
            $reservation->save();
            $order->save();
            $this->output->writeln("Order reservation: " . $order->reservation);
            $this->output->writeln("Order items: " . $order->items);
        }

        if ($validated['deposite']) {
            $deposite_price = $validated['deposite'];
            $deposite_total = 0;
            $calculated_vat = 0;
            if ($add_vat) {
                $calculated_vat = $this->calculatedVat($deposite_price);
                $deposite_total = $deposite_price + $calculated_vat;
            } else {
                $deposite_total = $deposite_price;
            }
            if (!isset($order)) {
                $order = Order::create([
                    "user_id" => $user->id,
                    "user_email" => $user->email,
                    "status" => "pending",
                    "price" => $deposite_price,
                    "vat" => $calculated_vat,
                    "total" => $deposite_total,
                    "payment_processor" => "sevenrooms",
                    "payment_reference" => "",
                ]);
                $this->output->writeln("Created order for deposite: " . $order->id);
            } else {
                $order->price = $deposite_price + $order->price;
                $order->vat = $calculated_vat + $order->vat;
                $order->total = $deposite_total + $order->total;
            }

            // Add special day deposite to order items
            $special_day = SpecialDays::where('date', $reservation->date)->first();
            $order->items()->create([
                    'itemable_id'   => $special_day->id,
                    'itemable_type' => SpecialDays::class,
                    'quantity'      => 1,
                    'price'         => $deposite_price,
                    'vat'           => $calculated_vat,
                    'total_price'   => $deposite_total,
                ]);
            $order->save();
            $reservation->order_id = $order->id;
            $reservation->save();
        }

        $this->output->writeln("Created reservation id: " . $reservation->id);
        $this->output->writeln("Created reservation order: " . $reservation->order);
        if ($reservation->order) $this->output->writeln("Created reservation items: " . $reservation->order->items);
        Log::alert("Created reservation with data: " . json_encode($reservation));
        if ($reservation->order) Log::alert("Created reservation order with data: " . json_encode($reservation->order) . " and items: " . json_encode($reservation->order->items));
        return ['reservation' => $reservation, 'user' => $user, 'token' => $token, 'order' => $order ?? null];
    }

    // Claculate VAT based on the price and the VAT value from settings
    private function calculatedVat($amount) {
        $vat_value = Setting::where('key', 'vat_value')->first()?->value ?? 0;
        return (($amount * $vat_value) / 100);
    }
}
