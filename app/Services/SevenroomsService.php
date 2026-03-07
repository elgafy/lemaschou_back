<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
use Illuminate\Support\Str;
use Laravel\Prompts\Output\ConsoleOutput;

class SevenroomsService
{
    public $token;
    public $output;
    public $venue_id;

    public function __construct() {
        $this->output = new ConsoleOutput();
        $this->venue_id = Cache::remember('venuId', 82800, function() {
            return Setting::where('key', 'sevenrooms_venue_id')->first()->value;
        });
        $this->token = Cache::remember('apiToken', 82800, function() {
            $this->output->writeln("API token cache miss, Getting new Sevenrooms API token...");
            $this->output->writeln("SEVENROOMS_BASE_URL: " . env('SEVENROOMS_BASE_URL'));
            try {
                $response = Http::asForm()->post(env('SEVENROOMS_BASE_URL') . 'auth', [
                    'client_id' => env('SEVENROOMS_CLIENT_ID'),
                    'client_secret' => env('SEVENROOMS_CLIENT_SECRET')
                ]);
                if ($response["status"] == 200) {
                    $data = $response["data"]["token"];
                    return $data;
                }
            } catch (\Throwable $th) {
                Cache::forget('apiToken');
                return null;
            }
        });
    }

    private function refreshToken() {
        Cache::forget('apiToken');
        $this->token = Cache::remember('apiToken', 82800, function() {
            $this->output->writeln("API token cache miss, Getting new Sevenrooms API token...");
            try {
                $response = Http::asForm()->post(env('SEVENROOMS_BASE_URL') . 'auth', [
                    'client_id' => env('SEVENROOMS_CLIENT_ID'),
                    'client_secret' => env('SEVENROOMS_CLIENT_SECRET')
                ]);
                if ($response["status"] == 200) {
                    $data = $response["data"]["token"];
                    return $data;
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        });
    }

    // Get Resturant Venues
    public function getVenues() {
        if (!$this->token) {
            $this->refreshToken();
        }
        $venusRes = Http::withHeaders([
            'Authorization' => $this->token
            ])->get(env('SEVENROOMS_BASE_URL') . 'venues');
        // dump($venusRes['data']['results']);
        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => $venusRes['data']['results'],
        ], 200);
    }

