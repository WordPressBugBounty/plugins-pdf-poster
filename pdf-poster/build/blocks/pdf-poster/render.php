<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use PDFPro\Helper\Functions as Utils;

$id = wp_unique_id('pdfp-');

extract($attributes );

if($protect){
    $attributes['file'] = Utils::scramble('encode', $attributes['file']);
}

// echo '<pre>';
// print_r( $attributes );
// echo '</pre>';

$className = $className ?? '';
$blockClassName = 'wp-block-pdfp-pdf-poster ' . $className . ' align' . $align;
$isPopupEnabled = isset($popupOptions['enabled']) ? $popupOptions['enabled'] : false;

?>

<div 
    class='<?php echo esc_attr( $blockClassName ); ?>'
    id='<?php echo esc_attr( $id ); ?>'
    data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'
    style="text-align: <?php echo esc_attr($alignment) ?>"
>
<?php if(!$protect && !$isPopupEnabled) { ?>

    <iframe class="pdfp_unsupported_frame" width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>"  src="//docs.google.com/gview?embedded=true&url=<?php echo esc_url($file) ?>" ></iframe>

    <?php } ?>
</div>