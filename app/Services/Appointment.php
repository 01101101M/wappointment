<?php

namespace Wappointment\Services;

use Wappointment\Models\Appointment as AppointmentModel;
use Wappointment\Models\Client;
use Wappointment\Helpers\Events;
use Wappointment\WP\Helpers as WPHelpers;

class Appointment
{
    public static function delete($id)
    {
        $status = AppointmentModel::destroy($id);
        if ($status) {
            (new Availability())->regenerate();
        }
        return $status;
    }

    public static function create($data)
    {
        return AppointmentModel::create($data);
    }

    public static function confirm($id)
    {
        $appointment = AppointmentModel::with('client')->where('id', $id)->where('status', AppointmentModel::STATUS_AWAITING_CONFIRMATION)->first();
        if (empty($appointment)) {
            throw new \WappointmentException("Can't find appointment", 1);
        } else {
            $result = $appointment->update(['status' => AppointmentModel::STATUS_CONFIRMED]);
            if ($result) {
                Events::dispatch('AppointmentConfirmedEvent', ['appointment' => $appointment, 'client' => $appointment->client]);
            }
            return $result;
        }
    }

    public static function patch($id, $data)
    {
        $result = AppointmentModel::where('id', $id)->update($data);
        if ($result) {
            (new Availability())->regenerate();
        }
        return $result;
    }

    public static function unixToDb($unixTS)
    {
        return \Wappointment\ClassConnect\Carbon::createFromTimestamp($unixTS)->toDateTimeString();
    }

    public static function tryBook(Client $client, $start_at, $end_at, $type, $service)
    {
        if (self::canBook($start_at, $end_at)) {
            return self::processBook($client, $start_at, $end_at, $type, $service);
        }
    }

    public static function adminBook(Client $client, $start_at, $end_at, $type, $service)
    {
        if (self::canBook($start_at, $end_at, true)) {
            return self::processBook($client, $start_at, $end_at, $type, $service, true);
        }
    }

    private static function processBook(Client $client, $start_at, $end_at, $type, $service, $forceConfirmed = false)
    {
        $start_at = self::unixToDb($start_at);
        $end_at = self::unixToDb($end_at);
        $appointmentData = [
            'start_at' => $start_at,
            'end_at' => $end_at,
            'type' => $type,
            'client_id' => $client->id,
            'edit_key' => md5($client->id . $start_at),
            'status' => $forceConfirmed ? AppointmentModel::STATUS_CONFIRMED : self::getDefaultStatus($service)
        ];

        return self::book($appointmentData, $client, $forceConfirmed);
    }

    private static function canBook($start_at, $end_at, $is_admin = false)
    {
        //test that this is not a double booking scenario
        $start_at_str = self::unixToDb($start_at);
        $end_at_str = self::unixToDb($end_at);


        if (AppointmentModel::where('status', '>=', AppointmentModel::STATUS_AWAITING_CONFIRMATION)
            ->where(function ($query) use ($start_at_str, $end_at_str) {
                $query->where(function ($query) use ($start_at_str, $end_at_str) {
                    $query->where('start_at', $start_at_str);
                    $query->where('end_at', $end_at_str);
                });

                /**
                 *     DB:  □□□□□□□□□□□■■■■■■■■■■■■■■■■■■■■■■■■□□□□□□□□□□
                 *     this: □□□□□□□□□□□□□□□□□□■■■■■■■■■■■□□□□□□□□□□□□□□□□
                 *     Db contains this
                 */
                $query->orWhere(function ($query) use ($start_at_str, $end_at_str) {
                    $query->where('start_at', '<=', $start_at_str);
                    $query->where('end_at', '>=', $end_at_str);
                });
                /*
                 *     DB:   □□□□□□□□□□□□□□□□□□■■■■■■■■■■■□□□□□□□□□□□□□□□□
                 *     This: □□□□□□□□□□□■■■■■■■■■■■■■■■■■■■■■■■■□□□□□□□□□□
                 *      this contains DB
                 */
                $query->orWhere(function ($query) use ($start_at_str, $end_at_str) {
                    $query->where('start_at', '>=', $start_at_str);
                    $query->where('end_at', '<=', $end_at_str);
                });
                /*
                 *     DB:   □□□□□□□□□□□■■■■■■■■■■■■■□□□□□□□□□□□□□□□□□□□□□
                 *     This: □□□□□□□□□□□□□□□□□□■■■■■■■■■■■□□□□□□□□□□□□□□□□
                 *     DB intersects this
                 */
                $query->orWhere(function ($query) use ($start_at_str, $end_at_str) {
                    $query->where('start_at', '<', $start_at_str);
                    $query->where('end_at', '>', $start_at_str);
                });
                /*
                 *     DB:   □□□□□□□□□□□□□□□□□□■■■■■■■■■■■□□□□□□□□□□□□□□□□
                 *     This: □□□□□□□□□□□■■■■■■■■■■■■■□□□□□□□□□□□□□□□□□□□□□
                 */
                $query->orWhere(function ($query) use ($start_at_str, $end_at_str) {
                    $query->where('start_at', '<', $end_at_str);
                    $query->where('end_at', '>', $end_at_str);
                });
            })
            ->exists()
        ) {
            throw new \WappointmentException('Slot already booked', 1);
        }
        if ($is_admin === true) return true;
        //staff being booked
        $activeStaff = Settings::get('activeStaffId');
        //test that were in the booking availability
        if ((new Availability())->isAvailable($start_at, $end_at, $activeStaff)) {
            return true;
        }
        throw new \WappointmentException('Slot not available', 1);
    }

