{{-- Shared payroll engine reference: PayrollGenerationService + PayrollSandboxController --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <button type="button"
        id="computationRefToggle"
        class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition"
        aria-expanded="true"
        aria-controls="computationRefPanel">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                <i class="fas fa-book-open text-sm"></i>
            </span>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Computation Reference &amp; Sandbox Workflow</h3>
                <p class="text-xs text-gray-500">Formulas from <code class="text-[10px] bg-gray-100 px-1 rounded">PayrollGenerationService</code> — same engine used by live payroll and the API.</p>
            </div>
        </div>
        <i id="computationRefChevron" class="fas fa-chevron-down text-gray-400 text-sm transition-transform rotate-180"></i>
    </button>

    <div id="computationRefPanel" class="border-t border-gray-100">
        <div class="p-5 grid grid-cols-1 xl:grid-cols-2 gap-6 text-xs text-gray-700 leading-relaxed">

            {{-- Workflow --}}
            <section>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 mb-3 flex items-center">
                    <i class="fas fa-project-diagram mr-2"></i> Sandbox workflow
                </h4>
                <ol class="space-y-2 list-decimal list-inside marker:font-semibold marker:text-indigo-600">
                    <li><strong>Configure scenario</strong> — employee, date range, attendance days, optional deductions.</li>
                    <li><strong>Build weekday plan</strong> — assigns OB → VL → holiday → absent → regular days across Mon–Fri in range (weekends excluded except rest-day demo).</li>
                    <li><strong>Clear prior sandbox data</strong> — removes test DTR, leave, OB, OT, schedules, and demo adjustments tagged <em>Sandbox simulation adjustment</em>.</li>
                    <li><strong>Seed schedules</strong> — creates <code class="bg-gray-100 px-1 rounded">EmployeeSchedule</code> rows only for planned dates (unplanned weekdays are ignored so partial demos are not treated as absences).</li>
                    <li><strong>Seed attendance &amp; requests</strong> — biometric punches, approved OB/VL, OT requests, night-shift pattern, or Saturday rest-day duty as configured.</li>
                    <li><strong>Optional demo adjustments</strong> — taxable bonus, de minimis allowance, manual deduction via <code class="bg-gray-100 px-1 rounded">PayrollAdjustment</code>.</li>
                    <li><strong>Run live engine</strong> — <code class="bg-gray-100 px-1 rounded">PayrollGenerationService::calculateEmployeePayroll()</code> reads comprehensive attendance (same path as production payroll generation).</li>
                    <li><strong>Return breakdown</strong> — JSON with rates, earnings, deductions, <em>summary_steps</em>, and <em>compliance</em> metadata for the results panel.</li>
                </ol>
                <p class="mt-3 text-[11px] text-gray-500 bg-gray-50 rounded-lg px-3 py-2 border border-gray-100">
                    <strong>API parity:</strong> <code class="bg-gray-100 px-1 rounded">PayrollController</code> endpoints
                    <em>preview</em>, <em>generate</em>, <em>approve</em>, <em>process-payments</em>, and <em>export</em> call the same service.
                    The sandbox skips DB persistence and lets you override divisor, loans, and statutory toggles.
                </p>
            </section>

            {{-- Base rates --}}
            <section>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-indigo-700 mb-3 flex items-center">
                    <i class="fas fa-calculator mr-2"></i> Base rates
                </h4>
                <dl class="space-y-2 font-mono text-[11px] bg-gray-50 rounded-lg p-3 border border-gray-100">
                    <div class="flex flex-wrap gap-x-2">
                        <dt class="font-semibold text-gray-800 min-w-[7rem]">Monthly Rate</dt>
                        <dd>= Employee salary or payroll template <code class="font-sans">monthly_rate</code></dd>
                    </div>
                    <div class="flex flex-wrap gap-x-2">
                        <dt class="font-semibold text-gray-800 min-w-[7rem]">Daily Rate</dt>
                        <dd>= Monthly × 12 ÷ divisor &nbsp;|&nbsp; divisors: 261, 313, 314, 365</dd>
                    </div>
                    <div class="flex flex-wrap gap-x-2">
                        <dt class="font-semibold text-gray-800 min-w-[7rem]">Hourly Rate</dt>
                        <dd>= Daily Rate ÷ 8</dd>
                    </div>
                    <div class="flex flex-wrap gap-x-2">
                        <dt class="font-semibold text-gray-800 min-w-[7rem]">Minute Rate</dt>
                        <dd>= Hourly Rate ÷ 60 &nbsp;(late / undertime penalties)</dd>
                    </div>
                    <div class="flex flex-wrap gap-x-2">
                        <dt class="font-semibold text-gray-800 min-w-[7rem]">Cutoff Basic</dt>
                        <dd>= Monthly Rate &nbsp;if period ≥ 25 days</dd>
                    </div>
                    <div class="flex flex-wrap gap-x-2 pl-4 text-gray-600">
                        <dt class="min-w-[6rem]"></dt>
                        <dd>= Monthly ÷ 2 &nbsp;if semi-monthly cutoff (&lt; 25 days)</dd>
                    </div>
                </dl>
                <p class="mt-2 text-[10px] text-gray-500">Paid leave &amp; OB days are covered by the fixed cutoff basic — not added again on top.</p>
            </section>

            {{-- Earnings --}}
            <section>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-green-700 mb-3 flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i> Earnings &amp; premiums
                </h4>
                <table class="w-full text-[11px] border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-gray-500">
                            <th class="py-1.5 pr-2 font-semibold">Component</th>
                            <th class="py-1.5 font-semibold">Formula</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr><td class="py-1.5 pr-2 font-medium">Regular OT</td><td class="py-1.5 font-mono">OT hrs × Hourly × multiplier (default 1.25)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">LH OT</td><td class="py-1.5 font-mono">OT hrs × Hourly × 2.60 (200% × 130%)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">SH OT</td><td class="py-1.5 font-mono">OT hrs × Hourly × 1.30</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Night differential</td><td class="py-1.5 font-mono">NSD hrs × Hourly × 10% &nbsp;(10 PM–6 AM)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Holiday premium</td><td class="py-1.5 font-mono">Daily × 30% per regular/special holiday day worked</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Rest day duty</td><td class="py-1.5 font-mono">Worked hrs × Hourly × 1.30 &nbsp;(Sat rest day)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Adjustments</td><td class="py-1.5">Active <code class="bg-gray-100 px-1 rounded">PayrollAdjustment</code> earnings (bonus, allowance, manual)</td></tr>
                    </tbody>
                </table>
                <p class="mt-2 font-mono text-[11px] bg-green-50 text-green-900 rounded-lg px-3 py-2 border border-green-100">
                    Gross Income = Basic + OT + NSD + Holiday + Rest Day + Allowances + Bonuses
                </p>
            </section>

            {{-- Deductions --}}
            <section>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-red-700 mb-3 flex items-center">
                    <i class="fas fa-minus-circle mr-2"></i> Deductions &amp; net pay
                </h4>
                <table class="w-full text-[11px] border-collapse mb-3">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-gray-500">
                            <th class="py-1.5 pr-2 font-semibold">Component</th>
                            <th class="py-1.5 font-semibold">Formula</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr><td class="py-1.5 pr-2 font-medium">Late / undertime</td><td class="py-1.5 font-mono">Minutes × (Hourly ÷ 60)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Absence</td><td class="py-1.5 font-mono">Daily × absent working days (no punch)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Unpaid leave</td><td class="py-1.5 font-mono">Daily × unpaid leave days</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">SSS (2026)</td><td class="py-1.5 font-mono">MSC × 5% &nbsp;(MSC ₱5k–₱35k, ÷2 semi-monthly)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">PhilHealth</td><td class="py-1.5 font-mono">Base × 2.5% &nbsp;(base ₱10k–₱100k, ÷2 semi-monthly)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Pag-IBIG</td><td class="py-1.5 font-mono">min(Salary, ₱10k) × 1% or 2% &nbsp;(÷2 semi-monthly)</td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Withholding tax</td><td class="py-1.5">TRAIN brackets via <code class="bg-gray-100 px-1 rounded">TaxCalculationService</code></td></tr>
                        <tr><td class="py-1.5 pr-2 font-medium">Loans</td><td class="py-1.5">Sandbox override or active loan amortization (÷2 semi-monthly)</td></tr>
                    </tbody>
                </table>
                <ol class="space-y-1.5 list-decimal list-inside text-[11px] text-gray-600">
                    <li>Attendance penalties (late, UT, absent, unpaid leave) — capped so they cannot exceed cutoff basic.</li>
                    <li>Taxable gross = remaining pay − non-taxable (de minimis) earnings.</li>
                    <li>Withholding tax — capped to remaining pay.</li>
                    <li>SSS → PhilHealth → Pag-IBIG → adjustments → loans — each capped to remaining pay.</li>
                    <li><strong class="text-gray-800">Net Pay</strong> = remaining balance (never negative).</li>
                </ol>
                <p class="mt-2 text-[10px] text-gray-500">Late grace: 10 minutes — punches within grace are not penalized.</p>
            </section>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('computationRefToggle');
    var panel = document.getElementById('computationRefPanel');
    var chevron = document.getElementById('computationRefChevron');
    if (!toggle || !panel) return;

    toggle.addEventListener('click', function () {
        var open = panel.classList.toggle('hidden') === false;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (chevron) {
            chevron.classList.toggle('rotate-180', open);
        }
    });
});
</script>
