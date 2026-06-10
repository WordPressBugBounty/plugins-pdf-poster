<?php
namespace PDFPro\Helper;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if (!class_exists('PDFPro\Helper\PDFP_Functions')) {
    class PDFP_Functions {

        protected static $meta = null;

        public static function i($array, $key1, $key2 = '', $default = false) {
            if (isset($array[$key1][$key2])) {
                return $array[$key1][$key2];
            } else if (isset($array[$key1])) {
                return $array[$key1];
            }
            return $default;
        }

        public static function isset($array, $key1, $default = false) {
            if (isset($array[$key1])) {
                return $array[$key1];
            }
            return $default;
        }

        public static function meta($id, $key, $default = null, $true = false) {
            $meta = metadata_exists('post', $id, '_fpdf') ? get_post_meta($id, '_fpdf', true) : '';
            if (isset($meta[$key]) && $meta != '') {
                if ($true == true) {
                    if ($meta[$key] == '1') {
                        return true;
                    } else if ($meta[$key] == '0') {
                        return false;
                    }
                } else {
                    return $meta[$key];
                }
            } else {
                return $default;
            }
        }

        /**
         * scrambel data removed (premium only)
         */
        public static function scramble__premium_only($do = 'encode', $data = '') {
            return $data;
        }

        /**
         * Detect Browser
         */
        public static function getBrowser() {
            $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
            $browser = "N/A";
            $browsers = array(
                '/msie/i' => 'Internet explorer',
                '/firefox/i' => 'Firefox',
                '/safari/i' => 'Safari',
                '/chrome/i' => 'Chrome',
                '/edge/i' => 'Edge',
                '/Edg/i' => 'Edge',
                '/opera/i' => 'Opera',
                '/mobile/i' => 'Mobile browser'
            );

            foreach ($browsers as $regex => $value) {
                if (preg_match($regex, $user_agent)) {
                    $browser = $value;
                }
            }

            return $browser;
        }

        public static function generate_pdf_poster_block($id) {

            if (!function_exists('pdfp__get_post_meta')) {
                return [
                    'blockName' => 'pdfp/pdfposter',
                ];
            }

            $meta = pdfp__get_post_meta($id, '_fpdf', true);

            $height = $meta('height', ['height' => 1122, 'unit' => 'px']);
            $width = $meta('width', ['width' => 100, 'unit' => '%']);
            $height_tablet = $meta('height_tablet', ['height' => 700, 'unit' => 'px']);
            $height_mobile = $meta('height_mobile', ['height' => 400, 'unit' => 'px']);
            $width_tablet = $meta('width_tablet', ['width' => 100, 'unit' => '%']);
            $width_mobile = $meta('width_mobile', ['width' => 100, 'unit' => '%']);

            $responsive_height = [
                'desktop' => $height['height'] . $height['unit'],
                'tablet' => $height_tablet['height'] . $height_tablet['unit'],
                'mobile' => $height_mobile['height'] . $height_mobile['unit'],
            ];

            $responsive_width = [
                'desktop' => $width['width'] . $width['unit'],
                'tablet' => $width_tablet['width'] . $width_tablet['unit'],
                'mobile' => $width_mobile['width'] . $width_mobile['unit'],
            ];

            $attrs = [
                'uniqueId' => wp_unique_id('pdf-poster'),
                'file' => $meta('source', ''),
                'title' => get_the_title($id),
                'height' => $responsive_height,
                'width' => $responsive_width,
                'print' => $meta('print', false, true),
                'showName' => $meta('show_filename', '1', true),
                'downloadButton' => $meta('show_download_btn', false, true),
                'downloadButtonText' => $meta('download_btn_text', 'Download File'),
                'fullscreenButton' => $meta('show_fullscreen_btn', '1', true),
                'fullscreenButtonText' => $meta('fullscreen_btn_text', 'View Fullscreen'),
                'newWindow' => $meta('new_window', false, true),
                'actionsPosition' => $meta('actions_position', 'top'),
                'protect' => $meta('protect', false, true),
            ];

            $popupBtnPadding = $meta('popup_btn_padding', ["top" => 10, "right" => 20, "bottom" => 10, "left" => 20]);
            $attrs['btnStyles'] = [
                "background" => $meta('popup_btn_bg', '#1e73be'),
                "color" => $meta('popup_btn_color', '#ffffff'),
                "fontSize" => $meta('popup_btn_font_size', 1) . 'rem',
                "padding" => $popupBtnPadding
            ];

            $attrs['socialShare'] = [
                'enabled' => $meta('social_share', false, true),
                'facebook' => $meta('social_share_facebook', true, true),
                'twitter' => $meta('social_share_twitter', true, true),
                'linkedin' => $meta('social_share_linkedin', true, true),
                'pinterest' => $meta('social_share_pinterest', true, true),
                'position' => $meta('social_share_position', 'top', false),
            ];

            return [
                "blockName" => "pdfp/pdfposter",
                "attrs" => $attrs
            ];
        }

        public function isUnsupportedDevice() {
            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

            // Detect iPad
            $isIPad = stripos($userAgent, 'iPad') !== false;

            // Detect iPhone 6
            $isIPhone6 = stripos($userAgent, 'iPhone') !== false &&
                isset($_SERVER['HTTP_USER_AGENT']) &&
                preg_match('/iPhone OS [0-10]\/', $userAgent) && // Adjust for iOS versions
                stripos($userAgent, '375x667') !== false;

            if ($isIPad) {
                return true;
            } elseif ($isIPhone6) {
                return true;
            } else {
                return false;
            }
        }

