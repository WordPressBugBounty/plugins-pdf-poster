<?php

namespace PDFPro\Field;

use PDFPro\Api\DropboxApi;
use PDFPro\Api\GoogleDriveApi;
use PDFPro\Helper\Functions as Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class MetaBox {

	private $metabox_prefix = '_fpdf';
	private $option = null;

	public function register() {
		add_action('init', array($this, 'register_metabox'), 0);
	}

	public function register_metabox() {
		if (class_exists('\CSF')) {
			\CSF::createMetabox($this->metabox_prefix, array(
				'title' => 'PDF Poster Configuration',
				'post_type' => 'pdfposter',
				'theme' => 'light'
			));

			$this->configure();
			$this->controls();
			$this->actions();
			$this->popup();
			$this->protect_content();
			$this->social_share();
			$this->styles();
			$this->ads();
			$this->analytics();
		}
	}

	public function configure() {
		if (!$this->option) {
			$this->option = get_option('fpdf_option');
		}

		new DropboxApi(Utils::isset($this->option, 'dropbox_app_key'));

		new GoogleDriveApi(Utils::isset($this->option, 'google_apikey'), Utils::isset($this->option, 'google_client_id'), Utils::isset($this->option, 'google_project_number'));

		\CSF::createSection($this->metabox_prefix, array(
			'title'  => 'General',
			'fields' => array(
				Utils::pdfp_lock_field(array(
					'id' => 'viewer',
					'type' => 'button_set',
					'title' => Utils::pdfp_pro_title(__('Viewer', 'pdfp')),
					'desc' => __('Select the PDF viewer engine.', 'pdfp'),
					'default' => 'default',
					'options' => array(
						'default' => __('Default', 'pdfp'),
						'adobe' => __('Adobe', 'pdfp'),
					)
				)),
				array(
					'id'    => 'source',
					'type'  => 'upload',
					'title' => __('PDF Source', 'pdfp'),
					'desc'  => __('Select or upload your PDF file.', 'pdfp'),
					'attributes' => array('id' => 'picker_field')
				),
				array(
					'id' => 'height',
					'title' => __('Height', 'pdfp'),
					'type' => 'dimensions',
					'width' => false,
					'desc'  => __('Set the height of the viewer.', 'pdfp'),
					'default' => Utils::pdfp_preset('preset_height', [
						'height' => 842,
						'unit' => 'px'
					])
				),
				array(
					'id' => 'width',
					'title' => __('Width', 'pdfp'),
					'type' => 'dimensions',
					'height' => false,
					'desc'  => __('Set the width of the viewer.', 'pdfp'),
					'default' => Utils::pdfp_preset('preset_width', [
						'width' => '100',
						'unit' => '%'
					])
				),
				Utils::pdfp_lock_field(array(
					'id' => 'default_browser',
					'title' => Utils::pdfp_pro_title(__('Google Doc Viewer', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_default_browser'),
					'desc' => __('Enable Google Doc Viewer as a fallback (Recommended for Edge).', 'pdfp'),
				)),
			)
		));
	}

	public function controls() {
		\CSF::createSection($this->metabox_prefix, array(
			'title' => __('Controls', 'pdfp'),
			'fields' => array(
				array(
					'id' => 'show_filename',
					'title' => __('Display Filename', 'pdfp'),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_show_filename', true),
					'desc' => __('Show the filename at the top of the viewer.', 'pdfp')
				),
				Utils::pdfp_lock_field(array(
					'id' => 'only_pdf',
					'title' => Utils::pdfp_pro_title(__('Reader Mode', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_only_pdf'),
					'desc' => __('Hide the PDF menu and background for a minimalist look.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'thumbnail_toggle_menu',
					'title' => Utils::pdfp_pro_title(__('Toggle Thumbnails', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_thumbnail_toggle_menu', true),
					'desc' => __('Enable thumbnail navigation in the viewer.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'sidebar_open',
					'title' => Utils::pdfp_pro_title(__('Sidebar Open', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_sidebar_open', false),
					'desc' => __('Open the thumbnail sidebar by default.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'ppv_load_last_version',
					'title' => Utils::pdfp_pro_title(__('Load Latest Version', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_ppv_load_last_version', false),
					'desc' => __('Automatically load the most recent version of the PDF.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'hr_scroll',
					'title' => Utils::pdfp_pro_title(__('Horizontal Scrollbar', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_hr_scroll', false),
					'desc' => esc_html__('Enable horizontal scrolling for wide documents.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'jump_to',
					'title' => Utils::pdfp_pro_title(__('Initial Page', 'pdfp')),
					'type' => 'number',
					'desc' => esc_html__('Set the page number displayed when the viewer loads.', 'pdfp'),
					'default' => 1
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'zoomLevel',
					'title' => Utils::pdfp_pro_title(esc_html__('Default Zoom', 'pdfp')),
					'type' => 'number',
					'desc' => esc_html__('Set the initial zoom level (leave empty for auto).', 'pdfp'),
					'default' => '',
					'unit' => '%'
				))
			)
		));
	}

	public function actions(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => __('Actions', 'pdfp'),
			'fields' => array(
				array(
					'id' => 'print',
					'title' => __('Allow Printing', 'pdfp'),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_print'),
					'desc' => __('Allow visitors to print the PDF document.', 'pdfp')
				),
				array(
					'id' => 'show_download_btn',
					'title' => __('Download Button', 'pdfp'),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_show_download_btn', true),
					'desc' => __('Display a download button at the top of the viewer.', 'pdfp')
				),
				Utils::pdfp_lock_field(array(
					'id' => 'download_btn_text',
					'title' => Utils::pdfp_pro_title(__('Download Label', 'pdfp')),
					'type' => 'text',
					'desc' => __('Customize the text for the download button.', 'pdfp'),
					'default' => Utils::pdfp_preset('preset_download_btn_text', 'Download File'),
					'dependency' => array('show_download_btn', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'view_fullscreen_btn',
					'title' => Utils::pdfp_pro_title(__('Fullscreen Button', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_view_fullscreen_btn', 0),
					'desc' => __('Display a fullscreen toggle button at the top of the viewer.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'fullscreen_btn_text',
					'title' => Utils::pdfp_pro_title(__('Fullscreen Label', 'pdfp')),
					'type' => 'text',
					'desc' => __('Customize the text for the fullscreen button.', 'pdfp'),
					'default' => Utils::pdfp_preset('preset_fullscreen_btn_text', 'View Fullscreen'),
					'dependency' => array('view_fullscreen_btn', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'view_fullscreen_btn_target_blank',
					'title' => Utils::pdfp_pro_title(__('Open in New Tab', 'pdfp')),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_view_fullscreen_btn_target_blank', false),
					'desc' => __('Open the fullscreen view in a new browser tab.', 'pdfp'),
					'dependency' => array('view_fullscreen_btn', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'actions_position',
					'title' => Utils::pdfp_pro_title(__('Actions Position', 'pdfp')),
					'type' => 'button_set',
					'options' => array(
						'top' => __('Top', 'pdfp'),
						'bottom' => __('Bottom', 'pdfp'),
					),
					'default' => 'top',
					'desc' => __('Select where the action buttons should appear.', 'pdfp'),
				)),
			)
		));
	}
	

	public function popup() {
		\CSF::createSection($this->metabox_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Popup', 'pdfp')),
			'fields' => array(
				Utils::upgrade_section(),
				Utils::pdfp_lock_field(array(
					'id' => 'popup',
					'title' => __('Enable Popup', 'pdfp'),
					'type' => 'switcher',
					'desc' => __('Open the PDF document in a modal popup.', 'pdfp'),
					'default' => false,
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'popup_trigger_type',
					'title' => __('Trigger Type', 'pdfp'),
					'type' => 'button_set',
					'options' => array(
						'button' => __('Button', 'pdfp'),
						'image' => __('Image', 'pdfp'),
					),
					'default' => 'button',
					'desc' => __('Select the trigger type for the popup.', 'pdfp'),
					'dependency' => array('popup', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'popup_trigger_alignment',
					'title' => __('Alignment', 'pdfp'),
					'type' => 'button_set',
					'options' => array(
						'left' => __('Left', 'pdfp'),
						'center' => __('Center', 'pdfp'),
						'right' => __('Right', 'pdfp'),
					),
					'default' => 'center',
					'desc' => __('Select the alignment for the popup trigger.', 'pdfp'),
					'dependency' => array('popup', '==', '1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'popup_image',
					'title' => __('Image', 'pdfp'),
					'type' => 'media',
					'library' => 'image',
					'desc' => __('Select an image to use as the popup trigger.', 'pdfp'),
					'dependency' => array('popup_trigger_type|popup', '==|==', 'image|1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'popup_btn_text',
					'title'   => __('Button Text', 'pdfp'),
					'type'    => 'text',
					'desc'    => __('Customize the text for the popup trigger button.', 'pdfp'),
					'default' => 'Open PDF',
					'dependency' => array('popup_trigger_type|popup', '==|==', 'button|1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'popup_image_height',
					'title' => __('Image Height', 'pdfp'),
					'type' => 'dimensions',
					'width' => false,
					'desc' => __('Set the height for the trigger image.', 'pdfp'),
					'default' => [
						'height' => 200,
						'unit' => 'px'
					],
					'dependency' => array('popup_trigger_type|popup', '==|==', 'image|1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'popup_image_width',
					'title' => __('Image Width', 'pdfp'),
					'type' => 'dimensions',
					'height' => false,
					'desc' => __('Set the width for the trigger image.', 'pdfp'),
					'default' =>  [
						'width' => '300',
						'unit' => 'px'
					],
					'dependency' => array('popup_trigger_type|popup', '==|==', 'image|1')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'popup_image_pdf_icon',
					'title' => __('Enable PDF Icon', 'pdfp'),
					'type' => 'switcher',
					'desc' => __('Show a PDF icon over the trigger image.', 'pdfp'),
					'default' => true,
					'dependency' => array('popup_trigger_type|popup', '==|==', 'image|1')
				)),
			),
		));
	}

	public function protect_content(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Protect Content', 'pdfp')),
			'fields' => array(
				Utils::upgrade_section(),
				Utils::pdfp_lock_field(array(
					'id' => 'protect',
					'title' => __('Protect Content', 'pdfp'),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_protect', 0),
					'desc' => __('Disable right-click and text selection to protect your content.', 'pdfp')
				)),
				Utils::pdfp_lock_field(array(
					'id' => 'disable_alert',
					'title' => __('Disable Alert', 'pdfp'),
					'type' => 'switcher',
					'default' => Utils::pdfp_preset('preset_disable_alert', true),
					'desc' => __('Suppress the warning message when right-click is blocked.', 'pdfp'),
					'dependency' => array('protect', '==', '1')
				)),
			)
		));
	}

	public function social_share(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => __('Social Share', 'pdfp'),
			'fields' => array(
				array(
					'id' => 'social_share',
					'title' => __('Enable Sharing', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Enable social sharing buttons for the PDF.', 'pdfp'),
					'default' => false,
				),
				array(
					'id' => 'social_share_position',
					'title' => __('Share Position', 'pdfp'),
					'type' => 'select',
					'desc' => esc_html__('Select where the sharing buttons should appear.', 'pdfp'),
					'default' => 'top',
					'options' => array(
						'top' => esc_html__('Top', 'pdfp'),
						'bottom' => esc_html__('Bottom', 'pdfp'),
					),
					'dependency' => array('social_share', '==', '1')
				),
				array(
					'id' => 'social_share_facebook',
					'title' => __('Enable Facebook', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Allow sharing on Facebook.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
				array(
					'id' => 'social_share_twitter',
					'title' => __('Enable Twitter', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Allow sharing on Twitter.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
				array(
					'id' => 'social_share_linkedin',
					'title' => __('Enable LinkedIn', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Allow sharing on LinkedIn.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
				array(
					'id' => 'social_share_pinterest',
					'title' => __('Enable Pinterest', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Allow sharing on Pinterest.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
			)
		));
	}

	public function styles(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Styles', 'pdfp')),
			'fields' => array(
				Utils::upgrade_section(),
				Utils::pdfp_lock_field(array(
					'id'      => 'popup_btn_bg',
					'title'   => __('Button Background', 'pdfp'),
					'type'    => 'color',
					'desc'    => __('Choose a background color for the buttons.', 'pdfp'),
					'default' => '#1e73be',
				)),
				Utils::pdfp_lock_field(array(
					'id'      => 'popup_btn_color',
					'title'   => __('Button Color', 'pdfp'),
					'type'    => 'color',
					'desc'    => __('Choose a text color for the buttons.', 'pdfp'),
					'default' => '#fff'
				)),
				Utils::pdfp_lock_field(array(
					'id'      => 'popup_btn_font_size',
					'title'   => __('Font Size', 'pdfp'),
					'type'    => 'number',
					'desc'    => esc_html__('Set the font size for the buttons.', 'pdfp'),
					'default' => 1,
					'unit' => 'rem'
				)),
				Utils::pdfp_lock_field(array(
					'id'      => 'popup_btn_padding',
					'title'   => __('Padding', 'pdfp'),
					'type'    => 'spacing',
					'desc'    => __('Set the internal spacing for the buttons.', 'pdfp'),
					'default' => [
						'top'    => '10',
						'bottom'    => '10',
						'left'    => '20',
						'right'    => '20',
					],
					'units' => array('px')
				)),
			),
		));
	}

	public function ads(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Ads', 'pdfp'), "Upcoming"),
			'fields' => array(
				Utils::upcoming_section()
			)
		));
	}

	public function analytics(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => Utils::pdfp_pro_title(__('Analytics', 'pdfp'), "Upcoming"),
			'fields' => array(
				Utils::upcoming_section()
			)
		));
	}

}
