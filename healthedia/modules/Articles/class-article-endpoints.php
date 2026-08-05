<?php
class Healthedia_Article_Endpoints {
	public function register_routes() {
		register_rest_route('healthedia/v1', '/manuscript/submit', array(
			'methods' => 'POST',
			'callback' => array($this, 'handle_submission'),
			'permission_callback' => function() { return current_user_can('submit_articles') || current_user_can('submit_ext_res') || current_user_can('submit_journal'); }
		));
		register_rest_route('healthedia/v1', '/manuscript/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array($this, 'delete_submission'),
			'permission_callback' => function() { return current_user_can('submit_articles') || current_user_can('submit_ext_res') || current_user_can('submit_journal'); }
		));
	}

	public function delete_submission($request) {
		$post_id = $request->get_param('id');
		$post = get_post($post_id);

		if (!$post || $post->post_type !== 'healthedia_article') {
			return new WP_Error('not_found', 'Manuscript not found.', array('status' => 404));
		}

		if ($post->post_author != get_current_user_id()) {
			return new WP_Error('unauthorized', 'You can only withdraw your own submissions.', array('status' => 403));
		}

		if ($post->post_status === 'publish') {
			return new WP_Error('unauthorized', 'Cannot withdraw a published manuscript.', array('status' => 403));
		}

		wp_delete_post($post_id, true);
		return rest_ensure_response(array('success' => true, 'message' => 'Manuscript successfully withdrawn.'));
	}

	public function handle_submission($request) {
		$type = sanitize_text_field($request->get_param('type'));
		if (empty($type)) $type = 'post';

		$allowed_types = ['post', 'healthedia_ext_res', 'healthedia_journal', 'healthedia_article'];
		if (!in_array($type, $allowed_types)) {
			$type = 'post';
		}

		$title = sanitize_text_field($request->get_param('title'));
		$abstract = wp_kses_post($request->get_param('abstract')); // Allow HTML for standard articles
		$specialty = sanitize_text_field($request->get_param('specialty'));
		$nct = sanitize_text_field($request->get_param('nct'));

		if (empty($title)) {
			return new WP_Error('missing_fields', 'Title is required.', array('status' => 400));
		}

		$files = $request->get_file_params();

		$upload_url = '';
		$upload_path = '';
		$attachment_id = 0;

		if (!empty($files['manuscript']) && $files['manuscript']['error'] === UPLOAD_ERR_OK) {
			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/media.php');

			if ($type === 'post') {
				// Cover image upload
				$attachment_id = media_handle_upload('manuscript', 0);
				if (is_wp_error($attachment_id)) {
					return new WP_Error('upload_error', $attachment_id->get_error_message(), array('status' => 500));
				}
			} else {
				// Document upload
				$upload_overrides = array(
					'test_form' => false,
					'mimes' => array(
						'pdf' => 'application/pdf',
						'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
					)
				);
				$upload = wp_handle_upload($files['manuscript'], $upload_overrides);
				if (isset($upload['error'])) {
					return new WP_Error('upload_error', $upload['error'], array('status' => 500));
				}
				$upload_url = $upload['url'];
				$upload_path = $upload['file'];
			}
		} elseif ($type === 'healthedia_ext_res' || $type === 'healthedia_journal') {
			// Require file for research and journal
			return new WP_Error('missing_file', 'Please upload a manuscript/document file.', array('status' => 400));
		}

		$post_id = wp_insert_post(array(
			'post_title' => $title,
			'post_content' => $abstract,
			'post_type' => $type,
			'post_status' => ($type === 'healthedia_ext_res') ? 'publish' : 'pending', // External research is published if they have permission
			'post_author' => get_current_user_id()
		));

		if (is_wp_error($post_id)) {
			return new WP_Error('db_error', 'Failed to save manuscript.', array('status' => 500));
		}

		if ($attachment_id) {
			set_post_thumbnail($post_id, $attachment_id);
		}

		update_post_meta($post_id, '_healthedia_specialty', $specialty);
		update_post_meta($post_id, '_healthedia_nct', $nct);
		if ($upload_url) update_post_meta($post_id, '_healthedia_file_url', $upload_url);
		if ($upload_path) update_post_meta($post_id, '_healthedia_file_path', $upload_path);

		return rest_ensure_response(array(
			'success' => true,
			'message' => ($type === 'healthedia_ext_res') ? 'Publication indexed successfully.' : 'Submission successful. Awaiting review.',
			'post_id' => $post_id
		));
	}
}
