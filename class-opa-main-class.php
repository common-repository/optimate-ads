<?php
/**
 * Plugin Name:       Optimate Ads - Advance Ad Inserter AdSense & Ad Manager
 * Description:       Place Ads Code Anywhere you want to.  Most Lite weighted plugin to place ads.
 * Version:           1.0.3
 * Author:            Optimate Ads
 * Developed By:      Shujahat Ali
 * Author URI:        https://optimateads.com
 * Support:           https://optimateads.com/contact-us
 * Domain Path:       /languages
 * Text Domain:       optimate-ads
 *
 * @package optimate-ads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Opa_Main_Class' ) ) {
	/**
	 * Main class pf the plugin.
	 */
	class Opa_Main_Class {

		/**
		 * Constructor of class main.
		 */
		public function __construct() {
			$this->global_constant_vars();
			include_once OPAD_CONSTANT_DIR . '/widgets/class-optimate-ad-widget.php';
			if ( is_admin() ) {
				include_once OPAD_CONSTANT_DIR . '/admin/class-opad-admin-class.php';
			} else {
				include_once OPAD_CONSTANT_DIR . '/front/class-opad-front-class.php';
			}

			add_action( 'wp_loaded', array( $this, 'load_text_domain' ) );

			add_action( 'init', array( $this, 'create_post_type' ) );

		}

		/**
		 * Function to register text domain.
		 */
		public function load_text_domain() {
			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'optimate-ads', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}
		}

		/**
		 * Function for registering global constants.
		 */
		public function global_constant_vars() {
			if ( ! defined( 'OPAD_CONSTANT_URL' ) ) {
				define( 'OPAD_CONSTANT_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'OPAD_CONSTANT_BASENAME' ) ) {
				define( 'OPAD_CONSTANT_BASENAME', plugin_basename( __FILE__ ) );
			}
			if ( ! defined( 'OPAD_CONSTANT_DIR' ) ) {
				define( 'OPAD_CONSTANT_DIR', plugin_dir_path( __FILE__ ) );
			}
		}

		/**
		 * Function for registering post type.
		 */
		public function create_post_type() {
			$label_product = array(
				'name'                => __( 'Optimate Ads', 'optimate-ads' ),
				'singular_name'       => __( 'Optimate Ads', 'optimate-ads' ),
				'add_new'             => __( 'Add New new Ad', 'optimate-ads' ),
				'add_new_item'        => __( 'Add new Ad', 'optimate-ads' ),
				'edit_item'           => __( 'Edit new Ad', 'optimate-ads' ),
				'new_item'            => __( 'New new Ad', 'optimate-ads' ),
				'view_item'           => __( 'View new Ad', 'optimate-ads' ),
				'search_items'        => __( 'Search new Ad', 'optimate-ads' ),
				'exclude_from_search' => true,
				'not_found'           => __( 'No rule found', 'optimate-ads' ),
				'not_found_in_trash'  => __( 'No rule found in trash', 'optimate-ads' ),
				'parent_item_colon'   => '',
				'all_items'           => __( 'All new Ads', 'optimate-ads' ),
				'menu_name'           => __( 'SSAG Ads', 'optimate-ads' ),
				'attributes'          => esc_html__( 'Rule order', 'optimate-ads' ),
			);
			$args_product  = array(
				'labels'             => $label_product,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => false,
				'query_var'          => true,
				'rewrite'            => true,
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 30,
				'rewrite'            => array(
					'slug'       => 'optimate-ads-rule',
					'with_front' => false,
				),
				'supports'           => array( 'title', 'page-attributes' ),
			);
			register_post_type( 'ssag_adds_post_type', $args_product );
		}

	}
	$ssag_op_ads_main_class_obj = new Opa_Main_Class();
}

if ( ! function_exists( 'ssag_all_posts_ads_fn' ) ) {
	/**
	 * Function for getting all ads.
	 */
	function opad_all_posts_ads_fn() {
		$args = array(
			'post_type'      => 'ssag_adds_post_type',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'ssag_ad_saved',
					'value'   => 'saved',
					'compare' => '==',
				),
			),

		);

		return (array) get_posts( $args );
	}
}
