<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'email'   => ['required', 'string', 'email:rfc', 'max:191'],
            'phone'   => ['nullable', 'string', 'max:32'],
            'subject' => ['required', 'string', 'max:191'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $contact = ContactMessage::create([
            ...$data,
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        $admins = User::role(['superadmin', 'admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewContactMessageNotification($contact));
        }

        return back()
            ->with('status', __('Thanks! Your message has been sent — we will get back to you within 24 hours.'))
            ->withFragment('contact-form');
    }
}
