<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CompanyHelper;
use Symfony\Component\HttpFoundation\Response;

class CompanyContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to authenticated users
        if (Auth::check()) {
            // Replace a missing, deleted, or inactive session company with a
            // valid active company so every downstream query has a context.
            if (!CompanyHelper::getCurrentCompany()?->is_active) {
                $firstCompany = \App\Models\Company::where('is_active', true)
                    ->orderBy('name')
                    ->first();
                
                if ($firstCompany) {
                    CompanyHelper::setCurrentCompany($firstCompany);
                } else {
                    CompanyHelper::clearCurrentCompany();
                }
            }
        }

        return $next($request);
    }
}
