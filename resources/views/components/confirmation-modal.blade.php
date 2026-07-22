<div id="app-confirmation-modal"
     class="fixed inset-0 z-[110] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="app-confirmation-title">
    <button type="button" class="absolute inset-0 h-full w-full cursor-default bg-black/60" aria-label="Close confirmation" onclick="closeAppConfirmationModal()"></button>
    <div class="relative w-full max-w-md rounded-xl border-2 border-gray-800 bg-white p-6 text-black shadow-2xl">
        <div class="flex items-start gap-4">
            <div id="app-confirmation-icon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-blue-700 bg-blue-100 text-blue-800">
                <i class="fas fa-question"></i>
            </div>
            <div>
                <h2 id="app-confirmation-title" class="text-lg font-bold text-black"></h2>
                <p id="app-confirmation-message" class="mt-2 text-sm leading-6 text-gray-700"></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" onclick="closeAppConfirmationModal()" class="rounded-lg border-2 border-gray-500 bg-white px-4 py-2 font-semibold text-black hover:bg-gray-100">Cancel</button>
            <button id="app-confirmation-submit" type="button" class="rounded-lg border-2 border-blue-800 bg-blue-500 px-4 py-2 font-bold text-black hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600"></button>
        </div>
    </div>
</div>

<script>
let appConfirmationFormId = null;

function openAppConfirmationModal(formId, title, message, confirmLabel = 'Confirm', tone = 'blue') {
    appConfirmationFormId = formId;
    document.getElementById('app-confirmation-title').textContent = title;
    document.getElementById('app-confirmation-message').textContent = message;

    const button = document.getElementById('app-confirmation-submit');
    button.disabled = false;
    button.textContent = confirmLabel;
    button.className = 'rounded-lg border-2 px-4 py-2 font-bold text-black focus:outline-none focus:ring-2 ' +
        (tone === 'red'
            ? 'border-red-800 bg-red-500 hover:bg-red-600 focus:ring-red-600'
            : tone === 'amber'
                ? 'border-amber-800 bg-amber-400 hover:bg-amber-500 focus:ring-amber-600'
                : tone === 'green'
                    ? 'border-green-800 bg-green-500 hover:bg-green-600 focus:ring-green-600'
                    : 'border-blue-800 bg-blue-500 hover:bg-blue-600 focus:ring-blue-600');

    const modal = document.getElementById('app-confirmation-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    button.focus();
}

function closeAppConfirmationModal() {
    const modal = document.getElementById('app-confirmation-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    appConfirmationFormId = null;
}

document.getElementById('app-confirmation-submit').addEventListener('click', function () {
    const form = document.getElementById(appConfirmationFormId);
    if (!form) return;
    if (!form.reportValidity()) return;

    this.disabled = true;
    this.classList.add('cursor-not-allowed', 'opacity-70');
    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing…';
    form.submit();
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeAppConfirmationModal();
});
</script>
