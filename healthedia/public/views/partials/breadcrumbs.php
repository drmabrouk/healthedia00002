<?php
// Breadcrumbs component
function healthedia_breadcrumbs() {
    $separator = '<span class="text-gray-400 mx-2">/</span>';
    echo '<nav class="font-mono text-xs uppercase tracking-widest text-gray-500 mb-6 flex flex-wrap items-center">';
    echo '<a href="' . home_url() . '" class="hover:text-black transition-colors">Home</a>';

    if (is_single() && in_array(get_post_type(), ['healthedia_article', 'healthedia_post', 'healthedia_ext_res', 'healthedia_journal'])) {
        echo $separator;
        echo '<a href="' . home_url('/archive-search') . '" class="hover:text-black transition-colors">Journal Archive</a>';
        echo $separator;
        echo '<span class="text-black font-bold truncate max-w-[200px]">' . get_the_title() . '</span>';
    } elseif (get_query_var('healthedia_profile') || get_query_var('healthedia_profile_user')) {
        echo $separator;
        echo '<a href="' . home_url('/archive-search') . '" class="hover:text-black transition-colors">Global Directory</a>';
        echo $separator;

        $profile_id = get_query_var('healthedia_profile');
		if (!$profile_id && get_query_var('healthedia_profile_user')) {
			$user_obj = get_query_var('healthedia_profile_user');
			$profile_id = $user_obj->ID;
		}
        $user = get_userdata($profile_id);
        echo '<span class="text-black font-bold">' . esc_html($user->display_name) . '</span>';
    } elseif (get_query_var('healthedia_page') === 'archive_search') {
        echo $separator;
        echo '<span class="text-black font-bold">Search Archive</span>';
    }

    echo '</nav>';
}
?>
