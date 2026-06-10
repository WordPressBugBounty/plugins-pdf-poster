<?php

namespace PDFPro\Base;

use PDFPro\Helper\PDFP_Functions as Utils;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'PDFPro\Base\PDFP_Shortcodes' ) ) {
    class PDFP_Shortcodes {

  public function register() {
    add_shortcode('pdf', [$this, 'pdf'], 10, 2);
    add_shortcode('raw_pdf', [$this, 'raw_pdf']);
    add_shortcode('pdf_embed', [$this, 'pdf_embed']);
  }

  public function pdf($atts, $content) {
    extract(shortcode_atts(array(
      'id' => null,
    ), $atts));

    if (empty($id)) {
      return current_user_can('manage_options') ? '<p style="color:red">PDF Poster: Please provide a valid ID in shortcode.</p>' : '';
    }

    $id = absint($id);
    $post_type = get_post_type($id);
    $post = get_post($id);

    if ($post_type !== 'pdfposter' || !$post) {
      return current_user_can('manage_options') ? '<p style="color:red">PDF Poster: Invalid PDF Poster ID.</p>' : '';
    }

    // Enqueue blocks assets
    wp_enqueue_script('pdfp-public');
    wp_enqueue_style('pdfp-public');
    wp_enqueue_script('pdfp-pdfposter-view-script');

    $isGutenberg = get_post_meta($id, 'isGutenberg', true);
    $hasBlock = has_block('pdfp/pdfposter', $post);

    if ($hasBlock || $isGutenberg) {
      $content = $post->post_content ?? ' ';
      $blocks = parse_blocks($content);
      
      foreach ($blocks as $block) {
        if ($block['blockName'] === 'pdfp/pdfposter') {
          return render_block($block);
        }
      }
      return render_block($blocks[0]);
    } else {
      $block = Utils::generate_pdf_poster_block($id);
      return render_block($block);
    }
  }

  // Raw PDF ShortCode
  public function raw_pdf($atts) {
    extract(shortcode_atts(array(
      'id' => null,
    ), $atts));

    if (empty($id)) {
      return current_user_can('manage_options') ? '<p style="color:red">PDF Poster: Please provide a valid ID in shortcode.</p>' : '';
    }

    $id = absint($id);
    $post_type = get_post_type($id);
    $post = get_post($id);

    if ($post_type !== 'pdfposter' || !$post) {
      return current_user_can('manage_options') ? '<p style="color:red">PDF Poster: Invalid PDF Poster ID.</p>' : '';
    }

    // Enqueue blocks assets
    wp_enqueue_script('pdfp-public');
    wp_enqueue_style('pdfp-public');
    wp_enqueue_script('pdfp-pdfposter-view-script');

    $isGutenberg = get_post_meta($id, 'isGutenberg', true);
    $hasBlock = has_block('pdfp/pdfposter', $post);

    if ($hasBlock || $isGutenberg) {
      $content = $post->post_content ?? false;
      if ($content) {
        $blocks = parse_blocks($content);
        foreach ($blocks as $block) {
          if ($block['blockName'] === 'pdfp/pdfposter') {
            $block['attrs']['onlyPDF'] = true;
            return render_block($block);
          }
        }
        $blocks[0]['attrs']['onlyPDF'] = true;
        return render_block($blocks[0]);
      }
    } else {
      $block = Utils::generate_pdf_poster_block($id);
      $block['attrs']['onlyPDF'] = true;
      return render_block($block);
    }
  }

  public function pdf_embed($atts) {
    $attrs = shortcode_atts($this->pdf_embed_attrs(), $atts);

    $block = $this->pdf_embed_to_block($attrs);

    // Enqueue blocks assets
    wp_enqueue_script('pdfp-public');
    wp_enqueue_style('pdfp-public');
    wp_enqueue_script('pdfp-pdfposter-view-script');

    return render_block($block);
  }


  public function pdf_embed_attrs() {
    $options = get_option('fpdf_option', []);
    
    $height_opt = Utils::isset($options, 'height', ['height' => '842', 'unit' => 'px']);
    $width_opt = Utils::isset($options, 'width', ['width' => '100', 'unit' => '%']);
    
    $default_height = is_array($height_opt) ? ($height_opt['height'] . $height_opt['unit']) : '842px';
    $default_width = is_array($width_opt) ? ($width_opt['width'] . $width_opt['unit']) : '100%';

    return [
      'url' => null,
      'width' => $default_width,
      'height' => $default_height,
      'print' => 'false',
      'title' => null,
      'download_btn' => (Utils::isset($options, 'show_download_btn', '0') === '1') ? 'true' : 'false',
      'download_btn_text' => Utils::isset($options, 'download_btn_text', __('Download File', 'pdf-poster')),
      'show_name' => (Utils::isset($options, 'show_filename', '0') === '1') ? 'true' : 'false',
      'fullscreen_btn_text' => __('View Fullscreen', 'pdf-poster')
    ];
  }

  public function pdf_embed_to_block($attrs) {
    extract($attrs);

    if (empty($title) && !empty($url)) {
      $title = basename(wp_parse_url($url, PHP_URL_PATH));
      $title = str_replace(['-', '_'], ' ', $title);
      $title = ucwords(pathinfo($title, PATHINFO_FILENAME));
    }

    return [
      "blockName" => "pdfp/pdfposter",
      "attrs" => [
        'uniqueId' => wp_unique_id('pdf-poster'),
        'file' => esc_url($url),
        'title' => esc_html($title),
        'titleFontSize' => '16px',
        'height' => esc_html($height),
        'width' => esc_html($width),
        'print' => $print === 'true',
        'showName' => $show_name === 'true',
        'downloadButton' => $download_btn === 'true',
        'downloadButtonText' => esc_html($download_btn_text),
        'fullscreenButtonText' => esc_html($fullscreen_btn_text),
        'fullscreenButton' => true
      ]
    ];
  }
}
}
