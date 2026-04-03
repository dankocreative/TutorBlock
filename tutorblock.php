<?php
/**
 * Plugin Name:       TutorBlock
 * Plugin URI:        https://github.com/dankocreative/TutorBlock
 * Description:       Powerful Gutenberg/Block Editor blocks for TutorLMS: course carousels, grids, previews, instructor profiles, stats, enrollment CTAs, and more.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Danko Creative
 * Author URI:        https://dankocreative.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tutorblock
 * Domain Path:       /languages
 *
 * @package TutorBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'TUTORBLOCK_VERSION', '1.0.0' );
define( 'TUTORBLOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TUTORBLOCK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TUTORBLOCK_PLUGIN_FILE', __FILE__ );

/**
 * Check if TutorLMS is active. Show an admin notice if not.
 */
function tutorblock_check_dependencies() {
	if ( ! class_exists( 'TUTOR\Tutor' ) && ! defined( 'TUTOR_VERSION' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-warning is-dismissible"><p>'
					. sprintf(
						/* translators: %s: TutorLMS plugin link */
						esc_html__( 'TutorBlock requires %s to be installed and activated for full functionality.', 'tutorblock' ),
						'<a href="https://wordpress.org/plugins/tutor/" target="_blank">TutorLMS</a>'
					)
					. '</p></div>';
			}
		);
	}
}
add_action( 'admin_init', 'tutorblock_check_dependencies' );

/**
 * Load plugin text domain for translations.
 */
function tutorblock_load_textdomain() {
	load_plugin_textdomain( 'tutorblock', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'tutorblock_load_textdomain' );

/**
 * Register the "TutorBlock" block category.
 *
 * @param array $categories Existing block categories.
 * @return array
 */
function tutorblock_register_block_category( array $categories ): array {
	return array_merge(
		array(
			array(
				'slug'  => 'tutorblock',
				'title' => __( 'TutorBlock', 'tutorblock' ),
				'icon'  => 'welcome-learn-more',
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'tutorblock_register_block_category', 10, 1 );

/**
 * Register all TutorBlock blocks.
 */
function tutorblock_register_blocks() {
	$blocks = array(
		'course-carousel'      => 'tutorblock_render_course_carousel',
		'course-grid'          => 'tutorblock_render_course_grid',
		'course-preview'       => 'tutorblock_render_course_preview',
		'category-course-grid' => 'tutorblock_render_category_course_grid',
		'instructor-profile'   => 'tutorblock_render_instructor_profile',
		'course-stats'         => 'tutorblock_render_course_stats',
		'enrollment-cta'       => 'tutorblock_render_enrollment_cta',
	);

	foreach ( $blocks as $block => $callback ) {
		$block_dir = TUTORBLOCK_PLUGIN_DIR . 'build/blocks/' . $block;
		if ( file_exists( $block_dir . '/block.json' ) ) {
			register_block_type(
				$block_dir,
				array(
					'render_callback' => $callback,
				)
			);
		}
	}
}
add_action( 'init', 'tutorblock_register_blocks' );

/**
 * Enqueue frontend assets (carousel JS + global styles).
 */
function tutorblock_enqueue_frontend_assets() {
	wp_enqueue_style(
		'tutorblock-global',
		TUTORBLOCK_PLUGIN_URL . 'assets/css/tutorblock-global.css',
		array(),
		TUTORBLOCK_VERSION
	);

	wp_enqueue_script(
		'tutorblock-carousel',
		TUTORBLOCK_PLUGIN_URL . 'assets/js/tutorblock-carousel.js',
		array(),
		TUTORBLOCK_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tutorblock_enqueue_frontend_assets' );

/**
 * Enqueue editor-specific assets.
 */
function tutorblock_enqueue_editor_assets() {
	wp_enqueue_style(
		'tutorblock-editor-preview',
		TUTORBLOCK_PLUGIN_URL . 'assets/css/tutorblock-editor.css',
		array(),
		TUTORBLOCK_VERSION
	);

	// Pass REST API base and nonce to JS for live course selection.
	wp_localize_script(
		'tutorblock-course-preview-editor-script',
		'tutorblockData',
		array(
			'restUrl'   => esc_url_raw( rest_url() ),
			'nonce'     => wp_create_nonce( 'wp_rest' ),
			'pluginUrl' => TUTORBLOCK_PLUGIN_URL,
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'tutorblock_enqueue_editor_assets' );

// Load shared render helpers first.
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/helpers.php';

// Load render callbacks.
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/course-carousel.php';
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/course-grid.php';
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/course-preview.php';
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/category-course-grid.php';
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/instructor-profile.php';
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/course-stats.php';
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/render/enrollment-cta.php';

// Load REST API endpoints for editor data.
require_once TUTORBLOCK_PLUGIN_DIR . 'includes/admin/rest-api.php';
