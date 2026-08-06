<?php
class Healthedia_Profile_Verification {
	public function register_routes() {
		// API for admin to verify users
		register_rest_route('healthedia/v1', '/profile/verify/(?P<id>\d+)', array(
			'methods' => 'POST',
			'callback' => array($this, 'verify_user'),
			'permission_callback' => function() { return current_user_can('manage_options'); }
		));
	}

	public function verify_user($request) {
		$user_id = $request['id'];
		update_user_meta($user_id, '_healthedia_verified', '1');
		return rest_ensure_response(array('success' => true));
	}

	public static function is_verified($user_id) {
		return get_user_meta($user_id, '_healthedia_verified', true) === '1';
	}
}
