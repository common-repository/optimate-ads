<?php
/**
 * Extent WPWidget
 *
 * @package for Widget settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for front end of plugin.
 */
class Opad_Front_Class {

	/**
	 * For Constructor of class.
	 */
	public function __construct() {
		$this->ssag_all_posts_ads = $this->opad_all_posts_ads_fn();

		$this->ssag_between_posts = 0;

		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );

		add_filter( 'the_content', array( $this, 'ssag_filter_content' ) );

		add_filter( 'the_excerpt', array( $this, 'ssag_filter_excerpt' ) );

		add_filter( 'comment_form_defaults', array( $this, 'ssad_comment_optimate_ads' ) );

		add_action( 'comment_form_logged_in_after', array( $this, 'ssag_ad_before_comment' ), 10, 2 );

		add_action( 'wp_head', array( $this, 'opad_filter_head' ) );
		add_action( 'wp_footer', array( $this, 'opad_filter_footer' ) );

		add_shortcode( 'ssag_ads_shortcode', array( $this, 'ssag_ads_shortcode_fn_cb' ) );

		add_action( 'admin_bar_menu', array( $this, 'wp_admin_menu' ), 999 );

		add_action( 'init', array( $this, 'ssag_debug_sessions' ) );

		add_action( 'loop_start', array( $this, 'ssag_debug_wp_before_post' ) );
		add_action( 'loop_end', array( $this, 'ssag_debug_wp_after_post' ) );

