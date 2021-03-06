<?php

namespace Wappointment\WP;

/** */
class Shortcodes
{
    public function __construct()
    {
        add_shortcode('wap_widget', ['\Wappointment\WP\Shortcodes', 'wapWidgetHandler']);
    }

    public static function wapWidgetHandler($atts)
    {
        $fetched_attributes = [];

        if (is_array($atts)) {
            $fetched_attributes = shortcode_atts(
                apply_filters('wappointment_shortcode_attributes', static::handleFilters($atts), $atts),
                $atts
            );
        }


        return Widget::baseHtml($fetched_attributes);
    }

    public static function handleFilters($atts)
    {
        return [
            'auto_open' => in_array('open', $atts),
            'large_version' => in_array('large', $atts),
            'week' => in_array('week', $atts),
            'button_title' => !empty($atts['title']) ?
                $atts['title'] : (new \Wappointment\Services\WidgetSettings)->get()['button']['title'],
        ];
    }
}
