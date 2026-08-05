<?php
class Healthedia_Profile_Endpoints {
	public function register_routes() {
		register_rest_route('healthedia/v1', '/profile', array(
			'methods' => 'POST',
			'callback' => array($this, 'update_profile'),
			'permission_callback' => function () {
				return is_user_logged_in();
			}
		));

		register_rest_route('healthedia/v1', '/profile/verify-request', array(
			'methods' => 'POST',
			'callback' => array($this, 'request_verification'),
			'permission_callback' => function () {
				return current_user_can('request_verification');
			}
		));
	}

	public function request_verification(WP_REST_Request $request) {
		$user_id = get_current_user_id();

		if (get_user_meta($user_id, '_healthedia_verified', true) === '1') {
			return new WP_Error('already_verified', 'Your account is already verified.', array('status' => 400));
		}

		if (get_user_meta($user_id, '_healthedia_verification_status', true) === 'pending') {
			return new WP_Error('already_pending', 'You already have a pending verification request.', array('status' => 400));
		}

		if (empty($_FILES['identity_document']) || $_FILES['identity_document']['error'] !== UPLOAD_ERR_OK) {
			return new WP_Error('missing_document', 'Please upload a valid identity document.', array('status' => 400));
		}

		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/media.php');

		$attachment_id = media_handle_upload('identity_document', 0);
		if (is_wp_error($attachment_id)) {
			return new WP_Error('upload_error', $attachment_id->get_error_message(), array('status' => 500));
		}

		update_user_meta($user_id, '_healthedia_verification_status', 'pending');
		update_user_meta($user_id, '_healthedia_verification_doc_id', $attachment_id);
		update_user_meta($user_id, '_healthedia_verification_date', time());

		// Notify admins conceptually (or can be seen in dashboard)

		return rest_ensure_response(array('success' => true, 'message' => 'Verification request submitted successfully.'));
	}

	public function update_profile(WP_REST_Request $request) {
		$user_id = get_current_user_id();

		$params = $request->get_json_params();
		if (empty($params)) {
			$params = $request->get_params();
		}

		$updatable_meta = array(
			'_healthedia_specialty',
			'_healthedia_institution',
			'_healthedia_country',
			'_healthedia_orcid',
			'_healthedia_privacy_mode'
		);

		foreach ($updatable_meta as $key) {
			if (isset($params[$key])) {
				update_user_meta($user_id, $key, sanitize_text_field($params[$key]));
			}
		}

		$user_data = array('ID' => $user_id);

		if (!empty($params['new_password'])) {
			if ($params['new_password'] !== $params['confirm_password']) {
				return new WP_Error('password_mismatch', 'New passwords do not match.', array('status' => 400));
			}
			$user_data['user_pass'] = $params['new_password'];
		}

		if (!empty($params['user_email'])) {
			$new_email = sanitize_email($params['user_email']);
			$current_user = get_userdata($user_id);
			if ($new_email !== $current_user->user_email) {
				if (email_exists($new_email)) {
					return new WP_Error('email_exists', 'Email address already in use.', array('status' => 400));
				}
				$user_data['user_email'] = $new_email;
			}
		}

		if (isset($params['first_name'])) {
			$user_data['first_name'] = sanitize_text_field($params['first_name']);
			update_user_meta($user_id, 'first_name', $user_data['first_name']);
		}
		if (isset($params['last_name'])) {
			$user_data['last_name'] = sanitize_text_field($params['last_name']);
			update_user_meta($user_id, 'last_name', $user_data['last_name']);
		}

		if (isset($params['first_name']) && isset($params['last_name'])) {
			$user_data['display_name'] = $user_data['first_name'] . ' ' . $user_data['last_name'];
		}

		if (isset($params['description'])) {
			$user_data['description'] = sanitize_textarea_field($params['description']);
		}

		$update_result = wp_update_user($user_data);
		if (is_wp_error($update_result)) {
			return new WP_Error('update_failed', $update_result->get_error_message(), array('status' => 500));
		}

		// If password was updated, we need to log the user back in
		if (!empty($params['new_password'])) {
			wp_clear_auth_cookie();
			wp_set_current_user($user_id);
			wp_set_auth_cookie($user_id);

			require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
			Healthedia_Notification_API::add_notification($user_id, "Your account password has been updated.", home_url('/account-settings'));
		}

		if (!empty($params['user_email'])) {
			$new_email = sanitize_email($params['user_email']);
			$current_user = get_userdata($user_id);
			if ($new_email !== $current_user->user_email && !email_exists($new_email)) {
				require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
				Healthedia_Notification_API::add_notification($user_id, "Your primary email address was updated.", home_url('/account-settings'));
			}
		}

		if (!empty($params['_healthedia_username'])) {
			$new_username = sanitize_title($params['_healthedia_username']);
			$current_username = get_user_meta($user_id, '_healthedia_username', true);

			if ($new_username !== $current_username) {
				$last_change = get_user_meta($user_id, '_healthedia_username_last_change', true);
				if ($last_change && (time() - intval($last_change)) < (7 * DAY_IN_SECONDS)) {
					return new WP_Error('username_rate_limit', 'Usernames can only be changed once every 7 days.', array('status' => 429));
				}

				// Check if username is already taken by someone else
				$exists = get_users(array(
					'meta_key' => '_healthedia_username',
					'meta_value' => $new_username,
					'exclude' => array($user_id),
					'number' => 1
				));

				$user_by_login = get_user_by('login', $new_username);
				$login_conflict = $user_by_login && $user_by_login->ID != $user_id;

				$user_by_slug = get_user_by('slug', $new_username);
				$slug_conflict = $user_by_slug && $user_by_slug->ID != $user_id;

				if (empty($exists) && !$login_conflict && !$slug_conflict) {
					update_user_meta($user_id, '_healthedia_username', $new_username);
					update_user_meta($user_id, '_healthedia_username_last_change', time());
				} else {
					return new WP_Error('username_exists', 'That username is already taken.', array('status' => 400));
				}
			}
		}

		// Handle Photo Upload
		if (!empty($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/media.php');

			$attachment_id = media_handle_upload('profile_photo', 0);
			if (is_wp_error($attachment_id)) {
				return new WP_Error('upload_error', $attachment_id->get_error_message(), array('status' => 500));
			}

			// Store the attachment ID in user meta
			$photo_url = wp_get_attachment_url($attachment_id);
			update_user_meta($user_id, '_healthedia_profile_photo', $photo_url);
			update_user_meta($user_id, '_healthedia_profile_photo_id', $attachment_id);
		}

		return rest_ensure_response(array('success' => true, 'message' => 'Profile updated successfully.'));
	}
}
