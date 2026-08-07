<?php
class Healthedia_Sitemap {
    public function init() {
        add_action('init', array($this, 'add_rewrite_rule'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'render_sitemap'));
        add_filter('robots_txt', array($this, 'add_sitemap_to_robots'), 10, 2);
    }

    public function add_rewrite_rule() {
        add_rewrite_rule('^healthedia-sitemap\.xml$', 'index.php?healthedia_sitemap=1', 'top');
    }

    public function add_query_vars($vars) {
        $vars[] = 'healthedia_sitemap';
        return $vars;
    }

    public function render_sitemap() {
        if (get_query_var('healthedia_sitemap')) {
            header('Content-Type: application/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // Home Page
            $this->add_url(home_url('/'), date('c'), 'weekly', '1.0');

            // Search / Directories
            $this->add_url(home_url('/archive-search'), date('c'), 'daily', '0.8');

            // Publications
            $articles = new WP_Query(array(
                'post_type' => array('healthedia_post', 'healthedia_ext_res', 'healthedia_journal', 'healthedia_article'),
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ));
            if ($articles->have_posts()) {
                while ($articles->have_posts()) {
                    $articles->the_post();
                    $this->add_url(get_permalink(), get_the_modified_time('c'), 'monthly', '0.7');
                }
                wp_reset_postdata();
            }

            // Profiles (Only Public)
            $users = get_users();
            foreach ($users as $user) {
                // Ensure they have the correct roles if needed, or simply check privacy mode
                $privacy = get_user_meta($user->ID, '_healthedia_privacy_mode', true);
                // default to public if not set for testing/legacy, or check explicit
                if (empty($privacy) || $privacy === 'public') {
                    $url = home_url('/' . $user->user_nicename);
                    $this->add_url($url, date('c'), 'weekly', '0.6');
                }
            }

            echo '</urlset>';
            exit;
        }
    }

    private function add_url($loc, $lastmod, $changefreq, $priority) {
        echo "\t<url>\n";
        echo "\t\t<loc>" . esc_url($loc) . "</loc>\n";
        echo "\t\t<lastmod>" . esc_html($lastmod) . "</lastmod>\n";
        echo "\t\t<changefreq>" . esc_html($changefreq) . "</changefreq>\n";
        echo "\t\t<priority>" . esc_html($priority) . "</priority>\n";
        echo "\t</url>\n";
    }

    public function add_sitemap_to_robots($output, $public) {
        $output .= "\n# Healthedia XML Sitemap & Directives\n";
        $output .= "Sitemap: " . home_url('/healthedia-sitemap.xml') . "\n";
        $output .= "Disallow: /portal/\n";
        $output .= "Disallow: /healthedia-admin/\n";
        $output .= "Disallow: /auth/\n";
        return $output;
    }
}
