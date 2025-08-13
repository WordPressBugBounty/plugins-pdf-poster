<?php

if (!class_exists('PDFPAdmin')) {
	class PDFPAdmin
	{
		function __construct()
		{
			add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
			add_action('admin_menu', [$this, 'adminMenu']);
		}

		function adminEnqueueScripts($hook)
		{
			if (str_contains($hook, 'pdf-poster')) {
				wp_enqueue_style('pdfp-dashboard-style', PDFPRO_PLUGIN_DIR . 'build/dashboard.css', [], PDFPRO_VER);

				wp_enqueue_script('pdfp-dashboard-script', PDFPRO_PLUGIN_DIR . 'build/dashboard.js', ['react', 'react-dom',  'wp-components', 'wp-i18n', 'wp-api', 'wp-util', 'lodash', 'wp-media-utils', 'wp-data', 'wp-core-data', 'wp-api-request'], PDFPRO_VER, true);
				wp_localize_script('pdfp-dashboard-script', 'pdfpDashboard', [
					'dir' => PDFPRO_PLUGIN_DIR,
				]);
			}
		}

		function adminMenu()
		{
			add_menu_page(
				__('PDF Poster', 'pdfp'),
				__('PDF Poster', 'pdfp'),
				'manage_options',
				'pdf-poster',
				[$this, 'dashboardPage'],
				PDFPRO_PLUGIN_DIR . '/img/icn.png',
				15
			);

			add_submenu_page(
				'pdf-poster',
				__('Dashboard', 'pdfp'),
				__('Dashboard', 'pdfp'),
				'manage_options',
				'pdf-poster',
				[$this, 'dashboardPage'],
				0
			);
		}

		function dashboardPage()
		{ ?>
			<div id='pdfpAdminDashboard' data-info=<?php echo esc_attr(wp_json_encode([
														'version' => PDFPRO_VER
													])); ?>></div>
		<?php }

		function upgradePage()
		{ ?>
			<div id='pdfpAdminUpgrade'>Coming soon...</div>
<?php }
	}
	new PDFPAdmin;
}
