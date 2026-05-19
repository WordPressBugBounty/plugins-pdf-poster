<?php

namespace PDFPro\Base;

use PDFPro\Helper\PDFP_Functions as Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'PDFPro\Base\PDFP_EnqueueAssets' ) ) {
    class PDFP_EnqueueAssets {

    public function register() {
        add_action("wp_enqueue_scripts", [$this, 'publicAssets']);
        add_action('admin_enqueue_scripts', [$this, 'adminAssets']);
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'publicAssets']);
        add_action('elementor/preview/enqueue_scripts', [$this, 'publicAssets']);
        // Media button
        add_action('wp_enqueue_media', [$this, 'pdfp_media_button_js_file']);
        add_action('script_loader_tag', [$this, 'script_loader_tag'], 10, 3);
        add_action('init', [$this, 'init']);
    }

    /** 
     * inti action
     */
    public function init() {
        // dflip asset registration removed (premium only)
    }

    /**
     * Enqueue public assets
     */
    public function publicAssets() {
        wp_enqueue_style('pdfp-public',  PDFPRO_PLUGIN_DIR . 'build/public.css', array(), PDFPRO_VER);
        wp_register_script('pdfp-public', PDFPRO_PLUGIN_DIR . 'build/public.js', array('jquery'), PDFPRO_VER, true);
        wp_register_script('pdfp-pdfposter-view-script', PDFPRO_PLUGIN_DIR . 'build/blocks/pdf-poster/view.js', array('react', 'react-dom', 'jquery'), PDFPRO_VER, true);

        // Premium assets removed

        $option = get_option('fpdf_option', []);

        $localize_data = [
            'dir' => PDFPRO_PLUGIN_DIR,
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'isPipe' => false,
        ];

        // Premium data localization removed

        wp_localize_script('pdfp-public', 'pdfp', $localize_data);

        wp_localize_script('pdfp-pdfposter-view-script', 'pdfp', $localize_data);
        
        
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $is_elementor_preview = isset($_GET['elementor-preview']) || (isset($_REQUEST['action']) && $_REQUEST['action'] === 'elementor_ajax') || did_action('elementor/frontend/after_enqueue_scripts') || did_action('elementor/preview/enqueue_scripts');

        if ($is_elementor_preview) {
            // Premium elementor script removed
            wp_enqueue_script('pdfp-public');
            wp_enqueue_script('pdfp-pdfposter-view-script');
            wp_enqueue_style('pdfp-public');
        }
    }

    public function script_loader_tag($tag, $handle, $src) {
        // Premium script loader tag removed
        return $tag;
    }

    /**
     * enqueue admin assets
     **/
    function adminAssets($hook) {
        $option = get_option('fpdf_option');
        $postType = get_post_type();
        if (in_array($hook, ['admin_page_pdf-poster-pricing-manual', 'pdfposter_page_fpdf-support', 'pdfposter_page_fpdf-settings', 'post.php', 'post-new.php']) || $postType === 'pdfposter') {
            // Premium admin assets removed
        }
        wp_enqueue_script('pdfp-admin', PDFPRO_PLUGIN_DIR . 'build/admin.js', array('jquery'), PDFPRO_VER, true);
        wp_enqueue_style('pdfp-admin', PDFPRO_PLUGIN_DIR . 'build/admin.css', array(), PDFPRO_VER);

        $current_screen = get_current_screen();
        if ('settings_page_pdf_poster_settings' == $hook) {
            $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
            wp_localize_script('jquery', 'cm_settings', $cm_settings);
            wp_enqueue_script('wp-theme-plugin-editor');
            wp_enqueue_style('wp-codemirror');
            wp_enqueue_script('pdfp-codemirror', PDFPRO_PLUGIN_DIR . 'assets/admin/js/codemirror-init.js', array('jquery'), PDFPRO_VER, true);
        }

        $fpdfAdmin = array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'isPipe' => false
        );

        // Premium admin data localization removed

        wp_localize_script('pdfp-admin', 'fpdfAdmin', $fpdfAdmin);
    }

    public function pdfp_media_button_js_file() {
        wp_enqueue_script('pdfp-direct', PDFPRO_PLUGIN_DIR . 'assets/admin/js/pdf_button.js', array('jquery'), PDFPRO_VER, true);
    }
}
}
