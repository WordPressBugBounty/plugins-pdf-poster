<?php
namespace PDFPro\Rest;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'PDFPro\Rest\PDFP_AjaxCall' ) ) {
    class PDFP_AjaxCall {
        protected static $_instance = null;

        public function __construct() {}

        /**
         * Create Instance
         */
        public static function instance() {
            if (self::$_instance === null) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function register() {
            add_action('rest_api_init', [$this, 'register_routes']);
        }

        public function register_routes() {
            register_rest_route('pdfp/v1', '/ajax', [
                'methods' => 'POST',
                'callback' => [$this, 'proceedRequest'],
                'permission_callback' => [$this, 'get_permission']
            ]);
        }

        public function get_permission() {
            return current_user_can('manage_options');
        }

        public function proceedRequest($request) {
            $data = $request->get_params();
            $requestModel = isset($data['model']) && is_string($data['model'])
                ? sanitize_text_field($data['model'])
                : '';

            $requestMethod = isset($data['method']) && is_string($data['method'])
                ? sanitize_text_field($data['method'])
                : '';

            $allowed_models = [
                'Analytics',
                'Block',
                'AjaxCall'
            ];

            if (!in_array($requestModel, $allowed_models, true)) {
                return new \WP_REST_Response('Invalid model', 400);
            }

            $class_name = 'PDFPro\\Model\\PDFP_' . $requestModel;

            if (!class_exists($class_name)) {
                return new \WP_REST_Response('Invalid model', 400);
            }

            $model = new $class_name();

            $allowed_methods = ['get', 'getBlock', 'totals', 'today', 'documents', 'summary', 'export'];

            if (
                in_array($requestMethod, $allowed_methods, true) &&
                is_callable([$model, $requestMethod])
            ) {
                return $model->{$requestMethod}($data);
            }

            return new \WP_REST_Response('Invalid method', 400);
        }
    }
}
