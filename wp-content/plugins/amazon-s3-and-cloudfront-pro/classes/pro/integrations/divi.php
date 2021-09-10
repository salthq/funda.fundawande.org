<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Integrations;

class Divi extends Integration {

	/**
	 * Is installed?
	 *
	 * @return bool
	 */
	public function is_installed() {
		$theme_info = wp_get_theme();

		if ( ! empty( $theme_info ) && is_a( $theme_info, 'WP_Theme' ) && ( $theme_info->get( 'Name' ) ) === 'Divi' ) {
			return true;
		}

		if ( is_child_theme() ) {
			$parent_info = $theme_info->parent();

			if ( ! empty( $parent_info ) && is_a( $parent_info, 'WP_Theme' ) && esc_html( $parent_info->get( 'Name' ) ) === 'Divi' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Init integration.
	 */
	public function init() {
		add_filter( 'et_fb_load_raw_post_content', function ( $content ) {
			$content = apply_filters( 'as3cf_filter_post_local_to_s3', $content ); // Backwards compatibility

			return apply_filters( 'as3cf_filter_post_local_to_provider', $content );
		} );
	}
}
