<?php
class Healthedia_Auth_Endpoints {
	public function register_routes() {
		register_rest_route('healthedia/v1', '/auth/request-otp', array(
			'methods' => 'POST',
			'callback' => array($this, 'request_otp'),
			'permission_callback' => '__return_true'
		));
		register_rest_route('healthedia/v1', '/auth/verify-otp', array(
			'methods' => 'POST',
			'callback' => array($this, 'verify_otp'),
			'permission_callback' => '__return_true'
		));
		register_rest_route('healthedia/v1', '/auth/login-standard', array(
			'methods' => 'POST',
			'callback' => array($this, 'login_standard'),
			'permission_callback' => '__return_true'
		));
		register_rest_route('healthedia/v1', '/auth/reset-password', array(
			'methods' => 'POST',
			'callback' => array($this, 'reset_password'),
			'permission_callback' => '__return_true'
		));
	}

	public function request_otp($request) {
		$email = sanitize_email($request->get_param('email'));
		if (!is_email($email)) {
			return new WP_Error('invalid_email', 'Invalid email address', array('status' => 400));
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		$rate_limit_key = 'healthedia_otp_rate_limit_' . md5($ip);
		$attempts = get_transient($rate_limit_key) ?: 0;
		if ($attempts > 5) {
			return new WP_Error('rate_limit', 'Too many requests. Please try again later.', array('status' => 429));
		}
		set_transient($rate_limit_key, $attempts + 1, 15 * MINUTE_IN_SECONDS);

		$is_register = $request->get_param('is_register');

		if ($is_register === 'true' || $is_register === true) {
			$registration_enabled = get_option('healthedia_enable_registration', 'yes');
			if ($registration_enabled !== 'yes') {
				return new WP_Error('registration_disabled', 'New user registration is currently disabled.', array('status' => 403));
			}
			if (email_exists($email)) {
				return new WP_Error('email_exists', 'An account with this email already exists. Please log in.', array('status' => 400));
			}
			// Store temporary registration data
			$temp_data = array(
				'name' => sanitize_text_field($request->get_param('name')),
				'password' => $request->get_param('password'), // raw password, we'll hash it on verify
				'specialty' => sanitize_text_field($request->get_param('specialty')),
				'institution' => sanitize_text_field($request->get_param('institution')),
				'country' => sanitize_text_field($request->get_param('country')),
				'orcid' => sanitize_text_field($request->get_param('orcid'))
			);
			set_transient('healthedia_reg_' . md5($email), $temp_data, 15 * MINUTE_IN_SECONDS);
		} else { // 'forgot' or standard OTP if needed
			$user = get_user_by('email', $email);
			if (!$user) {
				return new WP_Error('email_not_found', 'No account found with this email.', array('status' => 400));
			}

			$restricted_until = get_user_meta($user->ID, '_healthedia_restricted_until', true);
			if ($restricted_until && intval($restricted_until) > time()) {
				$reason = get_user_meta($user->ID, '_healthedia_restricted_reason', true);
				$message = 'Your account has been temporarily restricted until ' . date('Y-m-d H:i', $restricted_until) . '.';
				if ($reason) {
					$message .= ' Reason: ' . esc_html($reason);
				}
				return new WP_Error('account_restricted', $message, array('status' => 403));
			} elseif ($restricted_until) {
				delete_user_meta($user->ID, '_healthedia_restricted_until');
				delete_user_meta($user->ID, '_healthedia_restricted_reason');
				delete_user_meta($user->ID, '_healthedia_restricted_notes');
			}
		}

		$otp = Healthedia_Auth_OTP::generate($email);
		Healthedia_Auth_Mailer::send_otp($email, $otp, ($is_register === 'true' || $is_register === true) ? 'register' : 'forgot');

		return rest_ensure_response(array('success' => true, 'message' => 'OTP sent to email.'));
	}

	public function verify_otp($request) {
		$email = sanitize_email($request->get_param('email'));
		$otp = sanitize_text_field($request->get_param('otp'));
		$is_register = $request->get_param('is_register');

		$ip = $_SERVER['REMOTE_ADDR'];
		$verify_limit_key = 'healthedia_otp_verify_limit_' . md5($email . '_' . $ip);
		$verify_attempts = get_transient($verify_limit_key) ?: 0;
		if ($verify_attempts > 5) {
			return new WP_Error('rate_limit', 'Too many failed attempts. Please request a new OTP.', array('status' => 429));
		}

		if (!Healthedia_Auth_OTP::verify($email, $otp)) {
			set_transient($verify_limit_key, $verify_attempts + 1, 15 * MINUTE_IN_SECONDS);
			return new WP_Error('invalid_otp', 'Invalid or expired OTP', array('status' => 401));
		}
		delete_transient($verify_limit_key);

		$user = get_user_by('email', $email);

		if ($is_register === 'true' || $is_register === true) {
			if ($user) {
				return new WP_Error('email_exists', 'Account already exists.', array('status' => 400));
			}
			$temp_data = get_transient('healthedia_reg_' . md5($email));
			if (!$temp_data) {
				return new WP_Error('session_expired', 'Registration session expired. Please start over.', array('status' => 400));
			}

			$user_id = wp_create_user($email, $temp_data['password'], $email);
			if (is_wp_error($user_id)) {
				return new WP_Error('creation_failed', 'Failed to create account.', array('status' => 500));
			}
			$user = get_user_by('id', $user_id);
			$user->set_role('member');

			// Save custom meta
			wp_update_user(array('ID' => $user_id, 'display_name' => $temp_data['name']));
			update_user_meta($user_id, '_healthedia_specialty', $temp_data['specialty']);
			update_user_meta($user_id, '_healthedia_institution', $temp_data['institution']);
			update_user_meta($user_id, '_healthedia_country', $temp_data['country']);
			update_user_meta($user_id, '_healthedia_orcid', $temp_data['orcid']);

			// Auto verify email based on OTP success
			update_user_meta($user_id, '_healthedia_email_verified', '1');

			delete_transient('healthedia_reg_' . md5($email));
		} else {
			if (!$user) {
				return new WP_Error('user_not_found', 'User not found.', array('status' => 400));
			}
			$restricted_until = get_user_meta($user->ID, '_healthedia_restricted_until', true);
			if ($restricted_until && intval($restricted_until) > time()) {
				$reason = get_user_meta($user->ID, '_healthedia_restricted_reason', true);
				$message = 'Your account has been temporarily restricted until ' . date('Y-m-d H:i', $restricted_until) . '.';
				if ($reason) {
					$message .= ' Reason: ' . esc_html($reason);
				}
				return new WP_Error('account_restricted', $message, array('status' => 403));
			} elseif ($restricted_until) {
				delete_user_meta($user->ID, '_healthedia_restricted_until');
				delete_user_meta($user->ID, '_healthedia_restricted_reason');
				delete_user_meta($user->ID, '_healthedia_restricted_notes');
			}

			// If it's the forgot password flow, we do NOT log them in yet.
			// We just return success so the frontend can show the reset password UI
			if ($is_register === 'forgot') {
				$reset_token_key = 'healthedia_reset_token_' . md5($email . '_' . $otp);
				set_transient($reset_token_key, true, 15 * MINUTE_IN_SECONDS);
				return rest_ensure_response(array('success' => true, 'message' => 'OTP verified. Proceed to reset password.'));
			}
		}

		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID);

		return rest_ensure_response(array('success' => true, 'message' => 'Authenticated successfully.'));
	}

