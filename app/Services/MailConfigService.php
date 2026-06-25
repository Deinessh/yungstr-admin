<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class MailConfigService
{
    public function __construct(private SettingService $settings) {}

    public function apply(): void
    {
        $host = $this->settings->get('mail_host');

        if (! $host) {
            return;
        }

        Config::set('mail.default', $this->settings->get('mail_mailer', 'smtp'));
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', (int) $this->settings->get('mail_port', 587));
        Config::set('mail.mailers.smtp.username', $this->settings->get('mail_username'));
        Config::set('mail.mailers.smtp.password', $this->settings->get('mail_password'));
        Config::set('mail.mailers.smtp.encryption', $this->settings->get('mail_encryption', 'tls') ?: null);
        Config::set('mail.from.address', $this->settings->get('mail_from_address', config('mail.from.address')));
        Config::set('mail.from.name', $this->settings->get('mail_from_name', config('mail.from.name')));
    }
}
