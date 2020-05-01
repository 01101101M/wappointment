<?php

namespace Wappointment\Listeners;

use Wappointment\Models\Reminder;

class AppointmentReminderListener extends AbstractJobAppointmentListener
{
    use IsReminder;

    protected $jobClass = '\Wappointment\Jobs\AppointmentEmailReminder';
    protected $is_reminder = true;
    protected function addToJobs($event)
    {
        $params = [
            'appointment' => $event->getAppointment(),
            'client' => $event->getClient(),
        ];
        foreach ($event->getReminders() as $reminder) {
            if ($reminder->type == Reminder::getType('email') && $reminder->event == Reminder::APPOINTMENT_STARTS) {
                $this->recordReminder($reminder, $event, $params);
            }
        }
    }
}
