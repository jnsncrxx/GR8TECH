<!-- Global Overtime Prompt Modal on Clock Out -->
<div id="overtime-prompt-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="z-index: 9999;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" onclick="closeOvertimePromptModal()" style="background-color: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px);"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal Card -->
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-slate-700" style="background-color: #ffffff; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <!-- Modal Header -->
            <div class="px-6 py-4 flex items-center justify-between" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: #ffffff;">
                <div class="flex items-center space-x-3" style="display: flex; align-items: center; gap: 12px;">
                    <div style="background-color: rgba(255, 255, 255, 0.2); padding: 8px 12px; border-radius: 8px; color: #ffffff;">
                        <i class="fas fa-user-clock text-xl" style="font-size: 20px; color: #ffffff;"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold" style="margin: 0; font-size: 18px; font-weight: 700; color: #ffffff;">Flexible Overtime Request</h3>
                        <p id="ot-prompt-date-subtitle" style="margin: 0; font-size: 12px; opacity: 0.9; color: #ffffff;"></p>
                    </div>
                </div>
                <button type="button" onclick="closeOvertimePromptModal()" style="background: transparent; border: none; color: #ffffff; opacity: 0.9; cursor: pointer; font-size: 18px;" class="hover:opacity-100 transition-opacity">
                    <i class="fas fa-times" style="color: #ffffff;"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 space-y-4" style="padding: 24px;">
                <div style="background-color: #fff7ed; border: 1px solid #ffedd5; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; text-align: center;">
                    <p style="margin: 0; font-size: 13px; font-weight: 600; color: #c2410c;">
                        <i class="fas fa-edit mr-1" style="margin-right: 6px;"></i>Feel free to select your preferred Overtime Start & End Time:
                    </p>
                </div>

                <!-- Flexible Time Selection Inputs -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                            <i class="fas fa-clock text-orange-500" style="margin-right: 4px; color: #ea580c;"></i>OT Start Time
                        </label>
                        <input type="time" id="ot-prompt-start-time" onchange="recalculateModalOtHours()" style="width: 100%; padding: 10px 14px; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 15px; font-weight: 700; color: #0f172a; background-color: #ffffff; cursor: pointer; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);" class="focus:border-orange-500 hover:border-slate-400">
                    </div>
                    <div>
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                            <i class="fas fa-clock text-orange-500" style="margin-right: 4px; color: #ea580c;"></i>OT End Time
                        </label>
                        <input type="time" id="ot-prompt-end-time" onchange="recalculateModalOtHours()" style="width: 100%; padding: 10px 14px; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 15px; font-weight: 700; color: #0f172a; background-color: #ffffff; cursor: pointer; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);" class="focus:border-orange-500 hover:border-slate-400">
                    </div>
                </div>

                <!-- Overtime Hours Input & Quick Presets -->
                <div style="margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <label style="font-size: 12px; font-weight: 700; color: #1e293b;">
                            <i class="fas fa-stopwatch text-orange-500" style="margin-right: 4px; color: #ea580c;"></i>Requested OT Hours
                        </label>
                        <span style="font-size: 11px; color: #64748b; font-weight: 500;">(Auto-calculated or type custom)</span>
                    </div>
                    <input type="number" step="0.25" min="0.1" id="ot-prompt-hours-input" style="width: 100%; padding: 10px 14px; border: 2px solid #fdba74; border-radius: 10px; font-size: 16px; font-weight: 800; color: #ea580c; background-color: #fff7ed; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                </div>

                <!-- Reason Input -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                        <i class="fas fa-comment-alt text-orange-500" style="margin-right: 4px; color: #ea580c;"></i>Reason / Purpose
                    </label>
                    <input type="text" id="ot-prompt-reason" placeholder="State reason for overtime..." style="width: 100%; padding: 10px 14px; border: 2px solid #cbd5e1; border-radius: 10px; font-size: 14px; color: #1e293b; background-color: #ffffff;" class="focus:border-orange-500">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 flex flex-col sm:flex-row gap-3 justify-end border-t border-gray-100" style="background-color: #f8fafc; padding: 16px 24px; display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid #f1f5f9;">
                <button type="button" id="ot-prompt-maybe-later" onclick="closeOvertimePromptModal()" style="padding: 10px 18px; background-color: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-weight: 600; font-size: 14px; border-radius: 8px; cursor: pointer; transition: all 0.2s;" class="hover:bg-slate-100">
                    <i class="fas fa-clock mr-2" style="margin-right: 6px; color: #94a3b8;"></i>Maybe Later
                </button>
                <button type="button" id="ot-prompt-request-now" onclick="submitQuickOvertimeFromModal()" style="padding: 10px 20px; background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); border: none; color: #ffffff !important; font-weight: 700; font-size: 14px; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(234, 88, 12, 0.3); transition: all 0.2s;" class="hover:opacity-95">
                    <i class="fas fa-paper-plane mr-2" style="margin-right: 6px; color: #ffffff;"></i>Submit OT Request
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentOtReminderGlobal = null;
let otModalReloadOnClose = false;

