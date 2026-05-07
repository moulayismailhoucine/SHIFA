@extends('layouts.app')

@section('title', 'Contact Message - ' . $contactMessage->subject)
@section('page-title', 'Contact Message Details')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">
                    <i class="fas fa-envelope-open-text" style="color: var(--accent); margin-right: 8px;"></i>
                    Message Details
                </h3>
                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('admin.contact-messages.index') }}" class="btn btn-outline-secondary" style="padding: 8px 16px;">
                        <i class="fas fa-arrow-left"></i> Back to Messages
                    </a>
                    @if($contactMessage->is_read)
                        <form method="POST" action="{{ route('admin.contact-messages.mark-unread', $contactMessage) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning" style="padding: 8px 16px;">
                                <i class="fas fa-envelope"></i> Mark as Unread
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.contact-messages.mark-read', $contactMessage) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success" style="padding: 8px 16px;">
                                <i class="fas fa-envelope-open"></i> Mark as Read
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 8px 16px;"
                                onclick="return confirm('Are you sure you want to delete this message?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body" style="padding: 30px;">
            @if(session('success'))
                <div style="background: #f0fdf4; color: #166534; padding: 16px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-check-circle" style="font-size: 20px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Message Header -->
            <div style="background: var(--bg); padding: 24px; border-radius: 12px; margin-bottom: 24px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Status</div>
                        <div>
                            @if(!$contactMessage->is_read)
                                <span style="background: var(--accent); color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-circle" style="font-size: 8px; margin-right: 6px;"></i> UNREAD
                                </span>
                            @else
                                <span style="background: var(--success); color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-check-circle" style="font-size: 12px; margin-right: 6px;"></i> READ
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Date & Time</div>
                        <div style="font-weight: 600;">{{ $contactMessage->created_at->format('M j, Y \a\t H:i') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">User Role</div>
                        <div>
                            <span style="background: var(--{{ $contactMessage->role_color }}); color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                {{ $contactMessage->role_label }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sender Information -->
            <div style="margin-bottom: 24px;">
                <h4 style="margin-bottom: 16px; color: var(--text); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-user" style="color: var(--accent);"></i>
                    Sender Information
                </h4>
                <div style="background: var(--bg); padding: 20px; border-radius: 12px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Full Name</div>
                        <div style="font-weight: 600; font-size: 16px;">
                            {{ $contactMessage->name }}
                            @if($contactMessage->user_id)
                                <i class="fas fa-user-check" style="color: var(--success); margin-left: 8px;" title="Registered User"></i>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Email Address</div>
                        <div style="font-weight: 600; font-size: 16px;">
                            <a href="mailto:{{ $contactMessage->email }}" style="color: var(--primary); text-decoration: none;">
                                {{ $contactMessage->email }}
                            </a>
                        </div>
                    </div>
                    @if($contactMessage->user_id && $contactMessage->user)
                        <div>
                            <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">User Account</div>
                            <div style="font-weight: 600; font-size: 16px;">
                                ID: #{{ $contactMessage->user_id }}
                                @if($contactMessage->user->username)
                                    ({{ $contactMessage->user->username }})
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Message Content -->
            <div>
                <h4 style="margin-bottom: 16px; color: var(--text); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-envelope" style="color: var(--accent);"></i>
                    Message Subject
                </h4>
                <div style="background: var(--bg); padding: 16px 20px; border-radius: 12px; margin-bottom: 20px;">
                    <div style="font-weight: 600; font-size: 18px; color: var(--text);">
                        {{ $contactMessage->subject }}
                    </div>
                </div>

                <h4 style="margin-bottom: 16px; color: var(--text); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-comment-dots" style="color: var(--accent);"></i>
                    Message Content
                </h4>
                <div style="background: white; border: 1px solid var(--border); padding: 24px; border-radius: 12px; line-height: 1.6; white-space: pre-wrap;">
                    {{ $contactMessage->message }}
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); display: flex; gap: 12px;">
                <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ urlencode($contactMessage->subject) }}" 
                   class="btn btn-primary" style="padding: 10px 20px;">
                    <i class="fas fa-reply"></i> Reply via Email
                </a>
                @if($contactMessage->user_id)
                    <a href="#" class="btn btn-outline-primary" style="padding: 10px 20px;" disabled>
                        <i class="fas fa-user"></i> View User Profile
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
