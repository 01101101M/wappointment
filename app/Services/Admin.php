<?php

namespace Wappointment\Services;


use Wappointment\Models\Client as MClient;
use Wappointment\Validators\HttpRequest\BookingAdmin;

class Admin
{
    public static function book(BookingAdmin $booking)
    {
        $client_id = $booking->get('clientid');

        if ($client_id > 0) {
            $client = MClient::find($client_id);
        } else {
            $client = MClient::firstOrCreate(
                ['email' => $booking->get('email')],
                [
                    'name' => $booking->get('name'),
                    'options' => [
                        'phone' => $booking->get('phone'),
                        'skype' => $booking->get('skype'),
                        'tz' => $booking->get('timezone'),
                    ]
                ]
            );
        }

        //book with that client
        return self::createAppointment($client, $booking->get('start'), $booking->get('end'), $booking->get('type'));
    }

    private static function createAppointment(MClient $client, $start, $end, $type, $service = false)
    {
        if ($service === false) {
            $service = Settings::get('service');
        }

        //test type is allowed
        if (!in_array($type, $service['type'])) {
            throw new \WappointmentException('Error booking type not allowed', 1);
        }

        $type = (int)call_user_func('Wappointment\Models\Appointment::getType' . ucfirst($type));

        //test that this is bookable
        $hasBeenBooked = Appointment::adminBook($client, $start, $end, $type, $service);
        if (!$hasBeenBooked) {
            throw new \WappointmentException('Error cannot book at this time', 1);
        }
        return $hasBeenBooked;
    }

    public function search($email, $size = 30)
    {
        $clients = MClient::where('email', 'like', $email . '%')->get()->toArray();
        foreach ($clients as &$client) {
            $client['avatar'] = get_avatar_url($client['email'], ['size' => $size]);
        }

        return $clients;
    }
}
