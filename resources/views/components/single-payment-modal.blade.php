<div id="single-payment-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="single-payment-modal-title">
    <button type="button" class="absolute inset-0 h-full w-full bg-black/60 cursor-default" aria-label="Close payment confirmation" onclick="closeSinglePaymentModal()"></button>

    <div class="relative w-full max-w-md rounded-xl border-2 border-gray-800 bg-white p-6 text-black shadow-2xl">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-green-700 bg-green-100 text-green-800">
                <i class="fas fa-credit-card"></i>
            </div>
            <div>
                <h2 id="single-payment-modal-title" class="text-lg font-bold text-black">Confirm employee payment</h2>
                <p id="single-payment-employee" class="mt-2 text-sm font-semibold text-gray-900"></p>
                <p class="mt-3 text-sm text-gray-700">Net amount to mark as paid:</p>
                <p id="single-payment-amount" class="mt-1 text-2xl font-bold text-green-800"></p>
                <p class="mt-3 text-xs leading-5 text-gray-600">This records the payment transaction and changes only this employee’s payroll status from Approved to Paid.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeSinglePaymentModal()" class="rounded-lg border-2 border-gray-500 bg-white px-4 py-2 font-semibold text-black hover:bg-gray-100">Cancel</button>
            <button id="single-payment-confirm" type="button" class="rounded-lg border-2 border-green-800 bg-green-500 px-4 py-2 font-bold text-black hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-600">
                <i class="fas fa-credit-card mr-2"></i>Confirm Payment
            </button>
        </div>
    </div>
</div>

<script>
let singlePaymentFormId = null;

function openSinglePaymentModal(formId, employeeName, amount) {
    singlePaymentFormId = formId;
    document.getElementById('single-payment-employee').textContent = employeeName;
    document.getElementById('single-payment-amount').textContent = '₱' + amount;
    const modal = document.getElementById('single-payment-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('single-payment-confirm').focus();
}

function closeSinglePaymentModal() {
    const modal = document.getElementById('single-payment-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    singlePaymentFormId = null;
}

document.getElementById('single-payment-confirm').addEventListener('click', function () {
    if (!singlePaymentFormId) return;
    const form = document.getElementById(singlePaymentFormId);
    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing…';
    form.submit();
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeSinglePaymentModal();
});
</script>
