<?php

namespace App\Http\Controllers\Developer;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\PayrollTemplate;
use Illuminate\Database\QueryException;

class RecycleBinController extends Controller
{
    /**
     * Registry of modules that support soft deletion, keyed by the route
     * segment used to identify them. Only models with a `deleted_at`
     * column belong here.
     */
    protected function modules(): array
    {
        return [
            'accounts' => [
                'label' => 'Accounts',
                'module' => 'Account',
                'model' => Account::class,
                'icon' => 'fa-user-cog',
                'with' => ['employee'],
                'title' => fn (Account $a) => $a->employee?->full_name ?? $a->email,
                'subtitle' => fn (Account $a) => $a->email . ' · ' . ucfirst($a->role),
            ],
            'payroll-templates' => [
                'label' => 'Payroll Templates',
                'module' => 'Payroll Template',
                'model' => PayrollTemplate::class,
                'icon' => 'fa-file-invoice-dollar',
                'with' => ['company'],
                'title' => fn (PayrollTemplate $t) => $t->name,
                'subtitle' => fn (PayrollTemplate $t) => $t->company?->name ?? 'No company',
            ],
        ];
    }

    protected function resolveModule(string $module): array
    {
        $modules = $this->modules();

        abort_unless(isset($modules[$module]), 404);

        return $modules[$module];
    }

    /**
     * Only admins may restore/permanently delete trashed admin accounts.
     */
    protected function assertCanManageRecord(string $module, $record): void
    {
        if ($module === 'accounts' && $record->role === 'admin' && auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can manage admin accounts.');
        }
    }

    public function index()
    {
        $groups = [];

        foreach ($this->modules() as $key => $config) {
            $records = $config['model']::onlyTrashed()
                ->with($config['with'])
                ->orderByDesc('deleted_at')
                ->get();

            $groups[$key] = [
                'label' => $config['label'],
                'icon' => $config['icon'],
                'records' => $records->map(fn ($record) => [
                    'id' => $record->getKey(),
                    'title' => $config['title']($record),
                    'subtitle' => $config['subtitle']($record),
                    'deleted_at' => $record->deleted_at,
                ]),
            ];
        }

        $user = auth()->user();
        $activeRoute = 'developer.recycle-bin.index';

        return view('developer.recycle-bin.index', compact('groups', 'user', 'activeRoute'));
    }

    public function restore(string $module, string $id)
    {
        $config = $this->resolveModule($module);

        $record = $config['model']::onlyTrashed()->findOrFail($id);

        $this->assertCanManageRecord($module, $record);

        $record->restore();

        ActivityLogger::log('restore', $config['module'], "Restored {$config['module']} \"{$config['title']($record)}\" from the recycle bin.");

        return redirect()->route('developer.recycle-bin.index')->with('success', "{$config['module']} restored successfully.");
    }

    public function forceDelete(string $module, string $id)
    {
        $config = $this->resolveModule($module);

        $record = $config['model']::onlyTrashed()->findOrFail($id);

        $this->assertCanManageRecord($module, $record);

        $title = $config['title']($record);

        try {
            $record->forceDelete();
        } catch (QueryException $e) {
            return redirect()->route('developer.recycle-bin.index')
                ->with('error', "Cannot permanently delete \"{$title}\" because it is still referenced by other records.");
        }

        ActivityLogger::log('permanent_delete', $config['module'], "Permanently deleted {$config['module']} \"{$title}\".");

        return redirect()->route('developer.recycle-bin.index')->with('success', "{$config['module']} permanently deleted.");
    }
}
