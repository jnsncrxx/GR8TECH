<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class HrController extends Controller
{
    public function profile(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee?->load('otherInfo');
        $departments = Department::all();
        $photoUrl = null;

        if ($employee && $employee->otherInfo && $employee->otherInfo->photo_path) {
            $photoUrl = asset('storage/' . $employee->otherInfo->photo_path);
        }

        return view('hr.profile', compact('user', 'employee', 'departments', 'photoUrl'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $canEditRestricted = in_array($user->role, ['admin', 'hr']);

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accounts,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'civil_status' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:255',
            'home_address' => 'nullable|string',
            'current_address' => 'nullable|string',
            'facebook_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'ig_link' => 'nullable|url',
            'other_link' => 'nullable|url',
            'emergency_full_name' => 'nullable|string|max:255',
            'emergency_relationship' => 'nullable|string|max:255',
            'emergency_home_address' => 'nullable|string|max:255',
            'emergency_current_address' => 'nullable|string|max:255',
            'emergency_mobile_number' => 'nullable|string|max:255',
            'emergency_email' => 'nullable|email|max:255',
            'emergency_facebook_link' => 'nullable|url',
            'loan_start_date' => 'nullable|date',
            'loan_end_date' => 'nullable|date',
            'loan_total_amount' => 'nullable|numeric|min:0',
            'loan_monthly_amortization' => 'nullable|numeric|min:0',
        ];

        if ($canEditRestricted) {
            $rules['position'] = 'nullable|string|max:255';
            $rules['department_id'] = 'nullable|exists:departments,id';
            $rules['employment_type'] = 'nullable|string|max:255';
            $rules['hire_date'] = 'nullable|date';
            $rules['salary'] = 'nullable|numeric|min:0';
        }

        $request->validate($rules);

        // Update User Account
        $user->update([
            'email' => $request->email,
        ]);

        // Update Employee Record
        if ($employee) {
            $employeeData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'civil_status' => $request->civil_status,
                'mobile_number' => $request->mobile_number,
                'home_address' => $request->home_address,
                'current_address' => $request->current_address,
                'facebook_link' => $request->facebook_link,
                'linkedin_link' => $request->linkedin_link,
                'ig_link' => $request->ig_link,
                'other_link' => $request->other_link,
                'emergency_full_name' => $request->emergency_full_name,
                'emergency_relationship' => $request->emergency_relationship,
                'emergency_home_address' => $request->emergency_home_address,
                'emergency_current_address' => $request->emergency_current_address,
                'emergency_mobile_number' => $request->emergency_mobile_number,
                'emergency_email' => $request->emergency_email,
                'emergency_facebook_link' => $request->emergency_facebook_link,
                'loan_start_date' => $request->loan_start_date,
                'loan_end_date' => $request->loan_end_date,
                'loan_total_amount' => $request->loan_total_amount,
                'loan_monthly_amortization' => $request->loan_monthly_amortization,
            ];

            if ($canEditRestricted) {
                $employeeData['position'] = $request->position;
                $employeeData['department_id'] = $request->department_id;
                $employeeData['employment_type'] = $request->employment_type;
                $employeeData['hire_date'] = $request->hire_date;
                $employeeData['salary'] = $request->salary;
            }

            $employee->update($employeeData);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:3072',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            return redirect()->back()->with('error', 'Unable to save photo because no employee record was found.');
        }

        $otherInfo = $employee->otherInfo()->firstOrNew();
        $otherInfo->employee_id = $employee->id;

        if ($otherInfo->photo_path && Storage::disk('public')->exists($otherInfo->photo_path)) {
            Storage::disk('public')->delete($otherInfo->photo_path);
        }

        $otherInfo->photo_path = $request->file('photo')->store('employee-photos', 'public');
        $otherInfo->save();

        return redirect()->back()->with('success', 'Profile photo updated successfully.');
    }

    public function settings(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $canManageHrSettings = in_array($user->role, ['admin', 'hr'], true);

        // Employees may view their own account settings, but they must not
        // receive company-wide HR configuration data.
        $departments = $canManageHrSettings
            ? Department::orderBy('name')->get()
            : collect();

        return view('hr.settings', compact(
            'user',
            'employee',
            'departments',
            'canManageHrSettings'
        ));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $canManageHrSettings = in_array($user->role, ['admin', 'hr'], true);

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:accounts,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:255'],
            'date_format' => ['nullable', 'string', 'max:20'],
            'dark_mode' => ['nullable', 'boolean'],
            'email_notifications' => ['nullable', 'boolean'],
            'auto_save' => ['nullable', 'boolean'],
        ];

        if ($canManageHrSettings) {
            $rules['department_id'] = ['nullable', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        if ($employee) {
            $employeeData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'] ?? null,
            ];

            if ($canManageHrSettings && array_key_exists('department_id', $validated)) {
                $employeeData['department_id'] = $validated['department_id'];
            }

            $employee->update($employeeData);
        }

        $user->update(['email' => $validated['email']]);

        $preferences = [
            'timezone' => $request->input('timezone', 'Asia/Manila'),
            'date_format' => $request->input('date_format', 'MM/DD/YYYY'),
            'dark_mode' => $request->boolean('dark_mode'),
            'email_notifications' => $request->boolean('email_notifications'),
            'auto_save' => $request->boolean('auto_save'),
        ];

        session(['user_preferences' => $preferences]);

        return redirect()->back()->with('success', 'Account settings saved successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
                'different:current_password',
            ],
        ]);

        $user = Auth::user();

        if (! $user->verifyPassword($validated['current_password'])) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ])->withInput();
        }

        // Account casts password as hashed, so assigning the plain new value
        // stores it securely without double hashing.
        $user->password = $validated['password'];
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function exportData(Request $request)
    {
        return response()->json(['message' => 'Export data not yet implemented'], 501);
    }

    public function backupData(Request $request)
    {
        return response()->json(['message' => 'Backup data not yet implemented'], 501);
    }

    public function getUserSessions(Request $request)
    {
        return view('hr.sessions', ['user' => Auth::user()]);
    }

    public function terminateSession(Request $request, $session)
    {
        return response()->json(['message' => 'Terminate session not yet implemented'], 501);
    }

    public function terminateAllOtherSessions(Request $request)
    {
        return response()->json(['message' => 'Terminate all sessions not yet implemented'], 501);
    }

    public function trackLoginSession(Request $request)
    {
        return response()->json(['message' => 'Track session not yet implemented'], 501);
    }
}
