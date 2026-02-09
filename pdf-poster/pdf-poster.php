<?php

/*
 * Plugin Name: PDF Poster
 * Plugin URI:  https://bplugins.com/products/pdf-poster/
 * Description: You can easily embed/ show pdf file in your wordress website using this plugin.
 * Version:     2.4.1
 * Author:      bPlugins
 * Author URI:  https://profiles.wordpress.org/abuhayat
 * License:     GPLv2
 * Text Domain: pdfp
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'pdfp_fs' ) ) {
    pdfp_fs()->set_basename( false, __FILE__ );
} else {
    /*Some Set-up*/
    define( 'PDFPRO_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
    define( 'PDFPRO_PATH', plugin_dir_path( __FILE__ ) );
    define( 'PDFPRO_VER', ( defined( 'WP_DEBUG' ) ? time() : '2.4.0' ) );
    define( 'PDFPRO_IMPORT_VER', '1.0.0' );
    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require_once dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    if ( file_exists( dirname( __FILE__ ) . '/inc/functions.php' ) ) {
        require_once dirname( __FILE__ ) . '/inc/functions.php';
    }
    if ( file_exists( dirname( __FILE__ ) . '/inc/admin.php' ) ) {
        require_once dirname( __FILE__ ) . '/inc/admin.php';
    }
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'pdfp_fs' ) ) {
        // Create a helper function for easy SDK access.
        function pdfp_fs() {
            global $pdfp_fs;
            if ( !isset( $pdfp_fs ) ) {
                // Include Freemius SDK.
                // SDK is auto-loaded through composer
                $pdfp_fs = fs_dynamic_init( array(
                    'id'             => '14261',
                    'slug'           => 'pdf-poster',
                    'premium_slug'   => 'pdf-poster-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_6e833032174d131283193892a44a2',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                        'slug'    => 'edit.php?post_type=pdfposter',
                        'support' => false,
                        'contact' => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $pdfp_fs;
        }

        // Init Freemius.
        pdfp_fs();
        // Signal that SDK was initiated.
        do_action( 'pdfp_fs_loaded' );
    }
    if ( class_exists( 'PDFPro\\Init' ) ) {
        PDFPro\Init::register_services();
    }
    require_once __DIR__ . '/upgrade.php';
    function get_p_option(  $array, $key = array(), $default = null  ) {
        if ( is_array( $array ) && array_key_exists( $key, $array ) ) {
            return $array[$key];
        }
        return $default;
    }

    add_action( 'media_buttons', 'pdfp_my_media_button', 3 );
    function pdfp_my_media_button() {
        echo wp_kses_post( '<a href="#" id="insert-pdf" class="button pdfp_insert_pdf_btn">
        <img src="' . PDFPRO_PLUGIN_DIR . '/img/icn.png' . '" alt="" width="20" height="20" style="position:relative; top:-1px">
        Add PDF</a>' );
    }

    add_action( 'admin_init', 'pdfp_admin_init' );
    function pdfp_admin_init() {
        if ( get_option( 'pdfp_import', '0' ) != PDFPRO_IMPORT_VER ) {
            PDFPro\Model\Import::meta();
            PDFPro\Model\Import::settings();
            update_option( 'pdfp_import', PDFPRO_IMPORT_VER );
        }
    }

    add_action( 'wp_head', function () {
        $option = get_option( 'fpdf_option' );
        ?>
        <style>
            <?php 
        echo esc_html( $option['custom_css'] ?? '' );
        ?>
        </style>
<?php 
    } );
}
add_action( 'admin_footer', function () {
    ?>
    <script>
      let tokenClient;
      let accessToken = null;
      let pickerInited = false;
      let gisInited = false;

      // Use the API Loader script to load google.picker.
      function onApiLoad() {
        gapi.load('picker', onPickerApiLoad);
        console.log('onApiLoad')
      }

      function onPickerApiLoad() {
        pickerInited = true;
        console.log('onPickerApiLoad')
        createPicker();
      }

      function gisLoaded() {
        // Replace with your client ID and required scopes.
        tokenClient = google.accounts.oauth2.initTokenClient({
          client_id: '637181304358-pj80esagrsfce2carm5638s7g6lkni3k.apps.googleusercontent.com',
          scope: 'https://www.googleapis.com/auth/drive.readonly',
        //   callback: createPicker, // defined later
        });
        gisInited = true;
        console.log('gisLoaded')
    }

   // Create and render a Google Picker object for selecting from Drive.
    function createPicker() {
        console.log('creating picker')
      const showPicker = () => {
        // Replace with your API key and App ID.
        const picker = new google.picker.PickerBuilder()
            .addView(google.picker.ViewId.DOCS)
            .setOAuthToken(accessToken)
            .setDeveloperKey('AIzaSyBlV3lAGilIjq0x2eJGodDs5DlkCigykZw')
            .setCallback(pickerCallback)
            .setAppId('637181304358')
            .build();
        picker.setVisible(true);
      }

      // Request an access token.
      tokenClient.callback = async (response) => {
        console.log('createPicker', response)
        if (response.error !== undefined) {
          throw (response);
        }
        accessToken = response.access_token;
        showPicker();
      };

      if (accessToken === null) {
        // Prompt the user to select a Google Account and ask for consent to share their data
        // when establishing a new session.
        tokenClient.requestAccessToken({prompt: 'consent'});
      } else {
        // Skip display of account chooser and consent dialog for an existing session.
        tokenClient.requestAccessToken({prompt: ''});
      }
    }

        // A callback implementation.
    function pickerCallback(data) {
      let url = 'nothing';
      if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
        const doc = data[google.picker.Response.DOCUMENTS][0];
        url = doc[google.picker.Document.URL];
      }
      const message = `You picked: ${url}`;
      document.getElementById('result').textContent = message;
    }

    </script>
 <!-- <script async defer src="https://apis.google.com/js/api.js" onload="onApiLoad()"></script> -->
    <!-- <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>     -->
<?php 
} );