<?php
/**
 * Admin file for settings and meta boxes
 *
 * @package for Admin settings
 * Admin settings File.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin class for Optimate Ads
 */
class Opad_Admin_Class {

	/**
	 * Constructor of class
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );

		add_action( 'admin_menu', array( $this, 'custom_menu' ) );

		add_action( 'wp_loaded', array( $this, 'add_ads_num' ) );

		add_action( 'wp_ajax_ssag_save_single_add', array( $this, 'ssag_save_single_add_cb' ) );

		add_action( 'wp_ajax_ssag_save_ads_txt', array( $this, 'ssag_save_ads_txt_cb' ) );
		add_action( 'wp_ajax_nopriv_ssag_save_ads_txt', array( $this, 'ssag_save_ads_txt_cb' ) );

		add_action( 'wp_ajax_ssag_save_header', array( $this, 'ssag_save_header_cb' ) );
		add_action( 'wp_ajax_nopriv_ssag_save_header', array( $this, 'ssag_save_header_cb' ) );

		add_action( 'wp_ajax_ssag_support', array( $this, 'ssag_support_cb' ) );
		add_action( 'wp_ajax_nopriv_ssag_support', array( $this, 'ssag_support_cb' ) );

	}

	/**
	 * Add scripts
	 */
	public function add_scripts() {

		global $pagenow;
		if( $pagenow == 'admin.php' ) {
			wp_enqueue_style( 'ssag-scripts_css', OPAD_CONSTANT_URL . 'assets/css/admin-styles.css', false, '1.0.0' );
		}
		wp_enqueue_style( 'ssag-font-google-scripts_css', 'https://fonts.googleapis.com/icon?family=Material+Icons', false, '1.0.0' );
		wp_enqueue_style( 'ssag-font-awsome-scripts_css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', false, '1.0.0' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'ssag-scripts_script', OPAD_CONSTANT_URL . 'assets/js/admin-script.js', false, '1.0.0', $in_footer = false );

		wp_localize_script(
			'ssag-scripts_script',
			'ssag_object',
			array(
				'nonce' => wp_create_nonce( 'ajax-nonce' ),
			)
		);
		wp_enqueue_style( 'select2', OPAD_CONSTANT_URL . 'assets/css/select2.css', true, '1.0.0' );
		wp_enqueue_script( 'select2', OPAD_CONSTANT_URL . 'assets/js/select2.js', true, '1.0.1', array( 'jquery' ) );

	}

