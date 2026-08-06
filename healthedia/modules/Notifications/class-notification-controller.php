<?php
class Healthedia_Notification_Controller extends Healthedia_Base_Controller {
    public function init( $loader ) {
        require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
        $api = new Healthedia_Notification_API();
        $loader->add_action( 'rest_api_init', $api, 'register_routes' );
    }
}
