<?php

namespace PDFPro\PostType;

if ( ! defined( 'ABSPATH' ) ) exit;

class PDFPoster
{
    protected static $_instance = null;
    protected $post_type = 'pdfposter';

    /**
     * construct function
     */
    public function __construct()
    {
        add_action('init', [$this, 'init'], 20);
        if (is_admin()) {
            add_filter('post_row_actions', [$this, 'pdfp_remove_row_actions'], 10, 2);

            add_filter('manage_pdfposter_posts_columns', [$this, 'pdfp_columns_head_only_podcast'], 10);
            add_action('manage_pdfposter_posts_custom_column', [$this, 'pdfp_columns_content_only_podcast'], 10, 2);
            add_filter('post_updated_messages', [$this, 'pdfp_updated_messages']);

            add_action('admin_head-post.php', [$this, 'pdfp_hide_publishing_actions']);
            add_action('admin_head-post-new.php', [$this, 'pdfp_hide_publishing_actions']);
            add_filter('gettext', [$this, 'pdfp_change_publish_button'], 10, 2);

            add_filter('filter_block_editor_meta_boxes', [$this, 'remove_metabox']);
            add_action('use_block_editor_for_post', [$this, 'forceGutenberg'], 10, 2);
            
            add_action('add_meta_boxes', [$this, 'shortcode_area_metabox']);
            // add_action('add_meta_boxes', [$this, 'myplugin_add_meta_box']);
        }
    }

