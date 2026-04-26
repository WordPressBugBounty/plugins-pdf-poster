<?php

namespace PDFPro\Field;

use PDFPro\Helper\Functions as Utils;

if (! defined('ABSPATH')) exit;

class Settings {

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
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Enable Printing', 'pdfp')),
					'type' => 'switcher',
					'default' => '0',
					'desc' => __('Allow visitors to print the PDF document.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Default Browser Viewer', 'pdfp')),
					'type' => 'switcher',
					'default' => '0',
					'desc' => __("Use the browser's native PDF viewer. (Bypasses content protection but improves compatibility with some mobile devices and Microsoft Edge).", 'pdfp')
				)),
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
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Fullscreen Button', 'pdfp')),
					'type' => 'switcher',
					'desc' => __('Display a fullscreen toggle button at the top of the viewer.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Fullscreen Label', 'pdfp')),
					'type' => 'text',
					'default' => __('View Fullscreen', 'pdfp'),
					'desc' => __('Custom text for the fullscreen button.', 'pdfp'),
					'dependency' => array('view_fullscreen_btn', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Open in New Tab', 'pdfp')),
					'type' => 'switcher',
					'desc' => __('Open the fullscreen view in a new browser tab.', 'pdfp'),
					'dependency' => array('view_fullscreen_btn', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Disable Context Menu', 'pdfp')),
					'type' => 'switcher',
					'desc' => __('Prevent right-click interactions and text selection to protect your content.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Disable Warning Alert', 'pdfp')),
					'type' => 'switcher',
					'desc' => __('Hide the alert message that appears when right-click is blocked.', 'pdfp'),
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Enable Thumbnails', 'pdfp')),
					'type' => 'switcher',
					'desc' => __('Enable the thumbnail navigation menu within the viewer.', 'pdfp')
				)),
			)
		));

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

		\CSF::createSection($this->option_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Custom CSS', 'pdfp')),
			'fields' => array(
				Utils::upgrade_section(),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'type' => 'code_editor',
					'title' => __('Custom CSS', 'pdfp'),
					'desc' => __('Add your custom CSS here to override the viewer styles.', 'pdfp'),
					'mode' => 'css'
				))
			)
		));

		\CSF::createSection($this->option_prefix, array(
			'title'  => Utils::pdfp_pro_title(__('Preset', 'pdfp')),
			'fields' => array(
				Utils::upgrade_section(),
				array(
					'content' => __('Preset only works for Classic Shortcode Generator', 'pdfp'),
					'type' => 'heading'
				),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Viewer Height', 'pdfp'),
					'type' => 'dimensions',
					'width' => false,
					'desc' => __('Set the default height for preset viewers.', 'pdfp'),
					'default' => [
						'height' => 842,
						'unit' => 'px'
					]
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Viewer Width', 'pdfp'),
					'type' => 'dimensions',
					'height' => false,
					'desc' => __('Set the default width for preset viewers.', 'pdfp'),
					'default' => [
						'width' => '100',
						'unit' => '%'
					]
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => Utils::pdfp_pro_title(__('Reader Mode', 'pdfp')),
					'type' => 'switcher',
					'default' => 0,
					'desc' => __('Hide the PDF menu and background for a minimalist look.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Enable Printing', 'pdfp'),
					'type' => 'switcher',
					'default' => 0,
					'desc' => __('Allow visitors to print the PDF document.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Display Filename', 'pdfp'),
					'type' => 'switcher',
					'default' => true,
					'desc' => __('Show the filename at the top of the viewer.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Download Button', 'pdfp'),
					'type' => 'switcher',
					'default' => 0,
					'desc' => __('Display a download button at the top of the viewer.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Download Label', 'pdfp'),
					'type' => 'text',
					'default' => 'Download File',
					'desc' => __('Custom text for the download button.', 'pdfp'),
					'dependency' => array('preset_show_download_btn', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Fullscreen Button', 'pdfp'),
					'type' => 'switcher',
					'default' => 0,
					'desc' => __('Display a fullscreen toggle button at the top of the viewer.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Fullscreen Label', 'pdfp'),
					'type' => 'text',
					'default' => 'View Fullscreen',
					'desc' => __('Custom text for the fullscreen button.', 'pdfp'),
					'dependency' => array('preset_view_fullscreen_btn', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Open in New Tab', 'pdfp'),
					'type' => 'switcher',
					'default' => true,
					'desc' => __('Open the fullscreen view in a new browser tab.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Disable Context Menu', 'pdfp'),
					'type' => 'switcher',
					'default' => 0,
					'desc' => __('Prevent right-click interactions and text selection to protect your content.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Disable Warning Alert', 'pdfp'),
					'type' => 'switcher',
					'default' => 1,
					'desc' => __('Hide the alert message that appears when right-click is blocked.', 'pdfp'),
					'dependency' => array('preset_protect', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'title' => __('Enable Thumbnails', 'pdfp'),
					'type' => 'switcher',
					'default' => 1,
					'desc' => __('Enable the thumbnail navigation menu within the viewer.', 'pdfp')
				)),
			)
		));

		\CSF::createSection($this->option_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Cloud API', 'pdfp')),
			'fields' => array(
				Utils::upgrade_section(),
				array(
					'type' => 'content',
					'content' => 'Dropbox APP key',
					'class' => 'csf-field-subheading',
				),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'type' => 'text',
					'title' => __('Dropbox App Key', 'pdfp'),
					'desc' => __('Enter your Dropbox API application key. <a href="https://www.dropbox.com/developers/apps?_tk=pilot_lp&_ad=topbar4&_camp=myapps" target="_blank">Create Here</a>', 'pdfp')
				)),
				array(
					'type' => 'content',
					'content' => 'Google API Setup',
					'class' => 'csf-field-subheading',
				),
				array(
					'type' => 'content',
					'title' => ' ',
					'content' => "<strong><a target='_blank' href='https://bplugins.com/docs/pdf-poster/settings/cloud-api/google-api/'>" . __('How to obtain Google API key?', 'pdfp') . "</a></strong>",
				),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'type' => 'text',
					'title' => __('Google API Key', 'pdfp'),
					'desc' => __('Enter your Google Cloud Platform API key.', 'pdfp'),
					'before' => '
					<p>Official Documentation: <a href="https://developers.google.com/identity/protocols/oauth2/service-account" target="_blank">Click Here</a></p>
					<p><a href="https://console.cloud.google.com/" target="_blank">Click Here</a> To Get Google Credentials</p>
					',
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'type' => 'text',
					'title' => __('Google Client ID', 'pdfp'),
					'desc' => __('Enter your Google OAuth 2.0 client ID.', 'pdfp'),
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'type' => 'text',
					'title' => __('Google Project Number', 'pdfp'),
					'desc' => __('Enter your Google Cloud project number.', 'pdfp'),
				)),
				array(
					'type' => 'content',
					'content' => 'Adobe',
					'class' => 'csf-field-subheading',
				),
				Utils::pdfp_lock_field(array(
					'id' => 'readonly',
					'type' => 'text',
					'title' => __('Adobe Client Key', 'pdfp'),
					'desc' => __("Enter your Adobe PDF Embed API client ID. <br>
						Step 1: <a href='https://developer.adobe.com/' target='_blank'>Sign In</a> to Adobe Developer Console <br />
						Step 2:  <a href='https://documentcloud.adobe.com/dc-integration-creation-app-cdn/main.html?api=pdf-embed-api' target='_blank'>Click here</a> To Create Client Key <br />
						Step 3: Copy the Client Key and paste here.", 'pdfp')
				)),
			)
		));
	}

}
