<?php

namespace Wappointment\Jobs;

use Wappointment\Models\Client;
use Wappointment\Models\Appointment;

trait IsAppointmentJob
{
    protected $client = null;
    protected $appointment = null;
    protected $reminder_id = false;

    protected function parseParams($params)
    {

        if (empty($params['client']) || empty($params['appointment'])) {
            throw new \WappointmentException('Missing parameters for the appointment job', 1);
        }

        $this->client = (new Client)->fill($params['client']);
        if (!empty($params['appointment']['options'])) {
            $params['appointment']['options'] = json_encode($params['appointment']['options']);
        }
        $this->appointment = (new Appointment)->newFromBuilder($params['appointment']);

        if (!empty($params['reminder_id'])) {
            $this->reminder_id = $params['reminder_id'];
        }
        $this->params = $params;
    }
}
