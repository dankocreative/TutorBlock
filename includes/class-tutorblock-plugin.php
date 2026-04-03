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
		add_action( 'init', array( $this, 'register_gutenberg_blocks' ) );
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

		wp_register_script(
			'tutorblock-editor-blocks',
			TUTORBLOCK_URL . 'assets/js/blocks.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n' ),
			TUTORBLOCK_VERSION,
			true
		);

		wp_register_style(
			'tutorblock-editor-style',
			TUTORBLOCK_URL . 'assets/css/editor.css',
			array(),
			TUTORBLOCK_VERSION
		);
	}

	/**
	 * Register Gutenberg blocks so they appear in inserter.
	 *
	 * @return void
	 */
	public function register_gutenberg_blocks() {
		register_block_type(
			'tutorblock/video-lesson',
			array(
				'editor_script'   => 'tutorblock-editor-blocks',
				'editor_style'    => 'tutorblock-editor-style',
				'style'           => 'tutorblock-frontend',
				'render_callback' => array( $this, 'render_video_lesson_block' ),
				'attributes'      => array(
					'src'       => array( 'type' => 'string', 'default' => '' ),
					'lesson_id' => array( 'type' => 'number', 'default' => 0 ),
					'title'     => array( 'type' => 'string', 'default' => __( 'Video Lesson', 'tutorblock' ) ),
				),
			)
		);

		register_block_type(
			'tutorblock/course-progress',
			array(
				'editor_script'   => 'tutorblock-editor-blocks',
				'editor_style'    => 'tutorblock-editor-style',
				'style'           => 'tutorblock-frontend',
				'render_callback' => array( $this, 'render_course_progress_block' ),
			)
		);
	}

	/**
	 * Render callback for video lesson block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_video_lesson_block( $attributes ) {
		return TutorBlock_Video::instance()->render_video_lesson( $attributes );
	}

	/**
	 * Render callback for progress block.
	 *
	 * @return string
	 */
	public function render_course_progress_block() {
		return TutorBlock_Video::instance()->render_course_progress();
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

		$post_id      = get_queried_object_id();
		$post_content = $post_id ? get_post_field( 'post_content', $post_id ) : '';

		$needs_assets = is_singular( 'courses' )
			|| has_shortcode( $post_content, 'tutorblock_video_lesson' )
			|| has_block( 'tutorblock/video-lesson', $post_content )
			|| has_block( 'tutorblock/course-progress', $post_content );

		if ( $needs_assets ) {
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
