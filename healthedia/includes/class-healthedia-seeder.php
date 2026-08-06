<?php
class Healthedia_Seeder {

	public static function seed_mock_data() {
		// Only run if option not set
		if (get_option('healthedia_mock_data_seeded')) {
			return;
		}

		$specialties = ['Oncology', 'Neurology', 'Cardiology', 'Sports Science', 'Immunology'];

		// 1. Generate 5 dummy institutions
		$institutions = ['Global Health Institute', 'Oxford Medical Lab', 'Tokyo Bio Research', 'Johns Hopkins Advanced Center', 'Sydney University'];
		foreach ($institutions as $i => $inst_name) {
			$post_id = wp_insert_post(array(
				'post_title' => $inst_name,
				'post_content' => 'This is a mock institution generated for UI testing. It represents a global research facility.',
				'post_status' => 'publish',
				'post_type' => 'healthedia_inst',
			));
			if (!is_wp_error($post_id)) {
				update_post_meta($post_id, '_healthedia_is_mock', 'yes');
				update_post_meta($post_id, '_healthedia_country', 'Global');
			}
		}

		// 2. Generate 10 dummy researchers
		$researcher_ids = array();
		for ($i = 1; $i <= 10; $i++) {
			$username = 'mock_researcher_' . $i;
			$email = $username . '@example.com';
			if (!username_exists($username)) {
				$user_id = wp_insert_user(array(
					'user_login' => $username,
					'user_pass' => wp_generate_password(),
					'user_email' => $email,
					'display_name' => 'Dr. Mock Researcher ' . $i,
					'description' => 'This is a generated mock researcher for UI testing.',
					'role' => 'researcher'
				));
				if (!is_wp_error($user_id)) {
					update_user_meta($user_id, '_healthedia_is_mock', 'yes');
					update_user_meta($user_id, '_healthedia_verified', '1');
					update_user_meta($user_id, '_healthedia_specialty', $specialties[array_rand($specialties)]);
					update_user_meta($user_id, '_healthedia_institution', $institutions[array_rand($institutions)]);
					update_user_meta($user_id, '_healthedia_country', 'Global');
					update_user_meta($user_id, '_healthedia_orcid', '0000-0000-0000-000' . $i);
					update_user_meta($user_id, '_healthedia_username', 'mock-researcher-' . $i);
					$researcher_ids[] = $user_id;
				}
			}
		}

		// 3. Generate 15 dummy articles
		$types = ['healthedia_post', 'healthedia_ext_res', 'healthedia_journal'];
		for ($i = 1; $i <= 15; $i++) {
			$author_id = !empty($researcher_ids) ? $researcher_ids[array_rand($researcher_ids)] : 1;
			$post_type = $types[array_rand($types)];
			$post_id = wp_insert_post(array(
				'post_title' => 'A Comprehensive Study on Mock Data Point ' . $i,
				'post_content' => 'This is the abstract for the mock article. It contains generated text to visualize the scientific journal layout accurately. Methodologies involve simulating UI.',
				'post_status' => 'publish',
				'post_type' => $post_type,
				'post_author' => $author_id
			));
			if (!is_wp_error($post_id)) {
				update_post_meta($post_id, '_healthedia_is_mock', 'yes');
				update_post_meta($post_id, '_healthedia_doi', '10.1234/healthedia.mock.' . $i);
				update_post_meta($post_id, '_healthedia_citations', rand(0, 100));
			}
		}

		update_option('healthedia_mock_data_seeded', true);
	}

	public static function wipe_mock_data() {
		// Delete mock users
		$mock_users = get_users(array('meta_key' => '_healthedia_is_mock', 'meta_value' => 'yes'));
		foreach ($mock_users as $user) {
			require_once(ABSPATH . 'wp-admin/includes/user.php');
			wp_delete_user($user->ID);
		}

		// Delete mock articles and institutions
		$mock_posts = get_posts(array(
			'post_type' => array('healthedia_article', 'healthedia_post', 'healthedia_ext_res', 'healthedia_journal', 'healthedia_inst'),
			'meta_key' => '_healthedia_is_mock',
			'meta_value' => 'yes',
			'posts_per_page' => -1
		));
		foreach ($mock_posts as $post) {
			wp_delete_post($post->ID, true);
		}

		delete_option('healthedia_mock_data_seeded');
	}
}