	/**
	 * Save single ad
	 */
	public function ssag_save_single_add_cb() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['nonce'] ) ? $_POST['nonce'] : '' ) ), 'ajax-nonce' ) ) {
			echo ( 'Destroy!' );
		}
		$all_data = sanitize_text_field( wp_unslash( isset( $_POST['all_data'] ) ? $_POST['all_data'] : '' ) );

		$ssag_ads_title                = sanitize_meta( 'ssag_ads_title', wp_unslash( isset( $_POST['ssag_ads_title'] ) ? $_POST['ssag_ads_title'] : '' ), '' );
		$ssag_ads_content_html         = sanitize_meta( 'ssag_ads_content_html', wp_unslash( isset( $_POST['ssag_ads_content_html'] ) ? $_POST['ssag_ads_content_html'] : '' ), '' );
		$ssag_ads_include_posts        = sanitize_meta( 'ssag_ads_include_posts', wp_unslash( isset( $_POST['ssag_ads_include_posts'] ) ? $_POST['ssag_ads_include_posts'] : '' ), '' );
		$ssag_ads_include_pages        = sanitize_meta( 'ssag_ads_include_pages', wp_unslash( isset( $_POST['ssag_ads_include_pages'] ) ? $_POST['ssag_ads_include_pages'] : '' ), '' );
		$ssag_ads_include_home_page    = sanitize_meta( 'ssag_ads_include_home_page', wp_unslash( isset( $_POST['ssag_ads_include_home_page'] ) ? $_POST['ssag_ads_include_home_page'] : '' ), '' );
		$ssag_ads_include_search_pages = sanitize_meta( 'ssag_ads_include_search_pages', wp_unslash( isset( $_POST['ssag_ads_include_search_pages'] ) ? $_POST['ssag_ads_include_search_pages'] : '' ), '' );
		$ssag_ads_include_cat_pages    = sanitize_meta( 'ssag_ads_include_cat_pages', wp_unslash( isset( $_POST['ssag_ads_include_cat_pages'] ) ? $_POST['ssag_ads_include_cat_pages'] : '' ), '' );
		$ssag_ads_include_arch_pages   = sanitize_meta( 'ssag_ads_include_arch_pages', wp_unslash( isset( $_POST['ssag_ads_include_arch_pages'] ) ? $_POST['ssag_ads_include_arch_pages'] : '' ), '' );
		$ssag_ads_insertion            = sanitize_meta( 'ssag_ads_insertion', wp_unslash( isset( $_POST['ssag_ads_insertion'] ) ? $_POST['ssag_ads_insertion'] : '' ), '' );
		$ssag_ads_para_id              = sanitize_meta( 'ssag_ads_para_id', wp_unslash( isset( $_POST['ssag_ads_para_id'] ) ? $_POST['ssag_ads_para_id'] : '' ), '' );
		$ssag_ads_align                = sanitize_meta( 'ssag_ads_align', wp_unslash( isset( $_POST['ssag_ads_align'] ) ? $_POST['ssag_ads_align'] : '' ), '' );
		$ssag_ad_custom_css            = sanitize_meta( 'ssag_ad_custom_css', wp_unslash( isset( $_POST['ssag_ad_custom_css'] ) ? $_POST['ssag_ad_custom_css'] : '' ), '' );
		$ssag_ads_categories           = sanitize_meta( 'ssag_ads_categories', wp_unslash( isset( $_POST['ssag_ads_categories'] ) ? (array) $_POST['ssag_ads_categories'] : array() ), '' );
		$ssag_ads_tags                 = sanitize_meta( 'ssag_ads_tags', wp_unslash( isset( $_POST['ssag_ads_tags'] ) ? (array) $_POST['ssag_ads_tags'] : array() ), '' );
		$ssag_ads_posts_ids            = sanitize_meta( 'ssag_ads_posts_ids', wp_unslash( isset( $_POST['ssag_ads_posts_ids'] ) ? (array) $_POST['ssag_ads_posts_ids'] : array() ), '' );
		$ssag_ads_url                  = sanitize_meta( 'ssag_ads_url', wp_unslash( isset( $_POST['ssag_ads_url'] ) ? $_POST['ssag_ads_url'] : '' ), '' );
		$ssag_ads_url_parameter        = sanitize_meta( 'ssag_ads_url_parameter', wp_unslash( isset( $_POST['ssag_ads_url_parameter'] ) ? $_POST['ssag_ads_url_parameter'] : '' ), '' );
		$ssag_ads_show_on_widget       = sanitize_meta( 'ssag_ads_show_on_widget', wp_unslash( isset( $_POST['ssag_ads_show_on_widget'] ) ? $_POST['ssag_ads_show_on_widget'] : '' ), '' );
		$ssag_ads_show_on_shortcode    = sanitize_meta( 'ssag_ads_show_on_shortcode', wp_unslash( isset( $_POST['ssag_ads_show_on_shortcode'] ) ? $_POST['ssag_ads_show_on_shortcode'] : '' ), '' );
		$ssag_ads_show_on_php_function = sanitize_meta( 'ssag_ads_show_on_php_function', wp_unslash( isset( $_POST['ssag_ads_show_on_php_function'] ) ? $_POST['ssag_ads_show_on_php_function'] : '' ), '' );
		$ssag_ads_use_desktop          = sanitize_meta( 'ssag_ads_use_desktop', wp_unslash( isset( $_POST['ssag_ads_use_desktop'] ) ? $_POST['ssag_ads_use_desktop'] : '' ), '' );
		$ssag_ads_use_tablet           = sanitize_meta( 'ssag_ads_use_tablet', wp_unslash( isset( $_POST['ssag_ads_use_tablet'] ) ? $_POST['ssag_ads_use_tablet'] : '' ), '' );
		$ssag_ads_use_phone            = sanitize_meta( 'ssag_ads_use_phone', wp_unslash( isset( $_POST['ssag_ads_use_phone'] ) ? $_POST['ssag_ads_use_phone'] : '' ), '' );
		$ad_post_id                    = sanitize_meta( 'ssag_ads_id', wp_unslash( isset( $_POST['ssag_ads_id'] ) ? $_POST['ssag_ads_id'] : '' ), '' );

		$check_upload_final = false;

		if ( '' !== $ad_post_id ) {
			$ad_post_id         = floatval( $ad_post_id );
			$check_upload_final = true;
			$check_upload       = update_post_meta( $ad_post_id, 'ssag_ad_saved', 'saved' );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_title', $ssag_ads_title ? $ssag_ads_title : '' );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_content_html', $ssag_ads_content_html );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_include_posts', $ssag_ads_include_posts );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_include_pages', $ssag_ads_include_pages );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_include_home_page', $ssag_ads_include_home_page );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_include_search_pages', $ssag_ads_include_search_pages );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_include_cat_pages', $ssag_ads_include_cat_pages );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', $ssag_ads_include_arch_pages );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_insertion', $ssag_ads_insertion );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_para_id', $ssag_ads_para_id );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_align', $ssag_ads_align );
			$check_upload = update_post_meta( $ad_post_id, 'ssag_ad_custom_css', $ssag_ad_custom_css );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_categories', $ssag_ads_categories );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_tags', $ssag_ads_tags );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_posts_ids', $ssag_ads_posts_ids );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_url', $ssag_ads_url );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_url_parameter', $ssag_ads_url_parameter );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_show_on_widget', $ssag_ads_show_on_widget );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_show_on_shortcode', $ssag_ads_show_on_shortcode );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', $ssag_ads_show_on_php_function );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_use_desktop', $ssag_ads_use_desktop );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_use_tablet', $ssag_ads_use_tablet );

			$check_upload = update_post_meta( $ad_post_id, 'ssag_ads_use_phone', $ssag_ads_use_phone );

		}

		$status  = '';
		$message = '';
		if ( $check_upload_final ) {
			$status   = 'pass';
			$message .= '<div class="success-msg">';
			$message .= '<i class="fa fa-check"></i>';
			$message .= 'Your Ad is successfully saved. ';
			if ( $ssag_ads_title && ( '' !== str_replace( ' ', '', $ssag_ads_title ) ) ) {
				$message .= ' With title "' . $ssag_ads_title . '"';
			}
			$message .= '</div>';
		} else {
			$message .= '<div class="error-msg">';
			$message .= '<i class="fa fa-times-circle"></i>';
			$message .= 'Some thing went wrong please try again later';
			$message .= '</div>';
		}

		wp_send_json(
			array(
				'status'  => $status,
				'message' => $message,
			)
		);
	}

	/**
	 * Updating ads numbers
	 */
	public function add_ads_num() {
		$args        = array(
			'post_type'      => 'ssag_adds_post_type',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		$all_posts   = get_posts( $args );
		$ads_allowed = 26;
		if ( $ads_allowed > count( $all_posts ) ) {
			$remaining = $ads_allowed;
			if ( 0 < $remaining ) {
				for ( $increment = count( $all_posts ); $increment < $remaining; $increment++ ) {
					$my_new_ad_args = array(
						'post_title'  => 'Optimate Ad ' . $increment,
						'post_type'   => 'ssag_adds_post_type',
						'post_status' => 'publish',
						'fields'      => 'ids',
					);
					$create_new_ad  = wp_insert_post( $my_new_ad_args );

					update_post_meta( $create_new_ad, 'ssad_ads_post_key', $increment + 1 );
					update_post_meta( $create_new_ad, 'ssag_ads_show_on_widget', 'yes' );
					update_post_meta( $create_new_ad, 'ssag_ads_show_on_shortcode', 'yes' );
					update_post_meta( $create_new_ad, 'ssag_ads_show_on_php_function', 'yes' );
					update_post_meta( $create_new_ad, 'ssag_ads_use_phone', 'yes' );
					update_post_meta( $create_new_ad, 'ssag_ads_use_tablet', 'yes' );
					update_post_meta( $create_new_ad, 'ssag_ads_use_desktop', 'yes' );
				}
			}
		}

	}

	/**
	 * Creating menu
	 */
	public function custom_menu() {
		add_menu_page(
			'Optimate Ads',
			'Optimate Ads',
			'edit_posts',
			'ssag-optimate-ads',
			array( $this, 'setting_page_callback_fn' ),
			'dashicons-chart-bar',
			'10'
		);
	}

	/**
	 * Get all tags
	 */
	public function ssag_get_tags() {
		$args = array(
			'taxonomy'     => array( 'tags', 'product_tag' ),
			'hierarchical' => 1,
			'hide_empty'   => false,
		);
		return (array) get_tags( $args );
	}

	/**
	 * Get all ads
	 */
	public function ssag_get_posts() {
		return get_posts(
			array(
				'post_type'   => array( 'post', 'page', 'product' ),
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'      => array( 'ids', 'post_title', 'post_type' ),
			)
		);
	}

	/**
	 * Get categories
	 */
	public function ssag_get_categories() {
		$cat_args      = array(
			'taxonomy'     => 'category',
			'hierarchical' => 1,
			'hide_empty'   => false,
		);
		$prod_cat_args = array(
			'taxonomy'     => 'product_cat',
			'hierarchical' => 1,
			'hide_empty'   => false,
		);
		return (array) array_merge( (array) get_categories( $cat_args ), (array) get_categories( $prod_cat_args ) );
	}

	/**
	 * Setting page function
	 */
	public function setting_page_callback_fn() {

		$all_post_categories = $this->ssag_get_categories();

		$all_post_tags = $this->ssag_get_tags();
		$all_posts     = $this->ssag_get_posts();

		$args          = array(
			'post_type'      => 'ssag_adds_post_type',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		$all_ads_posts = get_posts( $args );

		?>
		<div class="ssag-content-main">
			<div class="ssag-menu">
				<div class="ssag-menu-item-first">
					<svg width="158" height="32" viewBox="0 0 158 32" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						<path d="M59.808 16.272C59.808 17.1573 59.696 17.9627 59.472 18.688C59.2587 19.4027 58.928 20.0213 58.48 20.544C58.0427 21.0667 57.4773 21.4667 56.784 21.744C56.1013 22.0213 55.296 22.16 54.368 22.16C53.44 22.16 52.6293 22.0213 51.936 21.744C51.2533 21.456 50.688 21.056 50.24 20.544C49.8027 20.0213 49.472 19.3973 49.248 18.672C49.0347 17.9467 48.928 17.1413 48.928 16.256C48.928 15.072 49.12 14.0427 49.504 13.168C49.8987 12.2933 50.496 11.616 51.296 11.136C52.1067 10.6453 53.136 10.4 54.384 10.4C55.6213 10.4 56.64 10.6453 57.44 11.136C58.24 11.616 58.832 12.2987 59.216 13.184C59.6107 14.0587 59.808 15.088 59.808 16.272ZM51.472 16.272C51.472 17.072 51.5733 17.76 51.776 18.336C51.9787 18.912 52.2933 19.3547 52.72 19.664C53.1467 19.9733 53.696 20.128 54.368 20.128C55.0613 20.128 55.616 19.9733 56.032 19.664C56.4587 19.3547 56.768 18.912 56.96 18.336C57.1627 17.76 57.264 17.072 57.264 16.272C57.264 15.0667 57.04 14.1227 56.592 13.44C56.144 12.7573 55.408 12.416 54.384 12.416C53.7013 12.416 53.1467 12.5707 52.72 12.88C52.2933 13.1893 51.9787 13.632 51.776 14.208C51.5733 14.784 51.472 15.472 51.472 16.272ZM66.8464 13.104C67.8277 13.104 68.6224 13.488 69.2304 14.256C69.8384 15.0133 70.1424 16.1333 70.1424 17.616C70.1424 18.608 69.9984 19.4453 69.7104 20.128C69.4224 20.8 69.0277 21.3067 68.5264 21.648C68.025 21.9893 67.4437 22.16 66.7824 22.16C66.3664 22.16 66.0037 22.1067 65.6944 22C65.3957 21.8933 65.1397 21.7547 64.9264 21.584C64.713 21.4133 64.5264 21.232 64.3664 21.04H64.2384C64.281 21.2427 64.313 21.456 64.3344 21.68C64.3557 21.8933 64.3664 22.1067 64.3664 22.32V25.84H61.9824V13.264H63.9184L64.2544 14.4H64.3664C64.5264 14.1653 64.7184 13.952 64.9424 13.76C65.1664 13.5573 65.433 13.3973 65.7424 13.28C66.0624 13.1627 66.4304 13.104 66.8464 13.104ZM66.0784 15.008C65.6624 15.008 65.3317 15.0933 65.0864 15.264C64.841 15.4347 64.6597 15.696 64.5424 16.048C64.4357 16.3893 64.377 16.8213 64.3664 17.344V17.6C64.3664 18.1653 64.4197 18.6453 64.5264 19.04C64.633 19.424 64.8144 19.7173 65.0704 19.92C65.3264 20.1227 65.673 20.224 66.1104 20.224C66.473 20.224 66.7717 20.1227 67.0064 19.92C67.241 19.7173 67.417 19.4187 67.5344 19.024C67.6517 18.6293 67.7104 18.1493 67.7104 17.584C67.7104 16.7307 67.577 16.0907 67.3104 15.664C67.0544 15.2267 66.6437 15.008 66.0784 15.008ZM75.7874 20.256C76.054 20.256 76.31 20.2293 76.5554 20.176C76.8007 20.1227 77.046 20.0587 77.2914 19.984V21.76C77.0354 21.8667 76.7154 21.9573 76.3314 22.032C75.958 22.1173 75.5474 22.16 75.0994 22.16C74.5767 22.16 74.1074 22.0747 73.6914 21.904C73.286 21.7333 72.9607 21.44 72.7154 21.024C72.4807 20.5973 72.3634 20.0107 72.3634 19.264V15.056H71.2274V14.048L72.5394 13.248L73.2274 11.408H74.7474V13.264H77.1954V15.056H74.7474V19.264C74.7474 19.5947 74.8434 19.8453 75.0354 20.016C75.2274 20.176 75.478 20.256 75.7874 20.256ZM81.4289 13.264V22H79.0449V13.264H81.4289ZM80.2449 9.84C80.5969 9.84 80.9009 9.92533 81.1569 10.096C81.4129 10.256 81.5409 10.56 81.5409 11.008C81.5409 11.4453 81.4129 11.7493 81.1569 11.92C80.9009 12.0907 80.5969 12.176 80.2449 12.176C79.8822 12.176 79.5729 12.0907 79.3169 11.92C79.0715 11.7493 78.9489 11.4453 78.9489 11.008C78.9489 10.56 79.0715 10.256 79.3169 10.096C79.5729 9.92533 79.8822 9.84 80.2449 9.84ZM94.1759 13.104C95.1679 13.104 95.9145 13.36 96.4159 13.872C96.9279 14.3733 97.1839 15.184 97.1839 16.304V22H94.7999V16.896C94.7999 16.2667 94.6932 15.7973 94.4799 15.488C94.2665 15.168 93.9359 15.008 93.4879 15.008C92.8585 15.008 92.4105 15.232 92.1439 15.68C91.8772 16.128 91.7439 16.7733 91.7439 17.616V22H89.3599V16.896C89.3599 16.48 89.3119 16.1333 89.2159 15.856C89.1199 15.5787 88.9759 15.3707 88.7839 15.232C88.5919 15.0827 88.3465 15.008 88.0479 15.008C87.6105 15.008 87.2639 15.12 87.0079 15.344C86.7519 15.568 86.5705 15.8933 86.4639 16.32C86.3572 16.7467 86.3039 17.2693 86.3039 17.888V22H83.9199V13.264H85.7439L86.0639 14.384H86.1919C86.3732 14.0853 86.5972 13.8453 86.8639 13.664C87.1305 13.472 87.4239 13.3333 87.7439 13.248C88.0745 13.152 88.4052 13.104 88.7359 13.104C89.3759 13.104 89.9199 13.2107 90.3679 13.424C90.8159 13.6267 91.1572 13.9467 91.3919 14.384H91.5999C91.8665 13.936 92.2345 13.6107 92.7039 13.408C93.1839 13.2053 93.6745 13.104 94.1759 13.104ZM103.223 13.088C104.396 13.088 105.292 13.344 105.911 13.856C106.54 14.3573 106.855 15.1307 106.855 16.176V22H105.191L104.727 20.816H104.663C104.417 21.1253 104.161 21.3813 103.895 21.584C103.639 21.7867 103.34 21.9307 102.999 22.016C102.668 22.112 102.257 22.16 101.767 22.16C101.255 22.16 100.791 22.064 100.375 21.872C99.9693 21.6693 99.6493 21.3653 99.4146 20.96C99.18 20.544 99.0626 20.0213 99.0626 19.392C99.0626 18.464 99.388 17.7813 100.039 17.344C100.689 16.896 101.665 16.6507 102.967 16.608L104.487 16.56V16.176C104.487 15.7173 104.364 15.3813 104.119 15.168C103.884 14.9547 103.553 14.848 103.127 14.848C102.7 14.848 102.284 14.912 101.879 15.04C101.473 15.1573 101.068 15.3067 100.663 15.488L99.8786 13.872C100.348 13.6267 100.865 13.4347 101.431 13.296C102.007 13.1573 102.604 13.088 103.223 13.088ZM103.559 17.984C102.791 18.0053 102.257 18.144 101.959 18.4C101.66 18.656 101.511 18.992 101.511 19.408C101.511 19.7707 101.617 20.032 101.831 20.192C102.044 20.3413 102.321 20.416 102.663 20.416C103.175 20.416 103.607 20.2667 103.959 19.968C104.311 19.6587 104.487 19.2267 104.487 18.672V17.952L103.559 17.984ZM112.975 20.256C113.242 20.256 113.498 20.2293 113.743 20.176C113.988 20.1227 114.234 20.0587 114.479 19.984V21.76C114.223 21.8667 113.903 21.9573 113.519 22.032C113.146 22.1173 112.735 22.16 112.287 22.16C111.764 22.16 111.295 22.0747 110.879 21.904C110.474 21.7333 110.148 21.44 109.903 21.024C109.668 20.5973 109.551 20.0107 109.551 19.264V15.056H108.415V14.048L109.727 13.248L110.415 11.408H111.935V13.264H114.383V15.056H111.935V19.264C111.935 19.5947 112.031 19.8453 112.223 20.016C112.415 20.176 112.666 20.256 112.975 20.256ZM119.832 13.104C120.643 13.104 121.336 13.2587 121.912 13.568C122.499 13.8773 122.952 14.3253 123.272 14.912C123.592 15.4987 123.752 16.2187 123.752 17.072V18.224H118.12C118.142 18.896 118.339 19.424 118.712 19.808C119.096 20.192 119.624 20.384 120.296 20.384C120.862 20.384 121.374 20.3307 121.832 20.224C122.291 20.1067 122.766 19.9307 123.256 19.696V21.536C122.83 21.7493 122.376 21.904 121.896 22C121.427 22.1067 120.856 22.16 120.184 22.16C119.31 22.16 118.536 22 117.864 21.68C117.192 21.3493 116.664 20.8533 116.28 20.192C115.896 19.5307 115.704 18.6987 115.704 17.696C115.704 16.672 115.875 15.824 116.216 15.152C116.568 14.4693 117.054 13.9573 117.672 13.616C118.291 13.2747 119.011 13.104 119.832 13.104ZM119.848 14.8C119.39 14.8 119.006 14.9493 118.696 15.248C118.398 15.5467 118.222 16.0107 118.168 16.64H121.512C121.512 16.288 121.448 15.9733 121.32 15.696C121.203 15.4187 121.022 15.2 120.776 15.04C120.531 14.88 120.222 14.8 119.848 14.8ZM137.026 22L136.194 19.28H132.034L131.202 22H128.594L132.626 10.528H135.586L139.634 22H137.026ZM134.786 14.592C134.732 14.4107 134.663 14.1813 134.578 13.904C134.492 13.6267 134.407 13.344 134.322 13.056C134.236 12.768 134.167 12.5173 134.114 12.304C134.06 12.5173 133.986 12.784 133.89 13.104C133.804 13.4133 133.719 13.712 133.634 14C133.559 14.2773 133.5 14.4747 133.458 14.592L132.642 17.248H135.618L134.786 14.592ZM143.657 22.16C142.686 22.16 141.891 21.7813 141.273 21.024C140.665 20.256 140.361 19.1307 140.361 17.648C140.361 16.1547 140.67 15.024 141.289 14.256C141.907 13.488 142.718 13.104 143.721 13.104C144.137 13.104 144.505 13.1627 144.825 13.28C145.145 13.3867 145.417 13.536 145.641 13.728C145.875 13.92 146.073 14.1387 146.233 14.384H146.313C146.281 14.2133 146.243 13.968 146.201 13.648C146.158 13.3173 146.137 12.9813 146.137 12.64V9.84H148.521V22H146.697L146.233 20.864H146.137C145.987 21.0987 145.795 21.3173 145.561 21.52C145.337 21.712 145.07 21.8667 144.761 21.984C144.451 22.1013 144.083 22.16 143.657 22.16ZM144.489 20.256C145.15 20.256 145.614 20.064 145.881 19.68C146.147 19.2853 146.286 18.6987 146.297 17.92V17.664C146.297 16.8107 146.163 16.16 145.897 15.712C145.641 15.264 145.161 15.04 144.457 15.04C143.934 15.04 143.523 15.2693 143.225 15.728C142.926 16.176 142.777 16.8267 142.777 17.68C142.777 18.5333 142.926 19.1787 143.225 19.616C143.523 20.0427 143.945 20.256 144.489 20.256ZM157.11 19.408C157.11 19.9947 156.971 20.496 156.694 20.912C156.416 21.3173 156 21.6267 155.446 21.84C154.891 22.0533 154.203 22.16 153.382 22.16C152.774 22.16 152.251 22.1173 151.814 22.032C151.376 21.9573 150.939 21.8293 150.502 21.648V19.68C150.971 19.8933 151.478 20.0693 152.022 20.208C152.566 20.3467 153.046 20.416 153.462 20.416C153.931 20.416 154.262 20.3467 154.454 20.208C154.656 20.0693 154.758 19.888 154.758 19.664C154.758 19.5147 154.715 19.3813 154.63 19.264C154.555 19.1467 154.384 19.0133 154.118 18.864C153.851 18.7147 153.435 18.5227 152.87 18.288C152.326 18.0533 151.878 17.824 151.526 17.6C151.174 17.3653 150.912 17.088 150.742 16.768C150.571 16.4373 150.486 16.0267 150.486 15.536C150.486 14.7253 150.8 14.1173 151.43 13.712C152.059 13.3067 152.896 13.104 153.942 13.104C154.486 13.104 155.003 13.1573 155.494 13.264C155.984 13.3707 156.491 13.5467 157.014 13.792L156.294 15.504C156.006 15.376 155.728 15.264 155.462 15.168C155.195 15.072 154.934 14.9973 154.678 14.944C154.432 14.8907 154.176 14.864 153.91 14.864C153.558 14.864 153.291 14.912 153.11 15.008C152.939 15.104 152.854 15.248 152.854 15.44C152.854 15.5787 152.896 15.7067 152.982 15.824C153.078 15.9307 153.254 16.048 153.51 16.176C153.776 16.304 154.166 16.4747 154.678 16.688C155.179 16.8907 155.611 17.104 155.974 17.328C156.336 17.5413 156.614 17.8133 156.806 18.144C157.008 18.464 157.11 18.8853 157.11 19.408Z" fill="white"/>
						<rect width="32" height="32" fill="url(#pattern0)"/>
						<defs>
							<pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
								<use xlink:href="#image0_4_18" transform="scale(0.00195312)"/>
							</pattern>
							<image id="image0_4_18" width="512" height="512" xlink:href="data:image/png;base64,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"/>
						</defs>
					</svg>
				</div>
				<div class="ssag-menu-item ssag-menu-item-btn  ssag-menu-item-active" data-div="ssag-all-ads-main-ads-placement">
					<svg width="139" height="20" viewBox="0 0 139 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M42.175 16L40.885 12.685H36.64L35.365 16H34L38.185 5.245H39.4L43.57 16H42.175ZM39.28 8.245C39.25 8.165 39.2 8.02 39.13 7.81C39.06 7.6 38.99 7.385 38.92 7.165C38.86 6.935 38.81 6.76 38.77 6.64C38.72 6.84 38.665 7.045 38.605 7.255C38.555 7.455 38.5 7.64 38.44 7.81C38.39 7.98 38.345 8.125 38.305 8.245L37.09 11.485H40.48L39.28 8.245ZM47.7051 16.15C46.7051 16.15 45.9051 15.805 45.3051 15.115C44.7051 14.415 44.4051 13.375 44.4051 11.995C44.4051 10.615 44.7051 9.575 45.3051 8.875C45.9151 8.165 46.7201 7.81 47.7201 7.81C48.1401 7.81 48.5051 7.865 48.8151 7.975C49.1251 8.075 49.3951 8.215 49.6251 8.395C49.8551 8.575 50.0501 8.775 50.2101 8.995H50.3001C50.2901 8.865 50.2701 8.675 50.2401 8.425C50.2201 8.165 50.2101 7.96 50.2101 7.81V4.6H51.5301V16H50.4651L50.2701 14.92H50.2101C50.0501 15.15 49.8551 15.36 49.6251 15.55C49.3951 15.73 49.1201 15.875 48.8001 15.985C48.4901 16.095 48.1251 16.15 47.7051 16.15ZM47.9151 15.055C48.7651 15.055 49.3601 14.825 49.7001 14.365C50.0501 13.895 50.2251 13.19 50.2251 12.25V12.01C50.2251 11.01 50.0601 10.245 49.7301 9.715C49.4001 9.175 48.7901 8.905 47.9001 8.905C47.1901 8.905 46.6551 9.19 46.2951 9.76C45.9451 10.32 45.7701 11.075 45.7701 12.025C45.7701 12.985 45.9451 13.73 46.2951 14.26C46.6551 14.79 47.1951 15.055 47.9151 15.055ZM59.3186 13.78C59.3186 14.3 59.1886 14.735 58.9286 15.085C58.6686 15.435 58.2986 15.7 57.8186 15.88C57.3386 16.06 56.7686 16.15 56.1086 16.15C55.5486 16.15 55.0636 16.105 54.6536 16.015C54.2536 15.925 53.8986 15.8 53.5886 15.64V14.44C53.9086 14.6 54.2936 14.75 54.7436 14.89C55.2036 15.02 55.6686 15.085 56.1386 15.085C56.8086 15.085 57.2936 14.98 57.5936 14.77C57.8936 14.55 58.0436 14.26 58.0436 13.9C58.0436 13.7 57.9886 13.52 57.8786 13.36C57.7686 13.2 57.5686 13.04 57.2786 12.88C56.9986 12.72 56.5936 12.54 56.0636 12.34C55.5436 12.14 55.0986 11.94 54.7286 11.74C54.3586 11.54 54.0736 11.3 53.8736 11.02C53.6736 10.74 53.5736 10.38 53.5736 9.94C53.5736 9.26 53.8486 8.735 54.3986 8.365C54.9586 7.995 55.6886 7.81 56.5886 7.81C57.0786 7.81 57.5336 7.86 57.9536 7.96C58.3836 8.05 58.7836 8.18 59.1536 8.35L58.7036 9.4C58.4836 9.3 58.2486 9.215 57.9986 9.145C57.7586 9.065 57.5136 9.005 57.2636 8.965C57.0136 8.915 56.7586 8.89 56.4986 8.89C55.9586 8.89 55.5436 8.98 55.2536 9.16C54.9736 9.33 54.8336 9.565 54.8336 9.865C54.8336 10.085 54.8986 10.275 55.0286 10.435C55.1586 10.585 55.3736 10.735 55.6736 10.885C55.9836 11.025 56.3936 11.195 56.9036 11.395C57.4136 11.585 57.8486 11.78 58.2086 11.98C58.5686 12.18 58.8436 12.425 59.0336 12.715C59.2236 12.995 59.3186 13.35 59.3186 13.78ZM68.1728 5.29C69.5728 5.29 70.5928 5.565 71.2328 6.115C71.8728 6.665 72.1928 7.44 72.1928 8.44C72.1928 8.88 72.1178 9.305 71.9678 9.715C71.8278 10.115 71.5928 10.475 71.2628 10.795C70.9328 11.115 70.4928 11.37 69.9428 11.56C69.3928 11.74 68.7178 11.83 67.9178 11.83H66.6878V16H65.3378V5.29H68.1728ZM68.0528 6.445H66.6878V10.675H67.7678C68.4478 10.675 69.0128 10.605 69.4628 10.465C69.9128 10.315 70.2478 10.08 70.4678 9.76C70.6878 9.44 70.7978 9.02 70.7978 8.5C70.7978 7.81 70.5778 7.295 70.1378 6.955C69.6978 6.615 69.0028 6.445 68.0528 6.445ZM75.5598 16H74.2398V4.6H75.5598V16ZM81.152 7.825C82.132 7.825 82.857 8.04 83.327 8.47C83.797 8.9 84.032 9.585 84.032 10.525V16H83.072L82.817 14.86H82.757C82.527 15.15 82.287 15.395 82.037 15.595C81.797 15.785 81.517 15.925 81.197 16.015C80.887 16.105 80.507 16.15 80.057 16.15C79.577 16.15 79.142 16.065 78.752 15.895C78.372 15.725 78.072 15.465 77.852 15.115C77.632 14.755 77.522 14.305 77.522 13.765C77.522 12.965 77.837 12.35 78.467 11.92C79.097 11.48 80.067 11.24 81.377 11.2L82.742 11.155V10.675C82.742 10.005 82.597 9.54 82.307 9.28C82.017 9.02 81.607 8.89 81.077 8.89C80.657 8.89 80.257 8.955 79.877 9.085C79.497 9.205 79.142 9.345 78.812 9.505L78.407 8.515C78.757 8.325 79.172 8.165 79.652 8.035C80.132 7.895 80.632 7.825 81.152 7.825ZM81.542 12.115C80.542 12.155 79.847 12.315 79.457 12.595C79.077 12.875 78.887 13.27 78.887 13.78C78.887 14.23 79.022 14.56 79.292 14.77C79.572 14.98 79.927 15.085 80.357 15.085C81.037 15.085 81.602 14.9 82.052 14.53C82.502 14.15 82.727 13.57 82.727 12.79V12.07L81.542 12.115ZM89.7402 16.15C89.0302 16.15 88.3952 16.005 87.8352 15.715C87.2852 15.425 86.8502 14.975 86.5302 14.365C86.2202 13.755 86.0652 12.975 86.0652 12.025C86.0652 11.035 86.2302 10.23 86.5602 9.61C86.8902 8.99 87.3352 8.535 87.8952 8.245C88.4652 7.955 89.1102 7.81 89.8302 7.81C90.2402 7.81 90.6352 7.855 91.0152 7.945C91.3952 8.025 91.7052 8.125 91.9452 8.245L91.5402 9.34C91.3002 9.25 91.0202 9.165 90.7002 9.085C90.3802 9.005 90.0802 8.965 89.8002 8.965C89.2602 8.965 88.8152 9.08 88.4652 9.31C88.1152 9.54 87.8552 9.88 87.6852 10.33C87.5152 10.78 87.4302 11.34 87.4302 12.01C87.4302 12.65 87.5152 13.195 87.6852 13.645C87.8552 14.095 88.1102 14.435 88.4502 14.665C88.7902 14.895 89.2152 15.01 89.7252 15.01C90.1652 15.01 90.5502 14.965 90.8802 14.875C91.2202 14.785 91.5302 14.675 91.8102 14.545V15.715C91.5402 15.855 91.2402 15.96 90.9102 16.03C90.5902 16.11 90.2002 16.15 89.7402 16.15ZM96.8273 7.81C97.5173 7.81 98.1073 7.96 98.5973 8.26C99.0973 8.56 99.4773 8.985 99.7373 9.535C100.007 10.075 100.142 10.71 100.142 11.44V12.235H94.6373C94.6573 13.145 94.8873 13.84 95.3273 14.32C95.7773 14.79 96.4023 15.025 97.2023 15.025C97.7123 15.025 98.1623 14.98 98.5523 14.89C98.9523 14.79 99.3623 14.65 99.7823 14.47V15.625C99.3723 15.805 98.9673 15.935 98.5673 16.015C98.1673 16.105 97.6923 16.15 97.1423 16.15C96.3823 16.15 95.7073 15.995 95.1173 15.685C94.5373 15.375 94.0823 14.915 93.7523 14.305C93.4323 13.685 93.2723 12.93 93.2723 12.04C93.2723 11.16 93.4173 10.405 93.7073 9.775C94.0073 9.145 94.4223 8.66 94.9523 8.32C95.4923 7.98 96.1173 7.81 96.8273 7.81ZM96.8123 8.89C96.1823 8.89 95.6823 9.095 95.3123 9.505C94.9523 9.905 94.7373 10.465 94.6673 11.185H98.7623C98.7623 10.725 98.6923 10.325 98.5523 9.985C98.4123 9.645 98.1973 9.38 97.9073 9.19C97.6273 8.99 97.2623 8.89 96.8123 8.89ZM111.009 7.81C111.919 7.81 112.599 8.045 113.049 8.515C113.499 8.975 113.724 9.725 113.724 10.765V16H112.419V10.825C112.419 10.195 112.284 9.72 112.014 9.4C111.754 9.08 111.344 8.92 110.784 8.92C110.004 8.92 109.444 9.145 109.104 9.595C108.774 10.045 108.609 10.7 108.609 11.56V16H107.304V10.825C107.304 10.405 107.244 10.055 107.124 9.775C107.004 9.485 106.824 9.27 106.584 9.13C106.344 8.99 106.034 8.92 105.654 8.92C105.114 8.92 104.689 9.035 104.379 9.265C104.069 9.485 103.844 9.81 103.704 10.24C103.574 10.67 103.509 11.2 103.509 11.83V16H102.189V7.96H103.254L103.449 9.055H103.524C103.694 8.775 103.899 8.545 104.139 8.365C104.389 8.175 104.664 8.035 104.964 7.945C105.264 7.855 105.574 7.81 105.894 7.81C106.514 7.81 107.029 7.92 107.439 8.14C107.859 8.36 108.164 8.7 108.354 9.16H108.429C108.699 8.7 109.064 8.36 109.524 8.14C109.994 7.92 110.489 7.81 111.009 7.81ZM119.313 7.81C120.003 7.81 120.593 7.96 121.083 8.26C121.583 8.56 121.963 8.985 122.223 9.535C122.493 10.075 122.628 10.71 122.628 11.44V12.235H117.123C117.143 13.145 117.373 13.84 117.813 14.32C118.263 14.79 118.888 15.025 119.688 15.025C120.198 15.025 120.648 14.98 121.038 14.89C121.438 14.79 121.848 14.65 122.268 14.47V15.625C121.858 15.805 121.453 15.935 121.053 16.015C120.653 16.105 120.178 16.15 119.628 16.15C118.868 16.15 118.193 15.995 117.603 15.685C117.023 15.375 116.568 14.915 116.238 14.305C115.918 13.685 115.758 12.93 115.758 12.04C115.758 11.16 115.903 10.405 116.193 9.775C116.493 9.145 116.908 8.66 117.438 8.32C117.978 7.98 118.603 7.81 119.313 7.81ZM119.298 8.89C118.668 8.89 118.168 9.095 117.798 9.505C117.438 9.905 117.223 10.465 117.153 11.185H121.248C121.248 10.725 121.178 10.325 121.038 9.985C120.898 9.645 120.683 9.38 120.393 9.19C120.113 8.99 119.748 8.89 119.298 8.89ZM128.544 7.81C129.504 7.81 130.229 8.045 130.719 8.515C131.209 8.975 131.454 9.725 131.454 10.765V16H130.149V10.855C130.149 10.205 130.004 9.72 129.714 9.4C129.424 9.08 128.969 8.92 128.349 8.92C127.459 8.92 126.844 9.17 126.504 9.67C126.164 10.17 125.994 10.89 125.994 11.83V16H124.674V7.96H125.739L125.934 9.055H126.009C126.189 8.775 126.409 8.545 126.669 8.365C126.939 8.175 127.234 8.035 127.554 7.945C127.874 7.855 128.204 7.81 128.544 7.81ZM136.632 15.07C136.832 15.07 137.037 15.055 137.247 15.025C137.457 14.985 137.627 14.945 137.757 14.905V15.91C137.617 15.98 137.417 16.035 137.157 16.075C136.897 16.125 136.647 16.15 136.407 16.15C135.987 16.15 135.597 16.08 135.237 15.94C134.887 15.79 134.602 15.535 134.382 15.175C134.162 14.815 134.052 14.31 134.052 13.66V8.98H132.912V8.35L134.067 7.825L134.592 6.115H135.372V7.96H137.697V8.98H135.372V13.63C135.372 14.12 135.487 14.485 135.717 14.725C135.957 14.955 136.262 15.07 136.632 15.07Z" fill="white"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M16.0875 1H1.9125C0.8577 1 0 1.897 0 3V17C0 18.103 0.8577 19 1.9125 19H16.0875C17.1423 19 18 18.103 18 17V3C18 1.897 17.1423 1 16.0875 1ZM16.0875 17H1.91248C1.8793 17 1.8529 16.9933 1.83425 16.9886C1.82406 16.986 1.81619 16.984 1.81078 16.984C1.80448 16.984 1.80088 16.986 1.79998 16.992L1.78918 3.04599C1.79548 3.03599 1.83598 2.99999 1.91248 2.99999H16.0875C16.1206 3.00045 16.1466 3.00655 16.1653 3.01092C16.1868 3.01594 16.1985 3.01868 16.2 3.00799L16.2108 16.954C16.2045 16.964 16.164 17 16.0875 17ZM3.59998 5.00001H8.99998V11H3.59998V5.00001ZM9.89998 13H3.59998V15H9.89998H10.8H14.4V13H10.8H9.89998ZM14.4 9.00002H10.8V11H14.4V9.00002ZM10.8 5.00001H14.4V7.00001H10.8V5.00001Z" fill="#435971"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M16.0875 1H1.9125C0.8577 1 0 1.897 0 3V17C0 18.103 0.8577 19 1.9125 19H16.0875C17.1423 19 18 18.103 18 17V3C18 1.897 17.1423 1 16.0875 1ZM16.0875 17H1.91248C1.8793 17 1.8529 16.9933 1.83425 16.9886C1.82406 16.986 1.81619 16.984 1.81078 16.984C1.80448 16.984 1.80088 16.986 1.79998 16.992L1.78918 3.04599C1.79548 3.03599 1.83598 2.99999 1.91248 2.99999H16.0875C16.1206 3.00045 16.1466 3.00655 16.1653 3.01092C16.1868 3.01594 16.1985 3.01868 16.2 3.00799L16.2108 16.954C16.2045 16.964 16.164 17 16.0875 17ZM3.59998 5.00001H8.99998V11H3.59998V5.00001ZM9.89998 13H3.59998V15H9.89998H10.8H14.4V13H10.8H9.89998ZM14.4 9.00002H10.8V11H14.4V9.00002ZM10.8 5.00001H14.4V7.00001H10.8V5.00001Z" fill="#8E9BAA"/>
					</svg>
				</div>
				<div class="ssag-menu-item  ssag-menu-item-btn" data-div="ssag-all-ads-main-ads-txt-div">
					<svg width="83" height="20" viewBox="0 0 83 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M42.175 16L40.885 12.685H36.64L35.365 16H34L38.185 5.245H39.4L43.57 16H42.175ZM39.28 8.245C39.25 8.165 39.2 8.02 39.13 7.81C39.06 7.6 38.99 7.385 38.92 7.165C38.86 6.935 38.81 6.76 38.77 6.64C38.72 6.84 38.665 7.045 38.605 7.255C38.555 7.455 38.5 7.64 38.44 7.81C38.39 7.98 38.345 8.125 38.305 8.245L37.09 11.485H40.48L39.28 8.245ZM47.7051 16.15C46.7051 16.15 45.9051 15.805 45.3051 15.115C44.7051 14.415 44.4051 13.375 44.4051 11.995C44.4051 10.615 44.7051 9.575 45.3051 8.875C45.9151 8.165 46.7201 7.81 47.7201 7.81C48.1401 7.81 48.5051 7.865 48.8151 7.975C49.1251 8.075 49.3951 8.215 49.6251 8.395C49.8551 8.575 50.0501 8.775 50.2101 8.995H50.3001C50.2901 8.865 50.2701 8.675 50.2401 8.425C50.2201 8.165 50.2101 7.96 50.2101 7.81V4.6H51.5301V16H50.4651L50.2701 14.92H50.2101C50.0501 15.15 49.8551 15.36 49.6251 15.55C49.3951 15.73 49.1201 15.875 48.8001 15.985C48.4901 16.095 48.1251 16.15 47.7051 16.15ZM47.9151 15.055C48.7651 15.055 49.3601 14.825 49.7001 14.365C50.0501 13.895 50.2251 13.19 50.2251 12.25V12.01C50.2251 11.01 50.0601 10.245 49.7301 9.715C49.4001 9.175 48.7901 8.905 47.9001 8.905C47.1901 8.905 46.6551 9.19 46.2951 9.76C45.9451 10.32 45.7701 11.075 45.7701 12.025C45.7701 12.985 45.9451 13.73 46.2951 14.26C46.6551 14.79 47.1951 15.055 47.9151 15.055ZM59.3186 13.78C59.3186 14.3 59.1886 14.735 58.9286 15.085C58.6686 15.435 58.2986 15.7 57.8186 15.88C57.3386 16.06 56.7686 16.15 56.1086 16.15C55.5486 16.15 55.0636 16.105 54.6536 16.015C54.2536 15.925 53.8986 15.8 53.5886 15.64V14.44C53.9086 14.6 54.2936 14.75 54.7436 14.89C55.2036 15.02 55.6686 15.085 56.1386 15.085C56.8086 15.085 57.2936 14.98 57.5936 14.77C57.8936 14.55 58.0436 14.26 58.0436 13.9C58.0436 13.7 57.9886 13.52 57.8786 13.36C57.7686 13.2 57.5686 13.04 57.2786 12.88C56.9986 12.72 56.5936 12.54 56.0636 12.34C55.5436 12.14 55.0986 11.94 54.7286 11.74C54.3586 11.54 54.0736 11.3 53.8736 11.02C53.6736 10.74 53.5736 10.38 53.5736 9.94C53.5736 9.26 53.8486 8.735 54.3986 8.365C54.9586 7.995 55.6886 7.81 56.5886 7.81C57.0786 7.81 57.5336 7.86 57.9536 7.96C58.3836 8.05 58.7836 8.18 59.1536 8.35L58.7036 9.4C58.4836 9.3 58.2486 9.215 57.9986 9.145C57.7586 9.065 57.5136 9.005 57.2636 8.965C57.0136 8.915 56.7586 8.89 56.4986 8.89C55.9586 8.89 55.5436 8.98 55.2536 9.16C54.9736 9.33 54.8336 9.565 54.8336 9.865C54.8336 10.085 54.8986 10.275 55.0286 10.435C55.1586 10.585 55.3736 10.735 55.6736 10.885C55.9836 11.025 56.3936 11.195 56.9036 11.395C57.4136 11.585 57.8486 11.78 58.2086 11.98C58.5686 12.18 58.8436 12.425 59.0336 12.715C59.2236 12.995 59.3186 13.35 59.3186 13.78ZM61.0663 15.19C61.0663 14.82 61.1563 14.56 61.3363 14.41C61.5163 14.26 61.7313 14.185 61.9813 14.185C62.2413 14.185 62.4613 14.26 62.6413 14.41C62.8313 14.56 62.9263 14.82 62.9263 15.19C62.9263 15.55 62.8313 15.81 62.6413 15.97C62.4613 16.13 62.2413 16.21 61.9813 16.21C61.7313 16.21 61.5163 16.13 61.3363 15.97C61.1563 15.81 61.0663 15.55 61.0663 15.19ZM67.96 15.07C68.16 15.07 68.365 15.055 68.575 15.025C68.785 14.985 68.955 14.945 69.085 14.905V15.91C68.945 15.98 68.745 16.035 68.485 16.075C68.225 16.125 67.975 16.15 67.735 16.15C67.315 16.15 66.925 16.08 66.565 15.94C66.215 15.79 65.93 15.535 65.71 15.175C65.49 14.815 65.38 14.31 65.38 13.66V8.98H64.24V8.35L65.395 7.825L65.92 6.115H66.7V7.96H69.025V8.98H66.7V13.63C66.7 14.12 66.815 14.485 67.045 14.725C67.285 14.955 67.59 15.07 67.96 15.07ZM72.5999 11.89L69.8249 7.96H71.3249L73.3949 10.99L75.4499 7.96H76.9349L74.1599 11.89L77.0849 16H75.5849L73.3949 12.79L71.1749 16H69.6899L72.5999 11.89ZM81.3194 15.07C81.5194 15.07 81.7244 15.055 81.9344 15.025C82.1444 14.985 82.3144 14.945 82.4444 14.905V15.91C82.3044 15.98 82.1044 16.035 81.8444 16.075C81.5844 16.125 81.3344 16.15 81.0944 16.15C80.6744 16.15 80.2844 16.08 79.9244 15.94C79.5744 15.79 79.2894 15.535 79.0694 15.175C78.8494 14.815 78.7394 14.31 78.7394 13.66V8.98H77.5994V8.35L78.7544 7.825L79.2794 6.115H80.0594V7.96H82.3844V8.98H80.0594V13.63C80.0594 14.12 80.1744 14.485 80.4044 14.725C80.6444 14.955 80.9494 15.07 81.3194 15.07Z" fill="white"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M16 1H2C0.897 1 0 1.897 0 3V17C0 18.103 0.897 19 2 19H16C17.103 19 18 18.103 18 17V3C18 1.897 17.103 1 16 1ZM16 3V6H2V3H16ZM2 17V8H16.001L16.002 17H2Z" fill="black"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M16 1H2C0.897 1 0 1.897 0 3V17C0 18.103 0.897 19 2 19H16C17.103 19 18 18.103 18 17V3C18 1.897 17.103 1 16 1ZM16 3V6H2V3H16ZM2 17V8H16.001L16.002 17H2Z" fill="#8E9BAA"/>
					</svg>
				</div>

				<div class="ssag-menu-item  ssag-menu-item-btn" data-div="ssag-all-ads-main-support">
					<svg width="92" height="20" viewBox="0 0 92 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M42.53 13.135C42.53 13.775 42.375 14.32 42.065 14.77C41.755 15.21 41.31 15.55 40.73 15.79C40.16 16.03 39.485 16.15 38.705 16.15C38.305 16.15 37.92 16.13 37.55 16.09C37.19 16.05 36.86 15.995 36.56 15.925C36.26 15.845 35.995 15.75 35.765 15.64V14.35C36.125 14.51 36.57 14.655 37.1 14.785C37.64 14.915 38.195 14.98 38.765 14.98C39.295 14.98 39.74 14.91 40.1 14.77C40.46 14.63 40.73 14.43 40.91 14.17C41.09 13.91 41.18 13.605 41.18 13.255C41.18 12.905 41.105 12.61 40.955 12.37C40.805 12.13 40.545 11.91 40.175 11.71C39.815 11.5 39.31 11.28 38.66 11.05C38.2 10.88 37.795 10.7 37.445 10.51C37.105 10.31 36.82 10.085 36.59 9.835C36.36 9.585 36.185 9.3 36.065 8.98C35.955 8.66 35.9 8.29 35.9 7.87C35.9 7.3 36.045 6.815 36.335 6.415C36.625 6.005 37.025 5.69 37.535 5.47C38.055 5.25 38.65 5.14 39.32 5.14C39.91 5.14 40.45 5.195 40.94 5.305C41.43 5.415 41.875 5.56 42.275 5.74L41.855 6.895C41.485 6.735 41.08 6.6 40.64 6.49C40.21 6.38 39.76 6.325 39.29 6.325C38.84 6.325 38.465 6.39 38.165 6.52C37.865 6.65 37.64 6.835 37.49 7.075C37.34 7.305 37.265 7.575 37.265 7.885C37.265 8.245 37.34 8.545 37.49 8.785C37.64 9.025 37.885 9.24 38.225 9.43C38.565 9.62 39.025 9.825 39.605 10.045C40.235 10.275 40.765 10.525 41.195 10.795C41.635 11.055 41.965 11.37 42.185 11.74C42.415 12.11 42.53 12.575 42.53 13.135ZM51.2274 7.96V16H50.1474L49.9524 14.935H49.8924C49.7224 15.215 49.5024 15.445 49.2324 15.625C48.9624 15.805 48.6674 15.935 48.3474 16.015C48.0274 16.105 47.6924 16.15 47.3424 16.15C46.7024 16.15 46.1624 16.05 45.7224 15.85C45.2924 15.64 44.9674 15.32 44.7474 14.89C44.5274 14.46 44.4174 13.905 44.4174 13.225V7.96H45.7524V13.135C45.7524 13.775 45.8974 14.255 46.1874 14.575C46.4774 14.895 46.9274 15.055 47.5374 15.055C48.1374 15.055 48.6074 14.945 48.9474 14.725C49.2974 14.495 49.5424 14.165 49.6824 13.735C49.8324 13.295 49.9074 12.765 49.9074 12.145V7.96H51.2274ZM57.6049 7.81C58.5949 7.81 59.3899 8.155 59.9899 8.845C60.5999 9.535 60.9049 10.575 60.9049 11.965C60.9049 12.875 60.7649 13.645 60.4849 14.275C60.2149 14.895 59.8299 15.365 59.3299 15.685C58.8399 15.995 58.2599 16.15 57.5899 16.15C57.1799 16.15 56.8149 16.095 56.4949 15.985C56.1749 15.875 55.8999 15.735 55.6699 15.565C55.4499 15.385 55.2599 15.19 55.0999 14.98H55.0099C55.0299 15.15 55.0499 15.365 55.0699 15.625C55.0899 15.885 55.0999 16.11 55.0999 16.3V19.6H53.7799V7.96H54.8599L55.0399 9.055H55.0999C55.2599 8.825 55.4499 8.615 55.6699 8.425C55.8999 8.235 56.1699 8.085 56.4799 7.975C56.7999 7.865 57.1749 7.81 57.6049 7.81ZM57.3649 8.92C56.8249 8.92 56.3899 9.025 56.0599 9.235C55.7299 9.435 55.4899 9.74 55.3399 10.15C55.1899 10.56 55.1099 11.08 55.0999 11.71V11.965C55.0999 12.625 55.1699 13.185 55.3099 13.645C55.4499 14.105 55.6849 14.455 56.0149 14.695C56.3549 14.935 56.8149 15.055 57.3949 15.055C57.8849 15.055 58.2849 14.92 58.5949 14.65C58.9149 14.38 59.1499 14.015 59.2999 13.555C59.4599 13.085 59.5399 12.55 59.5399 11.95C59.5399 11.03 59.3599 10.295 58.9999 9.745C58.6499 9.195 58.1049 8.92 57.3649 8.92ZM66.8334 7.81C67.8234 7.81 68.6184 8.155 69.2184 8.845C69.8284 9.535 70.1334 10.575 70.1334 11.965C70.1334 12.875 69.9934 13.645 69.7134 14.275C69.4434 14.895 69.0584 15.365 68.5584 15.685C68.0684 15.995 67.4884 16.15 66.8184 16.15C66.4084 16.15 66.0434 16.095 65.7234 15.985C65.4034 15.875 65.1284 15.735 64.8984 15.565C64.6784 15.385 64.4884 15.19 64.3284 14.98H64.2384C64.2584 15.15 64.2784 15.365 64.2984 15.625C64.3184 15.885 64.3284 16.11 64.3284 16.3V19.6H63.0084V7.96H64.0884L64.2684 9.055H64.3284C64.4884 8.825 64.6784 8.615 64.8984 8.425C65.1284 8.235 65.3984 8.085 65.7084 7.975C66.0284 7.865 66.4034 7.81 66.8334 7.81ZM66.5934 8.92C66.0534 8.92 65.6184 9.025 65.2884 9.235C64.9584 9.435 64.7184 9.74 64.5684 10.15C64.4184 10.56 64.3384 11.08 64.3284 11.71V11.965C64.3284 12.625 64.3984 13.185 64.5384 13.645C64.6784 14.105 64.9134 14.455 65.2434 14.695C65.5834 14.935 66.0434 15.055 66.6234 15.055C67.1134 15.055 67.5134 14.92 67.8234 14.65C68.1434 14.38 68.3784 14.015 68.5284 13.555C68.6884 13.085 68.7684 12.55 68.7684 11.95C68.7684 11.03 68.5884 10.295 68.2284 9.745C67.8784 9.195 67.3334 8.92 66.5934 8.92ZM79.2269 11.965C79.2269 12.635 79.1369 13.23 78.9569 13.75C78.7869 14.26 78.5369 14.695 78.2069 15.055C77.8869 15.415 77.4919 15.69 77.0219 15.88C76.5619 16.06 76.0469 16.15 75.4769 16.15C74.9469 16.15 74.4569 16.06 74.0069 15.88C73.5569 15.69 73.1669 15.415 72.8369 15.055C72.5069 14.695 72.2469 14.26 72.0569 13.75C71.8769 13.23 71.7869 12.635 71.7869 11.965C71.7869 11.075 71.9369 10.325 72.2369 9.715C72.5369 9.095 72.9669 8.625 73.5269 8.305C74.0869 7.975 74.7519 7.81 75.5219 7.81C76.2519 7.81 76.8919 7.975 77.4419 8.305C78.0019 8.625 78.4369 9.095 78.7469 9.715C79.0669 10.325 79.2269 11.075 79.2269 11.965ZM73.1519 11.965C73.1519 12.595 73.2319 13.145 73.3919 13.615C73.5619 14.075 73.8219 14.43 74.1719 14.68C74.5219 14.93 74.9669 15.055 75.5069 15.055C76.0469 15.055 76.4919 14.93 76.8419 14.68C77.1919 14.43 77.4469 14.075 77.6069 13.615C77.7769 13.145 77.8619 12.595 77.8619 11.965C77.8619 11.325 77.7769 10.78 77.6069 10.33C77.4369 9.88 77.1769 9.535 76.8269 9.295C76.4869 9.045 76.0419 8.92 75.4919 8.92C74.6719 8.92 74.0769 9.19 73.7069 9.73C73.3369 10.27 73.1519 11.015 73.1519 11.965ZM85.0689 7.81C85.2189 7.81 85.3789 7.82 85.5489 7.84C85.7289 7.85 85.8839 7.87 86.0139 7.9L85.8489 9.115C85.7189 9.085 85.5739 9.06 85.4139 9.04C85.2639 9.02 85.1189 9.01 84.9789 9.01C84.6689 9.01 84.3739 9.075 84.0939 9.205C83.8139 9.335 83.5639 9.52 83.3439 9.76C83.1239 9.99 82.9489 10.27 82.8189 10.6C82.6989 10.93 82.6389 11.3 82.6389 11.71V16H81.3189V7.96H82.3989L82.5489 9.43H82.6089C82.7789 9.13 82.9839 8.86 83.2239 8.62C83.4639 8.37 83.7389 8.175 84.0489 8.035C84.3589 7.885 84.6989 7.81 85.0689 7.81ZM90.2002 15.07C90.4002 15.07 90.6052 15.055 90.8152 15.025C91.0252 14.985 91.1952 14.945 91.3252 14.905V15.91C91.1852 15.98 90.9852 16.035 90.7252 16.075C90.4652 16.125 90.2152 16.15 89.9752 16.15C89.5552 16.15 89.1652 16.08 88.8052 15.94C88.4552 15.79 88.1702 15.535 87.9502 15.175C87.7302 14.815 87.6202 14.31 87.6202 13.66V8.98H86.4802V8.35L87.6352 7.825L88.1602 6.115H88.9402V7.96H91.2652V8.98H88.9402V13.63C88.9402 14.12 89.0552 14.485 89.2852 14.725C89.5252 14.955 89.8302 15.07 90.2002 15.07Z" fill="white"/>
						<path d="M4.6 17.4V8.4C4.6 6.96783 5.16893 5.59432 6.18162 4.58162C7.19432 3.56893 8.56783 3 10 3C11.4322 3 12.8057 3.56893 13.8184 4.58162C14.8311 5.59432 15.4 6.96783 15.4 8.4V17.4M1 11.6085C1 9.7608 2.8 9.3 4.6 9.3V17.4C3.64522 17.4 2.72955 17.0207 2.05442 16.3456C1.37928 15.6705 1 14.7548 1 13.8V11.6085ZM19 11.6085C19 9.7608 17.2 9.3 15.4 9.3V17.4C16.3548 17.4 17.2705 17.0207 17.9456 16.3456C18.6207 15.6705 19 14.7548 19 13.8V11.6085Z" stroke="#8E9BAA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<div class="ssag-menu-item ssag-updates-btn" data-div="">					
					<div class="ssag-updates-detail">
						<div class="ssad-update-item"><h3><?php echo esc_html__( 'Latest Updates ', 'optimate-ads' ); ?></h3></div>
						<div class="ssad-update-item">
						
							<h4>
								<?php echo esc_html__( 'Updated to New Version 1.0.1 ', 'optimate-ads' ); ?>
								<br>
								<?php echo esc_html__( 'Added After Paragraph Ads Insertion ', 'optimate-ads' ); ?>
							</h4>
						</div>
					</div>
					<svg width="112" height="20" viewBox="0 0 112 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M41.045 6.325C40.475 6.325 39.96 6.425 39.5 6.625C39.04 6.815 38.65 7.1 38.33 7.48C38.01 7.85 37.765 8.305 37.595 8.845C37.425 9.375 37.34 9.975 37.34 10.645C37.34 11.525 37.475 12.29 37.745 12.94C38.025 13.59 38.435 14.09 38.975 14.44C39.525 14.79 40.21 14.965 41.03 14.965C41.5 14.965 41.945 14.925 42.365 14.845C42.785 14.765 43.195 14.665 43.595 14.545V15.715C43.195 15.865 42.78 15.975 42.35 16.045C41.93 16.115 41.425 16.15 40.835 16.15C39.745 16.15 38.835 15.925 38.105 15.475C37.375 15.025 36.825 14.385 36.455 13.555C36.095 12.725 35.915 11.75 35.915 10.63C35.915 9.82 36.025 9.08 36.245 8.41C36.475 7.74 36.805 7.16 37.235 6.67C37.675 6.18 38.215 5.805 38.855 5.545C39.495 5.275 40.23 5.14 41.06 5.14C41.61 5.14 42.14 5.195 42.65 5.305C43.16 5.415 43.615 5.57 44.015 5.77L43.475 6.91C43.145 6.76 42.775 6.625 42.365 6.505C41.965 6.385 41.525 6.325 41.045 6.325ZM47.0725 7.945C47.0725 8.135 47.0625 8.33 47.0425 8.53C47.0325 8.73 47.0175 8.91 46.9975 9.07H47.0875C47.2575 8.79 47.4725 8.56 47.7325 8.38C47.9925 8.2 48.2825 8.065 48.6025 7.975C48.9225 7.875 49.2525 7.825 49.5925 7.825C50.2425 7.825 50.7825 7.93 51.2125 8.14C51.6525 8.34 51.9825 8.655 52.2025 9.085C52.4225 9.515 52.5325 10.075 52.5325 10.765V16H51.2275V10.855C51.2275 10.205 51.0825 9.72 50.7925 9.4C50.5025 9.08 50.0475 8.92 49.4275 8.92C48.8275 8.92 48.3575 9.035 48.0175 9.265C47.6775 9.485 47.4325 9.815 47.2825 10.255C47.1425 10.685 47.0725 11.215 47.0725 11.845V16H45.7525V4.6H47.0725V7.945ZM58.07 7.825C59.05 7.825 59.775 8.04 60.245 8.47C60.715 8.9 60.95 9.585 60.95 10.525V16H59.99L59.735 14.86H59.675C59.445 15.15 59.205 15.395 58.955 15.595C58.715 15.785 58.435 15.925 58.115 16.015C57.805 16.105 57.425 16.15 56.975 16.15C56.495 16.15 56.06 16.065 55.67 15.895C55.29 15.725 54.99 15.465 54.77 15.115C54.55 14.755 54.44 14.305 54.44 13.765C54.44 12.965 54.755 12.35 55.385 11.92C56.015 11.48 56.985 11.24 58.295 11.2L59.66 11.155V10.675C59.66 10.005 59.515 9.54 59.225 9.28C58.935 9.02 58.525 8.89 57.995 8.89C57.575 8.89 57.175 8.955 56.795 9.085C56.415 9.205 56.06 9.345 55.73 9.505L55.325 8.515C55.675 8.325 56.09 8.165 56.57 8.035C57.05 7.895 57.55 7.825 58.07 7.825ZM58.46 12.115C57.46 12.155 56.765 12.315 56.375 12.595C55.995 12.875 55.805 13.27 55.805 13.78C55.805 14.23 55.94 14.56 56.21 14.77C56.49 14.98 56.845 15.085 57.275 15.085C57.955 15.085 58.52 14.9 58.97 14.53C59.42 14.15 59.645 13.57 59.645 12.79V12.07L58.46 12.115ZM67.3032 7.81C68.2632 7.81 68.9882 8.045 69.4782 8.515C69.9682 8.975 70.2132 9.725 70.2132 10.765V16H68.9082V10.855C68.9082 10.205 68.7632 9.72 68.4732 9.4C68.1832 9.08 67.7282 8.92 67.1082 8.92C66.2182 8.92 65.6032 9.17 65.2632 9.67C64.9232 10.17 64.7532 10.89 64.7532 11.83V16H63.4332V7.96H64.4982L64.6932 9.055H64.7682C64.9482 8.775 65.1682 8.545 65.4282 8.365C65.6982 8.175 65.9932 8.035 66.3132 7.945C66.6332 7.855 66.9632 7.81 67.3032 7.81ZM75.5557 7.81C76.0857 7.81 76.5607 7.91 76.9807 8.11C77.4107 8.31 77.7757 8.615 78.0757 9.025H78.1507L78.3307 7.96H79.3807V16.135C79.3807 16.895 79.2507 17.53 78.9907 18.04C78.7307 18.56 78.3357 18.95 77.8057 19.21C77.2757 19.47 76.6007 19.6 75.7807 19.6C75.2007 19.6 74.6657 19.555 74.1757 19.465C73.6957 19.385 73.2657 19.26 72.8857 19.09V17.875C73.1457 18.005 73.4307 18.115 73.7407 18.205C74.0507 18.305 74.3857 18.38 74.7457 18.43C75.1057 18.48 75.4757 18.505 75.8557 18.505C76.5457 18.505 77.0857 18.3 77.4757 17.89C77.8757 17.49 78.0757 16.94 78.0757 16.24V15.925C78.0757 15.805 78.0807 15.635 78.0907 15.415C78.1007 15.185 78.1107 15.025 78.1207 14.935H78.0607C77.7807 15.345 77.4307 15.65 77.0107 15.85C76.6007 16.05 76.1207 16.15 75.5707 16.15C74.5307 16.15 73.7157 15.785 73.1257 15.055C72.5457 14.325 72.2557 13.305 72.2557 11.995C72.2557 11.135 72.3857 10.395 72.6457 9.775C72.9157 9.145 73.2957 8.66 73.7857 8.32C74.2757 7.98 74.8657 7.81 75.5557 7.81ZM75.7357 8.92C75.2857 8.92 74.9007 9.04 74.5807 9.28C74.2707 9.52 74.0307 9.87 73.8607 10.33C73.7007 10.79 73.6207 11.35 73.6207 12.01C73.6207 13 73.8007 13.76 74.1607 14.29C74.5307 14.81 75.0657 15.07 75.7657 15.07C76.1757 15.07 76.5257 15.02 76.8157 14.92C77.1057 14.81 77.3457 14.645 77.5357 14.425C77.7257 14.195 77.8657 13.905 77.9557 13.555C78.0457 13.205 78.0907 12.79 78.0907 12.31V11.995C78.0907 11.265 78.0057 10.675 77.8357 10.225C77.6757 9.775 77.4207 9.445 77.0707 9.235C76.7207 9.025 76.2757 8.92 75.7357 8.92ZM85.0392 7.81C85.7292 7.81 86.3192 7.96 86.8092 8.26C87.3092 8.56 87.6892 8.985 87.9492 9.535C88.2192 10.075 88.3542 10.71 88.3542 11.44V12.235H82.8492C82.8692 13.145 83.0992 13.84 83.5392 14.32C83.9892 14.79 84.6142 15.025 85.4142 15.025C85.9242 15.025 86.3742 14.98 86.7642 14.89C87.1642 14.79 87.5742 14.65 87.9942 14.47V15.625C87.5842 15.805 87.1792 15.935 86.7792 16.015C86.3792 16.105 85.9042 16.15 85.3542 16.15C84.5942 16.15 83.9192 15.995 83.3292 15.685C82.7492 15.375 82.2942 14.915 81.9642 14.305C81.6442 13.685 81.4842 12.93 81.4842 12.04C81.4842 11.16 81.6292 10.405 81.9192 9.775C82.2192 9.145 82.6342 8.66 83.1642 8.32C83.7042 7.98 84.3292 7.81 85.0392 7.81ZM85.0242 8.89C84.3942 8.89 83.8942 9.095 83.5242 9.505C83.1642 9.905 82.9492 10.465 82.8792 11.185H86.9742C86.9742 10.725 86.9042 10.325 86.7642 9.985C86.6242 9.645 86.4092 9.38 86.1192 9.19C85.8392 8.99 85.4742 8.89 85.0242 8.89ZM91.721 16H90.401V4.6H91.721V16ZM101.258 11.965C101.258 12.635 101.168 13.23 100.988 13.75C100.818 14.26 100.568 14.695 100.238 15.055C99.9182 15.415 99.5232 15.69 99.0532 15.88C98.5932 16.06 98.0782 16.15 97.5082 16.15C96.9782 16.15 96.4882 16.06 96.0382 15.88C95.5882 15.69 95.1982 15.415 94.8682 15.055C94.5382 14.695 94.2782 14.26 94.0882 13.75C93.9082 13.23 93.8182 12.635 93.8182 11.965C93.8182 11.075 93.9682 10.325 94.2682 9.715C94.5682 9.095 94.9982 8.625 95.5582 8.305C96.1182 7.975 96.7832 7.81 97.5532 7.81C98.2832 7.81 98.9232 7.975 99.4732 8.305C100.033 8.625 100.468 9.095 100.778 9.715C101.098 10.325 101.258 11.075 101.258 11.965ZM95.1832 11.965C95.1832 12.595 95.2632 13.145 95.4232 13.615C95.5932 14.075 95.8532 14.43 96.2032 14.68C96.5532 14.93 96.9982 15.055 97.5382 15.055C98.0782 15.055 98.5232 14.93 98.8732 14.68C99.2232 14.43 99.4782 14.075 99.6382 13.615C99.8082 13.145 99.8932 12.595 99.8932 11.965C99.8932 11.325 99.8082 10.78 99.6382 10.33C99.4682 9.88 99.2082 9.535 98.8582 9.295C98.5182 9.045 98.0732 8.92 97.5232 8.92C96.7032 8.92 96.1082 9.19 95.7382 9.73C95.3682 10.27 95.1832 11.015 95.1832 11.965ZM106.2 7.81C106.73 7.81 107.205 7.91 107.625 8.11C108.055 8.31 108.42 8.615 108.72 9.025H108.795L108.975 7.96H110.025V16.135C110.025 16.895 109.895 17.53 109.635 18.04C109.375 18.56 108.98 18.95 108.45 19.21C107.92 19.47 107.245 19.6 106.425 19.6C105.845 19.6 105.31 19.555 104.82 19.465C104.34 19.385 103.91 19.26 103.53 19.09V17.875C103.79 18.005 104.075 18.115 104.385 18.205C104.695 18.305 105.03 18.38 105.39 18.43C105.75 18.48 106.12 18.505 106.5 18.505C107.19 18.505 107.73 18.3 108.12 17.89C108.52 17.49 108.72 16.94 108.72 16.24V15.925C108.72 15.805 108.725 15.635 108.735 15.415C108.745 15.185 108.755 15.025 108.765 14.935H108.705C108.425 15.345 108.075 15.65 107.655 15.85C107.245 16.05 106.765 16.15 106.215 16.15C105.175 16.15 104.36 15.785 103.77 15.055C103.19 14.325 102.9 13.305 102.9 11.995C102.9 11.135 103.03 10.395 103.29 9.775C103.56 9.145 103.94 8.66 104.43 8.32C104.92 7.98 105.51 7.81 106.2 7.81ZM106.38 8.92C105.93 8.92 105.545 9.04 105.225 9.28C104.915 9.52 104.675 9.87 104.505 10.33C104.345 10.79 104.265 11.35 104.265 12.01C104.265 13 104.445 13.76 104.805 14.29C105.175 14.81 105.71 15.07 106.41 15.07C106.82 15.07 107.17 15.02 107.46 14.92C107.75 14.81 107.99 14.645 108.18 14.425C108.37 14.195 108.51 13.905 108.6 13.555C108.69 13.205 108.735 12.79 108.735 12.31V11.995C108.735 11.265 108.65 10.675 108.48 10.225C108.32 9.775 108.065 9.445 107.715 9.235C107.365 9.025 106.92 8.92 106.38 8.92Z" fill="white"/>
						<path d="M10 19C14.9706 19 19 14.9706 19 10C19 5.02944 14.9706 1 10 1C5.02944 1 1 5.02944 1 10C1 14.9706 5.02944 19 10 19Z" stroke="#8E9BAA" stroke-width="1.5"/>
						<path d="M14.1247 7.9003C13.429 6.4873 11.8387 5.5 9.9892 5.5C7.6492 5.5 5.725 7.0786 5.5 9.1" stroke="#8E9BAA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M5.87531 11.5003C6.57191 13.2661 8.16131 14.5 10.0108 14.5C12.3508 14.5 14.2732 12.5263 14.5 10M12.2446 7.94981H13.96C14.0309 7.94981 14.1011 7.93584 14.1667 7.90871C14.2322 7.88157 14.2917 7.84179 14.3418 7.79165C14.392 7.74151 14.4318 7.68198 14.4589 7.61646C14.486 7.55095 14.5 7.48073 14.5 7.40981V5.95001L12.2446 7.94981Z" stroke="#8E9BAA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M7.7554 11.4598H6.04C5.89678 11.4598 5.75943 11.5167 5.65816 11.618C5.55689 11.7192 5.5 11.8566 5.5 11.9998V13.96" stroke="#8E9BAA" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</div>
				<div class="ssag-menu-item ssag-updates-btn" data-div="">	
					
				</div>
			</div>
			<div class="ssag-all-ads-main ssag-all-ads-main-ads-placement">
				<div class="ssag-save-post-messages"></div>
				<div class="ssag-ads-btn-main"> 
					<?php
					$ads_exists = 9;
					foreach ( $all_ads_posts as $key => $value ) {
						$ad_post_id     = $value->ID;
						$hidden_class   = '';
						$check_if_saved = get_post_meta( $ad_post_id, 'ssag_ad_saved', true );
						if ( 8 < $key ) {

							if ( 'saved' !== $check_if_saved ) {
								$hidden_class = ' ssag-hidden ';
								if ( 9 < ! $ads_exists ) {
									$ads_exists = $key;

								}
							}
						}
						$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
						?>
								<button class="ssag-ads-count-btn ssag-ads-count-btn-<?php echo esc_attr( $key ); ?> <?php
								echo ( 0 === $key ) ? 'ssag-ads-count-btn-active' : '';
								echo esc_attr( $hidden_class );

								echo esc_attr( ( 'disable' != $ssag_ads_insertion && '' != $ssag_ads_insertion ) ? ' ssag-ads-menu-saved ' : '' );

								?>
								" data-options="ssag-ads-count-options-<?php echo esc_attr( $key ); ?>" disabled><?php echo esc_attr( $key + 1 ); ?></button>
							<?php
					}
					?>
							<button class="ssag-ads-count-btn-add-new <?php echo $ads_exists > 24 ? ' ssag-hidden ' : ' '; ?>" data-ads_exists="<?php echo esc_attr( $ads_exists ); ?>">+</button>
						<button class="ssag-ads-save-this-ads  "><span class="ssag-ads-save-this-ads-spin"> <span class="icon">&#9881;</span> </span> <h4> <?php echo esc_html__( 'Save Settings ', 'optimate-ads' ); ?> </h4> </button>
				</div>
				<?php
				foreach ( $all_ads_posts as $key => $value ) {
					
					$ads_no_post_id = $key;
					$ad_post_id   = $value->ID;
					$hidden_class = '';
					if ( 0 < $key ) {
							$hidden_class = ' ssag-hidden ';
					}
					$ssag_ads_title                = get_post_meta( $ad_post_id, 'ssag_ads_title', true );
					$ssag_ads_content_html         = get_post_meta( $ad_post_id, 'ssag_ads_content_html', true );
					$ssag_ads_include_posts        = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );
					$ssag_ads_include_pages        = get_post_meta( $ad_post_id, 'ssag_ads_include_pages', true );
					$ssag_ads_include_home_page    = get_post_meta( $ad_post_id, 'ssag_ads_include_home_page', true );
					$ssag_ads_include_search_pages = get_post_meta( $ad_post_id, 'ssag_ads_include_search_pages', true );
					$ssag_ads_include_cat_pages    = get_post_meta( $ad_post_id, 'ssag_ads_include_cat_pages', true );
					$ssag_ads_include_arch_pages   = get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
					$ssag_ads_insertion            = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
					$ssag_ads_para_id              = get_post_meta( $ad_post_id, 'ssag_ads_para_id', true );
					$ssag_ads_align                = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
					$ssag_ad_custom_css            = get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true );
					$ssag_ads_categories           = get_post_meta( $ad_post_id, 'ssag_ads_categories', true );
					$ssag_ads_tags                 = get_post_meta( $ad_post_id, 'ssag_ads_tags', true );
					$ssag_ads_posts_ids            = get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );
					$ssag_ads_url                  = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
					$ssag_ads_url_parameter        = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
					$ssag_ads_show_on_widget       = get_post_meta( $ad_post_id, 'ssag_ads_show_on_widget', true );
					$ssag_ads_show_on_shortcode    = get_post_meta( $ad_post_id, 'ssag_ads_show_on_shortcode', true );
					$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );
					$ssag_ads_use_desktop          = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
					$ssag_ads_use_tablet           = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
					$ssag_ads_use_phone            = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
					?>
							<div data-active_btn="ssag-ads-count-btn-<?php echo esc_attr( $key ); ?>" class="ssag-single-ads-main ssag-ads-count-options-<?php echo esc_attr( $key ) . ' ' . esc_attr( $hidden_class ) . ' '; ?> hidden_class">
								<form action="" class="ssag-ads-form" method="post">
									<input type="hidden" name="ssag_ads_id" class="ssag_ads_id" value="<?php echo esc_attr( $ad_post_id ); ?>">
									<input type="text" class="ssag_ads_title" placeholder="Add label for Ad" name="ssag_ads_title" value="<?php echo wp_kses_post( $ssag_ads_title ); ?>">
									<textarea name="ssag_ads_content_html" class="ssag_ads_content_html" cols="30" rows="10"><?php echo esc_attr( $ssag_ads_content_html ); ?></textarea>

									<div class="ssag-posts-cbs-div">
										<div class="ssag-posts-cbs-single">
											<input type="checkbox" name="ssag_ads_include_posts" id="ssag_ads_include_posts<?php echo esc_attr( $key ); ?>" class="ssag_ads_include_posts" <?php checked( $ssag_ads_include_posts, 'yes' ); ?> value="yes">
											<label for="ssag_ads_include_posts<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Posts ', 'optimate-ads' ); ?></label>
										</div>
										<div class="ssag-posts-cbs-single">						
											<input type="checkbox" name="ssag_ads_include_pages" id="ssag_ads_include_pages<?php echo esc_attr( $key ); ?>" class="ssag_ads_include_pages"<?php checked( $ssag_ads_include_pages, 'yes' ); ?> value="yes">
											<label for="ssag_ads_include_pages<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Pages ', 'optimate-ads' ); ?></label>
										</div>
										<div class="ssag-posts-cbs-single">						
											<input type="checkbox" name="ssag_ads_include_home_page" id="ssag_ads_include_home_page<?php echo esc_attr( $key ); ?>" class="ssag_ads_include_home_page"<?php checked( $ssag_ads_include_home_page, 'yes' ); ?> value="yes">
											<label for="ssag_ads_include_home_page<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Homepage ', 'optimate-ads' ); ?></label>
										</div>
										<div class="ssag-posts-cbs-single">						
											<input type="checkbox" name="ssag_ads_include_search_pages" id="ssag_ads_include_search_pages<?php echo esc_attr( $key ); ?>" class="ssag_ads_include_search_pages"<?php checked( $ssag_ads_include_search_pages, 'yes' ); ?> value="yes">
											<label for="ssag_ads_include_search_pages<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Search Pages ', 'optimate-ads' ); ?></label>
										</div>
										<div class="ssag-posts-cbs-single">						
											<input type="checkbox" name="ssag_ads_include_cat_pages" id="ssag_ads_include_cat_pages<?php echo esc_attr( $key ); ?>" class="ssag_ads_include_cat_pages"<?php checked( $ssag_ads_include_cat_pages, 'yes' ); ?> value="yes">
											<label for="ssag_ads_include_cat_pages<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Category Pages ', 'optimate-ads' ); ?></label>
										</div>
										<div class="ssag-posts-cbs-single">													
											<input type="checkbox" name="ssag_ads_include_arch_pages" id="ssag_ads_include_arch_pages<?php echo esc_attr( $key ); ?>" class="ssag_ads_include_arch_pages"<?php checked( $ssag_ads_include_arch_pages, 'yes' ); ?> value="yes">
											<label for="ssag_ads_include_arch_pages<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Tag/ Archive ', 'optimate-ads' ); ?></label>
										</div>
									</div>

									<div class="ssag-posts-insertion-div">
										<div class="each-item-insetion">
											<label for="ssag_ads_insertion<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Insertion ', 'optimate-ads' ); ?></label>
											<select name="ssag_ads_insertion" id="ssag_ads_insertion<?php echo esc_attr( $key ); ?>" class="ssag_ads_insertion">
												<option value="disable" <?php selected( 'disable', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Disabled ', 'optimate-ads' ); ?></option>
												<option value="before-post" <?php selected( 'before-post', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Before Post ', 'optimate-ads' ); ?></option>
												<option value="before-content" <?php selected( 'before-content', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Before Content ', 'optimate-ads' ); ?></option>
												<option value="mid-content" <?php selected( 'mid-content', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Mid Content ', 'optimate-ads' ); ?></option>
												<option value="before-para" <?php selected( 'before-para', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Before Paragraph ', 'optimate-ads' ); ?></option>
												<option value="after-para" <?php selected( 'after-para', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'After Paragraph ', 'optimate-ads' ); ?></option>
												<option value="before-img" <?php selected( 'before-img', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Before Image ', 'optimate-ads' ); ?></option>
												<option value="after-img" <?php selected( 'after-img', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'After Image ', 'optimate-ads' ); ?></option>
												<option value="after-content" <?php selected( 'after-content', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'After Content ', 'optimate-ads' ); ?></option>
												<option value="after-post" <?php selected( 'after-post', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'After Post ', 'optimate-ads' ); ?></option>
												<option value="before-excerpt" <?php selected( 'before-excerpt', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Before Excerpt ', 'optimate-ads' ); ?></option>
												<option value="between-posts" <?php selected( 'between-posts', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Between Posts ', 'optimate-ads' ); ?></option>
												<option value="before-comments" <?php selected( 'before-comments', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Before Comments ', 'optimate-ads' ); ?></option>
												<option value="after-comments" <?php selected( 'after-comments', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'After Comments ', 'optimate-ads' ); ?></option>
												<option value="footer" <?php selected( 'footer', $ssag_ads_insertion ); ?> ><?php echo esc_html__( 'Footer ', 'optimate-ads' ); ?></option>
											</select>
										</div>
										<div class="each-item-insetion ssag_ads_para_id-main <?php echo ( ( 'before-para' !== $ssag_ads_insertion ) && ( 'after-para' !== $ssag_ads_insertion ) && ( 'before-img' !== $ssag_ads_insertion ) && ( 'after-img' !== $ssag_ads_insertion ) && ( 'between-posts' !== $ssag_ads_insertion ) ) ? ' ssag-hidden ' : ''; ?>">
											<input type="text" name="ssag_ads_para_id" id="ssag_ads_para_id<?php echo esc_attr( $key ); ?>" class="ssag_ads_para_id" placeholder="Enter by ',' comma seprated" value="<?php echo esc_attr( $ssag_ads_para_id ); ?>">
										</div>
										<div class="each-item-align">
											<label for="ssag_ads_align<?php echo esc_attr( $key ); ?>"><?php echo esc_html__( 'Alignment ', 'optimate-ads' ); ?></label>
											<select name="ssag_ads_align" class="ssag_ads_align">
												<option value="default" <?php selected( 'default', $ssag_ads_align ); ?>><?php echo esc_html__( 'Default ', 'optimate-ads' ); ?></option>
												<option value="left" <?php selected( 'left', $ssag_ads_align ); ?>><?php echo esc_html__( 'Left ', 'optimate-ads' ); ?></option>
												<option value="center" <?php selected( 'center', $ssag_ads_align ); ?>><?php echo esc_html__( 'Center ', 'optimate-ads' ); ?></option>
												<option value="right" <?php selected( 'right', $ssag_ads_align ); ?>><?php echo esc_html__( 'Right ', 'optimate-ads' ); ?></option>
												<option value="float-left" <?php selected( 'float-left', $ssag_ads_align ); ?>><?php echo esc_html__( 'Float Left ', 'optimate-ads' ); ?></option>
												<option value="float-right" <?php selected( 'float-right', $ssag_ads_align ); ?>><?php echo esc_html__( 'Float Right ', 'optimate-ads' ); ?></option>
												<option value="custom-css" <?php selected( 'custom-css', $ssag_ads_align ); ?>><?php echo esc_html__( 'Custom CSS ', 'optimate-ads' ); ?></option>
												<option value="no-wrap" <?php selected( 'no-wrap', $ssag_ads_align ); ?>><?php echo esc_html__( 'No Wrapping ', 'optimate-ads' ); ?></option>
											</select>
										</div>
									</div>

									<div class="ssag-this-ad-custom-css <?php echo esc_attr( 'custom-css' === $ssag_ads_align ? '' : ' ssag-hidden ' ); ?>">
										<textarea name="ssag_ad_custom_css" cols="30" rows="10"><?php echo esc_attr( $ssag_ad_custom_css ); ?></textarea>
									</div>

									<div class="ssag-posts-restrictions-btn">
										<button class="ssag-posts-restrict-btn ssag-posts-btn-active" data-value="ssag-posts-restrict-options">&#128683; &nbsp;<?php echo esc_html__( 'Restrictions ', 'optimate-ads' ); ?></button>
										<button class="ssag-posts-shortcode-btn" data-value="ssag-posts-shortcode-options"><?php echo esc_html__( '</> Short Code ', 'optimate-ads' ); ?></button>
										<button class="ssag-posts-devices-btn" data-value="ssag-posts-devices-options">&#128241; &nbsp;<?php echo esc_html__( 'Devices ', 'optimate-ads' ); ?></button>
									</div>

									<div class="ssag-posts-restrict-options">
										<div class="each-row-options">
											<div class="label first">
												<label for=""><?php echo esc_html__( 'Categories ', 'optimate-ads' ); ?></label>
											</div>
											<div class="box"></div>
											<div class="value ssag-select2-main">
												<select name="ssag_ads_categories" class="ssag-select-select2" multiple style="width: 100%;">
												<?php
												foreach ( $all_post_categories as $key => $value ) {
													?>
																<option value="<?php echo esc_attr( $value->term_id ); ?>" <?php echo in_array( $value->term_id, (array) $ssag_ads_categories ) ? ' selected="selected" ' : ''; ?> ><?php echo esc_attr( $value->name ); ?></option>
														<?php
												}
												?>
												</select>
											</div>
											<div class="remove"><span>&times;</span></div>
										</div>
										<div class="each-row-options">
											<div class="label ">
												<label for=""><?php echo esc_html__( 'Tags ', 'optimate-ads' ); ?></label>
											</div>
											<div class="box"></div>
											<div class="value ssag-select2-main">
												<select name="ssag_ads_tags" class="ssag-select-select2" multiple style="width: 100%;">
													<?php
													foreach ( $all_post_tags as $key => $value ) {
														if ( isset( $value->term_id ) ) {
															?>
																<option value="<?php echo esc_attr( $value->term_id ); ?>"  <?php echo in_array( $value->term_id, (array) $ssag_ads_tags ) ? ' selected="selected" ' : ''; ?> ><?php echo esc_attr( $value->name ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
											<div class="remove"><span>&times;</span></div>
										</div>
										<div class="each-row-options">
											<div class="label ">
												<label for=""><?php echo esc_html__( "Posts (Post ID's' ", 'optimate-ads' ); ?></label>
											</div>
											<div class="box"></div>
											<div class="value ssag-select2-main">
												<select name="ssag_ads_posts_ids" class="ssag-select-select2" multiple style="width: 100%; max-width:100%; ">
													<?php
													foreach ( $all_posts as $key => $value ) {
														$text = ' (' . $value->post_type . ') ' . $value->post_title;
														?>
																<option value="<?php echo esc_attr( $value->ID ); ?>"   <?php echo in_array( $value->ID, (array) $ssag_ads_posts_ids ) ? ' selected="selected" ' : ''; ?> ><?php echo esc_attr( $text ); ?></option>
															<?php
													}
													?>
												</select>
											</div>
											<div class="remove"><span>&times;</span></div>
										</div>
										<div class="each-row-options">
											<div class="label ">
												<label for=""><?php echo esc_html__( "URL's ", 'optimate-ads' ); ?></label>
											</div>
											<div class="box"></div>
											<div class="value">
												<input type="text" name="ssag_ads_url" placeholder="<?php echo esc_html__( "Enter URL's seprataed by comma ',' ", 'optimate-ads' ); ?>" value="<?php echo esc_attr( $ssag_ads_url ); ?>">
											</div>
											<div class="remove"><span>&times;</span></div>
										</div>
										<div class="each-row-options">
											<div class="label ">
												<label for=""><?php echo esc_html__( 'URL Parameter ', 'optimate-ads' ); ?></label>
											</div>
											<div class="box"></div>
											<div class="value">
												<input type="text" name="ssag_ads_url_parameter" placeholder="<?php echo esc_html__( "Enter URL parameters ',' ", 'optimate-ads' ); ?>" value="<?php echo esc_attr( $ssag_ads_url_parameter ); ?>">
											</div>
											<div class="remove"><span>&times;</span></div>
										</div>						
									</div>

									<div class="ssag-posts-shortcode-options">
										<table class="ssag-column-1">
											<tr class="">
												<td class="widget">
													<input type="checkbox" <?php checked( $ssag_ads_show_on_widget, 'yes' ); ?> name="ssag_ads_show_on_widget" >
													<h4> <i class="material-icons" >widgets</i> <span> <?php echo esc_html__( 'Widget ', 'optimate-ads' ); ?> </span> </h4>
												</td>
											</tr>
											<tr>
												<td class="widget">
													<input type="checkbox" <?php checked( $ssag_ads_show_on_shortcode, 'yes' ); ?> name="ssag_ads_show_on_shortcode" >
													<h4> { } <?php echo esc_html__( 'Shortcode ', 'optimate-ads' ); ?> </span> </h4>
												</td>
												<td>
													<p><?php echo esc_attr( '[ssag_ads_shortcode id="' . $ads_no_post_id . '"]' ); ?></p>
												</td>
											</tr>
											<tr>
												<td class="widget">
													<input type="checkbox" <?php checked( $ssag_ads_show_on_php_function, 'yes' ); ?> name="ssag_ads_show_on_php_function" >
													<h4> <i class="ssag-php-icon " >php</i> <span> <?php echo esc_html__( 'PHP Function ', 'optimate-ads' ); ?> </span> </h4>
												</td>
												<td>
													<p><?php echo esc_attr( '<?php if (function_exists ("optimate_ad")) echo optimate_ad(' . $ads_no_post_id . '); ?>' ); ?></p>
												</td>
											</tr>
										</table>
									</div>

									<div class="ssag-posts-devices-options">
										<div class="right">
											<input type="checkbox" <?php checked( $ssag_ads_use_desktop, 'yes' ); ?> name="ssag_ads_use_desktop" >
											<h4><?php echo esc_html__( ' Desktop ', 'optimate-ads' ); ?></h4>
										</div>
										<div class="right">
											<input type="checkbox" <?php checked( $ssag_ads_use_tablet, 'yes' ); ?> name="ssag_ads_use_tablet" >
											<h4><?php echo esc_html__( ' Tablet ', 'optimate-ads' ); ?></h4>
										</div>
										<div class="right">
											<input type="checkbox" <?php checked( $ssag_ads_use_phone, 'yes' ); ?> name="ssag_ads_use_phone" >
											<h4><?php echo esc_html__( ' Phone ', 'optimate-ads' ); ?></h4>
										</div>
									</div>


									<button class="ssag-ads-save-this-ads form-ssag-ads-save-this-ads "><span class="ssag-ads-save-this-ads-spin"> <span class="icon">&#9881;</span> </span> <h4> <?php echo esc_html__( 'Save Settings ', 'optimate-ads' ); ?> </h4> </button>

								</form>
							</div>
						<?php
				}
				?>
			</div>
			<div class="ssag-all-ads-main ssag-all-ads-main-ads-txt ssag-all-ads-main-ads-txt-div ssag-all-ads-hidden">
				<div class="ssag-save-post-messages"></div>
				<?php
					$this->setting_page_ads_txt_callback_fn();
				?>
			</div>
			<div class="ssag-all-ads-main ssag-all-ads-main-support ssag-all-ads-hidden">
				<div class="ssag-save-post-messages"></div>
				<?php
					$this->setting_page_support_callback_fn();
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Function to save header and footer
	 */
	public function ssag_save_header_cb() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['nonce'] ) ? $_POST['nonce'] : '' ) ), 'ajax-nonce' ) ) {
			die( 'Destroy!' );
		}

		$ssag_styles_for_header = htmlentities( stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['ssag_styles_for_header'] ) ? $_POST['ssag_styles_for_header'] : '' ) ) ) );
		$ssag_script_for_footer = htmlentities( stripslashes( sanitize_text_field( wp_unslash( isset( $_POST['ssag_script_for_footer'] ) ? $_POST['ssag_script_for_footer'] : '' ) ) ) );

		update_option( 'ssag_styles_for_header', $ssag_styles_for_header );
		update_option( 'ssag_script_for_footer', $ssag_script_for_footer );
			$message      = '';
			$message     .= '<div class="success-msg">';
				$message .= '<i class="fa fa-check"></i>';
				$message .= 'Setting successfully saved. ';
			$message     .= '</div>';

		wp_send_json(
			array(
				'message'     => $message,
				'html is -> ' => $ssag_styles_for_header,
			)
		);
	}

	/**
	 * Function for creating ads.txt file
	 */
	public function ssag_save_ads_txt_cb() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['nonce'] ) ? $_POST['nonce'] : '' ) ), 'ajax-nonce' ) ) {
			echo 'Nonce not verfied';
		}
		$ssag_ads_txt_all_code = sanitize_text_field( wp_unslash( isset( $_POST['ssag_ads_txt_all_code'] ) ? $_POST['ssag_ads_txt_all_code'] : '' ) );

		$robots_file = get_home_path() . 'ads.txt';
		if ( file_exists( $robots_file ) ) {
			unlink( $robots_file );
		}
		$write = true;
		if ( '' !== $ssag_ads_txt_all_code ) {
			$write = file_put_contents( $robots_file, $ssag_ads_txt_all_code );
		}

		if ( $write ) {
			$message  = '';
			$message .= '<div class="success-msg">';
			$message .= '<i class="fa fa-check"></i>';
			$message .= 'Setting successfully saved. ';
			$message .= '</div>';
		} else {
			$message      = '<div class="error-msg">';
				$message .= '<i class="fa fa-times-circle"></i>';
				$message .= 'Some thing went wrong please try again later';
			$message     .= '</div>';
		}

		wp_send_json( array( 'message' => $message ) );
	}

	/**
	 * Function for geting ads.txt file
	 */
	public function setting_page_ads_txt_callback_fn() {

		$ads_txt     = '';
		$ad_txt_file = get_home_path() . 'ads.txt';
		if ( file_exists( $ad_txt_file ) ) {
			$txt_file = fopen( $ad_txt_file, 'r' );
			$ads_txt  = fgets( $txt_file );
			fclose( $txt_file );
		}
		?>
			<h2><?php echo esc_html__( 'Upload Your ads.txt ', 'optimate-ads' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Upload Your ads.txt file ', 'optimate-ads' ); ?></p>
			<button class="button-primary ssag-upload-ads-txt"><?php echo esc_html__( 'Upload ', 'optimate-ads' ); ?></button>
			<input type="file"  accept=".txt" class="ssag-upload-ads-txt-input ssag-hidden" >
			<h2 class="description"><?php echo esc_html__( "If you don't  have file to upload, you can past the ads.txt code below", 'optimate-ads' ); ?></h2>

			<form action="" class="ssag-form-ads-txt" method="post">
				<p class="description"><?php echo esc_html__( 'Past your ads.txt code here ', 'optimate-ads' ); ?></p>
				<textarea name="ssag_ads_txt_all_code" class="ssag_ads_txt_all_code" cols="30" rows="10"><?php echo esc_attr( $ads_txt ); ?></textarea>
				<br>
				<button type="submit" class="button-primary" name="ssag_ads_txt_all_code_submit"><?php echo esc_html__( 'Save Settings ', 'optimate-ads' ); ?></button>
			</form>
		<?php
	}


	/**
	 * Function for saving header and footer
	 */
	public function setting_page_header_tracking_code_callback_fn() {

		?>
				<h1><?php echo esc_html__( 'Insert Headers and Footers', 'optimate-ads' ); ?></h1>
				<br>

				<form action="" class="ssag-form-header" method="post">
					<h2 class="description"><?php echo esc_html__( 'Scripts in Header (Your Header Tracking code here )', 'optimate-ads' ); ?></h2>
					<textarea name="ssag_styles_for_header" class="ssag_styles_for_header" cols="30" rows="10"><?php echo esc_attr( get_option( 'ssag_styles_for_header' ) ); ?></textarea>
					<p class="description"><?php echo esc_html__( 'These Scripts will be printed in the ', 'optimate-ads' ); ?><span class="hightlight"><?php echo esc_html__( ' <head> ', 'optimate-ads' ); ?></span><?php echo esc_html__( ' Section.', 'optimate-ads' ); ?></p>
					<br>
					<h2 class="description"><?php echo esc_html__( 'Scripts in Footer ', 'optimate-ads' ); ?></h2>
					<textarea name="ssag_script_for_footer" class="ssag_script_for_footer" cols="30" rows="10"><?php echo esc_attr( get_option( 'ssag_script_for_footer' ) ); ?></textarea>
					<p class="description"><?php echo esc_html__( 'These Scripts will be printed above the ', 'optimate-ads' ); ?><span class="hightlight"><?php echo esc_html__( ' </head> ', 'optimate-ads' ); ?></span><?php echo esc_html__( ' tag.', 'optimate-ads' ); ?></p>
					<button type="submit" class="button-primary" name="ssag_ssag_script_for_footer_submit"><?php echo esc_html__( 'Save Settings ', 'optimate-ads' ); ?></button>
				</form>
		<?php
	}

	/**
	 * Function for sending support mail
	 */
	public function ssag_support_cb() {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['nonce'] ) ? $_POST['nonce'] : '' ) ), 'ajax-nonce' ) ) {
			die( 'Destroy!' );
		}
		$first_name = sanitize_text_field( wp_unslash( isset( $_POST['ssag_support_fname'] ) ? $_POST['ssag_support_fname'] : '' ) );
		$last_name  = sanitize_text_field( wp_unslash( isset( $_POST['ssag_support_lname'] ) ? $_POST['ssag_support_lname'] : '' ) );
		$email      = sanitize_text_field( wp_unslash( isset( $_POST['ssag_support_email'] ) ? $_POST['ssag_support_email'] : '' ) );
		$url        = sanitize_text_field( wp_unslash( isset( $_POST['ssag_support_website_url'] ) ? $_POST['ssag_support_website_url'] : '' ) );
		$msg        = sanitize_text_field( wp_unslash( isset( $_POST['ssag_support_message'] ) ? $_POST['ssag_support_message'] : '' ) );

		$notification = '';
			$message  = 'Name : ' . $first_name . ' ' . $last_name . '<br>';
			$message .= 'Website URL ' . $url . '<br>';
			$message .= 'MMessage is ' . $msg;
			apply_filters( 'wp_mail_from', $email );

		// email message header attachment.
		if ( wp_mail( 'Optimateads@gmail.com', '', $message, '', array() ) ) {
			$message      = '<div class="success-msg">';
				$message .= '<i class="fa fa-check"></i>';
				$message .= ' Mail Send to Support Successfully ';
			$message     .= '</div>';
		} else {
			$message      = '<div class="error-msg">';
				$message .= '<i class="fa fa-times-circle"></i>';
				$message .= 'Some thing went wrong please try again later';
			$message     .= '</div>';
		}

		wp_send_json( array( 'message' => $message ) );
	}

	/**
	 * Function for support information and sending messages
	 */
	public function setting_page_support_callback_fn() {
		$notification = '';

		?>
				<?php
				if ( '' !== $notification ) {
					?>
							<div class="ssag-save-post-messages"><?php echo wp_kses_post( $notification ); ?></div>
						<?php
				}
				?>

				<h1><?php echo esc_html__( 'Contact us', 'optimate-ads' ); ?></h1>
				<br>

				<form action="" class="ssag-support-form" method="post">
					<div class="row">
						<div class="column">
							<label for=""><?php echo esc_html__( 'First Name ', 'optimate-ads' ); ?></label>
							<input type="text" name="ssag_support_fname" >
						</div>
						<div class="column">
							<label for=""><?php echo esc_html__( 'Last Name ', 'optimate-ads' ); ?></label>
							<input type="text" name="ssag_support_lname" >
						</div>
					</div>
					<div class="row">
						<div class="column">
							<label for=""><?php echo esc_html__( 'Email ', 'optimate-ads' ); ?></label>
							<input type="text" name="ssag_support_email" >
						</div>
						<div class="column">
							<label for=""><?php echo esc_html__( 'Website (URL) ', 'optimate-ads' ); ?></label>
							<input type="text" name="ssag_support_website_url" >
						</div>
					</div>
					<div class="row">
						<p class="description"><?php echo esc_html__( 'Message ', 'optimate-ads' ); ?></p>
						<textarea name="ssag_support_message" cols="30" rows="10"></textarea>
					</div>
					<button type="submit" class="button-primary" name="ssag_ssag_script_for_footer_submit"><?php echo esc_html__( 'Send Message ', 'optimate-ads' ); ?></button>
				</form>
		<?php
	}

}
new Opad_Admin_Class();

