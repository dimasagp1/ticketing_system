<?php

namespace App\Services;

use App\Helpers\SettingsHelper;
use App\Mail\SystemEventMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SystemEmailNotifier
{
    public static function sendToUser(
        ?User $recipient,
        string $subject,
        string $headline,
        string $body,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?string $footerNote = null
    ): void {
        if (!$recipient || !filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        self::sendToAddress(
            $recipient->email,
            $subject,
            $headline,
            $body,
            $actionUrl,
            $actionLabel,
            $recipient->name,
            $footerNote
        );
    }

    public static function sendToAddress(
        ?string $email,
        string $subject,
        string $headline,
        string $body,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        ?string $recipientName = null,
        ?string $footerNote = null
    ): void {
        if (!self::isEnabled()) {
            return;
        }

        if (!is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($email)->send(new SystemEventMail(
                headline: $headline,
                body: $body,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                recipientName: $recipientName,
                footerNote: $footerNote,
                mailSubject: $subject,
            ));
        } catch (\Throwable $e) {
            Log::warning('Failed sending system email notification.', [
                'email' => $email,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function isEnabled(): bool
    {
        return (bool) SettingsHelper::get('email_notifications', true);
    }
}