	public function login_standard(WP_REST_Request $request) {
		$login = sanitize_text_field($request->get_param('login'));
		$password = $request->get_param('password');

		if (empty($login) || empty($password)) {
			return new WP_Error('missing_fields', 'Username/Email and Password are required.', array('status' => 400));
		}

		$user = wp_authenticate($login, $password);

		if (is_wp_error($user)) {
			return new WP_Error('invalid_credentials', 'Invalid credentials.', array('status' => 401));
		}

		$restricted_until = get_user_meta($user->ID, '_healthedia_restricted_until', true);
		if ($restricted_until && intval($restricted_until) > time()) {
			$reason = get_user_meta($user->ID, '_healthedia_restricted_reason', true);
			$message = 'Your account has been temporarily restricted until ' . date('Y-m-d H:i', $restricted_until) . '.';
			if ($reason) {
				$message .= ' Reason: ' . esc_html($reason);
			}
			return new WP_Error('account_restricted', $message, array('status' => 403));
		} elseif ($restricted_until) {
			delete_user_meta($user->ID, '_healthedia_restricted_until');
			delete_user_meta($user->ID, '_healthedia_restricted_reason');
			delete_user_meta($user->ID, '_healthedia_restricted_notes');
		}

		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID);

		return rest_ensure_response(array('success' => true, 'message' => 'Logged in successfully.'));
	}

	public function reset_password(WP_REST_Request $request) {
		$email = sanitize_email($request->get_param('email'));
		$otp = sanitize_text_field($request->get_param('otp'));
		$password = $request->get_param('password');

		if (empty($email) || empty($otp) || empty($password)) {
			return new WP_Error('missing_fields', 'Missing required fields.', array('status' => 400));
		}

		// Re-verify the OTP to ensure security of the reset request
		if (!Healthedia_Auth_OTP::verify($email, $otp, false)) { // don't invalidate it yet, or we assume it was verified recently
			// Actually, if we consumed the OTP on the previous step, we can't re-verify.
			// A better pattern is to use a secure token.
			// For this implementation, since OTP is deleted on verify, we will trust the session or create a temporary reset token.
			// Let's implement a quick reset token based on the OTP verify step above.
		}

		// To fix the OTP consumption issue:
		// We should have created a reset token in `verify_otp`. Let's assume for this specific implementation
		// we issue a short-lived transient.
		$reset_token_key = 'healthedia_reset_token_' . md5($email . '_' . $otp);
		if (!get_transient($reset_token_key)) {
			return new WP_Error('invalid_reset_token', 'Invalid or expired reset session.', array('status' => 403));
		}

		$user = get_user_by('email', $email);
		if (!$user) {
			return new WP_Error('user_not_found', 'User not found.', array('status' => 400));
		}

		wp_set_password($password, $user->ID);
		delete_transient($reset_token_key);

		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID);

		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
		Healthedia_Notification_API::add_notification($user->ID, "Your password was successfully reset.", home_url('/account-settings'));

		return rest_ensure_response(array('success' => true, 'message' => 'Password reset successfully.'));
	}
}
