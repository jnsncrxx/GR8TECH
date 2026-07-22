<div id="selected-payment-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="selected-payment-modal-title">
    <button type="button" class="absolute inset-0 h-full w-full bg-black/60 cursor-default" aria-label="Close payment confirmation" onclick="closeSelectedPaymentModal()"></button>
    <div class="relative w-full max-w-md rounded-xl border-2 border-gray-800 bg-white p-6 text-black shadow-2xl">
        <h2 id="selected-payment-modal-title" class="text-lg font-bold text-black">Process selected payments?</h2>
        <p class="mt-3 text-sm text-gray-700">Only the checked Approved employees will be paid.</p>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-lg border border-gray-300 bg-gray-50 p-3">
                <div class="text-xs text-gray-600">Employees</div>
                <div id="selected-payment-modal-count" class="text-xl font-bold text-black">0</div>
            </div>
            <div class="rounded-lg border border-gray-300 bg-gray-50 p-3">
                <div class="text-xs text-gray-600">Total net</div>
                <div id="selected-payment-modal-net" class="text-xl font-bold text-green-800">₱0.00</div>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeSelectedPaymentModal()" class="rounded-lg border-2 border-gray-500 bg-white px-4 py-2 font-semibold text-black hover:bg-gray-100">Cancel</button>
            <button id="selected-payment-confirm" type="button" class="rounded-lg border-2 border-green-800 bg-green-500 px-4 py-2 font-bold text-black hover:bg-green-600">
                <i class="fas fa-credit-card mr-2"></i>Confirm Payments
            </button>
        </div>
    </div>
</div>

<script>
let selectedPaymentIds = [];

function openSelectedPaymentModal() {
    const selected = Array.from(document.querySelectorAll('.payroll-checkbox:checked:not(:disabled)'));
    if (selected.length === 0) return;
    selectedPaymentIds = selected.map(checkbox => checkbox.value);
    const total = selected.reduce((sum, checkbox) => sum + Number(checkbox.dataset.netPay || 0), 0);
    document.getElementById('selected-payment-modal-count').textContent = selected.length;
    document.getElementById('selected-payment-modal-net').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const modal = document.getElementById('selected-payment-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSelectedPaymentModal() {
    const modal = document.getElementById('selected-payment-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    selectedPaymentIds = [];
}

document.getElementById('selected-payment-confirm').addEventListener('click', async function () {
    if (selectedPaymentIds.length === 0) return;
    const button = this;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing…';
    try {
        const response = await fetch('{{ route('payrolls.process-selected-payments') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                payroll_ids: selectedPaymentIds,
                start_date: document.getElementById('paymentStartDate').value,
                end_date: document.getElementById('paymentEndDate').value
            })
        });
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || 'Payment processing failed.');
        window.location.reload();
    } catch (error) {
        alert(error.message);
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Confirm Payments';
    }
});
</script>
