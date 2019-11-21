<?php

namespace Wappointment\Validators\HttpRequest;

use Wappointment\Validators\Phone;

class BookingAdmin extends AbstractProcessor
{
    protected function validationMessages()
    {
        return [
            'is_phone' => 'Your phone number is not valid',
            'email' => 'Your email is not valid',
            'skype:regex' => 'Your skype username is not valid',
        ];
    }

    protected function validationRules()
    {
        return [
            'name' => 'present|max:100',
            'email' => 'present|email',
            'type' => 'present|in:physical,phone,skype',
            'phone' => 'required_if:type,phone|is_phone',
            'skype' => 'required_if:type,skype|regex:/^[a-zA-Z][a-zA-Z0-9.\-_]{5,31}$/',
            'start' => 'required|min:' . time(),
            'end' => 'required|min:' . time(),
            'timezone' => '',
            'clientid' => ''
        ];
    }

    protected function addValidators()
    {
        $service = \Wappointment\Services\Service::getObject();
        $this->validator->addValidator('is_phone', new Phone($service->getCountries()));
    }

    public function prepareInputs($inputs): array
    {
        $inputs['start'] = (int) $inputs['start'];
        $inputs['end'] = (int) $inputs['end'];
        return $inputs;
    }
}
