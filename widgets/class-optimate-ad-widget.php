<?php
/**
 * Extent WPWidget
 *
 * @package for Widget settings
 */

 /**
  * Class for Extending Widget
  */
class Optimate_Ad_Widget extends WP_Widget {

	/**
	 * Constructor for Widget Classs
	 */
	public function __construct() {
		$this->all_posts_ads = $this->all_posts_ads_fn();
		parent::__construct(
			'ssag_optimate_ad_widget',
			__( 'Optimate Ad Widget', 'optimate-ads' )
		);
	}

	/**
	 * For getting all Ad posts.
	 */
	public function all_posts_ads_fn() {
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
	 * Implementing Widget
	 *
	 * @param array  $args for arguments.
	 * @param string $instance for instance text.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $args['after_title'] );
		}
		if ( get_post( floatval( $title ) ) ) {
			ssag_single_optimate_html_ad_fn( $title, 'widget' );
		}
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Form for Widget
	 *
	 * @param string $instance for instance text.
	 */
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '';
		}
		// Widget admin form.
		?>
<label for="
		<?php
		echo esc_attr( $this->get_field_id( 'title' ) );
		?>
">
		<?php
		echo esc_attr( 'Optimate Ad:' );
		?>
	<select  class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" >
		<?php
		foreach ( $this->all_posts_ads as $key => $value ) {
			$ad_title      = get_post_meta( $value->ID, 'ssag_ads_title', true );
			$ad_key        = get_post_meta( $value->ID, 'ssad_ads_post_key', true );
			$widget_enable = get_post_meta( $value->ID, 'ssag_ads_show_on_widget', true );
			$ad_title      = ( '' !== str_replace( ' ', '', $ad_title ) ? $ad_title : ' { No Name for Ad } ' );
			?>
			<option value="<?php echo esc_attr( $value->ID ); ?>" <?php selected( $title, $value->ID ); ?> > <?php echo wp_kses_post( $ad_title ); ?> </option>
			<?php
		}
		?>
	</select>

		<?php
	}

	/**
	 * Update Widget
	 *
	 * @param string $new_instance for new instance.
	 * @param string $old_instance for old instance.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}

/**
 * Register Widget
 */
function ssag_optimate_ad_load_widget() {
	register_widget( 'Optimate_Ad_widget' );
}
add_action( 'widgets_init', 'ssag_optimate_ad_load_widget' );


if ( ! function_exists( 'ssag_single_optimate_html_ad_fn' ) ) {
	/**
	 * Single ad HTML
	 *
	 * @param int    $ad_post_id for id.
	 * @param string $type for type.
	 */
	function ssag_single_optimate_html_ad_fn( $ad_post_id, $type ) {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( isset( $_POST['nonce'] ) ? $_POST['nonce'] : '' ) ), 'ajax-nonce' ) ) {
			echo ( 'Destroy!' );
		}
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
		$ads_html = '<div style="' . $align_class . '" class=" ' . ( 'yes' !== $enable_phone ? ' ssag-hide-for-phone ' : ' ' ) . ( 'yes' !== $enable_tablet ? ' ssag-hide-for-tablet ' : ' ' ) . ( 'yes' !== $enable_desktop ? ' ssag-hide-for-pcs ' : ' ' ) . ' " >' . get_post_meta( $ad_post_id, 'ssag_ads_content_html', true ) . '</div>';

		if ( 'disable' === $ssag_ads_insertion ) {
			return '';
		}
		$rest_url                        = get_post_meta( $ad_post_id, 'ssag_ads_url', true );
						$current_web_url = get_permalink( get_queried_object_id() );
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if ( is_shop() ) {
				$current_web_url = get_permalink( get_option( 'woocommerce_shop_page_id' ) );
			}
		}

			$rest_array  = (array) explode( ',', str_replace( '/', '', $rest_url ) );
			$current_url = str_replace( '/', '', $current_web_url );

		if ( in_array( $current_url, $rest_array ) ) {
			return '';
		}
			$rest_param       = get_post_meta( $ad_post_id, 'ssag_ads_url_parameter', true );
			$rest_array_param = (array) explode( ',', $rest_param );
		foreach ( $rest_array_param as $sing_param ) {
			if ( in_array( $sing_param, (array) $_GET ) ) {
				return '';
			}
		}

		if ( 'short-code' === $type ) {
			$ssag_ads_show_on_shortcode = get_post_meta( $ad_post_id, 'ssag_ads_show_on_shortcode', true );
			if ( 'yes' !== $ssag_ads_show_on_shortcode ) {
				return '';
			}
		}

		if ( 'php-fn' === $type ) {
			$php_fn = get_post_meta( $ad_post_id, 'ssag_ads_show_on_php_function', true );
			if ( 'yes' === $php_fn ) {
				echo wp_kses_post( $ads_html );
			}
		}

		if ( 'widget' === $type ) {
			$widget = get_post_meta( $ad_post_id, 'ssag_ads_show_on_widget', true );

			if ( 'yes' === $widget ) {
				echo wp_kses_post( $ads_html );
			}
		}
	}
}
