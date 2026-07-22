<div id="payroll-lock-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="payroll-lock-modal-title">
    <button type="button"
            class="absolute inset-0 w-full h-full bg-black/60 cursor-default"
            aria-label="Close lock confirmation"
            onclick="closePayrollLockModal()"></button>

    <div class="relative w-full max-w-md rounded-xl border-2 border-gray-800 bg-white p-6 text-black shadow-2xl">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-amber-700 bg-amber-100 text-amber-800">
                <i class="fas fa-lock"></i>
            </div>
            <div>
                <h2 id="payroll-lock-modal-title" class="text-lg font-bold text-black">Lock payroll permanently?</h2>
                <p id="payroll-lock-modal-message" class="mt-2 text-sm leading-6 text-gray-800"></p>
                <p class="mt-2 text-sm font-semibold text-black">After locking, the run becomes view/export-only and eligible for payment.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button"
                    onclick="closePayrollLockModal()"
                    class="rounded-lg border-2 border-gray-500 bg-white px-4 py-2 font-semibold text-black hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500">
                Cancel
            </button>
            <button id="payroll-lock-confirm-button"
                    type="button"
                    class="rounded-lg border-2 border-amber-800 bg-amber-400 px-4 py-2 font-bold text-black hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-600">
                <i class="fas fa-lock mr-2"></i>Confirm Lock
            </button>
        </div>
    </div>
</div>

<script>
let payrollLockFormId = null;

function openPayrollLockModal(formId, periodName) {
    payrollLockFormId = formId;
    const modal = document.getElementById('payroll-lock-modal');
    const message = document.getElementById('payroll-lock-modal-message');
    message.textContent = 'You are about to lock “' + periodName + '”. Confirm only after verifying all payroll computations.';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('payroll-lock-confirm-button').focus();
}

function closePayrollLockModal() {
    const modal = document.getElementById('payroll-lock-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    payrollLockFormId = null;
}

document.getElementById('payroll-lock-confirm-button').addEventListener('click', function () {
    if (!payrollLockFormId) return;
    const form = document.getElementById(payrollLockFormId);
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Locking…';
    form.submit();
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closePayrollLockModal();
});
</script>
