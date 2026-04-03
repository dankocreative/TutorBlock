<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin bootstrap.
 */
class TutorBlock_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var TutorBlock_Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return TutorBlock_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_block_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		TutorBlock_Video::instance();
		TutorBlock_Admin::instance();
	}

	/**
	 * Load plugin translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'tutorblock', false, dirname( plugin_basename( TUTORBLOCK_FILE ) ) . '/languages' );
	}

	/**
	 * Register assets used by blocks and shortcodes.
	 *
	 * @return void
	 */
	public function register_block_assets() {
		wp_register_style(
			'tutorblock-frontend',
			TUTORBLOCK_URL . 'assets/css/frontend.css',
			array(),
			TUTORBLOCK_VERSION
		);

		wp_register_script(
			'tutorblock-frontend',
			TUTORBLOCK_URL . 'assets/js/frontend.js',
			array(),
			TUTORBLOCK_VERSION,
			true
		);
	}

	/**
	 * Enqueue global assets when needed.
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets() {
		if ( is_admin() ) {
			return;
		}

		if ( is_singular( 'courses' ) || has_shortcode( get_post_field( 'post_content', get_the_ID() ), 'tutorblock_video_lesson' ) ) {
			wp_enqueue_style( 'tutorblock-frontend' );
			wp_enqueue_script( 'tutorblock-frontend' );
			wp_localize_script(
				'tutorblock-frontend',
				'TutorBlockData',
				array(
					'restUrl' => esc_url_raw( rest_url( 'tutorblock/v1' ) ),
					'nonce'   => wp_create_nonce( 'wp_rest' ),
				)
			);
		}
	}
}