function showOvertimePromptModal(reminder, shouldReloadOnClose = true) {
    currentOtReminderGlobal = reminder;
    otModalReloadOnClose = shouldReloadOnClose;
    const modal = document.getElementById('overtime-prompt-modal');
    
    const subtitleEl = document.getElementById('ot-prompt-date-subtitle');
    const startTimeInput = document.getElementById('ot-prompt-start-time');
    const endTimeInput = document.getElementById('ot-prompt-end-time');
    const hoursInput = document.getElementById('ot-prompt-hours-input');
    const reasonInput = document.getElementById('ot-prompt-reason');

    if (subtitleEl) subtitleEl.textContent = `${reminder.date_formatted || 'Today'}`;
    if (startTimeInput) startTimeInput.value = reminder.start_time || '17:00';
    if (endTimeInput) endTimeInput.value = reminder.end_time || '18:30';
    if (hoursInput) hoursInput.value = parseFloat(reminder.extra_hours || 1.0).toFixed(2);
    if (reasonInput) reasonInput.value = 'Auto-detected rendered overtime after clock out';

    if (modal) modal.classList.remove('hidden');
}

function recalculateModalOtHours() {
    const startVal = document.getElementById('ot-prompt-start-time')?.value;
    const endVal = document.getElementById('ot-prompt-end-time')?.value;
    if (startVal && endVal) {
        const [sH, sM] = startVal.split(':').map(Number);
        const [eH, eM] = endVal.split(':').map(Number);
        let startMin = sH * 60 + sM;
        let endMin = eH * 60 + eM;
        if (endMin < startMin) endMin += 24 * 60; // Overnight
        const diffHours = (endMin - startMin) / 60;
        const hoursInput = document.getElementById('ot-prompt-hours-input');
        if (hoursInput && diffHours > 0) {
            hoursInput.value = diffHours.toFixed(2);
        }
    }
}

function closeOvertimePromptModal() {
    const modal = document.getElementById('overtime-prompt-modal');
    if (modal) modal.classList.add('hidden');
    if (otModalReloadOnClose) {
        window.location.reload();
    }
}

async function submitQuickOvertimeFromModal() {
    if (!currentOtReminderGlobal) return;

    const btn = document.getElementById('ot-prompt-request-now');
    const originalContent = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
    }

    try {
        const selectedStartTime = document.getElementById('ot-prompt-start-time')?.value || currentOtReminderGlobal.start_time;
        const selectedEndTime = document.getElementById('ot-prompt-end-time')?.value || currentOtReminderGlobal.end_time;
        const selectedHours = parseFloat(document.getElementById('ot-prompt-hours-input')?.value || currentOtReminderGlobal.extra_hours);
        const selectedReason = document.getElementById('ot-prompt-reason')?.value || 'Auto-detected rendered overtime';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const response = await fetch('{{ route("attendance.overtime.quick-submit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                reminder_id: currentOtReminderGlobal.id,
                date: currentOtReminderGlobal.date,
                extra_hours: selectedHours,
                start_time: selectedStartTime,
                end_time: selectedEndTime,
                reason: selectedReason
            })
        });

        const data = await response.json();
        if (response.ok && data.success) {
            if (typeof showSuccess === 'function') {
                showSuccess(data.message || 'Overtime request submitted successfully!');
            } else if (typeof showSidebarMessage === 'function') {
                showSidebarMessage(data.message || 'Overtime request submitted successfully!', 'success');
            } else {
                alert(data.message || 'Overtime request submitted successfully!');
            }
            closeOvertimePromptModal();
        } else {
            const errorMsg = data.error || 'Failed to submit overtime request';
            if (typeof showError === 'function') {
                showError(errorMsg);
            } else if (typeof showSidebarMessage === 'function') {
                showSidebarMessage(errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
        }
    } catch (e) {
        console.error('Error submitting quick overtime:', e);
        if (typeof showError === 'function') {
            showError('Failed to submit overtime request');
        } else if (typeof showSidebarMessage === 'function') {
            showSidebarMessage('Failed to submit overtime request', 'error');
        }
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    }
}
</script>