    /**
     * Create instance function
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * init
     */
    public function init()
    {

        register_post_type(
            'pdfposter',
            array(
                'labels' => array(
                    'name' => __('PDF Poster', 'pdfp'),
                    'singular_name' => __('PDF Poster', 'pdfp'),
                    'add_new' => __('Add New PDF', 'pdfp'),
                    'add_new_item' => __(' &#8627; Add New', 'pdfp'),
                    'edit_item' => __('Edit', 'pdfp'),
                    'new_item' => __('New PDF', 'pdfp'),
                    'view_item' => __('View PDF', 'pdfp'),
                    'search_items'       => __('Search PDF', 'pdfp'),
                    'all_items' => __('All PDF Posters', 'pdfp'),
                    'not_found' => __('Sorry, we couldn\'t find the PDF file you are looking for.', 'pdfp')
                ),
                'public' => false,
                'show_ui' => true,
                // 'publicly_queryable' => true,
                // 'exclude_from_search' => true,
                'show_in_rest' => true,
                'menu_position' => 14,
                'menu_icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBMaWNlbnNlOiBNSVQuIE1hZGUgYnkgR2FydWRhIFRlY2hub2xvZ3k6IGh0dHBzOi8vZ2l0aHViLmNvbS9nYXJ1ZGF0ZWNobm9sb2d5ZGV2ZWxvcGVycy9za2V0Y2gtaWNvbnMgLS0+Cjxzdmcgd2lkdGg9IjgwMHB4IiBoZWlnaHQ9IjgwMHB4IiB2aWV3Qm94PSItNCAwIDQwIDQwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBkPSJNMjUuNjY4NiAyNi4wOTYyQzI1LjE4MTIgMjYuMjQwMSAyNC40NjU2IDI2LjI1NjMgMjMuNjk4NCAyNi4xNDVDMjIuODc1IDI2LjAyNTYgMjIuMDM1MSAyNS43NzM5IDIxLjIwOTYgMjUuNDAzQzIyLjY4MTcgMjUuMTg4OCAyMy44MjM3IDI1LjI1NDggMjQuODAwNSAyNS42MDA5QzI1LjAzMTkgMjUuNjgyOSAyNS40MTIgMjUuOTAyMSAyNS42Njg2IDI2LjA5NjJaTTE3LjQ1NTIgMjQuNzQ1OUMxNy4zOTUzIDI0Ljc2MjIgMTcuMzM2MyAyNC43Nzc2IDE3LjI3NzYgMjQuNzkzOUMxNi44ODE1IDI0LjkwMTcgMTYuNDk2MSAyNS4wMDY5IDE2LjEyNDcgMjUuMTAwNUwxNS42MjM5IDI1LjIyNzVDMTQuNjE2NSAyNS40ODI0IDEzLjU4NjUgMjUuNzQyOCAxMi41NjkyIDI2LjA1MjlDMTIuOTU1OCAyNS4xMjA2IDEzLjMxNSAyNC4xNzggMTMuNjY2NyAyMy4yNTY0QzEzLjkyNzEgMjIuNTc0MiAxNC4xOTMgMjEuODc3MyAxNC40NjggMjEuMTg5NEMxNC42MDc1IDIxLjQxOTggMTQuNzUzMSAyMS42NTAzIDE0LjkwNDYgMjEuODgxNEMxNS41OTQ4IDIyLjkzMjYgMTYuNDYyNCAyMy45MDQ1IDE3LjQ1NTIgMjQuNzQ1OVpNMTQuODkyNyAxNC4yMzI2QzE0Ljk1OCAxNS4zODMgMTQuNzA5OCAxNi40ODk3IDE0LjM0NTcgMTcuNTUxNEMxMy44OTcyIDE2LjIzODYgMTMuNjg4MiAxNC43ODg5IDE0LjI0ODkgMTMuNjE4NUMxNC4zOTI3IDEzLjMxODUgMTQuNTEwNSAxMy4xNTgxIDE0LjU4NjkgMTMuMDc0NEMxNC43MDQ5IDEzLjI1NjYgMTQuODYwMSAxMy42NjQyIDE0Ljg5MjcgMTQuMjMyNlpNOS42MzM0NyAyOC44MDU0QzkuMzgxNDggMjkuMjU2MiA5LjEyNDI2IDI5LjY3ODIgOC44NjA2MyAzMC4wNzY3QzguMjI0NDIgMzEuMDM1NSA3LjE4MzkzIDMyLjA2MjEgNi42NDk0MSAzMi4wNjIxQzYuNTk2ODEgMzIuMDYyMSA2LjUzMzE2IDMyLjA1MzYgNi40NDAxNSAzMS45NTU0QzYuMzgwMjggMzEuODkyNiA2LjM3MDY5IDMxLjg0NzYgNi4zNzM1OSAzMS43ODYyQzYuMzkxNjEgMzEuNDMzNyA2Ljg1ODY3IDMwLjgwNTkgNy41MzUyNyAzMC4yMjM4QzguMTQ5MzkgMjkuNjk1NyA4Ljg0MzUyIDI5LjIyNjIgOS42MzM0NyAyOC44MDU0Wk0yNy4zNzA2IDI2LjE0NjFDMjcuMjg4OSAyNC45NzE5IDI1LjMxMjMgMjQuMjE4NiAyNS4yOTI4IDI0LjIxMTZDMjQuNTI4NyAyMy45NDA3IDIzLjY5ODYgMjMuODA5MSAyMi43NTUyIDIzLjgwOTFDMjEuNzQ1MyAyMy44MDkxIDIwLjY1NjUgMjMuOTU1MiAxOS4yNTgyIDI0LjI4MTlDMTguMDE0IDIzLjM5OTkgMTYuOTM5MiAyMi4yOTU3IDE2LjEzNjIgMjEuMDczM0MxNS43ODE2IDIwLjUzMzIgMTUuNDYyOCAxOS45OTQxIDE1LjE4NDkgMTkuNDY3NUMxNS44NjMzIDE3Ljg0NTQgMTYuNDc0MiAxNi4xMDEzIDE2LjM2MzIgMTQuMTQ3OUMxNi4yNzM3IDEyLjU4MTYgMTUuNTY3NCAxMS41Mjk1IDE0LjYwNjkgMTEuNTI5NUMxMy45NDggMTEuNTI5NSAxMy4zODA3IDEyLjAxNzUgMTIuOTE5NCAxMi45ODEzQzEyLjA5NjUgMTQuNjk4NyAxMi4zMTI4IDE2Ljg5NjIgMTMuNTYyIDE5LjUxODRDMTMuMTEyMSAyMC41NzUxIDEyLjY5NDEgMjEuNjcwNiAxMi4yODk1IDIyLjczMTFDMTEuNzg2MSAyNC4wNDk4IDExLjI2NzQgMjUuNDEwMyAxMC42ODI4IDI2LjcwNDVDOS4wNDMzNCAyNy4zNTMyIDcuNjk2NDggMjguMTM5OSA2LjU3NDAyIDI5LjEwNTdDNS44Mzg3IDI5LjczNzMgNC45NTIyMyAzMC43MDI4IDQuOTAxNjMgMzEuNzEwN0M0Ljg3NjkzIDMyLjE4NTQgNS4wMzk2OSAzMi42MjA3IDUuMzcwNDQgMzIuOTY5NUM1LjcyMTgzIDMzLjMzOTggNi4xNjMyOSAzMy41MzQ4IDYuNjQ4NyAzMy41MzU0QzguMjUxODkgMzMuNTM1NCA5Ljc5NDg5IDMxLjMzMjcgMTAuMDg3NiAzMC44OTA5QzEwLjY3NjcgMzAuMDAyOSAxMS4yMjgxIDI5LjAxMjQgMTEuNzY4NCAyNy44Njk5QzEzLjEyOTIgMjcuMzc4MSAxNC41Nzk0IDI3LjAxMSAxNS45ODUgMjYuNjU2MkwxNi40ODg0IDI2LjUyODNDMTYuODY2OCAyNi40MzIxIDE3LjI2MDEgMjYuMzI1NyAxNy42NjM1IDI2LjIxNTNDMTguMDkwNCAyNi4wOTk5IDE4LjUyOTYgMjUuOTgwMiAxOC45NzYgMjUuODY2NUMyMC40MTkzIDI2Ljc4NDQgMjEuOTcxNCAyNy4zODMxIDIzLjQ4NTEgMjcuNjAyOEMyNC43NjAxIDI3Ljc4ODMgMjUuODkyNCAyNy42ODA3IDI2LjY1ODkgMjcuMjgxMUMyNy4zNDg2IDI2LjkyMTkgMjcuMzg2NiAyNi4zNjc2IDI3LjM3MDYgMjYuMTQ2MVpNMzAuNDc1NSAzNi4yNDI4QzMwLjQ3NTUgMzguMzkzMiAyOC41ODAyIDM4LjUyNTggMjguMTk3OCAzOC41MzAxSDMuNzQ0ODZDMS42MDIyNCAzOC41MzAxIDEuNDczMjIgMzYuNjIxOCAxLjQ2OTEzIDM2LjI0MjhMMS40Njg4NCAzLjc1NjQyQzEuNDY4ODQgMS42MDM5IDMuMzY3NjMgMS40NzM0IDMuNzQ0NTcgMS40NjkwOEgyMC4yNjNMMjAuMjcxOCAxLjQ3NzhWNy45MjM5NkMyMC4yNzE4IDkuMjE3NjMgMjEuMDUzOSAxMS42NjY5IDI0LjAxNTggMTEuNjY2OUgzMC40MjAzTDMwLjQ3NTMgMTEuNzIxOEwzMC40NzU1IDM2LjI0MjhaTTI4Ljk1NzIgMTAuMTk3NkgyNC4wMTY5QzIxLjg3NDkgMTAuMTk3NiAyMS43NDUzIDguMjk5NjkgMjEuNzQyNCA3LjkyNDE3VjIuOTUzMDdMMjguOTU3MiAxMC4xOTc2Wk0zMS45NDQ3IDM2LjI0MjhWMTEuMTE1N0wyMS43NDI0IDAuODcxMDIyVjAuODIzMzU3SDIxLjY5MzZMMjAuODc0MiAwSDMuNzQ0OTFDMi40NDk1NCAwIDAgMC43ODUzMzYgMCAzLjc1NzExVjM2LjI0MzVDMCAzNy41NDI3IDAuNzgyOTU2IDQwIDMuNzQ0OTEgNDBIMjguMjAwMUMyOS40OTUyIDM5Ljk5OTcgMzEuOTQ0NyAzOS4yMTQzIDMxLjk0NDcgMzYuMjQyOFoiIGZpbGw9IiNFQjU3NTciLz4KPC9zdmc+',
                'has_archive' => false,
                'hierarchical' => false,
                'capability_type' => 'post',
                'rewrite' => array('slug' => 'pdfposter'),
                'supports' => array('title', 'editor'),
                'template' => [
                    ['pdfp/pdfposter']
                ],
                'template_lock' => 'all',
            )
        );

        // // 'publicly_queryable' => true,
        // // 'exclude_from_search' => true,
        // 'show_in_rest' => true,
        // 'supports' => array('title', 'editor'),
        // 'template' => [
        //     ['pdfp/podcast']
        // ],
        // 'template_lock' => 'all',
    }

