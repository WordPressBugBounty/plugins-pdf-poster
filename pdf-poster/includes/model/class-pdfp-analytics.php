<?php

namespace PDFPro\Model;

use PDFPro\Helper\PDFP_Functions as Utils;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PDFPro\Model\PDFP_Analytics')) {

    class PDFP_Analytics
    {
        /**
         * All-time totals for one document.
         */
        public function totals($data = array())
        {
            global $wpdb;

            $doc = isset($data['doc']) ? sanitize_text_field($data['doc']) : '';
            $post = isset($data['post']) ? (int) $data['post'] : 0;
            $file = isset($data['file']) ? esc_url_raw($data['file']) : '';

            if ($doc === '' && $post > 0) {
                $doc = 'p:' . $post;
            }

            if ($doc === '' && $file !== '') {
                $doc = Utils::pdfp_doc_key($file, 0);
            }

            if (!Utils::pdfp_is_doc_key($doc)) {
                return new \WP_REST_Response(
                    array('error' => 'invalid document'),
                    400
                );
            }

            // SECURITY FIX: Verify permission for saved documents.
            $post_id = Utils::pdfp_doc_post_id($doc);

            if ($post_id && !current_user_can('edit_post', $post_id)) {
                return new \WP_REST_Response(
                    array('error' => 'forbidden'),
                    403
                );
            }

            $table = Utils::pdfp_stats_table();

            $today = Utils::pdfp_doc_today($doc);

            $payload = array(
                'doc' => $doc,
                'pro' => Utils::pdfp_analytics_available(),
                'today' => $today,
                'firstDay' => Utils::pdfp_first_day($doc),
                'countingSince' => Utils::pdfp_counting_since_text($doc),
                'reportUrl' => admin_url(
                    'edit.php?post_type=pdfposter&page=pdf-poster-analytics'
                ),
                'reportDocUrl' => add_query_arg(
                    'doc',
                    $doc,
                    admin_url('edit.php?post_type=pdfposter&page=pdf-poster-analytics')
                ),
                'tracking' => Utils::pdfp_tracking_enabled(),
                'excludeEditors' => Utils::pdfp_excluding_editors(),
            );

            if (!$payload['pro']) {
                $payload['history'] = Utils::pdfp_recorded_history($doc);

                return new \WP_REST_Response($payload, 200);
            }

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT SUM(views) AS views,
                            SUM(downloads) AS downloads,
                            SUM(prints) AS prints,
                            MAX(day) AS last_day,
                            MAX(last_seen) AS last_seen
                     FROM {$table}
                     WHERE doc_id = %s",
                    $doc
                ),
                ARRAY_A
            );

            $spark = Utils::pdfp_doc_series($doc, 14);

            $half = (int) floor(count($spark) / 2);
            $recent = array_sum(array_slice($spark, $half));
            $earlier = array_sum(array_slice($spark, 0, $half));

            return new \WP_REST_Response(
                array_merge($payload, array(
                    'views' => isset($row['views']) ? (int) $row['views'] : 0,
                    'downloads' => isset($row['downloads']) ? (int) $row['downloads'] : 0,
                    'prints' => isset($row['prints']) ? (int) $row['prints'] : 0,
                    'lastDay' => isset($row['last_day']) ? $row['last_day'] : null,
                    'lastSeen' => isset($row['last_seen']) ? $row['last_seen'] : null,
                    'spark' => $spark,
                    'sparkDays' => 14,
                    'trend' => $earlier > 0
                        ? (int) round((($recent - $earlier) / $earlier) * 100)
                        : null,
                )),
                200
            );
        }

        /**
         * Get available documents.
         */
        public function documents($data = array())
        {
            global $wpdb;

            $posters = array();
            $embeds = array();

            $posts = get_posts(array(
                'post_type' => 'pdfposter',
                'post_status' => array(
                    'publish',
                    'draft',
                    'pending',
                    'private',
                    'future'
                ),
                'numberposts' => 500,
                'orderby' => 'title',
                'order' => 'ASC',
                'suppress_filters' => false,
            ));

            foreach ($posts as $post) {

                // Existing permission check.
                if (!current_user_can('edit_post', $post->ID)) {
                    continue;
                }

                $title = $this->plain_title($post);

                $posters[] = array(
                    'doc' => 'p:' . (int) $post->ID,
                    'title' => $title !== '' ? $title : sprintf(
                        __('(no title) · %d', 'pdf-poster'),
                        (int) $post->ID
                    ),
                );
            }

            $table = Utils::pdfp_stats_table();

            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $seen = $wpdb->get_col(
                "SELECT DISTINCT doc_id
                 FROM {$table}
                 WHERE doc_id LIKE 'u:%'
                 LIMIT 500"
            );

            $keys = array_unique(
                array_merge(
                    (array) $seen,
                    array_keys(Utils::pdfp_embed_origin_map())
                )
            );

            foreach ($keys as $key) {

                if (strpos((string) $key, 'u:') !== 0) {
                    continue;
                }

                $origin = $this->origin_of(
                    $this->origin_for_doc($key),
                    $key
                );

                // SECURITY FIX: Do not expose embeds with inaccessible origins.
                if (empty($origin)) {
                    continue;
                }

                $hash = substr((string) $key, 2, 6);

                $embeds[] = array(
                    'doc' => $key,
                    'title' => !empty($origin['title'])
                        ? sprintf(
                            __('%1$s · %2$s', 'pdf-poster'),
                            $origin['title'],
                            $hash
                        )
                        : sprintf(
                            __('Embedded file · %s', 'pdf-poster'),
                            $hash
                        ),
                );
            }

            usort($embeds, function ($a, $b) {
                return strcasecmp($a['title'], $b['title']);
            });

            return new \WP_REST_Response(array(
                'posters' => $posters,
                'embeds' => $embeds,
            ), 200);
        }

        /**
         * Today's analytics.
         */
        public function today($data = array())
        {
            global $wpdb;

            $table = Utils::pdfp_stats_table();
            $doc = $this->resolve_doc($data);
            $day = current_time('Y-m-d');

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT doc_id,
                            MAX(post_id) AS post_id,
                            MAX(origin_post_id) AS origin_post_id,
                            SUM(views) AS views,
                            SUM(downloads) AS downloads
                     FROM {$table}
                     WHERE day = %s" . ($doc ? " AND doc_id = %s" : "") . "
                     GROUP BY doc_id
                     ORDER BY views DESC, downloads DESC
                     LIMIT 20",
                    $doc ? array($day, $doc) : array($day)
                ),
                ARRAY_A
            );

            $views = 0;
            $downloads = 0;
            $docs = array();

            foreach ((array) $rows as $row) {

                $post_id = (int) $row['post_id'];

                // SECURITY FIX: Check permission before counting analytics.
                if ($post_id && !current_user_can('edit_post', $post_id)) {
                    continue;
                }

                // SECURITY FIX: Check embedded document origin.
                if (!$post_id) {
                    $origin_post_id = (int) $row['origin_post_id'];

                    if (
                        !$origin_post_id ||
                        !current_user_can('edit_post', $origin_post_id)
                    ) {
                        continue;
                    }
                }

                // Count only authorized documents.
                $views += (int) $row['views'];
                $downloads += (int) $row['downloads'];

                $title = $post_id ? $this->plain_title($post_id) : '';
                $origin = array();

                if ($title === '') {
                    $title = sprintf(
                        __('Embedded file · %s', 'pdf-poster'),
                        substr((string) $row['doc_id'], 2, 6)
                    );

                    $origin = $this->origin_of(
                        (int) $row['origin_post_id'],
                        $row['doc_id']
                    );
                }

                $docs[] = array(
                    'doc' => $row['doc_id'],
                    'postId' => $post_id,
                    'title' => $title,
                    'editUrl' => $post_id
                        ? get_edit_post_link($post_id, 'raw')
                        : '',
                    'isEmbed' => $post_id === 0,
                    'origin' => $origin,
                    'views' => (int) $row['views'],
                    'downloads' => (int) $row['downloads'],
                );
            }

            $pro = Utils::pdfp_analytics_available();

            $payload = array(
                'day' => $day,
                'pro' => $pro,
                'doc' => $doc,
                'docTitle' => $doc ? $this->doc_title($doc) : null,
                'totals' => array(
                    'views' => $views,
                    'downloads' => $downloads
                ),
                'docs' => $docs,
                'docCount' => $this->window_doc_count($day, $day, $doc),
                'firstDay' => Utils::pdfp_first_day($doc),
                'countingSince' => Utils::pdfp_counting_since_text($doc),
                'tracking' => Utils::pdfp_tracking_enabled(),
                'excludeEditors' => Utils::pdfp_excluding_editors(),
            );

            if (!$pro) {
                $payload['history'] = Utils::pdfp_recorded_history($doc);
            }

            return new \WP_REST_Response($payload, 200);
        }

        /**
         * Analytics summary.
         *
         * Site-wide data access is protected by the REST endpoint's
         * Administrator-only permission callback.
         */
        public function summary($data = array())
        {
            if (!Utils::pdfp_analytics_available()) {
                return new \WP_REST_Response(
                    array('error' => 'pro_required'),
                    403
                );
            }

            global $wpdb;

            $table = Utils::pdfp_stats_table();

            list($from, $to, $days) = $this->resolve_range($data);
            $doc = $this->resolve_doc($data);

            $prev_to = gmdate(
                'Y-m-d',
                strtotime($from . ' -1 day')
            );

            $prev_from = gmdate(
                'Y-m-d',
                strtotime($prev_to . ' -' . ($days - 1) . ' day')
            );

            $totals = $this->window_totals($from, $to, $doc);
            $previous = $this->window_totals($prev_from, $prev_to, $doc);

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT day,
                            SUM(views) AS views,
                            SUM(downloads) AS downloads
                     FROM {$table}
                     WHERE day BETWEEN %s AND %s" . ($doc ? " AND doc_id = %s" : "") . "
                     GROUP BY day
                     ORDER BY day ASC",
                    $doc ? array($from, $to, $doc) : array($from, $to)
                ),
                ARRAY_A
            );

            $by_day = array();

            foreach ((array) $rows as $row) {
                $by_day[$row['day']] = array(
                    'views' => (int) $row['views'],
                    'downloads' => (int) $row['downloads']
                );
            }

            $series = array();

            for ($i = 0; $i < $days; $i++) {
                $day = gmdate(
                    'Y-m-d',
                    strtotime($from . ' +' . $i . ' day')
                );

                $series[] = array(
                    'day' => $day,
                    'views' => isset($by_day[$day])
                        ? $by_day[$day]['views']
                        : 0,
                    'downloads' => isset($by_day[$day])
                        ? $by_day[$day]['downloads']
                        : 0,
                );
            }

            return new \WP_REST_Response(array(
                'range' => array(
                    'from' => $from,
                    'to' => $to,
                    'days' => $days
                ),
                'firstDay' => Utils::pdfp_first_day($doc),
                'totals' => $totals,
                'previous' => array(
                    'views' => $previous['views'],
                    'downloads' => $previous['downloads']
                ),
                'series' => $series,
                'docs' => $this->window_docs($from, $to, 20, $doc),
                'docCount' => $this->window_doc_count($from, $to, $doc),
                'doc' => $doc,
                'docTitle' => $doc ? $this->doc_title($doc) : null,
                'docEditUrl' => ($doc && Utils::pdfp_doc_post_id($doc))
                    ? get_edit_post_link(Utils::pdfp_doc_post_id($doc), 'raw')
                    : null,
                'docOrigin' => ($doc && !Utils::pdfp_doc_post_id($doc))
                    ? $this->origin_of($this->origin_for_doc($doc), $doc)
                    : array(),
                'allUrl' => admin_url(
                    'edit.php?post_type=pdfposter&page=pdf-poster-analytics'
                ),
                'tracking' => Utils::pdfp_tracking_enabled(),
                'excludeEditors' => Utils::pdfp_excluding_editors(),
            ), 200);
        }

        /**
         * Export analytics as CSV.
         */
        public function export($data = array())
        {
            if (!Utils::pdfp_analytics_available()) {
                return new \WP_REST_Response(
                    array('error' => 'pro_required'),
                    403
                );
            }

            list($from, $to, $days) = $this->resolve_range($data);
            $filter = $this->resolve_doc($data);

            $lines = array();

            $lines[] = $this->csv_row(array(
                'Document',
                'Document key',
                'Views',
                'Downloads',
                'Download rate'
            ));

            foreach ($this->window_docs($from, $to, 500, $filter) as $doc) {
                $lines[] = $this->csv_row(array(
                    $doc['title'],
                    $doc['doc'],
                    $doc['views'],
                    $doc['downloads'],
                    $doc['views'] > 0
                    ? round(($doc['downloads'] / $doc['views']) * 100, 1) . '%'
                    : '',
                ));
            }

            return new \WP_REST_Response(array(
                'filename' => 'pdf-poster-insights-' . $from . '-to-' . $to . '.csv',
                'csv' => implode("\r\n", $lines),
            ), 200);
        }

        /** CSV row formatting. */
        protected function csv_row($cells)
        {
            $out = array();

            foreach ($cells as $cell) {
                $out[] = '"' . str_replace('"', '""', (string) $cell) . '"';
            }

            return implode(',', $out);
        }

        /** Resolve date range. */
        protected function resolve_range($data)
        {
            $today = current_time('Y-m-d');

            $from_raw = isset($data['from'])
                ? sanitize_text_field($data['from'])
                : '';

            $to_raw = isset($data['to'])
                ? sanitize_text_field($data['to'])
                : '';

            $valid = function ($value) {
                return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)
                    && strtotime($value);
            };

            if ($valid($from_raw) && $valid($to_raw)) {

                $from = $from_raw;
                $to = $to_raw;

                if (strtotime($from) > strtotime($to)) {
                    list($from, $to) = array($to, $from);
                }

            } else {

                $days = isset($data['days'])
                    ? (int) $data['days']
                    : 30;

                if (!in_array($days, array(1, 7, 30, 90), true)) {
                    $days = 30;
                }

                $to = $today;

                $from = gmdate(
                    'Y-m-d',
                    strtotime($to . ' -' . ($days - 1) . ' day')
                );
            }

            $span = (int) round(
                (strtotime($to) - strtotime($from)) / DAY_IN_SECONDS
            ) + 1;

            if ($span > 366) {
                $span = 366;
                $from = gmdate('Y-m-d', strtotime($to . ' -365 day'));
            }

            if ($span < 1) {
                $span = 1;
            }

            return array($from, $to, $span);
        }

        /** Resolve document key. */
        protected function resolve_doc($data)
        {
            $doc = isset($data['doc'])
                ? sanitize_text_field($data['doc'])
                : '';

            return Utils::pdfp_is_doc_key($doc) ? $doc : '';
        }

        /**
         * Get embedded document origin.
         */
        protected function origin_of($origin_post_id, $doc = '')
        {
            $origin_post_id = (int) $origin_post_id;

            $post = $origin_post_id > 0
                ? get_post($origin_post_id)
                : null;

            if ((!$post || $post->post_status === 'trash') && $doc !== '') {
                $map = Utils::pdfp_embed_origin_map();

                if (isset($map[$doc])) {
                    $origin_post_id = (int) $map[$doc];
                    $post = get_post($origin_post_id);
                }
            }

            if (!$post || $post->post_status === 'trash') {
                return array();
            }

            // SECURITY FIX: Verify permission before exposing origin details.
            if (!current_user_can('edit_post', $post->ID)) {
                return array();
            }

            $origin_post_id = (int) $post->ID;

            $title = $this->plain_title($post);

            $post_type_object = get_post_type_object($post->post_type);

            return array(
                'id' => $origin_post_id,
                'title' => $title !== ''
                    ? $title
                    : __('(no title)', 'pdf-poster'),
                'type' => $post_type_object
                    ? $post_type_object->labels->singular_name
                    : $post->post_type,
                'editUrl' => (string) get_edit_post_link($origin_post_id, 'raw'),
                'viewUrl' => (string) get_permalink($origin_post_id),
            );
        }

        /** Plain document title. */
        protected function plain_title($post)
        {
            $title = get_the_title($post);

            return html_entity_decode(
                (string) $title,
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            );
        }

        /** Get document title. */
        protected function doc_title($doc)
        {
            $post_id = Utils::pdfp_doc_post_id($doc);

            if ($post_id && !current_user_can('edit_post', $post_id)) {
                return '';
            }

            $title = $post_id
                ? $this->plain_title($post_id)
                : '';

            if ($title !== '') {
                return $title;
            }

            return sprintf(
                __('Embedded file · %s', 'pdf-poster'),
                substr((string) $doc, 2, 6)
            );
        }

        /** Window totals. */
        protected function window_totals($from, $to, $doc = '')
        {
            global $wpdb;

            $table = Utils::pdfp_stats_table();

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $row = $wpdb->get_row(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT SUM(views) AS views,
                            SUM(downloads) AS downloads,
                            SUM(prints) AS prints
                     FROM {$table}
                     WHERE day BETWEEN %s AND %s" . ($doc ? " AND doc_id = %s" : ""),
                    $doc ? array($from, $to, $doc) : array($from, $to)
                ),
                ARRAY_A
            );

            return array(
                'views' => isset($row['views']) ? (int) $row['views'] : 0,
                'downloads' => isset($row['downloads']) ? (int) $row['downloads'] : 0,
                'prints' => isset($row['prints']) ? (int) $row['prints'] : 0,
            );
        }

        /** Ranked documents. */
        protected function window_docs($from, $to, $limit = 20, $doc = '')
        {
            global $wpdb;

            $table = Utils::pdfp_stats_table();

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT doc_id,
                            MAX(post_id) AS post_id,
                            MAX(origin_post_id) AS origin_post_id,
                            SUM(views) AS views,
                            SUM(downloads) AS downloads
                     FROM {$table}
                     WHERE day BETWEEN %s AND %s" . ($doc ? " AND doc_id = %s" : "") . "
                     GROUP BY doc_id
                     ORDER BY views DESC, downloads DESC
                     LIMIT %d",
                    $doc
                    ? array($from, $to, $doc, (int) $limit)
                    : array($from, $to, (int) $limit)
                ),
                ARRAY_A
            );

            $out = array();

            foreach ((array) $rows as $row) {

                $post_id = (int) $row['post_id'];

                // SECURITY FIX: Do not return unauthorized saved documents.
                if ($post_id && !current_user_can('edit_post', $post_id)) {
                    continue;
                }

                $title = $post_id
                    ? $this->plain_title($post_id)
                    : '';

                $origin = array();

                if ($title === '') {

                    $origin = $this->origin_of(
                        (int) $row['origin_post_id'],
                        $row['doc_id']
                    );

                    // SECURITY FIX: Do not expose inaccessible embedded documents.
                    if (empty($origin)) {
                        continue;
                    }

                    $title = sprintf(
                        __('Embedded file · %s', 'pdf-poster'),
                        substr((string) $row['doc_id'], 2, 6)
                    );
                }

                $out[] = array(
                    'doc' => $row['doc_id'],
                    'postId' => $post_id,
                    'title' => $title,
                    'editUrl' => $post_id
                        ? get_edit_post_link($post_id, 'raw')
                        : '',
                    'isEmbed' => $post_id === 0,
                    'origin' => $origin,
                    'views' => (int) $row['views'],
                    'downloads' => (int) $row['downloads'],
                );
            }

            return $out;
        }

        /** Get most recent origin. */
        protected function origin_for_doc($doc)
        {
            global $wpdb;

            $table = Utils::pdfp_stats_table();

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            return (int) $wpdb->get_var(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT MAX(origin_post_id)
                     FROM {$table}
                     WHERE doc_id = %s",
                    $doc
                )
            );
        }

        /** Count distinct documents. */
        protected function window_doc_count($from, $to, $doc = '')
        {
            global $wpdb;

            $table = Utils::pdfp_stats_table();

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            return (int) $wpdb->get_var(
                $wpdb->prepare(
                    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    "SELECT COUNT(DISTINCT doc_id)
                     FROM {$table}
                     WHERE day BETWEEN %s AND %s" . ($doc ? " AND doc_id = %s" : ""),
                    $doc ? array($from, $to, $doc) : array($from, $to)
                )
            );
        }
    }
}