    // Get Resturant Venues
    public function checkAvailability($date, $guests, $starttime, $endtime) {
        if (!$this->token) {
            $this->refreshToken();
        }
        $query = http_build_query([
            'date' => $date,
            'start_time' => $starttime,
            'end_time' => $endtime,
            'party_size' => $guests,
        ]);
        // $this->output->writeln("Api Token: " . $this->token);
        $special_day = SpecialDays::where('date', $date)->first();
        // $this->output->writeln("Special Day for " . $date . ": " . $special_day);
        try {
            // $this->output->writeln("Sevenrooms Availability Request Query: " . $query);
            $response = Http::withHeaders([
            'Authorization' => $this->token
            ])->get(env('SEVENROOMS_BASE_URL') . 'venues/' . $this->venue_id . '/availability?' . $query);
            // $this->output->writeln("Sevenrooms Availability Response: " . $response);
            if ($response["status"] == 200) {
                $times = [];
                if (count($response['data']['availability']) > 0) {
                    foreach ($response['data']['availability'] as $shift) {
                        $amount;
                        if ($special_day) {
                            $shift['shift_category'] == "DINNER" ? $amount = $special_day->dinner_shift_payment_amount : $amount = $special_day->lunch_shift_payment_amount;
                            $this->output->writeln("Special Day Payment Amount for shift: " . $amount);
                        }
                        for ($i = 0; $i < count($shift['times']); $i++) {
                            $avail_time = $shift['times'][$i];
                            // $this->output->writeln("Sevenrooms Availability Time: " . $avail_time['time']);
                            $times[] = [
                                "time" => $avail_time['time'],
                                "duration" => isset($avail_time['duration_minutes_by_party_size']) ? $avail_time['duration_minutes_by_party_size'] : 120,
                                "payment" => $amount ?? null,
                                ];
                        }
                    }
                }
                return response()->json([
                    'success' => true, // based on the success() function name in your code
                    'data' => $times,
                    'special_day' => $special_day,
                ], 200);
            }
            if ($response["status"] != 200) {
                return response()->json([
                    'success' => true, // based on the success() function name in your code
                    'data' => $response['data'],
                    'message' => $response['message'],
                ], $response["status"]);
            }
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function sevenroomsBook($reservation, $user) {
        // Implement booking logic here
        $this->output->writeln("Sevenrooms Booking Request for Reservation: " . $reservation);

        $tags = "";
        // Add occasion food allergies to tags
        if ($reservation['food_allergies']) {
            foreach($reservation['food_allergies'] as $allergy){ $tags .= "Allergies:".$allergy.","; }
        }
        // Add occasion special items to tags
        if ($reservation['occasion_items']) {
            foreach($reservation['occasion_items'] as $item){ $tags .= "Special Occasion Items:". OccasionSpecialItems::findOrFail((int)$item)->name_en .","; }
        }
        // Add occasion type to tags
        if ($reservation['occasion_type']) {
            $tags .= "Special Occasions:".$reservation['occasion_type'].",";
        }
        // Add notes
        $notes = "API TEST \n";
        if ($reservation['special_request']) {
            $notes .= "Special Requests: " . $reservation['special_request'] . ". \n";
        }
        if ($reservation['options']['card_content']) {
            $notes .= "Gift Card Content: " . $reservation['options']['card_content'] . ". \n";
        }

        if ($reservation['order_id']) {
            $order = Order::find($reservation['order_id']);
            $order_total = $order->total;
            $notes .= "Payment Total Amount: " . $order_total . " SAR. \n";
            if ($order->payment_status == "pending") {
                $tags .= "Payments:Processing,";
            }
            if ($order->payment_status == "completed") {
                $tags .= "Payments:Payed,";
            }
        }

        $this->output->writeln("Tags: " . $tags);
        $this->output->writeln("Notes: " . $notes);
        $venue_id = Setting::where('key', 'sevenrooms_venue_id')->first()->value;
        $query = http_build_query([
                'date' => $reservation->date,
                'time' => $reservation->time,
                'party_size' => $reservation->guests_count,
                'first_name' => $reservation->first_name,
                'last_name' => $reservation->last_name,
                'phone' => $reservation->mobile,
                'email' => $reservation->email,
                'external_user_id' => $user->id,
                'external_id' => $reservation->id,
                // 'prepayment_total' => isset($order_total) ? $order_total : 0,
                'tags' => $tags,
                'notes' => $notes,
                // 'prepayment_total' => 300,
                "bypass_duplicate_reservation_check" => "true",

            ]);
        $this->output->writeln("Sevenrooms Query: " . $query);

        // Sevenrooms API call
        $response = Http::withHeaders([
            'Authorization' => $this->token,
            'Accept' => "application/json",
        ])->put(env('SEVENROOMS_BASE_URL') . 'venues/' . $venue_id . '/book?' . $query);
        $this->output->writeln("Sevenrooms response: " . $response);
        if ($response["status"] == 200) {
            $reservation->sevenrooms_reservation_id = $response['data']['reservation_reference_code'];
            $reservation->save();
            return response()->json([
                'success' => true,
                'data' => $response['data'],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'data' => json_encode($response),
            ], 400);
        }
    }

    public function sevenroomsCancel($sevenrooms_reservation_id) {
        // Implement cancellation logic here
        // /reservations/{reservation_id}/cancel
        try {
            // Sevenrooms API call
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Accept' => "application/json",
            ])->post(env('SEVENROOMS_BASE_URL') . 'reservations/' . $sevenrooms_reservation_id . '/cancel');
            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function sevenroomsUpdate($sevenrooms_reservation_id, $updates) {

        $query = http_build_query($updates);
        $this->output->writeln("Sevenrooms Update Query: " . $query);
        try {
            // Sevenrooms API call
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Accept' => "application/json",
            ])->post(env('SEVENROOMS_BASE_URL') . 'reservations/' . $sevenrooms_reservation_id . '?' . $query);
            return $response;
        } catch (\Throwable $th) {
            throw $th;
        }


    }
}
