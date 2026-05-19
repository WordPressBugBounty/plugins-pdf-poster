<?php

namespace PDFPro\Admin;

use PDFPro\Helper\PDFP_Functions as Utils;

if (! defined('ABSPATH')) exit;

if ( ! class_exists( 'PDFPro\Admin\PDFP_Settings' ) ) {
	class PDFP_Settings {

	private $option_prefix = 'fpdf_option';
	public function register() {
		add_action('init', array($this, 'init'), 0);
	}


	public function init() {
		if (class_exists('\CSF')) {
			\CSF::createOptions($this->option_prefix, array(
				'framework_title' => __('PDF Poster Settings', 'pdfp'),
				'menu_title'  => __('Settings', 'pdfp'),
				'menu_slug'   => 'fpdf-settings',
				'menu_type'   => 'submenu',
				'menu_parent' => 'edit.php?post_type=pdfposter',
				'theme' => 'light',
				'show_bar_menu' => false,
				'footer_text' => 'Thank you for using PDF Poster',
			));
			

			$this->shortcode();
			$this->gutenberg_integration();
			$this->custom_css();
			$this->preset();
			$this->cloud_api();
		}
	}

	public function shortcode() {
		\CSF::createSection($this->option_prefix, array(
			'title' => __('Quick Embedder', 'pdfp'),
			'fields' => array(
				array(
					'type'    => 'content',
					'content' => '
						<div class="pdfp-docs-notice">
							<div class="pdfp-docs-notice-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
							</div>
							<div class="pdfp-docs-notice-content">
								<h4>' . __('Documentation & Help', 'pdfp') . '</h4>
								<p>' . __('Need help configuring the Quick Embedder? Check out our full documentation for expert tips and advanced settings.', 'pdfp') . '</p>
							</div>
							<div class="pdfp-docs-notice-action">
								<a href="https://bplugins.com/docs/pdf-poster/settings/quick-embedder-2/" target="_blank" class="pdfp-docs-btn">
									' . __('View Documentation', 'pdfp') . '
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
								</a>
							</div>
						</div>
					'
				),
				Utils::quick_embed_shortcode(),
				array(
					'id' => 'height',
					'title' => __('Viewer Height', 'pdfp'),
					'type' => 'dimensions',
					'default' => [
						'height' => '800',
						'unit' => 'px'
					],
					'width' => false,
					'desc' => __('Set the height of the PDF viewer.', 'pdfp')
				),
				array(
					'id' => 'width',
					'title' => __('Viewer Width', 'pdfp'),
					'type' => 'dimensions',
					'default' => [
						'width' => '100',
						'unit' => '%'
					],
					'height' => false,
					'desc' => __('Set the width of the PDF viewer.', 'pdfp')
				),
				array(
					'id' => 'show_filename',
					'title' => __('Display Filename', 'pdfp'),
					'type' => 'switcher',
					'desc' => __('Show the filename at the top of the viewer.', 'pdfp')
				),
				array(
					'id' => 'show_download_btn',
					'title' => __('Download Button', 'pdfp'),
					'type' => 'switcher',
					'desc' => __('Display a download button at the top of the viewer.', 'pdfp')
				),
				array(
					'id' => 'download_btn_text',
					'title' => __('Download Label', 'pdfp'),
					'type' => 'text',
					'default' => 'Download File',
					'desc' => __('Custom text for the download button.', 'pdfp'),
					'dependency' => array('show_download_btn', '==', '1')
				),
				Utils::pro_feature_list(array(
					__('Enable Printing', 'pdfp'),
					__('Default Browser Viewer Support', 'pdfp'),
					__('Premium Fullscreen Button & Label', 'pdfp'),
					__('Open Fullscreen in New Tab', 'pdfp'),
					__('Advanced Content Protection (Disable Right-Click)', 'pdfp'),
					__('Suppress Blocked Warning Alerts', 'pdfp'),
					__('Enable Thumbnails Navigation', 'pdfp'),
				)),
			)
		));
	}

	public function gutenberg_integration() {
		\CSF::createSection($this->option_prefix, array(
			'title' => __('Shortcode', 'pdfp'),
			'fields' => array(
				array(
					'id' => 'pdfp_gutenberg_enable',
					'type' => 'switcher',
					'title' => __('Gutenberg Integration', 'pdfp'),
					'desc' => __('Enable the PDF Poster block and shortcode generator in the Gutenberg editor.', 'pdfp'),
					'default' => get_option('pdfp_gutenberg_enable', false)
				)
			)
		));
	}

	public function custom_css() {
		\CSF::createSection($this->option_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Custom CSS', 'pdfp')),
			'fields' => array(
				Utils::pro_feature_list(array(
					__('Full Design Control & Branding', 'pdfp'),
					__('Override Default Viewer Styles', 'pdfp'),
					__('Global CSS Application', 'pdfp'),
				)),
			)
		));
	}

	public function preset() {
		\CSF::createSection($this->option_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Presets', 'pdfp')),
			'fields' => array(
				Utils::pro_feature_list(array(
					__('Global Site-Wide Defaults', 'pdfp'),
					__('Automatic Settings Application', 'pdfp'),
					__('Consistent Viewer Experience', 'pdfp'),
					__('Classic Shortcode Integration', 'pdfp'),
				)),
			)
		));
	}

	public function cloud_api() {
		\CSF::createSection($this->option_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Cloud Integration', 'pdfp')),
			'fields' => array(
				Utils::pro_feature_list(array(
					__('Direct Dropbox Connectivity', 'pdfp'),
					__('Google Drive Cloud Picker', 'pdfp'),
					__('Premium Adobe PDF Embed API', 'pdfp'),
					__('Seamless External Hosting', 'pdfp'),
				)),
			)
		));
	}
}
}
