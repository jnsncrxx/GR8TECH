<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpSupportController extends Controller
{
    public function index(Request $request)
    {
        $faqs = [
            [
                'category' => 'Attendance',
                'items' => [
                    [
                        'question' => 'How do I fix a missing time log?',
                        'answer' => 'Open a support ticket with the date, time, and a short explanation so HR can review and correct the entry.',
                    ],
                    [
                        'question' => 'Why is my attendance marked late?',
                        'answer' => 'Late marks usually reflect your first recorded clock-in. If there was a system or device issue, submit a ticket for review.',
                    ],
                ],
            ],
            [
                'category' => 'Leave',
                'items' => [
                    [
                        'question' => 'How do I follow up on a leave request?',
                        'answer' => 'Use the contact HR feature or submit a support ticket with the leave dates and request reference, if available.',
                    ],
                    [
                        'question' => 'Who approves leave requests?',
                        'answer' => 'Leave requests are reviewed by your manager and processed by HR based on company policy and coverage needs.',
                    ],
                ],
            ],
            [
                'category' => 'Payroll',
                'items' => [
                    [
                        'question' => 'What should I do if my payslip looks wrong?',
                        'answer' => 'Send a support ticket with the payslip period and the specific line item you think is incorrect so HR can investigate.',
                    ],
                    [
                        'question' => 'When are payroll concerns reviewed?',
                        'answer' => 'Payroll questions are reviewed during the current payroll cycle or the next available HR support window.',
                    ],
                ],
            ],
        ];

        $troubleshooting = [
            [
                'title' => 'Login or access issue',
                'steps' => [
                    'Confirm you are using the correct company account email.',
                    'Clear your browser cache and try again.',
                    'If the issue continues, submit a support ticket with a screenshot.',
                ],
            ],
            [
                'title' => 'Attendance device issue',
                'steps' => [
                    'Check that the device is connected and online.',
                    'Try clocking in again after a short wait.',
                    'If the log still does not appear, notify HR with the exact time of the attempt.',
                ],
            ],
        ];

        return view('help-support', [
            'user' => Auth::user(),
            'faqs' => $faqs,
            'troubleshooting' => $troubleshooting,
        ]);
    }

    public function storeTicket(Request $request)
    {
        return response()->json(['message' => 'Store ticket not yet implemented'], 501);
    }
}
