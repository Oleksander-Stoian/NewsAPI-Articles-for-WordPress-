<?php
class NewsAPI_API {
    public static function fetch_articles() {
        $apiKey = get_option('newsapi_api_key');
        $topic = get_option('newsapi_topic');

        if (empty($apiKey) || empty($topic)) {
            error_log("❌ API Key або Topic не задані. Вихід.");
            return;
        }

        $url = 'https://newsapi.org/v2/everything?' . http_build_query(array(
            'category'  => $topic,
            'apiKey'    => $apiKey,
            'pageSize'  => 20,
            'sortBy'    => 'publishedAt',
            'language'  => 'en'
        ));

        error_log("🌍 Запит до API: " . $url);

        // Fulfillment of the request
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'User-Agent: MyNewsPlugin/1.0 (https://yourwebsite.com)'
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        error_log("✅ API відповідь, HTTP Code: " . $httpCode);

        if ($httpCode != 200) {
            error_log("❌ Помилка API: отримано код " . $httpCode);
            return;
        }

        // Let's decode JSON
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("❌ ПОМИЛКА JSON: " . json_last_error_msg());
            return;
        }

        if (!isset($data['articles']) || !is_array($data['articles']) || empty($data['articles'])) {
            error_log("⚠️ Відповідь порожня або не існує.");
            return;
        }

        error_log("📰 Починаємо обробку " . count($data['articles']) . " статей.");

        foreach ($data['articles'] as $article) {
            if (!isset($article['title']) || !isset($article['url'])) {
                error_log("⚠️ Пропущена стаття через відсутність title або url.");
                continue;
            }

            $title   = sanitize_text_field($article['title']);
            $desc    = isset($article['description']) ? sanitize_text_field($article['description']) : '';
            $content = isset($article['content']) ? wp_kses_post($article['content']) : '';
            $url     = esc_url_raw($article['url']);
            $source  = isset($article['source']['name']) ? sanitize_text_field($article['source']['name']) : '';
            $date    = isset($article['publishedAt']) ? $article['publishedAt'] : null;

            error_log("📝 Додаємо статтю: " . $title);

            $exists = get_posts(array(
				'post_type'  => 'news_article',
				'title'      => $title, // Checking for a header
				'meta_key'   => 'newsapi_original_url',
				'meta_value' => $url,
				'fields'     => 'ids'
			));

			if (!empty($exists)) {
				error_log("⚠️ Стаття вже існує, пропускаємо: " . $title);
				continue;
			}


            // Adding an article
            $post_id = wp_insert_post(array(
                'post_title'    => $title,
                'post_content'  => $content ?: $desc,
                'post_type'     => 'news_article',
                'post_status'   => 'publish',
                'post_date'     => date('Y-m-d H:i:s'),
                'post_date_gmt' => gmdate('Y-m-d H:i:s')
            ));

            if (is_wp_error($post_id)) {
                error_log("❌ ПОМИЛКА wp_insert_post: " . $post_id->get_error_message());
            } else {
                error_log("✅ Стаття додана, ID: " . $post_id);
            }

           
            error_log("🆔 Post insert attempt: " . print_r($post_id, true));

            if (!is_wp_error($post_id) && $post_id) {
                // Add meta data
                error_log("📌 Додаємо мета-дані...");
                add_post_meta($post_id, 'newsapi_original_url', $url);
                if ($source) {
                    add_post_meta($post_id, 'newsapi_source', $source);
                }
                if ($date) {
                    add_post_meta($post_id, 'newsapi_published_at', $date);
                    $wpDate = date('Y-m-d H:i:s', strtotime($date));
                    wp_update_post(array('ID' => $post_id, 'post_date' => $wpDate));
                }
            }
        }

        error_log("✅ Усі статті оброблені.");

        // Update the cache
        $cacheMinutes = intval(get_option('newsapi_cache_duration', 30));
        set_transient('newsapi_last_fetch', time(), $cacheMinutes * 60);
    }
}
