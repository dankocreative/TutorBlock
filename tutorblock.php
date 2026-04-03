<?php
/**
 * Plugin Name: TutorBlock for Tutor LMS
 * Plugin URI:  https://example.com/tutorblock
 * Description: Zip-install ready Tutor LMS enhancement plugin with Gutenberg blocks, video lessons, progress tracking, and zero Node.js build requirements.
 * Version:     1.0.0
 * Author:      TutorBlock
 * Text Domain: tutorblock
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TUTORBLOCK_VERSION', '1.0.0' );
define( 'TUTORBLOCK_FILE', __FILE__ );
define( 'TUTORBLOCK_PATH', plugin_dir_path( __FILE__ ) );
define( 'TUTORBLOCK_URL', plugin_dir_url( __FILE__ ) );

require_once TUTORBLOCK_PATH . 'includes/class-tutorblock-plugin.php';
require_once TUTORBLOCK_PATH . 'includes/class-tutorblock-video.php';
require_once TUTORBLOCK_PATH . 'includes/class-tutorblock-admin.php';

TutorBlock_Plugin::instance();
