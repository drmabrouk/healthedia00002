<?php
class Healthedia_Profile_Model {
	public function track_views() {
		$user_id = get_query_var('healthedia_profile');
		if (!$user_id && get_query_var('healthedia_profile_user')) {
			$user_obj = get_query_var('healthedia_profile_user');
			$user_id = $user_obj->ID;
		}

		if ( $user_id ) {
			if ( is_numeric($user_id) && get_userdata($user_id) ) {
				global $wpdb;
				$table = $wpdb->prefix . 'healthedia_metrics';

				$existing = $wpdb->get_row( $wpdb->prepare( "SELECT id, views FROM $table WHERE object_id = %d AND object_type = 'user'", $user_id ) );
				if ( $existing ) {
					$wpdb->query( $wpdb->prepare( "UPDATE $table SET views = views + 1 WHERE id = %d", $existing->id ) );
					if ( ($existing->views + 1) % 100 === 0 ) {
						require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
						Healthedia_Notification_API::add_notification($user_id, "Your public profile has reached " . ($existing->views + 1) . " views.", home_url('/'.get_user_meta($user_id, '_healthedia_username', true)));
					}
				} else {
					$wpdb->insert( $table, array(
						'object_id' => $user_id,
						'object_type' => 'user',
						'views' => 1
					) );
				}
			}
		}
	}

	public static function get_metrics( $user_id ) {
		global $wpdb;
		$table = $wpdb->prefix . 'healthedia_metrics';
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE object_id = %d AND object_type = 'user'", $user_id ) );
		return $row ? $row : (object) array('views' => 0, 'citations' => 0);
	}
}
