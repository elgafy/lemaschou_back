<?php

namespace App\Http\Controllers;

use App\Models\OccasionSpecialItems;
use App\Models\OccasionSpecialItemsCategory;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Setting;
use App\Models\User;
use App\Services\ReservationService;
use App\Services\SevenroomsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Prompts\Output\ConsoleOutput;

class ReservationController extends Controller
{
    public $token = '';
    public $sevenroomsService;
    public $reservationService;
    public function __construct() {
        $this->sevenroomsService = new SevenroomsService();
        $this->reservationService = new ReservationService();
        $this->token = Cache::remember('apiToken', 82800, function() {
            // dump("cache miss");
            // dump(env('SEVENROOMS_BASE_URL'));
            try {
                //code...
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
    public function getVenues() {
        $venusRes = Http::withHeaders([
            'Authorization' => $this->token
            ])->get(env('SEVENROOMS_BASE_URL') . 'venues');
        // dump($venusRes['data']['results']);
        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => $venusRes['data']['results'],
        ], 200);
    }

    public function getReservationSettings() {
        return $this->reservationService->getSettings();
    }


    public function checkAvailability($date, $guests, $start_time = "9am", $end_time = "11pm") {
        return $this->sevenroomsService->checkAvailability($date, $guests, $start_time, $end_time);
    }

    // Delete after develpment
    public function getOccasionItems() {
        $items = OccasionSpecialItemsCategory::with('items')->get();
        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => $items,
        ], 200);
    }

    public function book(Request $request) {
        $output = new ConsoleOutput();
        $output->writeln($request->all());

        // Validate the request data
        $result = $this->reservationService->makeReservation($request);
        $reservation = $result['reservation'];
        $user = $result['user'];
        $token = $result['token'];
        $order = $result['order'];

        $output->writeln("Reservation created: " . $reservation);
        $output->writeln("User created: " . $user);
        $output->writeln("Token created: " . $token);
        $output->writeln("Order created: " . $order);



        // Create the reservation in Sevenrooms system
        // $this->sevenroomsService->sevenroomsBook($reservation, $user);

        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => [
                // "reservation_id" => $reservation->id,
                "user_token" => $token,
                "reservation" => json_encode($reservation),
                "message" => 'Reservation created successfully',
                ],
        ], 201);
    }
}
