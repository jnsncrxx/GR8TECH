<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class HrController extends Controller
{
    public function profile(Request $request)
    {
        return view('hr.profile', $this->buildProfileViewData());
    }

    public function updateProfile(Request $request)
    {
        return $this->persistProfileUpdate($request);
    }

    public function updateProfilePhoto(Request $request)
    {
        return $this->persistProfilePhoto($request);
    }

    /**
     * "My Information > Personal Information" page.
     *
     * Separate route/view from profile() above (header's "Profile" link),
     * but both operate on the same underlying employee record, so the
     * validation/persist logic is shared via the private helpers below.
     */
    public function myPersonalInfo(Request $request)
    {
        return view('hr.my-information.info', $this->buildProfileViewData());
    }

    public function updateMyPersonalInfo(Request $request)
    {
        return $this->persistProfileUpdate($request);
    }

    public function uploadMyPersonalInfoPhoto(Request $request)
    {
        return $this->persistProfilePhoto($request);
    }

    private function buildProfileViewData(): array
    {
        $user = Auth::user();
        $employee = $user->employee?->load(['otherInfo', 'info']);
        $departments = Department::all();
        $photoUrl = null;

        if ($employee && $employee->otherInfo && $employee->otherInfo->photo_path) {
            $photoUrl = asset('storage/' . $employee->otherInfo->photo_path);
        }

        return compact('user', 'employee', 'departments', 'photoUrl');
    }

    private function persistProfileUpdate(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $canEditRestricted = in_array($user->role, ['admin', 'hr']);

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accounts,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'sex' => 'nullable|string|max:255',
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
            // Banking & Government IDs
            'payment_method' => 'nullable|string|max:255',
            'bank' => 'nullable|required_if:payment_method,Bank|string|max:255',
            'account_no' => 'nullable|required_if:payment_method,Bank|string|max:255',
            'tax_code' => 'nullable|string|max:255',
            'tin_no' => 'nullable|string|max:255',
            'sss_no' => 'nullable|string|max:255',
            'hdmf_no' => 'nullable|string|max:255',
            'philhealth_no' => 'nullable|string|max:255',
            'hmo_no' => 'nullable|string|max:255',
            'tax_computation_method' => 'nullable|string|max:255',
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
                'sex' => $request->sex,
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
                // Government-issued IDs live directly on the employee record
                'tax_code' => $request->tax_code,
                'tin_no' => $request->tin_no,
                'sss_no' => $request->sss_no,
                'hdmf_no' => $request->hdmf_no,
                'philhealth_no' => $request->philhealth_no,
                'hmo_no' => $request->hmo_no,
                'tax_computation_method' => $request->tax_computation_method,
            ];

            if ($canEditRestricted) {
                $employeeData['position'] = $request->position;
                $employeeData['department_id'] = $request->department_id;
                $employeeData['employment_type'] = $request->employment_type;
                $employeeData['hire_date'] = $request->hire_date;
                $employeeData['salary'] = $request->salary;
            }

            $employee->update($employeeData);

            // Banking details live on the employee's "info" relation
            // (same relation edit_blade.php reads via $employee->info?->...).
            $info = $employee->info ?: $employee->info()->make();
            $info->fill([
                'payment_method' => $request->payment_method,
                'bank' => $request->bank,
                'account_no' => $request->account_no,
            ]);
            $employee->info()->save($info);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    private function persistProfilePhoto(Request $request)
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

    /*
    |--------------------------------------------------------------------------
    | My Information (employee self-service)
    |--------------------------------------------------------------------------
    |
    | Mirrors the admin "Employees" screens (Other Employee Info, Education/
    | Training/Rating, Previous Employer & Other, Documents, YTD-INFO, Bio-ZK)
    | but always scoped to the signed-in user's own employee record - there is
    | no employee picker, and every field is pre-filled with that employee's
    | existing data when available.
    |
    */

    /**
     * Personal Information tab (My Information self-service).
     *
     * This is intentionally separate from profile()/updateProfile() above -
     * that pair backs the "My Profile" page, while this pair backs the
     * "Personal Information" item under the My Information menu, styled
     * after the admin's Edit Employee screen.
     */
    public function myPersonalInformation(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $canEditRestricted = in_array($user->role, ['admin', 'hr'], true);

        $departments = $canEditRestricted ? Department::orderBy('name')->get() : collect();

        return view('hr.my-information.personal', compact('user', 'employee', 'departments', 'canEditRestricted'));
    }

    public function updateMyPersonalInformation(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (! $employee) {
            return redirect()->route('hr.my-information.personal')
                ->with('error', 'No employee record was found for your account.');
        }

        $canEditRestricted = in_array($user->role, ['admin', 'hr'], true);

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accounts,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'mobile_number' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'civil_status' => 'nullable|string|max:255',
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
            'loan_end_date' => 'nullable|date|after_or_equal:loan_start_date',
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

        $validated = $request->validate($rules);

        $user->update(['email' => $validated['email']]);

        $employeeData = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'civil_status' => $validated['civil_status'] ?? null,
            'home_address' => $validated['home_address'] ?? null,
            'current_address' => $validated['current_address'] ?? null,
            'facebook_link' => $validated['facebook_link'] ?? null,
            'linkedin_link' => $validated['linkedin_link'] ?? null,
            'ig_link' => $validated['ig_link'] ?? null,
            'other_link' => $validated['other_link'] ?? null,
            'emergency_full_name' => $validated['emergency_full_name'] ?? null,
            'emergency_relationship' => $validated['emergency_relationship'] ?? null,
            'emergency_home_address' => $validated['emergency_home_address'] ?? null,
            'emergency_current_address' => $validated['emergency_current_address'] ?? null,
            'emergency_mobile_number' => $validated['emergency_mobile_number'] ?? null,
            'emergency_email' => $validated['emergency_email'] ?? null,
            'emergency_facebook_link' => $validated['emergency_facebook_link'] ?? null,
            'loan_start_date' => $validated['loan_start_date'] ?? null,
            'loan_end_date' => $validated['loan_end_date'] ?? null,
            'loan_total_amount' => $validated['loan_total_amount'] ?? null,
            'loan_monthly_amortization' => $validated['loan_monthly_amortization'] ?? null,
        ];

        if ($canEditRestricted) {
            $employeeData['position'] = $validated['position'] ?? null;
            $employeeData['department_id'] = $validated['department_id'] ?? null;
            $employeeData['employment_type'] = $validated['employment_type'] ?? null;
            $employeeData['hire_date'] = $validated['hire_date'] ?? null;
            $employeeData['salary'] = $validated['salary'] ?? null;
        }

        $employee->update($employeeData);

        return redirect()->route('hr.my-information.personal')
            ->with('success', 'Personal information updated successfully.');
    }

    public function uploadMyPersonalPhoto(Request $request)
    {
        $employee = Auth::user()->employee;
        abort_unless($employee, 404);

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $employee->profile_photo = $request->file('photo')->store('employee-photos', 'public');
        $employee->save();

        return redirect()->route('hr.my-information.personal')
            ->with('success', 'Photo uploaded successfully.');
    }

    /**
     * Other Info tab - address, physical details, family background, photo.
     */
    public function myOtherInfo(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $hasOtherInfoTable = Schema::hasTable('employee_other_infos');
        $otherInfo = null;

        if ($employee && $hasOtherInfoTable) {
            $employee->load('otherInfo');
            $otherInfo = $employee->otherInfo;
        }

        return view('hr.my-information.other-info', compact('user', 'employee', 'otherInfo', 'hasOtherInfoTable'));
    }

    public function saveMyOtherInfo(Request $request)
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('hr.my-information.other-info')
                ->with('error', 'No employee record was found for your account.');
        }

        if (! Schema::hasTable('employee_other_infos')) {
            return redirect()->route('hr.my-information.other-info')
                ->with('error', 'Other employee info table is not ready yet. Please contact HR/Admin.');
        }

        $validated = $request->validate([
            'address' => 'nullable|string|max:255',
            'pov_address' => 'nullable|string|max:255',
            'no_street' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'town_district' => 'nullable|string|max:255',
            'city_province' => 'nullable|string|max:255',
            'birthplace' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:255',
            'blood_type' => 'nullable|string|max:255',
            'citizenship' => 'nullable|string|max:255',
            'height' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'drivers_license' => 'nullable|string|max:255',
            'prc_no' => 'nullable|string|max:255',
            'father' => 'nullable|string|max:255',
            'mother' => 'nullable|string|max:255',
            'spouse' => 'nullable|string|max:255',
            'spouse_employed' => 'nullable|boolean',
        ]);

        $validated['spouse_employed'] = $request->boolean('spouse_employed');

        $otherInfo = $employee->otherInfo()->firstOrNew([]);
        $otherInfo->employee_id = $employee->id;
        $otherInfo->fill($validated);
        $otherInfo->save();

        return redirect()->route('hr.my-information.other-info')
            ->with('success', 'Other info saved successfully.');
    }

    public function uploadMyOtherInfoPhoto(Request $request)
    {
        $employee = Auth::user()->employee;
        abort_unless($employee, 404);

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $employee->profile_photo = $request->file('photo')->store('employee-photos', 'public');
        $employee->save();

        return redirect()->route('hr.my-information.other-info')
            ->with('success', 'Photo uploaded successfully.');
    }

    public function clearMyOtherInfoPhoto(Request $request)
    {
        $employee = Auth::user()->employee;
        abort_unless($employee, 404);

        if ($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo)) {
            Storage::disk('public')->delete($employee->profile_photo);
        }

        $employee->profile_photo = null;
        $employee->save();

        return redirect()->route('hr.my-information.other-info')
            ->with('success', 'Photo removed successfully.');
    }

    /**
     * Education / Training / Rating tab.
     */
    public function myEducationTrainingRating()
    {
        $user = Auth::user();
        $employee = $user->employee;

        return view('hr.my-information.education-training-rating', compact('user', 'employee'));
    }

    /**
     * Previous Employer & Other tab.
     */
    public function myPrevEmpOth()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $hasPreviousEmploymentsTable = Schema::hasTable('previous_employments');
        $prefilledRows = collect();

        if ($employee && $hasPreviousEmploymentsTable) {
            $employee->load('previousEmployments');
            $prefilledRows = $employee->previousEmployments->keyBy('sequence');
        }

        $yearNow = now()->year;
        $years = range($yearNow, $yearNow - 60);

        return view('hr.my-information.prev-emp-oth', compact('user', 'employee', 'prefilledRows', 'years', 'hasPreviousEmploymentsTable'));
    }

    public function saveMyPrevEmpOth(Request $request)
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('hr.my-information.prev-emp-oth')
                ->with('error', 'No employee record was found for your account.');
        }

        if (! Schema::hasTable('previous_employments')) {
            return redirect()->route('hr.my-information.prev-emp-oth')
                ->with('error', 'Previous employment table is not ready yet. Please contact HR/Admin.');
        }

        $validated = $request->validate([
            'rows' => 'required|array|size:5',
            'rows.*.employment_name' => 'nullable|string|max:255',
            'rows.*.position' => 'nullable|string|max:255',
            'rows.*.start_month' => 'nullable|integer|min:1|max:12',
            'rows.*.start_year' => 'nullable|integer|min:1900|max:2100',
            'rows.*.end_month' => 'nullable|integer|min:1|max:12',
            'rows.*.end_year' => 'nullable|integer|min:1900|max:2100',
        ]);

        DB::transaction(function () use ($employee, $validated) {
            $employee->previousEmployments()->delete();

            foreach ($validated['rows'] as $index => $row) {
                $hasValue = filled($row['employment_name'] ?? null)
                    || filled($row['position'] ?? null)
                    || filled($row['start_month'] ?? null)
                    || filled($row['start_year'] ?? null)
                    || filled($row['end_month'] ?? null)
                    || filled($row['end_year'] ?? null);

                if (! $hasValue) {
                    continue;
                }

                $employee->previousEmployments()->create([
                    'sequence' => $index + 1,
                    'employment_name' => $row['employment_name'] ?? null,
                    'position' => $row['position'] ?? null,
                    'start_month' => $row['start_month'] ?? null,
                    'start_year' => $row['start_year'] ?? null,
                    'end_month' => $row['end_month'] ?? null,
                    'end_year' => $row['end_year'] ?? null,
                ]);
            }
        });

        return redirect()->route('hr.my-information.prev-emp-oth')
            ->with('success', 'Previous employment details saved successfully.');
    }

    /**
     * Documents tab.
     */
    public function myDocuments()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $existingDocuments = collect();

        if ($employee) {
            $employee->load('documents');
            $existingDocuments = $employee->documents->keyBy('type');
        }

        return view('hr.my-information.documents', compact('user', 'employee', 'existingDocuments'));
    }

    public function saveMyDocuments(Request $request)
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            return redirect()->route('hr.my-information.documents')
                ->with('error', 'No employee record was found for your account.');
        }

        for ($i = 1; $i <= 36; $i++) {
            if ($request->hasFile("document_{$i}")) {
                $file = $request->file("document_{$i}");
                $path = $file->store("documents/{$employee->id}", 'public');

                $existing = $employee->documents()->where('type', "document_{$i}")->first();

                if ($existing) {
                    $existing->update([
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                    ]);
                } else {
                    $employee->documents()->create([
                        'id' => Str::uuid()->toString(),
                        'type' => "document_{$i}",
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'description' => null,
                    ]);
                }
            }
        }

        return redirect()->route('hr.my-information.documents')
            ->with('success', 'Documents uploaded successfully.');
    }

    /**
     * YTD-INFO tab.
     */
    public function myYtdInfo()
    {
        $user = Auth::user();
        $employee = $user->employee;

        return view('hr.my-information.ytd-info', compact('user', 'employee'));
    }

    /**
     * Bio-ZK tab.
     */
    public function myBioZk()
    {
        $user = Auth::user();
        $employee = $user->employee?->load('department');

        return view('hr.my-information.bio-zk', compact('user', 'employee'));
    }
}