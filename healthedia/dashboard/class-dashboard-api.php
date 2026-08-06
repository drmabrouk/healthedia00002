<?php
class Healthedia_Dashboard_API {
	public function register_routes() {
		register_rest_route('healthedia/v1', '/admin/stats', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_stats'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/users', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_users'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/users', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_user'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/users/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array($this, 'update_user'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/users/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array($this, 'delete_user'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));

		register_rest_route('healthedia/v1', '/admin/researchers', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_researchers'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/researchers', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_researcher'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/researchers/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array($this, 'update_researcher'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/researchers/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array($this, 'delete_user'), // reuse
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/articles', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_articles'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/articles/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array($this, 'delete_article'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/articles/(?P<id>\d+)/status', array(
			'methods' => 'PUT',
			'callback' => array($this, 'update_article_status'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/articles/bulk', array(
			'methods' => 'POST',
			'callback' => array($this, 'bulk_action_articles'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));

		register_rest_route('healthedia/v1', '/admin/wipe-mock-data', array(
			'methods' => 'POST',
			'callback' => array($this, 'wipe_mock_data'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/settings', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_settings'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/settings', array(
			'methods' => 'POST',
			'callback' => array($this, 'save_settings'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));

		register_rest_route('healthedia/v1', '/admin/certificates', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_certificates'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/certificates', array(
			'methods' => 'POST',
			'callback' => array($this, 'create_certificate'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/certificates/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array($this, 'update_certificate'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/certificates/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array($this, 'delete_certificate'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));

		register_rest_route('healthedia/v1', '/admin/verifications', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_verification_requests'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/verifications/(?P<id>\d+)/approve', array(
			'methods' => 'POST',
			'callback' => array($this, 'approve_verification'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
		register_rest_route('healthedia/v1', '/admin/verifications/(?P<id>\d+)/reject', array(
			'methods' => 'POST',
			'callback' => array($this, 'reject_verification'),
			'permission_callback' => array($this, 'check_admin_permissions')
		));
	}

	public function check_admin_permissions() {
		return current_user_can('manage_options');
	}

	public function get_stats() {
		global $wpdb;
		$total_users = count_users();

		// Sum up all the custom publication post types plus standard posts
		$total_articles = 0;
		$types = ['post', 'healthedia_ext_res', 'healthedia_journal'];
		foreach ($types as $type) {
			$counts = wp_count_posts($type);
			if (isset($counts->publish)) {
				$total_articles += $counts->publish;
			}
		}

		$metrics_table = $wpdb->prefix . 'healthedia_metrics';
		// Safely check if metrics table exists to prevent crash on fresh install
		$total_views = 0;
		if ($wpdb->get_var("SHOW TABLES LIKE '$metrics_table'") == $metrics_table) {
			$total_views = $wpdb->get_var("SELECT SUM(views) FROM $metrics_table");
		}

		return rest_ensure_response(array(
			'users' => $total_users['total_users'],
			'articles' => $total_articles,
			'total_views' => $total_views ?: 0
		));
	}

	public function get_users() {
		$users = get_users();
		$data = array();
		foreach ($users as $user) {
			$restricted_until = get_user_meta($user->ID, '_healthedia_restricted_until', true);
			$is_restricted = false;
			if ($restricted_until && intval($restricted_until) > time()) {
				$is_restricted = true;
			} elseif ($restricted_until) {
				// Expired restriction, clean it up
				delete_user_meta($user->ID, '_healthedia_restricted_until');
				delete_user_meta($user->ID, '_healthedia_restricted_reason');
				delete_user_meta($user->ID, '_healthedia_restricted_notes');
			}

			$data[] = array(
				'id' => $user->ID,
				'email' => $user->user_email,
				'name' => $user->display_name,
				'registered' => $user->user_registered,
				'roles' => $user->roles,
				'is_restricted' => $is_restricted,
				'restricted_until' => $is_restricted ? $restricted_until : null,
				'restricted_reason' => $is_restricted ? get_user_meta($user->ID, '_healthedia_restricted_reason', true) : null,
				'restricted_notes' => $is_restricted ? get_user_meta($user->ID, '_healthedia_restricted_notes', true) : null
			);
		}
		return rest_ensure_response($data);
	}

	public function create_user(WP_REST_Request $request) {
		$params = $request->get_json_params();
		$email = sanitize_email($params['email']);
		$name = sanitize_text_field($params['name']);
		$roles = isset($params['roles']) && is_array($params['roles']) ? array_map('sanitize_text_field', $params['roles']) : array('member');

		if (empty($email) || empty($name)) {
			return new WP_Error('missing_fields', 'Name and Email are required.', array('status' => 400));
		}

		if (email_exists($email)) {
			return new WP_Error('email_exists', 'User with this email already exists.', array('status' => 400));
		}

		$user_id = wp_insert_user(array(
			'user_login' => $email,
			'user_pass' => wp_generate_password(),
			'user_email' => $email,
			'display_name' => $name
		));

		if (!is_wp_error($user_id)) {
			$user = get_userdata($user_id);
			$user->set_role(''); // clear all roles
			foreach ($roles as $r) {
				$user->add_role($r);
			}
		}

		if (is_wp_error($user_id)) {
			return new WP_Error('create_failed', $user_id->get_error_message(), array('status' => 500));
		}

		return rest_ensure_response(array('success' => true, 'id' => $user_id));
	}

	public function update_user(WP_REST_Request $request) {
		$id = $request->get_param('id');
		$params = $request->get_json_params();

		$user_data = array('ID' => $id);
		if (isset($params['name'])) $user_data['display_name'] = sanitize_text_field($params['name']);
		if (isset($params['email'])) $user_data['user_email'] = sanitize_email($params['email']);

		$user_id = wp_update_user($user_data);
		if (is_wp_error($user_id)) {
			return new WP_Error('update_failed', $user_id->get_error_message(), array('status' => 500));
		}

		if (isset($params['roles']) && is_array($params['roles'])) {
			$roles = array_map('sanitize_text_field', $params['roles']);
			$user = get_userdata($id);
			if ($user) {
				$user->set_role(''); // clear all roles
				foreach ($roles as $r) {
					$user->add_role($r);
				}
			}
		}

		if (isset($params['is_restricted'])) {
			if ($params['is_restricted'] === '1' && isset($params['restricted_duration'])) {
				$duration_days = intval($params['restricted_duration']);
				if ($duration_days > 0) {
					$until = time() + ($duration_days * 86400);
					update_user_meta($id, '_healthedia_restricted_until', $until);
					if (isset($params['restricted_reason'])) update_user_meta($id, '_healthedia_restricted_reason', sanitize_text_field($params['restricted_reason']));
					if (isset($params['restricted_notes'])) update_user_meta($id, '_healthedia_restricted_notes', sanitize_textarea_field($params['restricted_notes']));
				}
			} else {
				delete_user_meta($id, '_healthedia_restricted_until');
				delete_user_meta($id, '_healthedia_restricted_reason');
				delete_user_meta($id, '_healthedia_restricted_notes');
			}
		}

		return rest_ensure_response(array('success' => true));
	}

	public function get_articles(WP_REST_Request $request) {
		$type = sanitize_text_field($request->get_param('type'));
		if (empty($type)) $type = 'post';

		$allowed_types = ['post', 'healthedia_ext_res', 'healthedia_journal', 'healthedia_article'];
		if (!in_array($type, $allowed_types)) {
			$type = 'post';
		}

		$args = array(
			'post_type' => $type,
			'post_status' => array('publish', 'pending', 'draft'),
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC'
		);
		$posts = get_posts($args);
		$data = array();
		foreach ($posts as $post) {
			$author = get_userdata($post->post_author);

			// Optional categories
			$categories = wp_get_post_terms($post->ID, 'category', array('fields' => 'names'));

			$data[] = array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'status' => $post->post_status,
				'author_name' => $author ? $author->display_name : 'Unknown',
				'categories' => $categories,
				'date' => $post->post_date,
				'permalink' => get_permalink($post->ID)
			);
		}
		return rest_ensure_response($data);
	}

	public function delete_article(WP_REST_Request $request) {
		$id = $request->get_param('id');
		wp_delete_post($id, true);
		return rest_ensure_response(array('success' => true));
	}

	public function bulk_action_articles(WP_REST_Request $request) {
		$params = $request->get_json_params();
		$action = sanitize_text_field($params['action'] ?? '');
		$ids = $params['ids'] ?? [];

		if (!is_array($ids) || empty($ids)) {
			return new WP_Error('missing_ids', 'No items selected.', array('status' => 400));
		}

		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
		foreach ($ids as $id) {
			$id = intval($id);
			if ($action === 'delete') {
				wp_delete_post($id, true);
			} elseif ($action === 'publish' || $action === 'pending' || $action === 'draft') {
				wp_update_post(array(
					'ID' => $id,
					'post_status' => $action
				));
				$post = get_post($id);
				if ($post) {
					$title = $post->post_title;
					if ($action === 'publish') {
						Healthedia_Notification_API::add_notification($post->post_author, "Your submission '{$title}' has been approved and published.", get_permalink($id));
					} elseif ($action === 'draft') {
						Healthedia_Notification_API::add_notification($post->post_author, "Your submission '{$title}' has been un-published.", home_url('/my-requests'));
					}
				}
			}
		}

		return rest_ensure_response(array('success' => true));
	}

	public function update_article_status(WP_REST_Request $request) {
		$id = $request->get_param('id');
		$params = $request->get_json_params();
		if (isset($params['status'])) {
			$status = sanitize_text_field($params['status']);
			wp_update_post(array(
				'ID' => $id,
				'post_status' => $status
			));

			// Notify author
			$post = get_post($id);
			if ($post) {
				require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
				$title = $post->post_title;
				if ($status === 'publish') {
					Healthedia_Notification_API::add_notification($post->post_author, "Your submission '{$title}' has been approved and published.", get_permalink($id));
				} elseif ($status === 'draft' || $status === 'rejected') {
					Healthedia_Notification_API::add_notification($post->post_author, "Your submission '{$title}' has been un-published or rejected.", home_url('/my-requests'));
				}
			}
		}
		return rest_ensure_response(array('success' => true));
	}

	public function delete_user(WP_REST_Request $request) {
		$id = $request->get_param('id');
		if ($id == get_current_user_id()) {
			return new WP_Error('delete_self', 'You cannot delete yourself.', array('status' => 400));
		}
		require_once(ABSPATH . 'wp-admin/includes/user.php');
		if (wp_delete_user($id)) {
			return rest_ensure_response(array('success' => true));
		}
		return new WP_Error('delete_failed', 'Failed to delete user.', array('status' => 500));
	}

	public function get_researchers() {
		// A researcher is any user who has specialty or institution meta, or is verified
		$users = get_users(array(
			'meta_query' => array(
				'relation' => 'OR',
				array('key' => '_healthedia_verified', 'compare' => 'EXISTS'),
				array('key' => '_healthedia_specialty', 'compare' => 'EXISTS')
			)
		));
		$data = array();
		foreach ($users as $user) {
			$data[] = array(
				'id' => $user->ID,
				'email' => $user->user_email,
				'name' => $user->display_name,
				'specialty' => get_user_meta($user->ID, '_healthedia_specialty', true),
				'institution' => get_user_meta($user->ID, '_healthedia_institution', true),
				'is_verified' => get_user_meta($user->ID, '_healthedia_verified', true) === '1',
				'is_mock' => get_user_meta($user->ID, '_healthedia_is_mock', true) === 'yes'
			);
		}
		return rest_ensure_response($data);
	}

	public function create_researcher(WP_REST_Request $request) {
		$params = $request->get_json_params();
		$user_id = intval($params['user_id']);
		$specialty = sanitize_text_field($params['specialty'] ?? '');
		$institution = sanitize_text_field($params['institution'] ?? '');
		$is_verified = sanitize_text_field($params['is_verified'] ?? '0');

		if (!$user_id) {
			return new WP_Error('missing_fields', 'User selection is required.', array('status' => 400));
		}

		$user = get_userdata($user_id);
		if (!$user) {
			return new WP_Error('invalid_user', 'Selected user does not exist.', array('status' => 400));
		}

		if ($is_verified === '1') {
			update_user_meta($user_id, '_healthedia_verified', '1');
		} else {
			delete_user_meta($user_id, '_healthedia_verified');
		}

		update_user_meta($user_id, '_healthedia_specialty', $specialty);
		update_user_meta($user_id, '_healthedia_institution', $institution);

		return rest_ensure_response(array('success' => true, 'id' => $user_id));
	}

	public function update_researcher(WP_REST_Request $request) {
		$id = $request->get_param('id');
		$params = $request->get_json_params();

		if (isset($params['specialty'])) update_user_meta($id, '_healthedia_specialty', sanitize_text_field($params['specialty']));
		if (isset($params['institution'])) update_user_meta($id, '_healthedia_institution', sanitize_text_field($params['institution']));
		if (isset($params['is_verified'])) {
			if ($params['is_verified'] === '1') {
				update_user_meta($id, '_healthedia_verified', '1');
			} else {
				delete_user_meta($id, '_healthedia_verified');
			}
		}

		return rest_ensure_response(array('success' => true));
	}

	public function wipe_mock_data() {
		require_once HEALTHEDIA_PLUGIN_DIR . 'includes/class-healthedia-seeder.php';
		Healthedia_Seeder::wipe_mock_data();
		return rest_ensure_response(array('success' => true, 'message' => 'Mock data wiped successfully.'));
	}

	public function get_verification_requests() {
		$users = get_users(array(
			'meta_key' => '_healthedia_verification_status',
			'meta_value' => 'pending'
		));
		$data = array();
		foreach ($users as $user) {
			$doc_id = get_user_meta($user->ID, '_healthedia_verification_doc_id', true);
			$doc_url = $doc_id ? wp_get_attachment_url($doc_id) : '';
			$data[] = array(
				'id' => $user->ID,
				'name' => $user->display_name,
				'email' => $user->user_email,
				'specialty' => get_user_meta($user->ID, '_healthedia_specialty', true),
				'institution' => get_user_meta($user->ID, '_healthedia_institution', true),
				'date' => get_user_meta($user->ID, '_healthedia_verification_date', true),
				'document_url' => $doc_url
			);
		}
		return rest_ensure_response($data);
	}

	public function approve_verification(WP_REST_Request $request) {
		$user_id = $request->get_param('id');
		update_user_meta($user_id, '_healthedia_verified', '1');
		update_user_meta($user_id, '_healthedia_verification_status', 'approved');

		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
		Healthedia_Notification_API::add_notification($user_id, 'Congratulations! Your account verification request has been approved. The Verified Badge is now active on your public profile.', home_url('/'.get_user_meta($user_id, '_healthedia_username', true)));

		$user = get_userdata($user_id);
		wp_mail($user->user_email, 'Healthedia Account Verified', "Dear {$user->display_name},\n\nCongratulations! Your account verification request has been approved. The Verified Badge (✔) is now active on your public profile.\n\nThank you for being a part of the Healthedia global network.\n\nThe Healthedia Editorial Board");

		return rest_ensure_response(array('success' => true));
	}

	public function reject_verification(WP_REST_Request $request) {
		$user_id = $request->get_param('id');
		$params = $request->get_json_params();
		$reason = sanitize_text_field($params['reason'] ?? 'Did not meet verification criteria.');

		update_user_meta($user_id, '_healthedia_verification_status', 'rejected');

		require_once HEALTHEDIA_PLUGIN_DIR . 'modules/Notifications/class-notification-api.php';
		Healthedia_Notification_API::add_notification($user_id, "Your account verification request was not approved. Reason: {$reason}", home_url('/account-settings'));

		$user = get_userdata($user_id);
		wp_mail($user->user_email, 'Healthedia Verification Update', "Dear {$user->display_name},\n\nWe have reviewed your verification request. Unfortunately, it was not approved at this time.\n\nReason: {$reason}\n\nYou may update your information and submit a new request via your Account Settings.\n\nThe Healthedia Editorial Board");

		return rest_ensure_response(array('success' => true));
	}

	public function get_certificates() {
		$posts = get_posts(array(
			'post_type' => 'healthedia_cert',
			'post_status' => 'any',
			'posts_per_page' => -1,
		));
		$data = array();
		foreach ($posts as $post) {
			$data[] = array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'cert_number' => get_post_meta($post->ID, '_healthedia_cert_number', true),
				'holder_name' => get_post_meta($post->ID, '_healthedia_cert_holder', true),
				'issue_date' => get_post_meta($post->ID, '_healthedia_cert_issue', true),
				'status' => $post->post_status
			);
		}
		return rest_ensure_response($data);
	}

	public function create_certificate(WP_REST_Request $request) {
		$params = $request->get_json_params();
		$post_id = wp_insert_post(array(
			'post_title' => sanitize_text_field($params['title'] ?? 'New Certificate'),
			'post_type' => 'healthedia_cert',
			'post_status' => sanitize_text_field($params['status'] ?? 'publish')
		));

		if (is_wp_error($post_id)) {
			return new WP_Error('create_failed', $post_id->get_error_message(), array('status' => 500));
		}

		if (isset($params['cert_number'])) update_post_meta($post_id, '_healthedia_cert_number', sanitize_text_field($params['cert_number']));
		if (isset($params['holder_name'])) update_post_meta($post_id, '_healthedia_cert_holder', sanitize_text_field($params['holder_name']));
		if (isset($params['issue_date'])) update_post_meta($post_id, '_healthedia_cert_issue', sanitize_text_field($params['issue_date']));

		return rest_ensure_response(array('success' => true, 'id' => $post_id));
	}

	public function update_certificate(WP_REST_Request $request) {
		$id = $request->get_param('id');
		$params = $request->get_json_params();

		$post_data = array('ID' => $id);
		if (isset($params['title'])) $post_data['post_title'] = sanitize_text_field($params['title']);
		if (isset($params['status'])) $post_data['post_status'] = sanitize_text_field($params['status']);

		wp_update_post($post_data);

		if (isset($params['cert_number'])) update_post_meta($id, '_healthedia_cert_number', sanitize_text_field($params['cert_number']));
		if (isset($params['holder_name'])) update_post_meta($id, '_healthedia_cert_holder', sanitize_text_field($params['holder_name']));
		if (isset($params['issue_date'])) update_post_meta($id, '_healthedia_cert_issue', sanitize_text_field($params['issue_date']));

		return rest_ensure_response(array('success' => true));
	}

	public function delete_certificate(WP_REST_Request $request) {
		$id = $request->get_param('id');
		wp_delete_post($id, true);
		return rest_ensure_response(array('success' => true));
	}

	public function get_settings() {
		return rest_ensure_response(array(
			'site_name' => get_option('blogname'),
			'site_desc' => get_option('blogdescription'),
			'admin_email' => get_option('admin_email'),
			'mock_data_seeded' => get_option('healthedia_mock_data_seeded', false),
			'enable_registration' => get_option('healthedia_enable_registration', 'yes'),
			'auth_maintenance_mode' => get_option('healthedia_auth_maintenance', 'no'),
			'privacy_policy_url' => get_option('healthedia_privacy_policy_url', ''),
			'terms_url' => get_option('healthedia_terms_url', '')
		));
	}

	public function save_settings(WP_REST_Request $request) {
		$params = $request->get_json_params();
		if (isset($params['site_name'])) update_option('blogname', sanitize_text_field($params['site_name']));
		if (isset($params['site_desc'])) update_option('blogdescription', sanitize_text_field($params['site_desc']));
		if (isset($params['admin_email'])) update_option('admin_email', sanitize_email($params['admin_email']));
		if (isset($params['enable_registration'])) update_option('healthedia_enable_registration', sanitize_text_field($params['enable_registration']));
		if (isset($params['auth_maintenance_mode'])) update_option('healthedia_auth_maintenance', sanitize_text_field($params['auth_maintenance_mode']));
		if (isset($params['privacy_policy_url'])) update_option('healthedia_privacy_policy_url', sanitize_text_field($params['privacy_policy_url']));
		if (isset($params['terms_url'])) update_option('healthedia_terms_url', sanitize_text_field($params['terms_url']));
		return rest_ensure_response(array('success' => true, 'message' => 'Settings saved.'));
	}
}
