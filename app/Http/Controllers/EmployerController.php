<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\JobCategory;
use App\Models\User;
use App\Models\VisaCategory;
use App\Notifications\NewContactMessageNotification;
use App\Support\SpamGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

/**
 * /for-employers — the B2B page for Japanese companies. Inquiries are saved
 * as contact messages (subject prefixed with the company name) so they show
 * up in Admin → Contact Messages and notify the admins like the contact form.
 */
class EmployerController extends Controller
{
    public function show(): View
    {
        return view('pages.employers', [
            'categories' => JobCategory::orderBy('sort_order')->get(),
            'visas'      => VisaCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $thanks = __('Thank you! Our team will contact you within 1–2 business days.');

        if (SpamGuard::isBot($request)) {
            return back()->with('status', $thanks)->withFragment('inquiry');
        }

        $data = $request->validate([
            'company'  => ['required', 'string', 'max:160'],
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'string', 'email:rfc', 'max:191'],
            'phone'    => ['nullable', 'string', 'max:40'],
            'location' => ['nullable', 'string', 'max:120'],
            'field'    => ['nullable', 'string', 'max:120'],
            'visa'     => ['nullable', 'string', 'max:120'],
            'workers'  => ['nullable', 'integer', 'min:1', 'max:999'],
            'start'    => ['nullable', 'string', 'max:120'],
            'message'  => ['nullable', 'string', 'max:5000'],
        ]);

        $lines = collect([
            __('Company') => $data['company'],
            __('Location') => $data['location'] ?? null,
            __('Field') => $data['field'] ?? null,
            __('Visa route') => $data['visa'] ?? null,
            __('Workers needed') => $data['workers'] ?? null,
            __('Preferred start') => $data['start'] ?? null,
        ])->filter()->map(fn ($v, $k) => "{$k}: {$v}")->implode("\n");

        $contact = ContactMessage::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => '['.__('Worker request').'] '.$data['company'],
            'message'    => trim($lines."\n\n".($data['message'] ?? '')),
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        $admins = User::role(['superadmin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewContactMessageNotification($contact));
        }

        return back()->with('status', $thanks)->withFragment('inquiry');
    }
}
