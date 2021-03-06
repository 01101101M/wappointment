<?php

namespace Wappointment\Managers;

/**
 * Singleton to replace specific core classes with addons classes
 */
class Central
{
    protected $services = [
        'Service' => [
            'class' => \Wappointment\Services\Service::class,
            'implements' => \Wappointment\Services\ServiceInterface::class
        ],
        'Client' => [
            'class' => \Wappointment\Models\Client::class,
        ]
    ];

    public static function instance()
    {
        static $manager_instance = null;
        if (is_null($manager_instance)) {
            $manager_instance = new self;
        }
        return $manager_instance;
    }

    public static function get($serviceName)
    {
        return static::instance()->getService($serviceName)['class'];
    }

    public function has($serviceName)
    {
        if (!isset($this->services[$serviceName])) {
            throw new \WappointmentException("Service '" . $serviceName . "' is not registered", 1);
        }
        return true;
    }


    public function replace($serviceName, $class)
    {
        if ($this->has($serviceName)) {
            $this->set($serviceName, $class);
        }
    }

    public function getService($serviceName)
    {
        if ($this->has($serviceName)) {
            return $this->services[$serviceName];
        }
    }

    protected function set($serviceName, $class)
    {
        $service = $this->getService($serviceName);

        if (isset($service['implements']) && !in_array($service['implements'], class_implements($class))) {
            throw new \WappointmentException("Central: " . $serviceName . ' class ' . $class . ' not implementing ' . $service['implements'], 1);
        }
        $this->services[$serviceName]['class'] = $class;
    }
}
