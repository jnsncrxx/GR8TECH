<div id="payroll-generation-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="payroll-generation-modal-title">
    <button type="button"
            class="absolute inset-0 h-full w-full cursor-default bg-black/60"
            aria-label="Close payroll generation confirmation"
            onclick="closePayrollGenerationModal()"></button>

    <div class="relative w-full max-w-lg rounded-xl border-2 border-gray-800 bg-white p-6 text-black shadow-2xl">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-green-700 bg-green-100 text-green-800">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 id="payroll-generation-modal-title" class="text-lg font-bold text-black">Generate payroll records?</h2>
                <p class="mt-2 text-sm leading-6 text-gray-700">
                    Confirm the reviewed preview for <strong class="text-black">{{ $generationPeriodName }}</strong>.
                </p>

                <dl class="mt-4 grid grid-cols-1 gap-2 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-gray-500">Employees</dt>
                        <dd class="font-bold text-gray-900">{{ $generationEmployeeCount }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Gross pay</dt>
                        <dd class="font-bold text-gray-900">₱{{ number_format($generationGrossPay, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Net pay</dt>
                        <dd class="font-bold text-gray-900">₱{{ number_format($generationNetPay, 2) }}</dd>
                    </div>
                </dl>

                <div class="mt-4 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm leading-5 text-amber-900">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    This creates employee payroll records and moves the cutoff to Payroll Processing. You can still review the generated run before finalizing and locking it.
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button"
                    onclick="closePayrollGenerationModal()"
                    class="rounded-lg border-2 border-gray-500 bg-white px-4 py-2 font-semibold text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Cancel
            </button>
            <button id="payroll-generation-confirm"
                    type="button"
                    class="rounded-lg border-2 border-green-800 bg-green-500 px-4 py-2 font-bold text-black hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-600">
                <i class="fas fa-check mr-2"></i>Confirm & Generate
            </button>
        </div>
    </div>
</div>

<script>
function openPayrollGenerationModal() {
    const modal = document.getElementById('payroll-generation-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('payroll-generation-confirm').focus();
}

function closePayrollGenerationModal() {
    const modal = document.getElementById('payroll-generation-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('payroll-generation-confirm').addEventListener('click', function () {
    const form = document.getElementById('generate-payroll-form');
    if (!form) return;

    this.disabled = true;
    this.classList.add('cursor-not-allowed', 'opacity-70');
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating…';
    form.submit();
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closePayrollGenerationModal();
});
</script>
