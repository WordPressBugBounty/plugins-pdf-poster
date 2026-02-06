<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly
use PDFPro\Helper\Functions as Utils;

$id = wp_unique_id('block-');
$attributes['isPremium'] = pdfp_fs()->can_use_premium_code();

extract($attributes);

if ($protect && $isPremium) {
    $attributes['file'] = Utils::scramble('encode', $attributes['file']);
}

$className = $className ?? '';
$blockClassName = 'wp-block-pdfp-pdf-poster ' . $className . ' align' . $align;
$isPopupEnabled = isset($popupOptions['enabled']) ? $popupOptions['enabled'] : false;

$isDropbox = strpos($file, 'dropbox.com') !== false;

if($isDropbox){
    ?>
    <a data-height="<?php echo esc_attr($height ?? '400px') ?>" data-width="<?php echo esc_attr($width ?? '100%') ?>" href="<?php echo esc_url($file) ?>" target="_blank" class="dropbox-embed" rel="noopener noreferrer">Open in new tab</a>
    <?php
}else {
?>

<div
    class='<?php echo esc_attr($blockClassName); ?>'
    id='<?php echo esc_attr($id); ?>'
    data-attributes='<?php echo esc_attr(wp_json_encode($attributes)); ?>'
    style="text-align: <?php echo esc_attr($alignment) ?>">
    <?php if (!$protect && !$isPopupEnabled) { ?>

        <iframe title="<?php echo esc_attr($attributes['title']); ?>" style="border:0;" width="100%" height="800px" class="pdfp_unsupported_frame" src="//docs.google.com/gview?embedded=true&url=<?php echo esc_url($file) ?>"></iframe>

    <?php } ?>
</div>
<?php 
}
