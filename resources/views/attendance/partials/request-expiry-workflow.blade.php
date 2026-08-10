@php
    $isOwner = auth()->user()?->employee?->id === $requestRecord->employee_id;
    $isPending = $requestRecord->status === 'pending';
    $canResubmit = $isOwner && method_exists($requestRecord, 'canBeResubmitted') && $requestRecord->canBeResubmitted();
    $isFinalExpiry = method_exists($requestRecord, 'isFinallyExpired') && $requestRecord->isFinallyExpired();
    $showAction = $showAction ?? true;
    $requestLabel = $requestLabel ?? match (class_basename($requestRecord)) {
        'OvertimeRequest' => 'overtime request',
        'LeaveRequest' => 'leave request',
        'OfficialBusinessRequest' => 'Official Business request',
        default => 'request',
    };
@endphp

<div class="mt-1 text-xs text-gray-500 dark:text-gray-300">
    @if($isPending && $requestRecord->expires_at)
        <span class="font-medium {{ (int) ($requestRecord->expiry_attempt ?? 1) === 2 ? 'text-orange-600 dark:text-orange-300' : '' }}">
            {{ (int) ($requestRecord->expiry_attempt ?? 1) === 2 ? 'Final review window' : 'Initial review window' }}
        </span>
        <span>&middot; expires {{ $requestRecord->expires_at->format('M d, Y h:i A') }}</span>
    @elseif($canResubmit)
        <span class="font-medium text-orange-600 dark:text-orange-300">Expired &mdash; one final re-request is available.</span>
    @elseif($requestRecord->status === 'expired' && $isFinalExpiry)
        <span class="font-medium text-red-600 dark:text-red-300">Finally expired &mdash; re-request is no longer available.</span>
    @endif
</div>

@if($showAction && $canResubmit && isset($resubmitRoute))
    <form method="POST" action="{{ $resubmitRoute }}" class="request-resubmit-form mt-2 inline-flex">
        @csrf
        <button type="button"
                data-request-label="{{ $requestLabel }}"
                onclick="openRequestResubmitModal(this.closest('form'), this.dataset.requestLabel)"
                class="inline-flex items-center gap-2 rounded-lg border border-orange-300 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-700 transition hover:bg-orange-100 dark:border-orange-500/60 dark:bg-orange-950/40 dark:text-orange-200 dark:hover:bg-orange-900/60"
                title="Re-request for the final 24-hour review window">
            <i class="fas fa-redo-alt" aria-hidden="true"></i>
            Re-request
        </button>
    </form>
@endif

@once
    <div id="requestResubmitModal"
         class="fixed inset-0 z-[10000] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
         role="dialog"
         aria-modal="true"
         aria-labelledby="requestResubmitModalTitle"
         onclick="if (event.target === this) closeRequestResubmitModal()">
        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-2xl dark:border-slate-700 dark:bg-neutral-800">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full border border-orange-200 bg-orange-50 text-orange-600 dark:border-orange-500/50 dark:bg-orange-950/50 dark:text-orange-300">
                <i class="fas fa-redo-alt text-xl" aria-hidden="true"></i>
            </div>
            <h3 id="requestResubmitModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">
                Confirm Re-request
            </h3>
            <p class="mt-2 w-full whitespace-normal break-words text-sm leading-6 text-gray-600 dark:text-gray-200">
                Re-request this <span id="requestResubmitType" class="whitespace-normal font-semibold">request</span>?
                This starts its final 24-hour review period and cannot be repeated after it expires again.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <button type="button"
                        onclick="closeRequestResubmitModal()"
                        class="inline-flex min-w-28 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-slate-600 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600">
                    Cancel
                </button>
                <button type="button"
                        onclick="confirmRequestResubmit()"
                        class="inline-flex min-w-36 items-center justify-center gap-2 rounded-lg border border-orange-600 bg-orange-600 px-4 py-2 text-sm font-semibold !text-white transition hover:bg-orange-700 hover:!text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:!text-white dark:ring-offset-neutral-800">
                    <i class="fas fa-redo-alt" aria-hidden="true"></i>
                    Confirm Re-request
                </button>
            </div>
        </div>
    </div>

    <script>
        let pendingRequestResubmitForm = null;

        function openRequestResubmitModal(form, requestLabel) {
            pendingRequestResubmitForm = form;
            document.getElementById('requestResubmitType').textContent = requestLabel || 'request';

            const modal = document.getElementById('requestResubmitModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeRequestResubmitModal() {
            const modal = document.getElementById('requestResubmitModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            pendingRequestResubmitForm = null;
        }

        function confirmRequestResubmit() {
            if (!pendingRequestResubmitForm) {
                return;
            }

            const form = pendingRequestResubmitForm;
            pendingRequestResubmitForm = null;
            form.submit();
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeRequestResubmitModal();
            }
        });
    </script>
@endonce
