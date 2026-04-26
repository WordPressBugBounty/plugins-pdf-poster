<?php 
namespace PDFPro\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

class Functions{

    protected static $meta = null;

    public static function i($array, $key1, $key2 = '', $default = false){
        if(isset($array[$key1][$key2])){
            return $array[$key1][$key2];
        }else if (isset($array[$key1])){
            return $array[$key1];
        }
        return $default;
    }

    public static function isset($array, $key1, $default = false){
        if (isset($array[$key1])){
            return $array[$key1];
        }
        return $default;
    }

    public static function meta($id, $key, $default = null, $true = false){
        $meta = metadata_exists( 'post', $id, '_fpdf' ) ? get_post_meta($id, '_fpdf', true) : '';
        if(isset($meta[$key]) && $meta != ''){
            if($true == true){
                if($meta[$key] == '1'){
                    return true;
                }else if($meta[$key] == '0'){
                    return false;
                }
            }else {
                return $meta[$key];
            }
        }else {
            return $default;
        }
    }

    /**
       * scrambel data ( password and video file if it is protected)
       */
    public static function scramble($do = 'encode', $data = ''){
        $originalKey = 'abcdefghijklmnopqrstuvwxyz1234567890';
		$key = 'z1ntg4ihmwj5cr09byx8spl7ak6vo2q3eduf';
		$resultData = '';
		if($do == 'encode'){
			if($data != ''){
				$length = strlen($data);
				for($i = 0; $i < $length; $i++){
					$position = strpos($originalKey, $data[$i]);
					if($position !== false){
						$resultData .= $key[$position];
					}else {
						$resultData .= $data[$i];
					}
				}
			}
		}

		if($do == 'decode'){
			if($data != ''){
				$length = strlen($data);
				for($i = 0; $i < $length; $i++){
					$position = strpos($key, $data[$i]);
					if($position !== false){
						$resultData .= $originalKey[$position];
					}else {
						$resultData .= $data[$i];
					}
				}
			}
		}

		return $resultData;
    }

