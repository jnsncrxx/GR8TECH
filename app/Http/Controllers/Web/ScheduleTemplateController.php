<?php

namespace App\Http\Controllers\Web;

use App\Helpers\CompanyHelper;
use App\Http\Controllers\Controller;
use App\Models\ScheduleTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ScheduleTemplateController extends Controller
{
    public function index(Request $request)
    {
        $currentCompany = CompanyHelper::getCurrentCompany();

        $templates = ScheduleTemplate::query()
            ->when($currentCompany, fn ($query) => $query->forCompany($currentCompany->id))
            ->when(!$currentCompany, fn ($query) => $query->whereNull('company_id'))
            ->orderBy('code')
            ->get();

        return view('attendance.schedule-templates.index', [
            'templates' => $templates,
            'currentCompany' => $currentCompany,
            'user' => Auth::user(),
        ]);
    }

    public function create()
    {
        $currentCompany = CompanyHelper::getCurrentCompany();

        return view('attendance.schedule-templates.create', [
            'currentCompany' => $currentCompany,
            'user' => Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $currentCompany = CompanyHelper::getCurrentCompany();

        $validated = $this->validated($request, $currentCompany?->id);

        ScheduleTemplate::create([
            ...$validated,
            'company_id' => $currentCompany?->id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('schedule-templates.index')
            ->with('success', "Schedule template \"{$validated['code']}\" created successfully.");
    }

    public function edit(ScheduleTemplate $scheduleTemplate)
    {
        return view('attendance.schedule-templates.edit', [
            'template' => $scheduleTemplate,
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request, ScheduleTemplate $scheduleTemplate)
    {
        $validated = $this->validated($request, $scheduleTemplate->company_id, $scheduleTemplate->id);

        $scheduleTemplate->update($validated);

        return redirect()->route('schedule-templates.index')
            ->with('success', "Schedule template \"{$validated['code']}\" updated successfully.");
    }

    public function destroy(ScheduleTemplate $scheduleTemplate)
    {
        $inUseCount = $scheduleTemplate->employeeSchedules()->count();

        if ($inUseCount > 0) {
            return redirect()->route('schedule-templates.index')
                ->with('error', "Cannot delete \"{$scheduleTemplate->code}\" - it's currently assigned to {$inUseCount} schedule(s). Reassign or remove those first.");
        }

        $code = $scheduleTemplate->code;
        $scheduleTemplate->delete();

        return redirect()->route('schedule-templates.index')
            ->with('success', "Schedule template \"{$code}\" deleted successfully.");
    }

    /**
     * Shared validation for store/update. Duplicate codes are only checked
     * within the same company - two different companies can each have their
     * own "A1", but the same company can't have two.
     */
    private function validated(Request $request, ?string $companyId, ?string $ignoreId = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('schedule_templates', 'code')
                    ->where(fn ($query) => $companyId
                        ? $query->where('company_id', $companyId)
                        : $query->whereNull('company_id'))
                    ->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'schedule_type' => ['required', 'in:fixed,flexible'],
            'time_in' => ['required_if:schedule_type,fixed', 'nullable', 'date_format:H:i'],
            'time_out' => ['required_if:schedule_type,fixed', 'nullable', 'date_format:H:i', 'after:time_in'],
            'required_hours' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);
    }
}