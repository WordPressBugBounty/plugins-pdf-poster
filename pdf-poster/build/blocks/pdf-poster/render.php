<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
use PDFPro\Helper\Functions as Utils;

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$pdfp_attributes = $attributes;
$pdfp_attributes['isPremium'] = false;

$pdfp_id = wp_unique_id('block-');
$pdfp_align = $pdfp_attributes['align'] ?? '';
$pdfp_class_name = $pdfp_attributes['className'] ?? '';
$pdfp_block_class_name = 'wp-block-pdfp-pdf-poster ' . $pdfp_class_name . ' align' . $pdfp_align;
$pdfp_popup_options = $pdfp_attributes['popupOptions'] ?? [];
$pdfp_is_popup_enabled = isset($pdfp_popup_options['enabled']) ? $pdfp_popup_options['enabled'] : false;

$pdfp_file = $pdfp_attributes['file'] ?? '';
$pdfp_is_dropbox = strpos($pdfp_file, 'dropbox.com') !== false;

$pdfp_height = $pdfp_attributes['height'] ?? '400px';
$pdfp_width = $pdfp_attributes['width'] ?? '100%';
$pdfp_alignment = $pdfp_attributes['alignment'] ?? 'left';
$pdfp_protect = $pdfp_attributes['protect'] ?? false;

if ($pdfp_is_dropbox) {
    ?>
    <a data-height="<?php echo esc_attr(is_string($pdfp_height) ? $pdfp_height : ($pdfp_height['desktop'] ?? '400px')) ?>" 
       data-width="<?php echo esc_attr(is_string($pdfp_width) ? $pdfp_width : ($pdfp_width['desktop'] ?? '100%')) ?>"
        href="<?php echo esc_url($pdfp_file) ?>" target="_blank" class="dropbox-embed" rel="noopener noreferrer">Open in new
        tab</a>
    <?php
} else {
    ?>

    <div class='<?php echo esc_attr($pdfp_block_class_name); ?>' id='<?php echo esc_attr($pdfp_id); ?>'
        data-attributes='<?php echo esc_attr(wp_json_encode($pdfp_attributes)); ?>'
        style="text-align: <?php echo esc_attr($pdfp_alignment) ?>">
        <?php if (!$pdfp_protect && !$pdfp_is_popup_enabled) {
            $pdfp_p_height = is_string($pdfp_height) ? $pdfp_height : ($pdfp_height['desktop'] ?? '800px');
            $pdfp_p_width = is_string($pdfp_width) ? $pdfp_width : ($pdfp_width['desktop'] ?? '100%');
            ?>
            <div class="pdfp_loading_placeholder"
                style="height: <?php echo esc_attr($pdfp_p_height); ?>; width: <?php echo esc_attr($pdfp_p_width); ?>; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border: 1px solid #ddd;">
                <p><?php esc_html_e('Loading Viewer...', 'pdfp'); ?></p>
            </div>
        <?php } ?>
    </div>
<?php
}
