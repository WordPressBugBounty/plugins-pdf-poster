<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('PDFPAdmin')) {
	class PDFPAdmin
	{
		function __construct()
		{
			add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
			add_action('admin_menu', [$this, 'adminMenu'], 15);
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
			// add_menu_page(
			// 	__('PDF Poster', 'pdfp'),
			// 	__('PDF Poster', 'pdfp'),
			// 	'edit_others_posts',
			// 	'pdf-poster',
			// 	[$this, 'dashboardPage'],
			// 	'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBMaWNlbnNlOiBNSVQuIE1hZGUgYnkgR2FydWRhIFRlY2hub2xvZ3k6IGh0dHBzOi8vZ2l0aHViLmNvbS9nYXJ1ZGF0ZWNobm9sb2d5ZGV2ZWxvcGVycy9za2V0Y2gtaWNvbnMgLS0+Cjxzdmcgd2lkdGg9IjgwMHB4IiBoZWlnaHQ9IjgwMHB4IiB2aWV3Qm94PSItNCAwIDQwIDQwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBkPSJNMjUuNjY4NiAyNi4wOTYyQzI1LjE4MTIgMjYuMjQwMSAyNC40NjU2IDI2LjI1NjMgMjMuNjk4NCAyNi4xNDVDMjIuODc1IDI2LjAyNTYgMjIuMDM1MSAyNS43NzM5IDIxLjIwOTYgMjUuNDAzQzIyLjY4MTcgMjUuMTg4OCAyMy44MjM3IDI1LjI1NDggMjQuODAwNSAyNS42MDA5QzI1LjAzMTkgMjUuNjgyOSAyNS40MTIgMjUuOTAyMSAyNS42Njg2IDI2LjA5NjJaTTE3LjQ1NTIgMjQuNzQ1OUMxNy4zOTUzIDI0Ljc2MjIgMTcuMzM2MyAyNC43Nzc2IDE3LjI3NzYgMjQuNzkzOUMxNi44ODE1IDI0LjkwMTcgMTYuNDk2MSAyNS4wMDY5IDE2LjEyNDcgMjUuMTAwNUwxNS42MjM5IDI1LjIyNzVDMTQuNjE2NSAyNS40ODI0IDEzLjU4NjUgMjUuNzQyOCAxMi41NjkyIDI2LjA1MjlDMTIuOTU1OCAyNS4xMjA2IDEzLjMxNSAyNC4xNzggMTMuNjY2NyAyMy4yNTY0QzEzLjkyNzEgMjIuNTc0MiAxNC4xOTMgMjEuODc3MyAxNC40NjggMjEuMTg5NEMxNC42MDc1IDIxLjQxOTggMTQuNzUzMSAyMS42NTAzIDE0LjkwNDYgMjEuODgxNEMxNS41OTQ4IDIyLjkzMjYgMTYuNDYyNCAyMy45MDQ1IDE3LjQ1NTIgMjQuNzQ1OVpNMTQuODkyNyAxNC4yMzI2QzE0Ljk1OCAxNS4zODMgMTQuNzA5OCAxNi40ODk3IDE0LjM0NTcgMTcuNTUxNEMxMy44OTcyIDE2LjIzODYgMTMuNjg4MiAxNC43ODg5IDE0LjI0ODkgMTMuNjE4NUMxNC4zOTI3IDEzLjMxODUgMTQuNTEwNSAxMy4xNTgxIDE0LjU4NjkgMTMuMDc0NEMxNC43MDQ5IDEzLjI1NjYgMTQuODYwMSAxMy42NjQyIDE0Ljg5MjcgMTQuMjMyNlpNOS42MzM0NyAyOC44MDU0QzkuMzgxNDggMjkuMjU2MiA5LjEyNDI2IDI5LjY3ODIgOC44NjA2MyAzMC4wNzY3QzguMjI0NDIgMzEuMDM1NSA3LjE4MzkzIDMyLjA2MjEgNi42NDk0MSAzMi4wNjIxQzYuNTk2ODEgMzIuMDYyMSA2LjUzMzE2IDMyLjA1MzYgNi40NDAxNSAzMS45NTU0QzYuMzgwMjggMzEuODkyNiA2LjM3MDY5IDMxLjg0NzYgNi4zNzM1OSAzMS43ODYyQzYuMzkxNjEgMzEuNDMzNyA2Ljg1ODY3IDMwLjgwNTkgNy41MzUyNyAzMC4yMjM4QzguMTQ5MzkgMjkuNjk1NyA4Ljg0MzUyIDI5LjIyNjIgOS42MzM0NyAyOC44MDU0Wk0yNy4zNzA2IDI2LjE0NjFDMjcuMjg4OSAyNC45NzE5IDI1LjMxMjMgMjQuMjE4NiAyNS4yOTI4IDI0LjIxMTZDMjQuNTI4NyAyMy45NDA3IDIzLjY5ODYgMjMuODA5MSAyMi43NTUyIDIzLjgwOTFDMjEuNzQ1MyAyMy44MDkxIDIwLjY1NjUgMjMuOTU1MiAxOS4yNTgyIDI0LjI4MTlDMTguMDE0IDIzLjM5OTkgMTYuOTM5MiAyMi4yOTU3IDE2LjEzNjIgMjEuMDczM0MxNS43ODE2IDIwLjUzMzIgMTUuNDYyOCAxOS45OTQxIDE1LjE4NDkgMTkuNDY3NUMxNS44NjMzIDE3Ljg0NTQgMTYuNDc0MiAxNi4xMDEzIDE2LjM2MzIgMTQuMTQ3OUMxNi4yNzM3IDEyLjU4MTYgMTUuNTY3NCAxMS41Mjk1IDE0LjYwNjkgMTEuNTI5NUMxMy45NDggMTEuNTI5NSAxMy4zODA3IDEyLjAxNzUgMTIuOTE5NCAxMi45ODEzQzEyLjA5NjUgMTQuNjk4NyAxMi4zMTI4IDE2Ljg5NjIgMTMuNTYyIDE5LjUxODRDMTMuMTEyMSAyMC41NzUxIDEyLjY5NDEgMjEuNjcwNiAxMi4yODk1IDIyLjczMTFDMTEuNzg2MSAyNC4wNDk4IDExLjI2NzQgMjUuNDEwMyAxMC42ODI4IDI2LjcwNDVDOS4wNDMzNCAyNy4zNTMyIDcuNjk2NDggMjguMTM5OSA2LjU3NDAyIDI5LjEwNTdDNS44Mzg3IDI5LjczNzMgNC45NTIyMyAzMC43MDI4IDQuOTAxNjMgMzEuNzEwN0M0Ljg3NjkzIDMyLjE4NTQgNS4wMzk2OSAzMi42MjA3IDUuMzcwNDQgMzIuOTY5NUM1LjcyMTgzIDMzLjMzOTggNi4xNjMyOSAzMy41MzQ4IDYuNjQ4NyAzMy41MzU0QzguMjUxODkgMzMuNTM1NCA5Ljc5NDg5IDMxLjMzMjcgMTAuMDg3NiAzMC44OTA5QzEwLjY3NjcgMzAuMDAyOSAxMS4yMjgxIDI5LjAxMjQgMTEuNzY4NCAyNy44Njk5QzEzLjEyOTIgMjcuMzc4MSAxNC41Nzk0IDI3LjAxMSAxNS45ODUgMjYuNjU2MkwxNi40ODg0IDI2LjUyODNDMTYuODY2OCAyNi40MzIxIDE3LjI2MDEgMjYuMzI1NyAxNy42NjM1IDI2LjIxNTNDMTguMDkwNCAyNi4wOTk5IDE4LjUyOTYgMjUuOTgwMiAxOC45NzYgMjUuODY2NUMyMC40MTkzIDI2Ljc4NDQgMjEuOTcxNCAyNy4zODMxIDIzLjQ4NTEgMjcuNjAyOEMyNC43NjAxIDI3Ljc4ODMgMjUuODkyNCAyNy42ODA3IDI2LjY1ODkgMjcuMjgxMUMyNy4zNDg2IDI2LjkyMTkgMjcuMzg2NiAyNi4zNjc2IDI3LjM3MDYgMjYuMTQ2MVpNMzAuNDc1NSAzNi4yNDI4QzMwLjQ3NTUgMzguMzkzMiAyOC41ODAyIDM4LjUyNTggMjguMTk3OCAzOC41MzAxSDMuNzQ0ODZDMS42MDIyNCAzOC41MzAxIDEuNDczMjIgMzYuNjIxOCAxLjQ2OTEzIDM2LjI0MjhMMS40Njg4NCAzLjc1NjQyQzEuNDY4ODQgMS42MDM5IDMuMzY3NjMgMS40NzM0IDMuNzQ0NTcgMS40NjkwOEgyMC4yNjNMMjAuMjcxOCAxLjQ3NzhWNy45MjM5NkMyMC4yNzE4IDkuMjE3NjMgMjEuMDUzOSAxMS42NjY5IDI0LjAxNTggMTEuNjY2OUgzMC40MjAzTDMwLjQ3NTMgMTEuNzIxOEwzMC40NzU1IDM2LjI0MjhaTTI4Ljk1NzIgMTAuMTk3NkgyNC4wMTY5QzIxLjg3NDkgMTAuMTk3NiAyMS43NDUzIDguMjk5NjkgMjEuNzQyNCA3LjkyNDE3VjIuOTUzMDdMMjguOTU3MiAxMC4xOTc2Wk0zMS45NDQ3IDM2LjI0MjhWMTEuMTE1N0wyMS43NDI0IDAuODcxMDIyVjAuODIzMzU3SDIxLjY5MzZMMjAuODc0MiAwSDMuNzQ0OTFDMi40NDk1NCAwIDAgMC43ODUzMzYgMCAzLjc1NzExVjM2LjI0MzVDMCAzNy41NDI3IDAuNzgyOTU2IDQwIDMuNzQ0OTEgNDBIMjguMjAwMUMyOS40OTUyIDM5Ljk5OTcgMzEuOTQ0NyAzOS4yMTQzIDMxLjk0NDcgMzYuMjQyOFoiIGZpbGw9IiNFQjU3NTciLz4KPC9zdmc+',
			// 	15
			// );

			// add_submenu_page(
			// 	'pdf-poster',
			// 	__('Add New', 'pdfp'),
			// 	__(' &#8627; Add New', 'pdfp'),
			// 	'manage_options',
			// 	'pdf-poster-add-new',
			// 	[$this, 'redirectToAddNew'],
			// 	2
			// );

			add_submenu_page(
				'edit.php?post_type=pdfposter',
				__('Demo and Help', 'pdfp'),
				__('Demo and Help', 'pdfp'),
				'edit_others_posts',
				'pdf-poster',
				[$this, 'dashboardPage'],
				15
			);
		}

		function dashboardPage()
		{ ?>
			<div id='pdfpAdminDashboard' data-info='<?php echo esc_attr(wp_json_encode([
														'version' => PDFPRO_VER,
														'isPremium' => pdfp_fs()->can_use_premium_code(),
														'hasPro' => true
													])); ?>'></div>
		<?php }

		function upgradePage()
		{ ?>
			<div id='pdfpAdminUpgrade' data-info='<?php echo esc_attr(wp_json_encode([
														'version' => PDFPRO_VER,
														'isPremium' => pdfp_fs()->can_use_premium_code(),
														'hasPro' => true
													])); ?>'>Coming soon...</div>
			<?php }

	}
	new PDFPAdmin;
}