    /**
     * Detect Browser
     */
    public static function getBrowser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
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
            if (preg_match($regex, $user_agent)) { $browser = $value; }
        }
        
        return $browser;
    }

    public static function generate_pdf_poster_block($id){

        if(!function_exists('pdfp__get_post_meta')) {
            return [
                'blockName' => 'pdfp/pdfposter',
            ];
        }

        $meta = pdfp__get_post_meta($id, '_fpdf', true);
    
        $height = $meta('height', ['height' => 1122, 'unit' => 'px']);
        $width = $meta('width', ['width' => 100, 'unit' => '%']);
        $popup_image_height = $meta('popup_image_height', ['height' => 200, 'unit' => 'px']);
        $popup_image_width = $meta('popup_image_width', ['width' => 300, 'unit' => 'px']);
        $popupBtnPadding = $meta('popup_btn_padding', [ "top"=> 10, "right"=> 20, "bottom"=> 10, "left"=> 10 ]);
        $popup_image = $meta('popup_image', []);
        $popup_image = $popup_image['url'] ?? '';
    
        return [
          "blockName" => "pdfp/pdfposter",
          "attrs" => [
            'uniqueId' => wp_unique_id( 'pdfp' ),
            'file' => $meta('source', ''),
            'title' => get_the_title( $id ),
             'height' => $height['height'].$height['unit'],
             'width' => $width['width'].$width['unit'],
             'print' => $meta('print', false, true),
             'fullscreenButton' => $meta('view_fullscreen_btn', '1', true),
             'fullscreenButtonText' => $meta('fullscreen_btn_text', 'View Fullscreen', false),
             'newWindow' => $meta('view_fullscreen_btn_target_blank', false, true),
             'showName' => $meta('show_filename', '1', true),
             'downloadButton' => $meta('show_download_btn', false, true),
             'downloadButtonText' => $meta('download_btn_text', 'Download File', false),
             'protect' => $meta('protect', false, true) ,
             'onlyPDF' => $meta('only_pdf', false, true),
             'defaultBrowser' => $meta('default_browser', false, true),
             'thumbMenu' => $meta('thumbnail_toggle_menu', false, true),
             'initialPage' => $meta('jump_to', 0, false),
             'sidebarOpen' => $meta('sidebar_open', false, true),
             'lastVersion' => $meta('ppv_load_last_version', false, true),
             'hrScroll' => $meta('hr_scroll', 0, true),
             'zoomLevel' => $meta('zoomLevel', null, false),
             'alert' => ! $meta('disable_alert', true, true),
             'btnStyles' => [
                "background" =>   $meta('popup_btn_bg', '#1e73be'),
                "color" =>   $meta('popup_btn_color', '#fff'),
                "fontSize" =>   $meta('popup_btn_font_size', 1).'rem',
                "padding" =>  $popupBtnPadding
             ],
             "popupOptions" => [
                "enabled" =>  $meta('popup', 0, true),
                "text" =>  $meta('popup_btn_text', 'Open PDF'),
                "triggerType" =>  $meta('popup_trigger_type', 'button'),
                "image" =>  $popup_image,
                "imageHeight" =>  $popup_image_height['height'].$popup_image_height['unit'],
                "imageWidth" =>  $popup_image_width['width'].$popup_image_width['unit'],
                "imagePdfIcon" =>  $meta('popup_image_pdf_icon', true, true),
                "triggerAlignment" =>  $meta('popup_trigger_alignment', 'center'),
                "btnStyle" =>  [
                    "background" =>   $meta('popup_btn_bg', '#1e73be'),
                    "color" =>   $meta('popup_btn_color', '#fff'),
                    "fontSize" =>   $meta('popup_btn_font_size', 1).'rem',
                    "padding" =>  $popupBtnPadding
                ]
            ],
            "actionsPosition" => $meta('actions_position', 'top', false),
            'socialShare' => [
                'enabled' => $meta('social_share', false, true),
                'facebook' => $meta('social_share_facebook', false, true),
                'twitter' => $meta('social_share_twitter', false, true),
                'linkedin' => $meta('social_share_linkedin', false, true),
                'pinterest' => $meta('social_share_pinterest', false, true),
                'position' => $meta('social_share_position', 'top', false),
            ],
            'adobeEmbedder' => $meta('viewer', 'default', false) === 'adobe',
          ]
        ];
    }

    public function isUnsupportedDevice() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
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

        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public static function pdfp_pro_title($title, $badge = 'PRO') {
        if (pdfp_fs()->can_use_premium_code()) {
            return esc_html($title);
        }

        return '
            <div class="pdfp-field-title">
                <h4>' . esc_html($title) . '</h4>
                <span class="pdfp-pro-badge">' . esc_html($badge) . '</span>
            </div>
        ';
    }

    public static function pdfp_lock_field($field, $is_section = false) {

        if (pdfp_fs()->can_use_premium_code()) {
            return $field;
        }

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
            'content' => '<div class="pdfp-metabox-upgrade-section">The Ultimate PDF Embedder Plugin for WordPress, Loved by Over 20,000+ Users. <a class="button button-bplugins" href="' . admin_url('admin.php?page=pdf-poster-pricing') . '">Upgrade to PRO </a></div>'
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
                            <span class="copy-text">' . __('Copy', 'pdfp') . '</span>
                        </button>
                    </div>
                    <p class="description">' . __('Copy and paste this shortcode into any page or post. Replace <code>your_file_url</code> with your actual PDF link.', 'pdfp') . '</p>
                </div>
            '
        ];
    }

    public static function upcoming_section() {
        return array(
            'type' => 'content',
            'content' => '<div class="pdfp-metabox-upcoming-section">This feature is coming soon. Stay tuned for updates!</div>'
        );
    }

    public static function pdfp_preset($key, $default = false) {
        $settings = get_option('fpdf_option');
        return $settings[$key] ?? $default;
    }

    public static function pipeError($prefix) {
        \CSF::createSection($prefix, array(
            'title' => '',
            'fields' => array(
                array(
                    'type' => 'heading',
                    'content' => '<p style="color:#7B2F31;background:#F8D7DA;padding:15px">PDF Poster PRO is not activated yet. Please active the license key by navigating to Plugins> PDF Poster PRO > Active License. 
                    Once you active the plugin you will get all the options availble here. </p>'
                ),
            ),
        ));
    }
}