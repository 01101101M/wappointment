<?php

namespace Wappointment\Jobs;

use Wappointment\Services\Settings;
use Wappointment\Services\Queue;

class AdminEmailWeeklySummary extends AbstractEmailJob
{
    const EMAIL = '\\Wappointment\\Messages\\AdminWeeklySummaryEmail';

    public function handle()
    {
        $emailObject = $this->generateEmail();

        $result = $this->mailer
            ->to(Settings::get('email_notifications'))
            ->send($emailObject);

        if (!$result) {
            throw new \WappointmentException('Error while sending email', 1);
        } else {
            //insert a new job for tomorrow
            Queue::queueWeeklyJob();
        }
    }
}
