<?php

namespace App\Providers;

use App\Models\OccasionSpecialItems;
use App\Models\SpecialDays;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
            // Define the response macro for res() logic
            Response::macro('res', function ($status, $key, $data = null,$code=null) {
                // Assuming getLang() is a global helper function
                $lang = getLang();

                $response = [
                    'status' => $status,
                    'message' => Config::get('response.' . $key . '.' . $lang),
                    'data' => $data ?? [],
                ];

                // Return the response as a JSON object
                return response()->json($response,$code);
            });

    }
}