        public static function pdfp_pro_title($title, $badge = 'PRO') {

            if ($badge == 'New') {
                return '
                <div class="pdfp-field-title">
                    <h4>' . esc_html($title) . '</h4>
                    <span class="pdfp-new-badge">' . esc_html($badge) . '</span>
                </div>
            ';
            } else {
                return '
                <div class="pdfp-field-title">
                    <h4>' . esc_html($title) . '</h4>
                    <span class="pdfp-pro-badge">' . esc_html($badge) . '</span>
                </div>
            ';
            }
        }

        public static function pdfp_new_badge($label = 'NEW') {
            return '<span class="pdfp-new-badge">' . esc_html($label) . '</span>';
        }

        public static function pdfp_lock_field($field, $is_section = false) {

            // Lock the UI
            $field['class'] = 'pdfp-lock-field ' . ($is_section ? 'section' : '');


            // Force safe default (prevents DB pollution)
            if (isset($field['default'])) {
                $field['value'] = $field['default'];
            }

            return $field;
        }

        public static function upgrade_section() {
            return array(
                'type' => 'content',
                'content' => '<div class="pdfp-metabox-upgrade-section">' . esc_html__('The Ultimate PDF Embedder Plugin for WordPress, Loved by Over 20,000+ Users.', 'pdf-poster') . ' <a class="button button-bplugins" href="' . esc_url(admin_url('admin.php?page=pdf-poster-pricing')) . '">' . esc_html__('Upgrade to PRO', 'pdf-poster') . '</a></div>'
            );
        }

        public static function pro_feature_list($features) {
            $html = '
        <style>
            .pdfp-pro-showcase {
                background: linear-gradient(145deg, #ffffff, #f0f7ff);
                border: 1px solid #e1e8f0;
                border-radius: 16px;
                padding: 32px;
                margin-top: 24px;
                box-shadow: 0 10px 30px rgba(20, 110, 245, 0.05);
                position: relative;
                overflow: hidden;
            }
            .pdfp-pro-showcase::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: linear-gradient(to bottom, #146ef5, #00d2ff);
            }
            .pdfp-pro-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 24px;
            }
            .pdfp-pro-badge {
                background: #eef5ff;
                color: #146ef5;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                padding: 4px 12px;
                border-radius: 20px;
                letter-spacing: 1px;
            }
            .pdfp-pro-showcase h4 {
                font-size: 18px;
                font-weight: 700;
                color: #001737;
                margin: 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            }
            .pdfp-pro-list {
                list-style: none !important;
                padding: 0 !important;
                margin: 0 0 32px 0 !important;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 16px;
            }
            .pdfp-pro-item {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                font-size: 14px;
                color: #3e5569;
                line-height: 1.5;
                margin: 0 !important;
            }
            .pdfp-pro-icon {
                background: #f0f7ff;
                color: #146ef5;
                width: 24px;
                height: 24px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                margin-top: 2px;
            }
            .pdfp-pro-icon svg {
                width: 14px;
                height: 14px;
            }
            .pdfp-pro-footer {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 20px;
                border-top: 1px solid #edf2f9;
                padding-top: 24px;
            }
            .pdfp-upgrade-btn {
                background: linear-gradient(90deg, #146ef5, #00d2ff);
                color: #fff !important;
                border: none;
                padding: 12px 28px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                transition: transform 0.2s, box-shadow 0.2s;
                box-shadow: 0 4px 15px rgba(20, 110, 245, 0.2);
            }
            .pdfp-upgrade-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(20, 110, 245, 0.3);
                color: #fff !important;
            }
            .pdfp-pro-hint {
                font-size: 13px;
                color: #8492a6;
                font-style: italic;
            }
        </style>
        <div class="pdfp-pro-showcase">
            <div class="pdfp-pro-header">
                <h4>' . __('Unlock Premium Experience', 'pdf-poster') . '</h4>
                <span class="pdfp-pro-badge">' . __('PRO ONLY', 'pdf-poster') . '</span>
            </div>
            <ul class="pdfp-pro-list">';
            foreach ($features as $feature) {
                $html .= '<li class="pdfp-pro-item">
                    <div class="pdfp-pro-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    </div>
                    <span>' . esc_html($feature) . '</span>
                </li>';
            }
            $html .= '</ul>
            <div class="pdfp-pro-footer">
                <a href="' . esc_url(admin_url('admin.php?page=pdf-poster-pricing')) . '" class="pdfp-upgrade-btn">
                    <span>' . __('Get Premium Now', 'pdf-poster') . '</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
                <span class="pdfp-pro-hint">' . __('Trusted by over 20,000+ WordPress sites.', 'pdf-poster') . '</span>
            </div>
        </div>';

            return array(
                'type' => 'content',
                'content' => $html
            );
        }

        public static function quick_embed_shortcode() {
            return [
                'type' => 'content',
                'content' => '
                <div class="pdfp-quick-embed-shortcode-wrapper">
                    <div class="shortcode-container">
                        <code id="pdfp-shortcode-text">[pdf_embed url="your_file_url"]</code>
                        <button type="button" class="pdfp-copy-shortcode" data-shortcode=\'[pdf_embed url="your_file_url"]\'>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="copy-icon"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                            <span class="copy-text">' . __('Copy', 'pdf-poster') . '</span>
                        </button>
                    </div>
                    <p class="description">' . __('Copy and paste this shortcode into any page or post. Replace <code>your_file_url</code> with your actual PDF link.', 'pdf-poster') . '</p>
                </div>
            '
            ];
        }

        public static function upcoming_section() {
            return array(
                'type' => 'content',
                'content' => '<div class="pdfp-metabox-upcoming-section">' . esc_html__('This feature is coming soon. Stay tuned for updates!', 'pdf-poster') . '</div>'
            );
        }

        public static function pdfp_preset($key, $default = false) {
            $settings = get_option('fpdf_option');
            return $settings[$key] ?? $default;
        }
    }
}