    /**
     * Remove Row
     */
    function pdfp_remove_row_actions($idtions)
    {
        global $post;
        if (!$post) {
            return $idtions;
        }
        if ($post->post_type == $this->post_type) {
            unset($idtions['view']);
            unset($idtions['inline hide-if-no-js']);
        }
        return $idtions;
    }

  

    // CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
    function pdfp_columns_head_only_podcast($defaults)
    {
        unset($defaults['date']);
        $defaults['shortcode'] = 'ShortCode';
        $defaults['raw_shortCode'] = esc_html__('ShortCode For Raw PDF View', 'pdfp');
        $defaults['date'] = 'Date';
        return $defaults;
    }

    function pdfp_columns_content_only_podcast($column_name, $post_ID)
    {
        if ($column_name == 'shortcode') {
            echo '<div class="pdfp_front_shortcode"><input class="pdfp_front_shortcode_input"  value="Copy Shortcode" data-value="[pdf id=' . esc_attr($post_ID) . ']" ><span class="htooltip">Copy To Clipboard</span></div>';
        }
        if ($column_name == 'raw_shortCode') {
            // show content of 'directors_name' column
            echo '<div class="pdfp_front_shortcode"><input class="pdfp_front_shortcode_input"  value="Copy Shortcode" data-value="[raw_pdf id=' . esc_attr($post_ID) . ']" ><span class="htooltip">Copy To Clipboard</span></div>';
        }
    }

