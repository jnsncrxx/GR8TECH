@php
    $isOwner = auth()->user()?->employee?->id === $requestRecord->employee_id;
    $isPending = $requestRecord->status === 'pending';
    $canResubmit = $isOwner && method_exists($requestRecord, 'canBeResubmitted') && $requestRecord->canBeResubmitted();
    $isFinalExpiry = method_exists($requestRecord, 'isFinallyExpired') && $requestRecord->isFinallyExpired();
    $showAction = $showAction ?? true;
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
    <form method="POST" action="{{ $resubmitRoute }}" class="mt-2 inline-flex">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg border border-orange-300 bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-700 transition hover:bg-orange-100 dark:border-orange-500/60 dark:bg-orange-950/40 dark:text-orange-200 dark:hover:bg-orange-900/60"
                title="Re-request for the final 24-hour review window">
            <i class="fas fa-redo-alt" aria-hidden="true"></i>
            Re-request
        </button>
    </form>
@endif
