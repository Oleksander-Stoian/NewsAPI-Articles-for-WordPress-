<?php
class NewsAPI_Frontend {
   

    public function register_post_type() {
        $labels = array(
            'name'          => __('News Articles', 'newsapi-plugin'),
            'singular_name' => __('News Article', 'newsapi-plugin'),
            'add_new_item'  => __('Add New Article', 'newsapi-plugin'),
            'edit_item'     => __('Edit News Article', 'newsapi-plugin'),
            'view_item'     => __('View News Article', 'newsapi-plugin'),
            'search_items'  => __('Search News Articles', 'newsapi-plugin'),
        );
        register_post_type('news_article', array(
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'rewrite'      => array('slug' => 'news'),  // nice URL slug
            'supports'     => array('title', 'editor', 'excerpt'), 
            'show_in_menu' => true,  // show in admin menu
            'menu_icon'    => 'dashicons-media-document'  // icon for admin menu
        ));
    }
	
	public function register_shortcode() {
        add_shortcode('newsapi_articles', array($this, 'render_articles'));
    }

    public function render_articles($atts = array(), $content = null) {
        // 1. Possibly fetch new articles if cache expired
        $cache_time = get_option('newsapi_cache_duration', 30);
        $last_fetch = get_transient('newsapi_last_fetch');
        if (!$last_fetch) {
            // Cache is expired or not set, fetch new data
            NewsAPI_API::fetch_articles();
        }

        // 2. Query the News Articles posts
        $posts_per_page = intval( get_option('newsapi_posts_per_page', 5) );
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $news_query = new WP_Query(array(
            'post_type'      => 'news_article',
            'posts_per_page' => $posts_per_page,
            'paged'          => $paged,
            'orderby'        => 'date',     // order by published date (we set post_date to article date)
            'order'          => 'DESC'
        ));

        // 3. Build the HTML output
        $output = '<div class="newsapi-articles">';
        if ($news_query->have_posts()) {
            while ($news_query->have_posts()) {
                $news_query->the_post();
                $post_id = get_the_ID();
                $title   = get_the_title();
                $link    = get_post_meta($post_id, 'newsapi_original_url', true);
                $source  = get_post_meta($post_id, 'newsapi_source', true);
                $date    = get_the_date(); // formatted date of the post
                $excerpt = get_the_excerpt();

                $output .= '<div class="news-article">';
                // Title linked to original source
                $output .= '<h2><a href="' . esc_url($link) . '" target="_blank" rel="noopener">' . esc_html($title) . '</a></h2>';
                // Meta info line
                if ($source) {
                    $output .= '<small>' . esc_html($date) . ' — ' . esc_html($source) . '</small>';
                } else {
                    $output .= '<small>' . esc_html($date) . '</small>';
                }
                // Article excerpt
                if (!empty($excerpt)) {
                    $output .= '<p>' . esc_html($excerpt) . '</p>';
                }
                $output .= '</div>'; // .news-article
            }

            // 4. Pagination links
            $total_pages = $news_query->max_num_pages;
            if ($total_pages > 1) {
                $output .= '<div class="newsapi-pagination">';
                $current_page = max(1, $paged);
                $output .= paginate_links(array(
                    'total'   => $total_pages,
                    'current' => $current_page,
                    'base'    => get_pagenum_link(1) . '%_%',   // base URL for page 1
                    'format'  => ( get_option('permalink_structure') ? '/page/%#%' : '&paged=%#%' ),
                    'prev_text' => '« Prev',
                    'next_text' => 'Next »'
                ));
                $output .= '</div>';
            }

            wp_reset_postdata();
        } else {
            $output .= '<p>No articles found for this topic.</p>';
        }
        $output .= '</div>'; // .newsapi-articles container

        return $output;
    }

}
