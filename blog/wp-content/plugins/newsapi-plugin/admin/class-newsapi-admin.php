<?php
class NewsAPI_Admin {
    public function __construct() {
        // Add the settings page to the admin panel
        add_action('admin_menu', array($this, 'add_settings_page'));
        // Register your settings
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_options_page(
            'NewsAPI Settings', // Page title
            'NewsAPI Settings', // Name in the menu
            'manage_options',   // Access for admins only
            'newsapi-settings', // Unique page slug
            array($this, 'render_settings_page') // Page rendering function
        );
    }

    public function register_settings() {
        register_setting('newsapi_options_group', 'newsapi_api_key');
        register_setting('newsapi_options_group', 'newsapi_topic');
        register_setting('newsapi_options_group', 'newsapi_posts_per_page');
        register_setting('newsapi_options_group', 'newsapi_cache_duration');

        add_settings_section('newsapi_section', 'NewsAPI Configuration', null, 'newsapi-settings');

        add_settings_field('newsapi_api_key', 'API Key', array($this, 'api_key_field_html'), 'newsapi-settings', 'newsapi_section');
        add_settings_field('newsapi_topic', 'Article Topic', array($this, 'topic_field_html'), 'newsapi-settings', 'newsapi_section');
        add_settings_field('newsapi_posts_per_page', 'Articles Per Page', array($this, 'per_page_field_html'), 'newsapi-settings', 'newsapi_section');
        add_settings_field('newsapi_cache_duration', 'Cache Duration (minutes)', array($this, 'cache_field_html'), 'newsapi-settings', 'newsapi_section');
    }

    // Input fields for each setting:
    public function api_key_field_html() {
        $apiKey = esc_attr(get_option('newsapi_api_key'));
        echo "<input type='text' name='newsapi_api_key' value='{$apiKey}' class='regular-text' />";
    }

    public function topic_field_html() {
        $topic = esc_attr(get_option('newsapi_topic'));
        echo "<input type='text' name='newsapi_topic' value='{$topic}' class='regular-text' placeholder='e.g., technology' />";
    }

    public function per_page_field_html() {
        $pp = intval(get_option('newsapi_posts_per_page', 5));
        echo "<input type='number' name='newsapi_posts_per_page' value='{$pp}' min='1' max='100' />";
    }

    public function cache_field_html() {
        $cache = intval(get_option('newsapi_cache_duration', 30));
        echo "<input type='number' name='newsapi_cache_duration' value='{$cache}' min='1' /> <small>Recommended: 30</small>";
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) return;

        // Prevent duplication of form output:
        if (get_transient('newsapi_settings_page_rendered')) {
            return;
        }
        set_transient('newsapi_settings_page_rendered', true, 5); // 5 second cache to prevent duplication

        // Clearing the cache if the Delete cache button is pressed"
        if (isset($_POST['clear_cache'])) {
            delete_transient('newsapi_last_fetch'); //  Delete the cache
            echo '<div class="updated"><p>✅ Cache successfully cleared!</p></div>';
        }

        echo '<div class="wrap"><h1>NewsAPI Settings</h1>
              <form method="post" action="options.php">';
        settings_fields('newsapi_options_group');
        do_settings_sections('newsapi-settings');
        submit_button('Save Settings');
        echo '</form>';

        // Button “Delete cache”
        echo '<form method="post" action="">
                <input type="submit" name="clear_cache" class="button-secondary" value="🗑 Delete cache" />
              </form>';
        echo '</div>';
    }
}
?>
