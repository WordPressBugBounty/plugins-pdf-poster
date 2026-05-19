<?php

namespace PDFPro\Admin;

use PDFPro\Helper\PDFP_Functions as Utils;

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('PDFPro\Admin\PDFP_MetaBox')) {
	class PDFP_MetaBox
	{
		private $metabox_prefix = '_fpdf';
		private $option = null;

		public function register() {
			add_action('init', array($this, 'register_metabox'), 0);
		}

		public function register_metabox() {
			if (class_exists('\CSF')) {
				\CSF::createMetabox($this->metabox_prefix, array(
					'title' => __('PDF Poster Configuration', 'pdfp'),
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

		public function configure()
		{
			if (!$this->option) {
				$this->option = get_option('fpdf_option');
			}

			\CSF::createSection($this->metabox_prefix, array(
				'title' => 'General',
				'fields' => array(
					array(
						'id' => 'source',
						'type' => 'upload',
						'title' => __('PDF Source', 'pdfp'),
						'desc' => __('Select or upload your PDF file.', 'pdfp'),
						'attributes' => array('id' => 'picker_field')
					),
					array(
						'id' => 'device_preview',
						'type' => 'button_set',
						'title' => __('Preview Device', 'pdfp') . Utils::pdfp_new_badge(),
						'options' => array(
							'desktop' => __('Desktop', 'pdfp'),
							'tablet' => __('Tablet', 'pdfp'),
							'mobile' => __('Mobile', 'pdfp'),
						),
						'default' => 'desktop',
					),
					array(
						'id' => 'height',
						'title' => __('Height (Desktop)', 'pdfp'),
						'type' => 'dimensions',
						'width' => false,
						'desc' => __('Set the height of the viewer for desktop.', 'pdfp'),
						'default' => Utils::pdfp_preset('preset_height', [
							'height' => 842,
							'unit' => 'px'
						]),
						'dependency' => array('device_preview', '==', 'desktop')
					),
					array(
						'id' => 'height_tablet',
						'title' => __('Height (Tablet)', 'pdfp'),
						'type' => 'dimensions',
						'width' => false,
						'desc' => __('Set the height of the viewer for tablet.', 'pdfp'),
						'default' => [
							'height' => 700,
							'unit' => 'px'
						],
						'dependency' => array('device_preview', '==', 'tablet')
					),
					array(
						'id' => 'height_mobile',
						'title' => __('Height (Mobile)', 'pdfp'),
						'type' => 'dimensions',
						'width' => false,
						'desc' => __('Set the height of the viewer for mobile.', 'pdfp'),
						'default' => [
							'height' => 400,
							'unit' => 'px'
						],
						'dependency' => array('device_preview', '==', 'mobile')
					),
					array(
						'id' => 'width',
						'title' => __('Width (Desktop)', 'pdfp'),
						'type' => 'dimensions',
						'height' => false,
						'desc' => __('Set the width of the viewer for desktop.', 'pdfp'),
						'default' => Utils::pdfp_preset('preset_width', [
							'width' => '100',
							'unit' => '%'
						]),
						'dependency' => array('device_preview', '==', 'desktop')
					),
					array(
						'id' => 'width_tablet',
						'title' => __('Width (Tablet)', 'pdfp'),
						'type' => 'dimensions',
						'height' => false,
						'desc' => __('Set the width of the viewer for tablet.', 'pdfp'),
						'default' => [
							'width' => '100',
							'unit' => '%'
						],
						'dependency' => array('device_preview', '==', 'tablet')
					),
					array(
						'id' => 'width_mobile',
						'title' => __('Width (Mobile)', 'pdfp'),
						'type' => 'dimensions',
						'height' => false,
						'desc' => __('Set the width of the viewer for mobile.', 'pdfp'),
						'default' => [
							'width' => '100',
							'unit' => '%'
						],
						'dependency' => array('device_preview', '==', 'mobile')
					),
					Utils::pro_feature_list(array(
						__('Interactive FlipBook Viewer', 'pdfp'),
						__('Industry-Leading Adobe Viewer', 'pdfp'),
						__('Effortless Cloud Sync (Dropbox & Google Drive)', 'pdfp'),
						__('Google Doc Viewer Fallback', 'pdfp'),
					)),
				)
			));
		}

		public function controls()
		{
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
					Utils::pro_feature_list(array(
						__("Distraction-Free 'Reader Mode'", 'pdfp'),
						__('Toggle Thumbnails Navigation', 'pdfp'),
						__('Auto-Open Sidebar by Default', 'pdfp'),
						__('Load Latest Document Version', 'pdfp'),
						__('Horizontal Scrollbar Support', 'pdfp'),
						__('Custom Initial Page & Zoom Level', 'pdfp'),
					)),
				)
			));
		}

		public function actions()
		{
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
					array(
						'id' => 'fullscreen_btn_text',
						'title' => __('Fullscreen Label', 'pdfp'),
						'type' => 'text',
						'desc' => __('Customize the text for the fullscreen button.', 'pdfp'),
						'default' => Utils::pdfp_preset('preset_fullscreen_btn_text', 'View Fullscreen')
					),
					Utils::pro_feature_list(array(
						__('Customize Download Button Label', 'pdfp'),
						__('Premium Fullscreen Button Control', 'pdfp'),
						__('Open Fullscreen in New Tab', 'pdfp'),
						__('Custom Actions Position (Top/Bottom)', 'pdfp'),
					)),
				)
			));
		}


		public function popup()
		{
			\CSF::createSection($this->metabox_prefix, array(
				'title' => Utils::pdfp_pro_title(__('Popup', 'pdfp')),
				'fields' => array(
					Utils::pro_feature_list(array(
						__('Enable Modal Popups', 'pdfp'),
						__('Multiple Trigger Types (Button/Image)', 'pdfp'),
						__('Custom Trigger Alignment', 'pdfp'),
						__('PDF Icon Overlay on Images', 'pdfp'),
					)),
				),
			));
		}

		public function protect_content()
		{
			\CSF::createSection($this->metabox_prefix, array(
				'title' => Utils::pdfp_pro_title(__('Protect Content', 'pdfp')),
				'fields' => array(
					Utils::pro_feature_list(array(
						__('Disable Right-Click Interactions', 'pdfp'),
						__('Disable Text Selection', 'pdfp'),
						__('Suppress Blocked Warning Alerts', 'pdfp'),
					)),
				)
			));
		}

		public function social_share()
		{
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

		public function styles()
		{
			\CSF::createSection($this->metabox_prefix, array(
				'title' => __('Styles', 'pdfp'),
				'fields' => array(
					array(
						'id' => 'popup_btn_bg',
						'title' => __('Button Background', 'pdfp'),
						'type' => 'color',
						'desc' => __('Choose a background color for the buttons.', 'pdfp'),
						'default' => '#1e73be',
					),
					array(
						'id' => 'popup_btn_color',
						'title' => __('Button Color', 'pdfp'),
						'type' => 'color',
						'desc' => __('Choose a text color for the buttons.', 'pdfp'),
						'default' => '#fff'
					),
					array(
						'id' => 'popup_btn_font_size',
						'title' => __('Font Size', 'pdfp'),
						'type' => 'number',
						'desc' => esc_html__('Set the font size for the buttons.', 'pdfp'),
						'default' => 1,
						'unit' => 'rem'
					),
					array(
						'id' => 'popup_btn_padding',
						'title' => __('Padding', 'pdfp'),
						'type' => 'spacing',
						'desc' => __('Set the internal spacing for the buttons.', 'pdfp'),
						'default' => [
							'top' => '10',
							'bottom' => '10',
							'left' => '20',
							'right' => '20',
						],
						'units' => array('px')
					),
				),
			));
		}

		public function ads()
		{
			\CSF::createSection($this->metabox_prefix, array(
				'title' => Utils::pdfp_pro_title(__('Ads', 'pdfp'), "Upcoming"),
				'fields' => array(
					Utils::upcoming_section()
				)
			));
		}

		public function analytics()
		{
			\CSF::createSection($this->metabox_prefix, array(
				'title' => Utils::pdfp_pro_title(__('Analytics', 'pdfp'), "Upcoming"),
				'fields' => array(
					Utils::upcoming_section()
				)
			));
		}

	}
}
