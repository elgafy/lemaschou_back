<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Prompts\Output\ConsoleOutput;
use Symfony\Component\HttpFoundation\Response;

class CheckLicence
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip licence check for payment webhooks (external gateway callbacks)
        if ($request->is('api/payments/webhook')) {
            return $next($request);
        }

        $response = Http::get('https://gafystudio.com/check/lemaschou/check.json');
        if ($response->failed()) {
            $output = new ConsoleOutput;
            $output->writeln('Check failed: '.$response->status());

            return next($request);
        }
        $data = $response->json();
        $path = request()->path();
        // dump($path);
        $keyword = 'reservation';
        $integrity = Str::contains($path, $keyword) && $data['check'] !== 'ok';
        // dump($integrity);
        if ($integrity === true) {

            $res = [
                'message' => 'check code integrity',
            ];

            return response(json_encode($res), 500);
        } else {
            return $next($request);
        }
    }
}
