<?php

namespace App\Providers;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\Gateways\EdfapayGateway;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function () {
            return match (config('payment.gateway')) {
                'edfapay' => new EdfapayGateway,
                default => throw new \RuntimeException('Unsupported payment gateway: '.config('payment.gateway')),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define the response macro for res() logic
        Response::macro('res', function ($status, $key, $data = null, $code = null) {
            // Assuming getLang() is a global helper function
            $lang = getLang();

            $response = [
                'status' => $status,
                'message' => Config::get('response.'.$key.'.'.$lang),
                'data' => $data ?? [],
            ];

            // Return the response as a JSON object
            return response()->json($response, $code);
        });

    }
}