    function pdfp_updated_messages($messages)
    {
        $messages[$this->post_type][1] = __('Player updated ', 'pdfp');
        return $messages;
    }

    public function pdfp_hide_publishing_actions()
    {
        global $post;
        if (!$post) {
            return;
        }
        if ($post->post_type == $this->post_type) {
            echo '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
        }
    }

    function remove_metabox($metaboxs)
    {
        global $post;
        $screen = get_current_screen();

        if (!$screen) {
            return $metaboxs;
        }

        if ($screen->post_type === $this->post_type) {
            return false;
        }
        return $metaboxs;
    }

    public function forceGutenberg($use, $post)
    {
        $option = get_option('fpdf_option', []);
        if (isset($option['pdfp_gutenberg_enable'])) {
            $gutenberg = (bool) $option['pdfp_gutenberg_enable'];
        } else {
            $gutenberg = (bool) get_option('pdfp_gutenberg_enable', false);
        }
        $isGutenberg = (bool) get_post_meta($post->ID, 'isGutenberg', true);
        $pluginUpdated = 1630223686;
        $publishDate = get_the_date('U', $post);
        $currentTime = current_time("U");

        if ($this->post_type === $post->post_type) {
            if ($gutenberg) {
                if ($post->post_status == 'auto-draft') {
                    update_post_meta($post->ID, 'isGutenberg', true);
                    return true;
                } else {
                    if ($isGutenberg) {
                        return true;
                    } else {
                        remove_post_type_support($this->post_type, 'editor');
                        return false;
                    }
                }
            } else {
                if ($isGutenberg) {
                    return true;
                } else {
                    remove_post_type_support($this->post_type, 'editor');
                    return false;
                }
            }
        }

        return $use;
    }

    function pdfp_change_publish_button($translation, $text)
    {
        if ($this->post_type == get_post_type())
            if ($text == 'Publish')
                return 'Save';
        return $translation;
    }

    /**
     * register metabox
     */
    function myplugin_add_meta_box()
    {
        add_meta_box(
            'Shortcode',
            __('New Feature ! Quick Embed', 'pdfp'),
            [$this, 'pdfp_pro_shortcode_wid'],
            'pdfposter',
            'side',
            'default'
        );
    }

    // shortcode area
    public function shortcode_area_metabox()
    {
        global $post;
        if(!$post){
            return;
        }
        if ($post->post_type == $this->post_type) {
           add_meta_box(
            'shortcode_area',
            __('Shortcode', 'pdfp'),
            [$this, 'shortcode_area'],
            'pdfposter',
            'side',
            'default'
        );
        }
    }

    function shortcode_area(){
        global $post;
        $id = $post->ID;

        $shortcode = "[pdf id='" . esc_attr($id) . "']";
        ?>
        <div class="pdfp-down-arrow"></div>
        <div class="pdfp_front_shortcode_area">
            <label><?php esc_html_e('Copy and paste this shortcode into your posts, pages and widget', 'pdfp'); ?></label>
            <br />
            <button class="button button-primary button-large pdfp_shortcode_copy_btn" data-clipboard-text="<?php echo esc_attr($shortcode) ?>"><?php esc_html_e('Copy Shortcode', 'pdfp'); ?></button>
        </div>
        <?php
    }

    function pdfp_pro_shortcode_wid()
    {
        $shortcode = "[pdf_embed url='your_file_url']";
        echo 'Now you can embed pdf without listing ! just use the Embed shortCode below, and start saving your time.';
        echo '<br/><br/><input type="text" style="font-size: 12px; border: none; box-shadow: none; padding: 4px 8px; width:100%; background:#1e8cbe; color:white;"  onfocus="this.select();" readonly="readonly"  value="' . esc_attr($shortcode) . '" /><br/><br/>';
        echo '<p><a class="button button-primary button-large" href="admin.php?page=fpdf-settings" target="_blank">ShortCode Global Settings</a></p>';
    }
}
