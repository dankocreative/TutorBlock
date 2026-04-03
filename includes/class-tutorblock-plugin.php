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
		add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		TutorBlock_Video::instance();
		TutorBlock_Admin::instance();
	}

	/**
	 * Add dedicated TutorBlock category in block inserter.
	 *
	 * @param array $categories Existing categories.
	 * @return array
	 */
	public function register_block_category( $categories ) {
		$categories[] = array(
			'slug'  => 'tutorblock',
			'title' => __( 'TutorBlock LMS', 'tutorblock' ),
			'icon'  => 'welcome-learn-more',
		);

		return $categories;
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
					'src'                  => array( 'type' => 'string', 'default' => '' ),
					'poster'               => array( 'type' => 'string', 'default' => '' ),
					'lesson_id'            => array( 'type' => 'number', 'default' => 0 ),
					'title'                => array( 'type' => 'string', 'default' => __( 'Video Lesson', 'tutorblock' ) ),
					'accent_color'         => array( 'type' => 'string', 'default' => '#1d4ed8' ),
					'padding'              => array( 'type' => 'number', 'default' => 16 ),
					'border_radius'        => array( 'type' => 'number', 'default' => 8 ),
					'autoplay'             => array( 'type' => 'boolean', 'default' => false ),
					'muted'                => array( 'type' => 'boolean', 'default' => false ),
					'show_download_button' => array( 'type' => 'boolean', 'default' => false ),
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
				'attributes'      => array(
					'label'        => array( 'type' => 'string', 'default' => __( 'Average video completion:', 'tutorblock' ) ),
					'accent_color' => array( 'type' => 'string', 'default' => '#1d4ed8' ),
				),
			)
		);

		register_block_type(
			'tutorblock/course-grid',
			array(
				'editor_script'   => 'tutorblock-editor-blocks',
				'editor_style'    => 'tutorblock-editor-style',
				'style'           => 'tutorblock-frontend',
				'render_callback' => array( $this, 'render_course_grid_block' ),
				'attributes'      => array(
					'count'    => array( 'type' => 'number', 'default' => 6 ),
					'columns'  => array( 'type' => 'number', 'default' => 3 ),
					'cta_text' => array( 'type' => 'string', 'default' => __( 'View Course', 'tutorblock' ) ),
				),
			)
		);

		register_block_type(
			'tutorblock/course-masonry',
			array(
				'editor_script'   => 'tutorblock-editor-blocks',
				'editor_style'    => 'tutorblock-editor-style',
				'style'           => 'tutorblock-frontend',
				'render_callback' => array( $this, 'render_course_masonry_block' ),
				'attributes'      => array(
					'count'    => array( 'type' => 'number', 'default' => 8 ),
					'columns'  => array( 'type' => 'number', 'default' => 3 ),
					'cta_text' => array( 'type' => 'string', 'default' => __( 'View Course', 'tutorblock' ) ),
				),
			)
		);

		register_block_type(
			'tutorblock/youtube-shorts',
			array(
				'editor_script'   => 'tutorblock-editor-blocks',
				'editor_style'    => 'tutorblock-editor-style',
				'style'           => 'tutorblock-frontend',
				'render_callback' => array( $this, 'render_youtube_shorts_block' ),
				'attributes'      => array(
					'ids'   => array( 'type' => 'string', 'default' => '' ),
					'title' => array( 'type' => 'string', 'default' => __( 'Lesson Shorts', 'tutorblock' ) ),
				),
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
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_course_progress_block( $attributes ) {
		return TutorBlock_Video::instance()->render_course_progress( $attributes );
	}


	/**
	 * Render callback for course grid block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_course_grid_block( $attributes ) {
		return TutorBlock_Video::instance()->render_course_grid( $attributes );
	}

	/**
	 * Render callback for course masonry block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_course_masonry_block( $attributes ) {
		return TutorBlock_Video::instance()->render_course_masonry( $attributes );
	}

	/**
	 * Render callback for YouTube shorts block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function render_youtube_shorts_block( $attributes ) {
		return TutorBlock_Video::instance()->render_youtube_shorts( $attributes );
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
			|| has_block( 'tutorblock/course-progress', $post_content )
			|| has_block( 'tutorblock/course-grid', $post_content )
			|| has_block( 'tutorblock/course-masonry', $post_content )
			|| has_block( 'tutorblock/youtube-shorts', $post_content )
			|| has_shortcode( $post_content, 'tutorblock_course_grid' )
			|| has_shortcode( $post_content, 'tutorblock_course_masonry' )
			|| has_shortcode( $post_content, 'tutorblock_youtube_shorts' );

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