    private static function book($data, Client $client, $is_admin = false)
    {
        $appointment = self::create($data);

        if ($appointment->status == AppointmentModel::STATUS_AWAITING_CONFIRMATION) {
            Events::dispatch('AppointmentBookedEvent', ['appointment' => $appointment, 'client' => $client]);
        } else {
            Events::dispatch('AppointmentConfirmedEvent', ['appointment' => $appointment, 'client' => $client]);
        }

        //return $appointment;
        //if availability has not been refreshed for a while we refresh it now otherwise we queue a job for it
        if (!defined('DISABLE_WP_CRON') || $is_admin === true) {
            //when web cron is disabled we need an immediate refresh of availability
            (new Availability())->regenerate();
        } else {
            if ((int) WPHelpers::getStaffOption('since_last_refresh') > 2) {
                (new Availability())->regenerate();
            } else {
                $count_since_last_refresh = (int) WPHelpers::getStaffOption('since_last_refresh', false, 0) + 1;
                WPHelpers::setStaffOption('since_last_refresh', $count_since_last_refresh);
                \Wappointment\Services\Queue::tryPush('Wappointment\Jobs\AvailabilityRegenerate', ['staff_id' => Settings::get('activeStaffId')], 'availability');
            }
            //we immediately spawn a process to trigger availability regenerate in the back
            spawn_cron();
        }


        return $appointment;
    }

    public static function reschedule($edit_key, $start_at)
    {
        if (!(bool) Settings::get('allow_rescheduling')) {
            throw new \WappointmentException('Appointment rescheduling is not allowed', 1);
        }

        $appointment = AppointmentModel::where('edit_key', $edit_key)->first();
        $oldAppointment = $appointment->replicate();
        if (empty($appointment)) {
            throw new \WappointmentException("Can't find appointment", 1);
        }
        if (!$appointment->canStillReschedule()) {
            throw new \WappointmentException("Can't reschedule appointment anymore", 1);
        }
        $result = $appointment->update(
            [
                'start_at' => self::unixToDb($start_at),
                'end_at' => self::unixToDb($start_at + $appointment->getDurationInSec()),
            ]
        );
        if ($result) {
            (new Availability())->regenerate();

            Events::dispatch(
                'AppointmentRescheduledEvent',
                [
                    'appointment' => $appointment,
                    'client' => $appointment->client()->first(),
                    'oldAppointment' => $oldAppointment
                ]
            );
        }
        return $result;
    }

    public static function tryCancel($edit_key)
    {
        if (!(bool) Settings::get('allow_cancellation')) {
            throw new \WappointmentException('Appointment cancellation is not allowed', 1);
        }

        $appointment = AppointmentModel::where('edit_key', $edit_key)
            ->where('status', '>=', AppointmentModel::STATUS_AWAITING_CONFIRMATION)->first();

        if (empty($appointment)) {
            throw new \WappointmentException("Can't find appointment", 1);
        }
        if (!$appointment->canStillCancel()) {
            throw new \WappointmentException("Can't cancel appointment anymore", 1);
        }

        self::cancel($appointment);
    }

    public static function cancel(AppointmentModel $appointment)
    {
        \Wappointment\Models\Log::canceledAppointment($appointment);

        $client = $appointment->client()->first();
        $staff_id_regenerate = $appointment->getStaffId();
        $result = $appointment->destroy($appointment->id);

        if ($result) {
            (new Availability())->regenerate($staff_id_regenerate);
            Events::dispatch('AppointmentCanceledEvent', ['appointment' => $appointment, 'client' => $client]);
        }
        return $result;
    }

    private static function getDefaultStatus($service)
    {
        $default_status = ((int) Settings::get('approval_mode') === 1) ? AppointmentModel::STATUS_CONFIRMED : AppointmentModel::STATUS_AWAITING_CONFIRMATION;

        return apply_filters('wappointment_appointment_default_status', $default_status, $service);
    }
}