		add_action( 'the_post', array( $this, 'my_the_post_action' ) );
	}

	/**
	 * For getting all ads.
	 */
	public function opad_all_posts_ads_fn() {
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

	/**
	 * Function for ad html.
	 *
	 * @param string $align_class for alignment of ad.
	 * @param string $enable_phone for phone.
	 * @param string $enable_tablet for tablet.
	 * @param string $enable_desktop for desktop.
	 * @param string $ad_post_id for ad ID.
	 */
	public function ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id ) {
		ob_start();
		$block_number = get_post_meta( $ad_post_id, 'ssad_ads_post_key', true );
		?>
				<div class="ssag-label-block-debug">
					<div class="ssag-label-block-no">
						<span class="left">
							<?php echo wp_kses_post( $block_number . ' Block ' . $block_number ); ?>
						</span>
					</div>
					<?php
						echo wp_kses_post( '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>' );
					?>
				</div>
			<?php
			$ads_html = ob_get_clean();
			return $ads_html;
	}

	/**
	 * Function for action post.
	 *
	 * @param object $post_object for object of post.
	 */
	public function my_the_post_action( $post_object ) {
		if ( ! is_archive() ) {
			return '';
		}

		if ( 0 === $this->ssag_session_check( 'opt-position' ) ) {
			echo wp_kses_post( $this->opad_debug_block( 'Between Post ' . $this->ssag_between_posts ) );
		}

		$all_ads_posts = $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}

			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';

			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$rest_url = get_post_meta( $ad_post_id, 'ssag_ads_url', true );

			$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			if ( 'between-posts' !== $ssag_ads_insertion ) {
				continue;
			}

			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}

			$enable_post      = get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			$ssag_ads_para_id = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );

			if ( 'yes' === $enable_post ) {

				$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );

				if ( in_array( $this->ssag_between_posts, (array) $ssag_ads_para_id ) ) {
					if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
					}
						echo wp_kses_post( $ads_html );
				}
			}
		}

	}

	/**
	 * Function for before post content.
	 */
	public function ssag_debug_wp_before_post() {

		global $post;
		$opad_post = $post;
		if ( 0 === $this->ssag_session_check( 'opt-position' ) ) {

			if ( $opad_post ) {
				echo wp_kses_post( $this->opad_debug_block( 'Before Post' ) );
			}
		}
		$all_ads_posts = $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';

			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}

			$rest_url = get_post_meta( $ad_post_id, 'ssag_ads_url', true );

			$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}

			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );

			if ( 'yes' === $enable_post ) {
				if ( 'post' === $opad_post->post_type ) {
					$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );

					if ( ! in_array( get_the_ID(), (array) $ssag_ads_posts_ids ) ) {

						if ( 'before-post' === $ssag_ads_insertion ) {
							if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
								$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
							}
							echo wp_kses_post( $ads_html );
						}
					}
				}
			}
		}

	}

	/**
	 * Function for after post content.
	 */
	public function ssag_debug_wp_after_post() {

		global $post;
		$opad_post = $post;
		if ( 0 === $this->ssag_session_check( 'opt-position' ) ) {

			if ( $opad_post ) {
				echo wp_kses_post( $this->opad_debug_block( 'After Post' ) );
			}
		}
		$all_ads_posts = $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';

			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}

			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );
			$rest_url                      = get_post_meta( $ad_post_id, 'ssag_ads_url', true );

			$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}

			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );

			if ( 'yes' === $enable_post ) {
				if ( 'post' === $opad_post->post_type ) {
					$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );

					if ( ! in_array( get_the_ID(), (array) $ssag_ads_posts_ids ) ) {

						if ( 'after-post' === $ssag_ads_insertion ) {
							if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
								$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
							}
							echo wp_kses_post( $ads_html );
						}
					}
				}
			}
		}

	}

	/**
	 * Function fo checking debug session.
	 */
	public function ssag_debug_sessions() {
		if ( ( '' == session_id() ) || ( ! session_id() ) ) {
			session_start();
		}
		// All.
		if ( isset( $_GET['opt-ads'] ) ) {
			if ( '1' === $_GET['opt-ads'] ) {
				$_SESSION['opt-ads']      = true;
				$_SESSION['opt-label']    = true;
				$_SESSION['opt-position'] = true;
				$_SESSION['opt-html']     = true;
			}
			if ( '0' === $_GET['opt-ads'] ) {
				$_SESSION['opt-ads']      = false;
				$_SESSION['opt-label']    = false;
				$_SESSION['opt-position'] = false;
				$_SESSION['opt-html']     = false;
			}
		}

		// label.
		if ( isset( $_GET['opt-label'] ) ) {
			if ( '1' === $_GET['opt-label'] ) {
				$_SESSION['opt-label'] = true;
			}
			if ( '0' === $_GET['opt-label'] ) {
				$_SESSION['opt-label'] = false;
			}
		}

		// Position.
		if ( isset( $_GET['opt-position'] ) ) {
			if ( '1' === $_GET['opt-position'] ) {
				$_SESSION['opt-position'] = true;
			}
			if ( '0' === $_GET['opt-position'] ) {
				$_SESSION['opt-position'] = false;
			}
		}
		if ( isset( $_GET['opt-html'] ) ) {
			if ( '1' === $_GET['opt-html'] ) {
				$_SESSION['opt-html'] = true;
			}
			if ( '0' === $_GET['opt-html'] ) {
				$_SESSION['opt-html'] = false;
			}
		}

		if ( ( isset( $_SESSION['opt-label'] ) ) && ( isset( $_SESSION['opt-position'] ) ) && ( isset( $_SESSION['opt-html'] ) ) ) {
			if ( ( ! $_SESSION['opt-label'] ) && ( ! $_SESSION['opt-position'] ) && ( ! $_SESSION['opt-html'] ) ) {
				$_SESSION['opt-ads'] = false;
			}
		}

	}

	/**
	 * Function for session check.
	 *
	 * @param string $type for type of session to be checked.
	 */
	public function ssag_session_check( $type ) {
		if ( isset( $_SESSION[ $type ] ) ) {
			if ( $_SESSION[ $type ] ) {
				return 0;
			}
		}
		return 1;
	}

	/**
	 * Function for adding items to menu bar front end.
	 *
	 * @param object $wp_admin_bar for adding items to menu bar.
	 */
	public function wp_admin_menu( $wp_admin_bar ) {
		$args = array(
			'id'    => 'ssad_optimate_ad_debug',
			'title' => 'Optimate Ad ',
			'class' => 'ssag-debug-main',
			'href'  => '?opt-ads=' . $this->ssag_session_check( 'opt-ads' ),
		);
		$wp_admin_bar->add_node( $args );

		$args = array(
			'id'     => 'ssad_optimate_label',
			'title'  => 'Label Blocks',
			'href'   => '?opt-label=' . $this->ssag_session_check( 'opt-label' ),
			'parent' => 'ssad_optimate_ad_debug',
		);
		$wp_admin_bar->add_node( $args );

		$args = array(
			'id'     => 'ssad_optimate_position',
			'title'  => 'Show Position',
			'href'   => '?opt-position=' . $this->ssag_session_check( 'opt-position' ),
			'parent' => 'ssad_optimate_ad_debug',
		);
		$wp_admin_bar->add_node( $args );

		$args = array(
			'id'     => 'ssad_optimate_html',
			'title'  => 'Show HTML Tags',
			'href'   => '?opt-html=' . $this->ssag_session_check( 'opt-html' ),
			'parent' => 'ssad_optimate_ad_debug',
		);
		$wp_admin_bar->add_node( $args );

	}


	/**
	 * Function for adding scripts.
	 */
	public function add_scripts() {
		$type    = '';
		$post_id = get_the_ID();

		wp_enqueue_style( 'ssag-scripts_css', OPAD_CONSTANT_URL . 'assets/css/front-styles.css', false, '1.0.0' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'ssag-scripts_script', OPAD_CONSTANT_URL . 'assets/js/front-script.js', false, '1.0.0', $in_footer = false );
		wp_enqueue_style( 'select2', OPAD_CONSTANT_URL . 'assets/css/select2.css', true, '1.0.0' );
		wp_enqueue_script( 'select2', OPAD_CONSTANT_URL . 'assets/js/select2.js', true, '1.0.1', array( 'jquery' ) );

	}

	/**
	 * Function for before shop loop.
	 */
	public function ssag_filter_before_shop_loop() {
		$all_ads_posts = (array) $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center;  margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$rest_url        = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
			$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}

			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );

			$category           = get_queried_object();
			$enable_cat_page    = get_post_meta( $ad_post_id, 'ssag_ads_include_cat_pages', true );
			$all_ads_categories = (array) get_post_meta( $ad_post_id, 'ssag_ads_categories', true );
			if ( 'yes' === $enable_cat_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_categories ) ) {
						if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
						}
						echo wp_kses_post( $ads_html );
					}
				}
			}
			$enable_tag_page = get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			$all_ads_tag     = (array) get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			if ( 'yes' === $enable_tag_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_tag ) ) {
						if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
						}
						echo wp_kses_post( $ads_html );
					}
				}
			}
		}
	}

	/**
	 * Filter for after shop loop.
	 */
	public function ssag_filter_after_shop_loop() {

		$all_ads_posts = (array) $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center;  margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$rest_url                    = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
						$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}

			$category           = get_queried_object();
			$enable_cat_page    = get_post_meta( $ad_post_id, 'ssag_ads_include_cat_pages', true );
			$all_ads_categories = (array) get_post_meta( $ad_post_id, 'ssag_ads_categories', true );
			if ( 'yes' === $enable_cat_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_categories ) ) {
						if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
						}
						echo wp_kses_post( $ads_html );
					}
				}
			}
			$enable_tag_page = get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			$all_ads_tag     = (array) get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			if ( 'yes' === $enable_tag_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_tag ) ) {
						if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
						}
						echo wp_kses_post( $ads_html );
					}
				}
			}
		}
	}

	/**
	 * Function for excerpt.
	 *
	 * @param string $content for excerpt html.
	 */
	public function ssag_filter_excerpt( $content ) {

		$all_ads_posts = (array) $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$rest_url                    = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
						$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}
			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );
			$opad_post   = get_post( get_the_ID() );

			if ( 'before-excerpt' === $ssag_ads_insertion ) {
				if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
					$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
				}
				$content = $ads_html . $this->opad_debug_block( ' Before Excerpt ' ) . $content;
			}
		}
		return $content;
	}

	/**
	 * Function for before comment ad.
	 *
	 * @param string $commenter for commenter of comment.
	 * @param string $user_identity for identity of user.
	 */
	public function ssag_ad_before_comment( $commenter, $user_identity ) {
		$all_ads_posts  = (array) $this->ssag_all_posts_ads;
		$content_before = '';

		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$enable_post                 = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );
			$opad_post                   = get_post( get_the_ID() );
			$rest_url                    = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
						$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}
			if ( 'before-comments' === $ssag_ads_insertion ) {
				if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
					$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
				}
				echo wp_kses_post( $ads_html );
				echo wp_kses_post( $this->opad_debug_block( 'Before Comments' ) );
			}
		}

	}

	/**
	 * Function for comment optimate ads.
	 *
	 * @param array $arg for arguments.
	 */
	public function ssad_comment_optimate_ads( $arg ) {

		$all_ads_posts  = (array) $this->ssag_all_posts_ads;
		$content_before = isset( $arg['comment_notes_before'] ) ? $arg['comment_notes_before'] : '';
		$content_after  = isset( $arg['comment_notes_after'] ) ? $arg['comment_notes_after'] : '';
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$rest_url                    = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
						$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}
			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );
			$opad_post   = get_post( get_the_ID() );

			if ( 'before-comments' === $ssag_ads_insertion ) {
				if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
					$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
				}
				$content_before = $ads_html . $content_before;
			}
			if ( 'after-comments' === $ssag_ads_insertion ) {
				if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
					$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
				}
				$content_after = $content_after . $ads_html;
			}
		}

		$arg['comment_notes_after'] = $content_after . ( ( 0 === $this->ssag_session_check( 'opt-position' ) ) ? $this->opad_debug_block( 'After Comments' ) : '' );
		return $arg;
	}


	/**
	 * Filter content of comments.
	 *
	 * @param string $content for content of comments on product page.
	 */
	public function ssag_filter_comments( $content ) {

		$all_ads_posts = (array) $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$rest_url                    = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
						$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}
			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );
			$opad_post   = get_post( get_the_ID() );

			if ( 'before-comments' === $ssag_ads_insertion ) {
				if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
					$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
				}
				$content = $ads_html . $content;
			}
			if ( 'after-comments' === $ssag_ads_insertion ) {
				if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
					$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
				}
				$content = $content . $ads_html;
			}
		}
		return $content;
	}

	/**
	 * Shortcode html for Optimate ad.
	 *
	 * @param array  $args for arguments.
	 * @param string $content for content of page.
	 */
	public function ssag_ads_shortcode_fn_cb( $args, $content ) {
		$sad_post_id = isset( $args['id'] ) ? floatval( $args['id'] ) : 1;
		opad_single_optimate_ad_fn( $sad_post_id, 'short-code' );
	}


	/**
	 * Filter to change and ad ad.
	 *
	 * @param string $content for content of page.
	 */
	public function ssag_filter_content( $content ) {

		global $post;
		if ( 0 === $this->ssag_session_check( 'opt-position' ) ) {
			$content = $this->opad_debug_block( 'Before Content' ) . $content;

			// Before Paragraph.
			$all_para_debug       = (array) explode( '<p', $content );
			$all_para_debug_debug = count( $all_para_debug );
			$new_content          = $all_para_debug[0];
			for ( $debug_p_incre = 1; $debug_p_incre < ( $all_para_debug_debug ); $debug_p_incre++ ) {
				$new_content .= $this->opad_debug_block( 'Before Paragraph ' . $debug_p_incre ) . '<p' . $all_para_debug[ $debug_p_incre ];
			}
			$content = $new_content;

			// After Paragraph.
			$all_para_debug = (array) explode( '</p>', $content );
			$new_content    = $all_para_debug[0];
			for ( $debug_p_incre = 1; $debug_p_incre < ( $all_para_debug_debug ); $debug_p_incre++ ) {
				$new_content .= $all_para_debug[ $debug_p_incre ] . '</p>' . $this->opad_debug_block( 'After Paragraph ' . $debug_p_incre );
			}
			$content = $new_content;

			// Before Image.
			$content_paras = explode( '<img', $content );
			$new_content   = '';
			$para_count    = 0;
			foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
				if ( 0 === $para_count ) {
					$new_content .= $each_content_para;
					++$para_count;
					continue;
				}
				if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
					$new_content .= $this->opad_debug_block( 'Before Image ' . $each_content_key_para ) . '<img' . $each_content_para;
				} else {
					$new_content .= '<img' . $each_content_para;
				}
			}
			$content = $new_content;

			// After Image.
			$filter_content       = (array) explode( '<img', $content );
			$new_content          = $filter_content[0];
			$filter_count_content = count( $filter_content );
			for ( $increment = 1; $increment < $filter_count_content; $increment++ ) {
				$next_html = (array) explode( '>', $filter_content[ $increment ] );
				if ( in_array( $increment, (array) $ssag_ads_para_id, true ) ) {
					$new_content .= '<img' . $next_html[0] . '>' . $this->opad_debug_block( 'After Image ' . $each_content_key_para );
				} else {
					$new_content .= '<img' . $next_html[0] . '>';
				}
				$next_count_html = count( $next_html );
				for ( $incr_2 = 1; $incr_2 < $next_count_html - 1; $incr_2++ ) {
					$new_content .= $next_html[ $incr_2 ] . '>';
				}
				$new_content .= $next_html[ $next_count_html - 1 ];
			}
			$content = $new_content;

				$filter_content             = explode( '<p', $content );
				$total_p                    = (int) ( floatval( count( $filter_content ) ) / 2 ) - 1;
				$filter_content[ $total_p ] = '>' . $this->opad_debug_block( 'Mid Content ' ) . '</p> <p' . $filter_content[ $total_p ];
				$content                    = implode( '<p', $filter_content );
		}

		$all_ads_posts = $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$rest_url        = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
			$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}

			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );
			$opad_post   = get_post( get_the_ID() );

			$check_to_show_before_content = false;
			if ( is_front_page() ) {
				$enable_home_page = get_post_meta( $ad_post_id, 'ssag_ads_include_home_page', true );
				if ( 'yes' === $enable_home_page ) {
					$check_to_show_before_content = true;

				}
			} elseif ( is_search() ) {
				$enable_search_page = get_post_meta( $ad_post_id, 'ssag_ads_include_search_pages', true );
				if ( 'yes' === $enable_search_page ) {
					$check_to_show_before_content = true;
				}
			} elseif ( 'post' === $opad_post->post_type ) {
				if ( 'yes' === $enable_post ) {
					$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );
					if ( ! in_array( get_the_ID(), (array) $ssag_ads_posts_ids ) ) {
						$check_to_show_before_content = true;
					}
				}
			} elseif ( 'page' === $opad_post->post_type ) {
				$enable_pages = get_post_meta( $ad_post_id, 'ssag_ads_include_pages', true );
				if ( 'yes' === $enable_pages ) {
					$page_id = get_queried_object_id();
					if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
						if ( is_shop() ) {
							$page_id = get_option( 'woocommerce_shop_page_id' );
						}
					}
					$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );
					if ( ! in_array( $page_id, (array) $ssag_ads_posts_ids ) ) {
						$check_to_show_before_content = true;
					}
				}
			}
			$category           = get_queried_object();
			$enable_cat_page    = get_post_meta( $ad_post_id, 'ssag_ads_include_cat_pages', true );
			$all_ads_categories = (array) get_post_meta( $ad_post_id, 'ssag_ads_categories', true );
			if ( 'yes' === $enable_cat_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_categories ) ) {
						$check_to_show_before_content = true;
					}
				}
			}
			$enable_tag_page = get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			$all_ads_tag     = (array) get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			if ( 'yes' === $enable_tag_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_tag ) ) {
						$check_to_show_before_content = true;
					}
				}
			}
			if ( 'before-content' === $ssag_ads_insertion ) {

				if ( $check_to_show_before_content ) {
					if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
						$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
					}
					$content = $ads_html . $content;
				}
			}
			if ( 'yes' === $enable_post ) {
				if ( 'post' === $opad_post->post_type ) {
					$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );
					if ( ! in_array( get_the_ID(), (array) $ssag_ads_posts_ids ) ) {

						if ( 'before-post' === $ssag_ads_insertion ) {
							if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
								$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
							}
							$content = ( $ads_html ) . $content;
						}
						if ( 'before-para' === $ssag_ads_insertion ) {
							$content_paras = explode( '<p', $content );
							$new_content   = '';
							$para_count    = 0;
							$ads_html = '';
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( ( 0 === $para_count ) && ( 0 === strpos( '<p', $content ) ) ) {
									$new_content .= $each_content_para;
									++$para_count;
									continue;
								}
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $ads_html . ' <p' . $each_content_para;
								} else {
									$new_content .= '<p' . $each_content_para;
								}
							}
							$content = $new_content;
						}
                        if ( 'after-para' === $ssag_ads_insertion ) {
							$content_paras = explode( 'p>', $content );
							$new_content   = '';
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $each_content_para . 'p>' . $ads_html;
								} else {
                                    $new_content .= $each_content_para . ( ( $each_content_key_para < ( count($content_paras) -1 ) ) ? 'p>' : '' );
								}
							}
							$content = $new_content;
						}
						if ( 'before-img' === $ssag_ads_insertion ) {
							$content_paras = explode( '<img', $content );
							$new_content   = '';
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $ads_html . '<img' . $each_content_para;
								} else {
									$new_content .= '<img' . $each_content_para;
								}
							}
							$content = $new_content;
							$content = str_replace( '><img<' , '> <' , $new_content );
							
						}
						if ( 'after-img' === $ssag_ads_insertion ) {
							$content .= 'We Love Cricket';
							$filter_content       = (array) explode( '<img', $content );
							$new_content          = $filter_content[0];
							$filter_count_content = count( $filter_content );
							$content .= '<br>1';
							for ( $increment = 1; $increment < $filter_count_content; $increment++ ) {
								$content .= '<br>2';
								
								$next_html = (array) explode( '>', $filter_content[ $increment ] );
								if ( in_array( $increment, (array) $ssag_ads_para_id, true ) ) {
							$content .= '<br>3';
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$content .= '<br>4';
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= '<img' . $next_html[0] . '>' . $ads_html;
								} else {
									$new_content .= '<img' . $next_html[0] . '>';
								}
								$next_count_html = count( $next_html );
								for ( $incr_2 = 1; $incr_2 < $next_count_html - 1; $incr_2++ ) {
									$new_content .= $next_html[ $incr_2 ] . '>';
								}
								$new_content .= $next_html[ $next_count_html - 1 ];

							}
							$content = $new_content;
						}
						if ( 'mid-content' === $ssag_ads_insertion ) {
							$filter_content = explode( '<p', $content );
							$total_p        = (int) ( floatval( count( $filter_content ) ) / 2 ) - 1;
							if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
								$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
							}
							$filter_content[ $total_p ] = '>' . $ads_html . '</p> <p' . $filter_content[ $total_p ];
							$content                    = implode( '<p', $filter_content );
						}
					}
				}
			}

			$enable_pages = get_post_meta( $ad_post_id, 'ssag_ads_include_pages', true );
			if ( 'yes' === $enable_pages ) {
				if ( 'page' === $opad_post->post_type ) {
					$page_id = get_queried_object_id();
					if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
						if ( is_shop() ) {
							$page_id = get_option( 'woocommerce_shop_page_id' );
						}
					}
					$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );
					if ( ! in_array( $page_id, (array) $ssag_ads_posts_ids ) ) {
						if ( 'before-post' === $ssag_ads_insertion ) {
							if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
								$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
							}
							$content = ( $ads_html ) . $content;
						}
						if ( 'before-para' === $ssag_ads_insertion ) {
							$content_paras = explode( '<p', $content );
							$new_content   = '';
							$para_count    = 0;
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( ( 0 === $para_count ) && ( 0 === strpos( '<p', $content ) ) ) {
									$new_content .= $each_content_para;
									++$para_count;
									continue;
								}
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $ads_html . '<p' . $each_content_para;
								} else {
									$new_content .= '<p' . $each_content_para;
								}
							}
							$content = $new_content;
						}
						if ( 'after-para' === $ssag_ads_insertion ) {
							$content_paras = explode( 'p>', $content );
							$new_content   = '';
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $each_content_para . 'p>' . $ads_html;
								} else {
                                    $new_content .= $each_content_para . ( ( $each_content_key_para < ( count($content_paras) -1 ) ) ? 'p>' : '' );
								}
							}
							$content = $new_content;
						}
						if ( 'before-img' === $ssag_ads_insertion ) {
							$content_paras = explode( '<img', $content );
							$new_content   = '';
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $ads_html . '<img' . $each_content_para;
								} else {
									$new_content .= '<img' . $each_content_para;
								}
							}
							$content = $new_content;
						}
						if ( 'after-img' === $ssag_ads_insertion ) {
							$filter_content       = (array) explode( '<img', $content );
							$new_content          = $filter_content[0];
							$filter_count_content = count( $filter_content );
							for ( $increment = 1; $increment < $filter_count_content; $increment++ ) {
								$next_html = (array) explode( '>', $filter_content[ $increment ] );
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= '<img' . $next_html[0] . '>' . $ads_html;
								} else {
									$new_content .= '<img' . $next_html[0] . '>';
								}
								$next_count_html = count( $next_html );
								for ( $incr_2 = 1; $incr_2 < $next_count_html - 1; $incr_2++ ) {
									$new_content .= $next_html[ $incr_2 ] . '>';
								}
								$new_content .= $next_html[ $next_count_html - 1 ];

							}
							$content = $new_content;
						}
						if ( 'mid-content' === $ssag_ads_insertion ) {
							$filter_content = explode( '<p', $content );
							$total_p        = (int) ( floatval( count( $filter_content ) ) / 2 ) - 1;
							if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
								$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
							}
							$filter_content[ $total_p ] = '>' . $ads_html . '</p> <p' . $filter_content[ $total_p ];
							$content                    = implode( '<p', $filter_content );
						}
					}
				}
			}
			$enable_home_page = get_post_meta( $ad_post_id, 'ssag_ads_include_home_page', true );
			if ( 'yes' === $enable_home_page ) {
				if ( is_front_page() ) {
					$ssag_ads_posts_ids = (array) get_post_meta( $ad_post_id, 'ssag_ads_posts_ids', true );
					if ( ! in_array( get_the_ID(), (array) $ssag_ads_posts_ids ) ) {

						if ( 'before-para' === $ssag_ads_insertion ) {
							$content_paras = explode( '<p', $content );
							$new_content   = '';
							$para_count    = 0;
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( ( 0 === $para_count ) && ( 0 === strpos( '<p', $content ) ) ) {
									$new_content .= $each_content_para;
									++$para_count;
									continue;
								}
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $ads_html . '<p' . $each_content_para;
								} else {
									$new_content .= '<p' . $each_content_para;
								}
							}
							$content = $new_content;
						}
						if ( 'after-para' === $ssag_ads_insertion ) {
							$content_paras = explode( 'p>', $content );
							$new_content   = '';
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $each_content_para . 'p>' . $ads_html;
								} else {
                                    $new_content .= $each_content_para . ( ( $each_content_key_para < ( count($content_paras) -1 ) ) ? 'p>' : '' );
								}
							}
							$content = $new_content;
						}
						if ( 'before-img' === $ssag_ads_insertion ) {
							$content_paras = explode( '<img', $content );
							$new_content   = '';
							foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= $ads_html . '<img' . $each_content_para;
								} else {
									$new_content .= '<img' . $each_content_para;
								}
							}
							$content = $new_content;
						}
						if ( 'after-img' === $ssag_ads_insertion ) {
							$filter_content       = (array) explode( '<img', $content );
							$new_content          = $filter_content[0];
							$filter_count_content = count( $filter_content );
							for ( $increment = 1; $increment < $filter_count_content; $increment++ ) {
								$next_html = (array) explode( '>', $filter_content[ $increment ] );
								if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
									if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
										$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
									}
									$new_content .= '<img' . $next_html[0] . '>' . $ads_html;
								} else {
									$new_content .= '<img' . $next_html[0] . '>';
								}
								$next_count_html = count( $next_html );
								for ( $incr_2 = 1; $incr_2 < $next_count_html - 1; $incr_2++ ) {
									$new_content .= $next_html[ $incr_2 ] . '>';
								}
								$new_content .= $next_html[ $next_count_html - 1 ];

							}
							$content = $new_content;
						}
						if ( 'mid-content' === $ssag_ads_insertion ) {
							$filter_content = explode( '<p', $content );
							$total_p        = (int) ( floatval( count( $filter_content ) ) / 2 ) - 1;
							if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
								$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
							}
							$filter_content[ $total_p ] = '>' . $ads_html . '</p> <p' . $filter_content[ $total_p ];
							$content                    = implode( '<p', $filter_content );
						}
					}
				}
			}
			$enable_search_page = get_post_meta( $ad_post_id, 'ssag_ads_include_search_pages', true );
			if ( 'yes' === $enable_search_page ) {
				if ( is_search() ) {

					if ( 'before-para' === $ssag_ads_insertion ) {
						$content_paras = explode( '<p', $content );
						$new_content   = '';
						$para_count    = 0;
						foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
							if ( ( 0 === $para_count ) && ( 0 === strpos( '<p', $content ) ) ) {
								$new_content .= $each_content_para;
								++$para_count;
								continue;
							}
							if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
								if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
									$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
								}
								$new_content .= $ads_html . '<p' . $each_content_para;
							} else {
								$new_content .= '<p' . $each_content_para;
							}
						}
						$content = $new_content;
					}
					if ( 'after-para' === $ssag_ads_insertion ) {
						$content_paras = explode( 'p>', $content );
						$new_content   = '';
						foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
							if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
								if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
									$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
								}
								$new_content .= $each_content_para . 'p>' . $ads_html;
							} else {
                                $new_content .= $each_content_para . ( ( $each_content_key_para < ( count($content_paras) -1 ) ) ? 'p>' : '' );
							}
						}
						$content = $new_content;
					}
					if ( 'before-img' === $ssag_ads_insertion ) {
						$content_paras = explode( '<img', $content );
						$new_content   = '';
						foreach ( $content_paras as $each_content_key_para => $each_content_para ) {
							if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
								if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
									$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
								}
								$new_content .= $ads_html . '<img' . $each_content_para;
							} else {
								$new_content .= '<img' . $each_content_para;
							}
						}
						$content = $new_content;
					}
					if ( 'after-img' === $ssag_ads_insertion ) {
						$filter_content       = (array) explode( '<img', $content );
						$new_content          = $filter_content[0];
						$filter_count_content = count( $filter_content );
						for ( $increment = 1; $increment < $filter_count_content; $increment++ ) {
							$next_html = (array) explode( '>', $filter_content[ $increment ] );
							if ( in_array( $each_content_key_para + 1, (array) $ssag_ads_para_id ) ) {
								if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
									$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
								}
								$new_content .= '<img' . $next_html[0] . '>' . $ads_html;
							} else {
								$new_content .= '<img' . $next_html[0] . '>';
							}
							$next_count_html = count( $next_html );
							for ( $incr_2 = 1; $incr_2 < $next_count_html - 1; $incr_2++ ) {
								$new_content .= $next_html[ $incr_2 ] . '>';
							}
							$new_content .= $next_html[ $next_count_html - 1 ];

						}
						$content = $new_content;
					}
					if ( 'mid-content' === $ssag_ads_insertion ) {
						$filter_content = explode( '<p', $content );
						$total_p        = (int) ( floatval( count( $filter_content ) ) / 2 ) - 1;
						if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
						}
						$filter_content[ $total_p ] = '>' . $ads_html . '</p> <p' . $filter_content[ $total_p ];
						$content                    = implode( '<p', $filter_content );
					}
				}
			}

			$category           = get_queried_object();
			$enable_cat_page    = get_post_meta( $ad_post_id, 'ssag_ads_include_cat_pages', true );
			$all_ads_categories = (array) get_post_meta( $ad_post_id, 'ssag_ads_categories', true );
			if ( 'yes' === $enable_cat_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_categories ) ) {
						if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
						}
						$content = $ads_html . $content;
					}
				}
			}
			$enable_tag_page = get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			$all_ads_tag     = (array) get_post_meta( $ad_post_id, 'ssag_ads_include_arch_pages', true );
			if ( 'yes' === $enable_tag_page ) {
				if ( isset( $category->term_id ) ) {
					if ( ! in_array( $category->term_id, (array) $all_ads_tag ) ) {
						if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
							$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
						}
						$content = $ads_html . $content;
					}
				}
			}
			if ( 'after-content' === $ssag_ads_insertion ) {
				if ( $check_to_show_before_content ) {
					if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
						$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
					}
					$content = $content . $ads_html;
				}
			}
		}

		if ( 0 === $this->ssag_session_check( 'opt-html' ) ) {
			$content = str_replace( '</span>', '<ssagspan class="ssag-debug-span">' . esc_attr( '</span>' ) . '</ssagspan> </span>', $content );
			$content = str_replace( '<span', '<ssagspan class="ssag-debug-span">' . esc_attr( '<span>' ) . '</ssagspan> <span', $content );

			$content = str_replace( '<p', '<ssagspan class="ssag-debug-p">' . esc_attr( '<p>' ) . '</ssagspan> <p', $content );
			$content = str_replace( '</p>', '</p> <ssagspan class="ssag-debug-p">' . esc_attr( '</p>' ) . '</ssagspan>', $content );

			$content = str_replace( '<div', '<ssagspan class="ssag-debug-div">' . esc_attr( '<div>' ) . '</ssagspan> <div', $content );
			$content = str_replace( '</div>', '</div> <ssagspan class="ssag-debug-div">' . esc_attr( '</div>' ) . '</ssagspan>', $content );

			$filter_content       = (array) explode( '<img', $content );
			$new_content          = $filter_content[0];
			$filter_count_content = count( $filter_content );
			for ( $increment = 1; $increment < $filter_count_content; $increment++ ) {
				$next_html        = (array) explode( '>', $filter_content[ $increment ] );
					$new_content .= '<ssagspan class="ssag-debug-img">' . esc_attr( '<img' . $next_html[0] . '>' ) . '</ssagspan> <img' . $next_html[0] . '>';
				$next_count_html  = count( $next_html );
				for ( $incr_2 = 1; $incr_2 < $next_count_html - 1; $incr_2++ ) {
					$new_content .= $next_html[ $incr_2 ] . '>';
				}
				$new_content .= $next_html[ $next_count_html - 1 ];

			}
			$content = $new_content;

		}

		if ( 0 === $this->ssag_session_check( 'opt-position' ) ) {
			$content = $content . $this->opad_debug_block( 'After Content' );
		}

		$content = str_replace( '>p>' , '>' , $content );
		return $content;
	}

	/**
	 * Function for footer code.
	 */
	public function opad_filter_footer() {

		$all_ads_posts = (array) $this->ssag_all_posts_ads;
		foreach ( $all_ads_posts as $key => $value ) {
			$ad_post_id         = $value->ID;
			$ssag_ads_insertion = get_post_meta( $ad_post_id, 'ssag_ads_insertion', true );
			$ssag_ads_para_id   = (array) explode( ',', str_replace( ' ', '', get_post_meta( $ad_post_id, 'ssag_ads_para_id', true ) ) );
			$enable_phone       = get_post_meta( $ad_post_id, 'ssag_ads_use_phone', true );
			$enable_tablet      = get_post_meta( $ad_post_id, 'ssag_ads_use_tablet', true );
			$enable_desktop     = get_post_meta( $ad_post_id, 'ssag_ads_use_desktop', true );
			$ads_align          = get_post_meta( $ad_post_id, 'ssag_ads_align', true );
			$align_class        = '';
			if ( 'left' === $ads_align ) {
				$align_class = ' text-align:left; ';
			} elseif ( 'center' === $ads_align ) {
				$align_class = ' text-align:center; margin: auto auto; ';
			} elseif ( 'right' === $ads_align ) {
				$align_class = ' text-align:right; ';
			} elseif ( 'float-left' === $ads_align ) {
				$align_class = ' float:left; ';
			} elseif ( 'float-right' === $ads_align ) {
				$align_class = ' float:right; ';
			} elseif ( 'no-wrap' === $ads_align ) {
				$align_class = '  white-space: nowrap; ';
			} elseif ( 'custom-css' === $ads_align ) {
				$align_class = '   ' . get_post_meta( $ad_post_id, 'ssag_ad_custom_css', true ) . ' ';
			}
			$ads_html = '<div style="' . $align_class . '" class="ssag-opads-main ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';
			if ( 'disable' === $ssag_ads_insertion ) {
				continue;
			}
			$ssag_ads_show_on_php_function = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );

			$rest_url                    = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
						$current_web_url = get_permalink( get_queried_object_id() );
			if ( in_array( 'woocommerce/woocommerce.php', (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				if ( is_shop() ) {
					$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
				}
			}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

			if ( in_array( $current_url, (array) $rest_array ) ) {
				continue;
			}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
			foreach ( $rest_array_param as $sing_param ) {
				if ( in_array( $sing_param, (array) $_GET ) ) {
					continue 2;
				}
			}
			$enable_post = get_post_meta( $ad_post_id, 'ssag_ads_include_posts', true );

			if ( 'footer' === $ssag_ads_insertion ) {
				if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
					$ads_html = $this->ssag_opads_html( $align_class, $enable_phone, $enable_tablet, $enable_desktop, $ad_post_id );
				}
				echo wp_kses_post( $ads_html );
			}
		}
		$opad_post = get_post( get_the_ID() );

		// echo wp_kses_post( stripslashes( html_entity_decode( get_option( 'ssag_script_for_footer' ) ) ) );.
		if ( 0 === $this->ssag_session_check( 'opt-position' ) ) {
			echo wp_kses_post( $this->opad_debug_block( 'Footer' ) );
		}
		if ( ( 0 === $this->ssag_session_check( 'opt-label' ) ) || ( 0 === $this->ssag_session_check( 'opt-position' ) ) || ( 0 === $this->ssag_session_check( 'opt-html' ) ) ) {
			?>
			<div class="ssad-debug-header">
				<span>
					<?php
						global $post;
					if ( is_front_page() ) {
						echo wp_kses_post( 'Home Page' );
					} elseif ( is_archive() ) {
						echo wp_kses_post( 'Archive Page' );
					} elseif ( $opad_post ) {
						if ( 'page' === $opad_post->post_type ) {
							echo wp_kses_post( 'Static Page' );
						} else {
							echo wp_kses_post( $opad_post->post_type . ' Page' );
						}
					}
					?>
				</span>
			</div>
			<?php
		}

	}


	/**
	 * Function for debug blog.
	 *
	 * @param string $text for text for debug block.
	 */
	public function opad_debug_block( $text ) {
		ob_start();
		?>
				<div class="ssad-debug-header ssag-debug-blue">
					<span>
						<?php
							echo wp_kses_post( $text );
						?>
					</span>
				</div>
			<?php
			$html = ob_get_clean();
			return $html;
	}

	/**
	 * Function for Header code.
	 */
	public function opad_filter_head() {
		// echo wp_kses_post( stripslashes( html_entity_decode( get_option( 'ssag_styles_for_header' ) ) ) );.

		if ( ( 0 === $this->ssag_session_check( 'opt-label' ) ) || ( 0 === $this->ssag_session_check( 'opt-position' ) ) || ( 0 === $this->ssag_session_check( 'opt-html' ) ) ) {
			?>
			<div class="ssad-debug-header">
				<span>
					<?php
						global $post;
						$opad_post = $post;
					if ( is_front_page() ) {
						echo wp_kses_post( 'Home Page' );
					} elseif ( is_archive() ) {
						echo wp_kses_post( 'Archive Page' );
					} elseif ( $opad_post ) {
						if ( 'page' === $opad_post->post_type ) {
							echo wp_kses_post( 'Static Page' );
						} else {
							echo wp_kses_post( $opad_post->post_type . ' Page' );
						}
					}
					?>
				</span>
			</div>
			<?php
		}
		?>
		<style>
			/* ssag_session_check('opt-position') */
			<?php
			if ( ( 0 === $this->ssag_session_check( 'opt-ads' ) ) || ( 0 === $this->ssag_session_check( 'opt-label' ) ) || ( 0 === $this->ssag_session_check( 'opt-position' ) ) || ( 0 === $this->ssag_session_check( 'opt-html' ) ) ) {
				?>
						/* #wp-admin-bar-ssad_optimate_ad_debug a, */
						#wp-admin-bar-ssad_optimate_ad_debug a:before{
							color: #00f200 !important;
						}
				<?php
			}

			if ( 0 === $this->ssag_session_check( 'opt-label' ) ) {
				?>
						/* #wp-admin-bar-ssad_optimate_label a, */
							#wp-admin-bar-ssad_optimate_label a:before{
							color: #00f200 !important;
						}
				<?php
			} else {
				?>
						/* #wp-admin-bar-ssad_optimate_label a, */
							#wp-admin-bar-ssad_optimate_label a:before{
							color: #fff !important;
						}
				<?php
			}

			if ( 0 === $this->ssag_session_check( 'opt-position' ) ) {
				?>
						/* #wp-admin-bar-ssad_optimate_position a, */
						#wp-admin-bar-ssad_optimate_position a:before{
							color: #00f200 !important;
						}
				<?php
			} else {
				?>
						/* #wp-admin-bar-ssad_optimate_position a, */
							#wp-admin-bar-ssad_optimate_position a:before{
							color: #fff !important;
						}
				<?php
			}

			if ( 0 === $this->ssag_session_check( 'opt-html' ) ) {
				?>
						/* #wp-admin-bar-ssad_optimate_html a, */
						#wp-admin-bar-ssad_optimate_html a:before{
							color: #00f200 !important;
						}
				<?php
			} else {
				?>
						/* #wp-admin-bar-ssad_optimate_html a, */
							#wp-admin-bar-ssad_optimate_html a:before{
							color: #fff !important;
						}
				<?php
			}

			?>
		</style>
		<?php
	}

}
$ssag_front_class = new Opad_Front_Class();

if ( ! function_exists( 'optimate_ad' ) ) {
	/**
	 * Get Ad by ID
	 *
	 * @param int $sad_post_id for ad post id.
	 */
	function optimate_ad( $sad_post_id ) {
		opad_single_optimate_ad_fn( $sad_post_id, 'php-fn' );
	}
}

if ( ! function_exists( 'opad_single_optimate_ad_fn' ) ) {
	/**
	 * Get Ads information
	 *
	 * @param int    $sad_post_id for ad post id.
	 * @param string $type for ad type.
	 */
	function opad_single_optimate_ad_fn( $sad_post_id, $type ) {
		$get_data  = array(
			'post_type'      => 'ssag_adds_post_type',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'ssad_ads_post_key',
					'value'   => $sad_post_id,
					'compare' => '==',
				),
			),
		);
		$opad_post = current( get_posts( $get_data ) );
		if ( ! $opad_post ) {
			return '';
		}
		if ( 'ssag_adds_post_type' !== $opad_post->post_type ) {
			return '';
		}
		$ad_post_id = $opad_post->ID;
		ssag_single_optimate_html_ad_fn( $ad_post_id, $type );
	}
}

