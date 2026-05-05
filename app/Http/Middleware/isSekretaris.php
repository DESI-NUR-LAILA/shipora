<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isSekretaris
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->hak_akses?->nama_hak_akses === 'Sekretaris') {
            return $next($request);
        } else {
            return redirect()->route('dashboard')->with('error', 'Anda Tidak Punya Akses');
        }
    }
}