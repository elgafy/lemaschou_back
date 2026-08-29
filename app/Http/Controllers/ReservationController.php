<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Reservation;
use App\Services\Payments\PaymentService;
use App\Services\ReservationService;
use App\Services\SevenroomsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Prompts\Output\ConsoleOutput;

class ReservationController extends Controller
{
    public $token = '';

    public $sevenroomsService;

    public $reservationService;

    public $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput;
        $this->sevenroomsService = new SevenroomsService;
        $this->reservationService = new ReservationService;
        $this->token = Cache::remember('apiToken', 82800, function () {
            // dump("cache miss");
            // dump(env('SEVENROOMS_BASE_URL'));
            try {
                // code...
                $response = Http::asForm()->post(env('SEVENROOMS_BASE_URL').'auth', [
                    'client_id' => env('SEVENROOMS_CLIENT_ID'),
                    'client_secret' => env('SEVENROOMS_CLIENT_SECRET'),
                ]);
                if ($response['status'] == 200) {
                    $data = $response['data']['token'];

                    return $data;
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        });
    }

    public function getVenues()
    {
        $venusRes = Http::withHeaders([
            'Authorization' => $this->token,
        ])->get(env('SEVENROOMS_BASE_URL').'venues');

        // dump($venusRes['data']['results']);
        return response()->json([
            'success' => true, // based on the success() function name in your code
            'data' => $venusRes['data']['results'],
        ], 200);
    }

    public function getReservationSettings()
    {
        return $this->reservationService->getSettings();
    }

    public function checkAvailability($date, $guests, $start_time = '9am', $end_time = '11pm')
    {
        return $this->sevenroomsService->checkAvailability($date, $guests, $start_time, $end_time);
    }

    // Delete after develpment
    public function getOccasionItems()
    {
        $items = $this->reservationService->getCachedOccasionItems();

        return response()->json([
            'success' => true,
            'data' => $items,
        ], 200);
    }

    // Booking endpoint
    public function book(Request $request)
    {
        $output = new ConsoleOutput;

        // Validate the request data
        $result = $this->reservationService->makeReservation($request);
        $reservation = $result['reservation'];
        $user = $result['user'];
        $token = $result['token'];
        $order = $result['order'];

        // Create the reservation in Sevenrooms system
        $this->sevenroomsService->sevenroomsBook($reservation, $user);

        // Build response data
        $responseData = [
            'user_token' => $token,
            'reservation' => json_encode($reservation),
            'message' => 'Reservation created successfully',
        ];

        // If order has a balance, initiate payment and return redirect URL
        if ($order && (float) $order->total > 0) {
            try {
                $paymentService = app(PaymentService::class);
                $paymentResult = $paymentService->initiate($order, $reservation);
                $responseData['payment'] = [
                    'payment_id' => $paymentResult['payment']->id,
                    'redirect_url' => $paymentResult['redirect_url'],
                ];
            } catch (\Throwable $e) {
                Log::error('Payment initiation failed during booking', [
                    'order_id' => $order->id,
                    'reservation_id' => $reservation->id,
                    'error' => $e->getMessage(),
                ]);
                $responseData['payment_error'] = 'Payment could not be initiated. Please try again later.';
            }
        }

        return response()->json([
            'success' => true,
            'data' => $responseData,
        ], 201);
    }

    public function getReservation(Request $request)
    {
        $reservation = Reservation::where('sevenrooms_reservation_id', $request->id)->first();
        $this->output->writeln('Reservation request by id: '.json_encode($reservation));
        // fix returns when reservation not found
        if ($reservation) {
            return response()->json([
                'success' => true,
                'data' => $reservation,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'data' => 'Reservation not found',
            'message' => 'Reservation not found',
        ], 404);

    }
}
