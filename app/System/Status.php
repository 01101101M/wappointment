<?php

namespace Wappointment\System;

use Wappointment\WP\Helpers as WPHelpers;
use Wappointment\Services\Settings;

class Status
{
    public static $version = WAPPOINTMENT_VERSION;
    private static $last_step = 4;
    private static $db_version_required = '1.2.0';

    public static function isInstalled()
    {
        return (bool) self::installationTime();
    }


    public static function installationTime()
    {
        return WPHelpers::getOption('installation_completed');
    }

    public static function installedForXDays()
    {
        return round((time() - self::installationTime()) / (60 * 60 * 24));
    }

    public static function dbVersion()
    {
        return WPHelpers::getOption('db_version');
    }

    public static function canSeeUpdatePage()
    {

        return !static::seenUpdatePage();
    }

    public static function seenUpdatePage()
    {
        $update_seen = static::viewedUpdates();
        return $update_seen && version_compare($update_seen, WAPPOINTMENT_VERSION) >= 0;
    }



    public static function hasPendingUpdates()
    {
        $current_version = self::dbVersion();
        return version_compare($current_version, self::$db_version_required) < 0;
    }

    public static function dbVersionUpdateComplete()
    {
        return WPHelpers::setOption('db_version', self::$db_version_required);
    }

    public static function hasCorePendingUpdates()
    {
        return false;
    }

    public static function setViewedUpdated()
    {
        return WPHelpers::setStaffOption(
            'viewed_updates',
            WAPPOINTMENT_VERSION,
            Settings::get('activeStaffId'),
            true
        );
    }

    public static function viewedUpdates()
    {
        return WPHelpers::getStaffOption('viewed_updates');
    }

    public static function helloPage()
    {
        return WPHelpers::getStaffOption('hello_page');
    }

    public static function setHelloPage($value)
    {
        return WPHelpers::setStaffOption(
            'hello_page',
            $value,
            Settings::get('activeStaffId'),
            true
        );
    }

    public static function wizardStep()
    {
        return (int) WPHelpers::getOption('wizard_step');
    }

    public static function wizardComplete()
    {
        return (int) WPHelpers::getOption('wizard_step') < 0
            || (int) WPHelpers::getOption('wizard_step') == self::$last_step;
    }

    public static function newUpdates()
    {
        $viewed_updates_until = self::viewedUpdates();
        $toView = [];
        foreach (self::allVersionsWithUpdates() as $version) {
            if (empty($viewed_updates_until) || version_compare($version, $viewed_updates_until) > 0) {
                $toView[] = self::getVersionUpdates($version);
            } else {
                return $toView;
            }
        }
        return $toView;
    }

    public static function allUpdates()
    {
        $toView = [];
        foreach (self::allVersionsWithUpdates() as $version) {
            $toView[] = self::getVersionUpdates($version);
        }
        return $toView;
    }

    public static function getVersionUpdates($version)
    {
        return [
            'version' => $version,
            'changes' => call_user_func(
                get_called_class() . '::' . 'version' . str_replace('.', '_', $version)
            )
        ];
    }

    public static function allVersionsWithUpdates()
    {
        return ['1.2.0', '1.1.0'];
    }

    public static function version1_2_0()
    {
        return [
            [
                'title' => 'Booking Button widget can float',
                'images' => [
                    ['src' => 'set_floating_button.gif', 'alt' => 'Tick option in Appearance > Widgets'],
                    ['src' => 'front_floating_button.gif', 'alt' => 'Button floats in the frontend'],
                ]
            ],
        ];
    }

    public static function version1_1_0()
    {
        return [
            [
                'title' => 'Change staff providing services',
                'images' => [
                    ['src' => 'change_staff.gif']
                ]
            ],
            [
                'title' => 'Change staff picture',
                'images' => [
                    ['src' => 'change_staff_picture.gif']
                ]
            ],
        ];
    }
}
