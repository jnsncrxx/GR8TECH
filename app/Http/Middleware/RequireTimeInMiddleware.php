<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireTimeInMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is not authenticated, let other middleware handle it
        if (!$user) {
            return $next($request);
        }

        // Allow admin, hr, and manager roles to access all modules regardless of time-in status
        if (in_array(strtolower($user->role), ['admin', 'hr', 'manager'])) {
            return $next($request);
        }

        // Try to get employee relationship, or find by employee_id if relationship fails
        $employee = $user->employee;
        
        // If relationship is null but employee_id exists, try to find the employee directly
        if (!$employee && $user->employee_id) {
            $employee = \App\Models\Employee::find($user->employee_id);
        }

        // If no employee record, let other middleware handle it
        if (!$employee) {
            return $next($request);
        }

        // Allow access to dashboard, logout, time-in/out routes, company switching,
        // and forgot-time support routes regardless of time-in status
        if ($request->routeIs([
            'dashboard',
            'logout',
            'attendance.time-in',
            'attendance.time-out',
            'attendance.status',
            'attendance.overtime.quick-submit',
            'attendance.overtime.dismiss-reminder',
            'companies.switch',
            'companies.index',
            'hr.help-support',
            'hr.help-support-ticket-store',
            'attendance.official-business',
            'attendance.official-business.store',
            'attendance.official-business.cancel',
            'attendance.official-business.statistics',
            'attendance.official-business.update-status',
            'notifications.mine',
            'notifications.read',
            'notifications.read-all',
            'search',
            'search.modules',
        ])) {
            return $next($request);
        }

        // Check if employee has an active session right now
        $todayAttendance = $employee->getTodayAttendance();

        if (!$todayAttendance || !$todayAttendance->hasActiveTimeEntry()) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be currently timed in to access other modules.');
        }
    

        return $next($request);
    }
}