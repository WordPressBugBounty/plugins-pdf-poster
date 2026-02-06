<?php

namespace PDFPro\Field;

use PDFPro\Api\DropboxApi;
use PDFPro\Api\GoogleDriveApi;
use PDFPro\Helper\Functions as Utils;

if ( ! defined( 'ABSPATH' ) ) exit;

class MetaBox
{

	private $metabox_prefix = '_fpdf';
	private $option = null;

	public function register()
	{
		add_action('init', array($this, 'register_metabox'), 0);
	}

	public function register_metabox()
	{
		global $pdfp_bs;
		if (class_exists('\CSF')) {
			\CSF::createMetabox($this->metabox_prefix, array(
				'title' => 'PDF Poster Configuration',
				'post_type' => 'pdfposter',
				'theme' => 'light'
			));

			$this->configure();
			$this->actions();
			$this->social_share();
			$this->controls();
			$this->popup();
			$this->protect_content();
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

		new DropboxApi(Utils::isset($this->option, 'dropbox_app_key'));

		new GoogleDriveApi(Utils::isset($this->option, 'google_apikey'), Utils::isset($this->option, 'google_client_id'), Utils::isset($this->option, 'google_project_number'));

		\CSF::createSection($this->metabox_prefix, array(
			'title'  => 'General',
			'fields' => array(
				array(
					'id'    => 'source',
					'type'  => 'upload',
					'title' => __('Add PDF source', 'pdfp'),
					'attributes' => array('id' => 'picker_field')
				),
				array(
					'id' => 'height',
					'title' => 'Height',
					'type' => 'dimensions',
					'width' => false,
					'default' => $this->pdfp_preset('preset_height', [
						'height' => 842,
						'unit' => 'px'
					])
				),
				array(
					'id' => 'width',
					'title' => 'Width',
					'type' => 'dimensions',
					'height' => false,
					'default' => $this->pdfp_preset('preset_width', [
						'width' => '100',
						'unit' => '%'
					])
				),
				array(
					'id' => 'print',
					'title' => __('Allow Print', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_print'),
					'desc' => __('Check if you allow visitor to print the pdf file .', 'pdfp')
				),
				array(
					'id' => 'show_filename',
					'title' => __('Show file name on top', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_show_filename', true),
					'desc' => __('Check if you want to show the file name in the top of the viewer.', 'pdfp')
				),
			)
		));
	}

	public function controls(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => 'Controls' . ' <span style="color:#fff;background:#146ef5;padding:3px 10px;border-radius:3px;">Pro</span>',
			'fields' => array(
				$this->upgrade_section(),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Show Only PDF', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_only_pdf'),
					'desc' => __('Enable if you want to hide black background and PDF menu', 'pdfp')
				),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Thumbnails toggle menu', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_thumbnail_toggle_menu', true),
					'desc' => __('Enable to enable Thumbnails Toogle Menu in the viewer', 'pdfp')
				),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Thumbnails open by default', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_sidebar_open', false),
					'desc' => __('Enable to enable Thumbnails Toogle Menu in the viewer', 'pdfp')
				),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Load the last version of the pdf', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_ppv_load_last_version', false),
					'desc' => __('Enable to Load the last version of the pdf', 'pdfp')
				),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Horizontal Scrollbar', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_hr_scroll', false),
					'desc' => esc_html__('Set Horizontal scrollbar as default', 'pdfp')
				),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Jump To Page', 'pdfp'),
					'type' => 'number',
					'desc' => esc_html__('Enter the page number that will be shown in the viewer', 'pdfp'),
					'default' => 1
				),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => esc_html__('Zoom Level', 'pdfp'),
					'type' => 'number',
					'desc' => esc_html__('Enter the zoom level. leave empty to set auto', 'pdfp'),
					'default' => '',
					'unit' => '%'
				)
			)
		));
	}

	public function actions(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => 'Actions',
			'fields' => array(
				array(
					'id' => 'print',
					'title' => __('Allow Print', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_print'),
					'desc' => __('Check if you allow visitor to print the pdf file .', 'pdfp')
				),
				array(
					'id' => 'show_download_btn',
					'title' => __('Show download button on top', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_show_download_btn', true),
					'desc' => __('Check if you want to show "Download" Button in the top of the viewer.', 'pdfp')
				),
				$this->upgrade_section(),
				array(
					'id' => 'readonly',
					'title' => __('Button Text', 'pdfp'),
					'type' => 'text',
					'class' => 'bplugins-meta-readonly',
					'default' => $this->pdfp_preset('preset_download_btn_text', 'Download File'),
					'dependency' => array('show_download_btn', '==', '1')
				),
				array(
					'id' => 'readonly',
					'title' => __('Show view fullscreen button on top', 'pdfp'),
					'type' => 'switcher',
					'class' => 'bplugins-meta-readonly',
					'default' => $this->pdfp_preset('preset_view_fullscreen_btn', 0),
					'desc' => __('Check if you want to show "View Full Screen" Button in the top of the viewer.', 'pdfp')
				),
				array(
					'id' => 'fullscreen_btn_text',
					'title' => __('Button Text', 'pdfp'),
					'type' => 'text',
					'default' => $this->pdfp_preset('preset_fullscreen_btn_text', 'View Fullscreen'),
					'dependency' => array('view_fullscreen_btn', '==', '1')
				),
				array(
					'id' => 'view_fullscreen_btn_target_blank',
					'title' => __('Open in new window', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_view_fullscreen_btn_target_blank', false),
					'dependency' => array('view_fullscreen_btn', '==', '1')
				),
			)
		));
	}
	

	public function popup(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => 'Popup' . ' <span style="color:#fff;background:#146ef5;padding:3px 10px;border-radius:3px;">Pro</span>',
			'fields' => array(
				$this->upgrade_section(),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Enable', 'pdfp'),
					'type' => 'switcher',
					'desc' => __('Enable or disable the popup functionality.', 'pdfp'),
					'default' => false,
				),
				array(
					'id' => 'readonly',					
					'class' => 'bplugins-meta-readonly',
					'title'   => __('Button Text', 'pdfp'),
					'type'    => 'text',
					'desc'    => __('Text on the button you want to show.', 'pdfp'),
					'default' => 'Open PDF',
					'dependency' => array('popup', '==', '1')
				),
				
			),
		));
	}

	public function protect_content(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => 'Protect Content' . ' <span style="color:#fff;background:#146ef5;padding:3px 10px;border-radius:3px;">Pro</span>',
			'fields' => array(
				$this->upgrade_section(),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Protect my content', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_protect', 0),
					'desc' => __('Check to disable Mouse clicks to protect your content.', 'pdfp')
				),
				array(
					'id' => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title' => __('Disable Alert Message', 'pdfp'),
					'type' => 'switcher',
					'default' => $this->pdfp_preset('preset_disable_alert', true),
					'desc' => __('Check to disable alert message.', 'pdfp'),
					'dependency' => array('protect', '==', '1')
				),
			)
		));
	}

	public function social_share(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => 'Social Share',
			'fields' => array(
				array(
					'id' => 'social_share',
					'title' => __('Enabled', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Enable or disable the social share functionality.', 'pdfp'),
					'default' => false,
				),
				// position top and bottom
				array(
					'id' => 'social_share_position',
					'title' => __('Position', 'pdfp'),
					'type' => 'select',
					'desc' => esc_html__('Select the position of the social share button.', 'pdfp'),
					'default' => 'top',
					'options' => array(
						'top' => esc_html__('Top', 'pdfp'),
						'bottom' => esc_html__('Bottom', 'pdfp'),
					),
					'dependency' => array('social_share', '==', '1')
				),
				// facebook
				array(
					'id' => 'social_share_facebook',
					'title' => __('Facebook', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Enable or disable the facebook share functionality.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
				// twitter
				array(
					'id' => 'social_share_twitter',
					'title' => __('Twitter', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Enable or disable the twitter share functionality.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
				// linkedin
				array(
					'id' => 'social_share_linkedin',
					'title' => __('Linkedin', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Enable or disable the linkedin share functionality.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
				// pinterest
				array(
					'id' => 'social_share_pinterest',
					'title' => __('Pinterest', 'pdfp'),
					'type' => 'switcher',
					'desc' => esc_html__('Enable or disable the pinterest share functionality.', 'pdfp'),
					'default' => true,
					'dependency' => array('social_share', '==', '1')
				),
			)
		));
	}

	public function styles(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => __('Styles', 'pdfp') .' <span style="color:#fff;background:#146ef5;padding:3px 10px;border-radius:3px;">Pro</span>',
			'fields' => array(
				$this->upgrade_section(),
				array(
					'id'      => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title'   => __('Button Background Color', 'pdfp'),
					'type'    => 'color',
					'desc'    => __('Choose a background color for the button.', 'pdfp'),
					'default' => '#1e73be',
				),
				array(
					'id'      => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title'   => __('Button Text Color', 'pdfp'),
					'type'    => 'color',
					'desc'    => __('Choose a text color for the button.', 'pdfp'),
					'default' => '#fff'
				),
				array(
					'id'      => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title'   => __('Button Font Size', 'pdfp'),
					'type'    => 'number',
					'desc'    => esc_html__('Specify the font size for the button (in px).', 'pdfp'),
					'default' => 1,
					'unit' => 'rem'
				),
				array(
					'id'      => 'readonly',
					'class' => 'bplugins-meta-readonly',
					'title'   => __('Button Padding', 'pdfp'),
					'type'    => 'spacing',
					'desc'    => __('Specify the padding for the button (e.g., 10px 20px).', 'pdfp'),
					'default' => [
						'top'    => '10',
						'bottom'    => '10',
						'left'    => '20',
						'right'    => '20',
					],
					'units' => array('px')
				),
			),
		));
	}

	public function ads(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => __('Ads', 'pdfp') . ' <span style="color:#fff;background:#146ef5;padding:3px 10px;border-radius:3px;">Upcoming</span>',
			'fields' => array(
				array(
					'id' => 'ads',
					'type' => 'content',
					'content' => __('This feature is coming soon. Stay tuned for updates!', 'pdfp'),
				)
			)
		));
	}

	public function analytics(){
		\CSF::createSection($this->metabox_prefix, array(
			'title' => __('Analytics', 'pdfp') . ' <span style="color:#fff;background:#146ef5;padding:3px 10px;border-radius:3px;">Upcoming</span>',
			'fields' => array(
				array(
					'id' => 'analytics',
					'type' => 'content',
					'content' => __('This feature is coming soon. Stay tuned for updates!', 'pdfp'),
				)
			)
		));
	}

	public function pipeError($prefix)
	{
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

	function pdfp_preset($key, $default = false)
	{
		$settings = get_option('fpdf_option');
		return $settings[$key] ?? $default;
	}

	function upgrade_section(){
		return array(
					'type' => 'content',
					'content' => '<div class="pdfp-metabox-upgrade-section">The Ultimate PDF Embedder Plugin for WordPress, Loved by Over 20,000+ Users. <a class="button button-bplugins" href="' . admin_url('admin.php?page=pdf-poster-pricing') . '">Upgrade to PRO </a></div>'
		);
	}
}
