<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckLicence
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $result = Http::get('https://gafystudio.com/check/lemaschou/check.json');
        $data = $result->json();
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
