<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage as ContactMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ];

        $validated = $request->validate($rules);

        // Honeypot check — bots fill hidden fields
        if ($request->filled('website') || $request->filled('confirm_email')) {
            return redirect()->back()->with('error', 'Suspicious activity detected.');
        }



        // Get authenticated user info (web session may be set by API login — use it if available)
        $userId   = Auth::id();
        $userRole = Auth::check() ? (Auth::user()->role ?? null) : null;

        // Sanitize and store
        ContactMessage::create([
            'name'      => htmlspecialchars($validated['name'],    ENT_QUOTES, 'UTF-8'),
            'email'     => strtolower(trim($validated['email'])),
            'subject'   => htmlspecialchars($validated['subject'], ENT_QUOTES, 'UTF-8'),
            'message'   => htmlspecialchars($validated['message'], ENT_QUOTES, 'UTF-8'),
            'user_id'   => $userId,
            'user_role' => $userRole,
            'status'    => 'new',
        ]);

        // Try to send email notification (non-blocking)
        try {
            $adminEmail = env('ADMIN_EMAIL', 'admin@shifa.local');
            Mail::to($adminEmail)->send(new ContactMail($validated));
        } catch (\Exception $e) {
            // Email failed — message is still saved in DB
        }

        return redirect()->route('contact.show')
            ->with('success', 'Your message has been sent! We will respond as soon as possible.');
    }
}
