<?php
/**
 * Render callback: tutorblock/course-stats
 *
 * @param array $attributes Block attributes.
 * @return string HTML output.
 *
 * @package TutorBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the platform stats block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Inner content (unused).
 * @return string
 */
function tutorblock_render_course_stats( array $attributes, string $content = '' ): string {
	$show_courses     = (bool) ( $attributes['showTotalCourses']     ?? true );
	$show_students    = (bool) ( $attributes['showTotalStudents']    ?? true );
	$show_instructors = (bool) ( $attributes['showTotalInstructors'] ?? true );
	$show_reviews     = (bool) ( $attributes['showTotalReviews']     ?? true );
	$show_categories  = (bool) ( $attributes['showTotalCategories']  ?? false );

	$label_courses     = sanitize_text_field( $attributes['labelCourses']     ?? __( 'Courses', 'tutorblock' ) );
	$label_students    = sanitize_text_field( $attributes['labelStudents']    ?? __( 'Students Enrolled', 'tutorblock' ) );
	$label_instructors = sanitize_text_field( $attributes['labelInstructors'] ?? __( 'Expert Instructors', 'tutorblock' ) );
	$label_reviews     = sanitize_text_field( $attributes['labelReviews']     ?? __( '5-Star Reviews', 'tutorblock' ) );
	$label_categories  = sanitize_text_field( $attributes['labelCategories']  ?? __( 'Categories', 'tutorblock' ) );

	$layout        = in_array( $attributes['layout'] ?? 'horizontal', array( 'horizontal', 'grid', 'vertical' ), true )
		? $attributes['layout'] : 'horizontal';
	$style         = in_array( $attributes['style'] ?? 'default', array( 'default', 'filled', 'gradient', 'minimal' ), true )
		? $attributes['style'] : 'default';
	$primary_color = sanitize_hex_color( $attributes['primaryColor'] ?? '#2563eb' ) ?: '#2563eb';
	$secondary_color = sanitize_hex_color( $attributes['secondaryColor'] ?? '#7c3aed' ) ?: '#7c3aed';
	$animated      = (bool) ( $attributes['animated'] ?? true );
	$show_icons    = (bool) ( $attributes['showIcons'] ?? true );
	$icon_style    = 'emoji' === ( $attributes['iconStyle'] ?? 'emoji' ) ? 'emoji' : 'svg';

	// Calculate stats.
	$stats = array();

	if ( $show_courses ) {
		$count = wp_count_posts( 'courses' );
		$stats[] = array(
			'value' => (int) ( $count->publish ?? 0 ),
			'label' => $label_courses,
			'icon'  => '🎓',
		);
	}

	if ( $show_students ) {
		$count = 0;
		if ( function_exists( 'tutor_utils' ) ) {
			// Count all enrolled students.
			$count = (int) tutor_utils()->get_total_users_by_role( 'student' );
		} else {
			$count = (int) count_users()['avail_roles']['subscriber'] ?? 0;
		}
		$stats[] = array(
			'value' => $count,
			'label' => $label_students,
			'icon'  => '👥',
		);
	}

	if ( $show_instructors ) {
		$count = 0;
		if ( function_exists( 'tutor_utils' ) ) {
			$instructors = get_users( array(
				'role'   => 'tutor_instructor',
				'fields' => 'ID',
			) );
			$count = count( $instructors );
		}
		$stats[] = array(
			'value' => $count,
			'label' => $label_instructors,
			'icon'  => '👨‍🏫',
		);
	}

	if ( $show_reviews ) {
		$count = 0;
		if ( function_exists( 'tutor_utils' ) ) {
			// Get total review count across all courses.
			global $wpdb;
			$table  = $wpdb->prefix . 'tutor_course_rating';
			$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			if ( $exists ) {
				$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery
			}
		}
		$stats[] = array(
			'value' => $count,
			'label' => $label_reviews,
			'icon'  => '⭐',
		);
	}

	if ( $show_categories ) {
		$cat_count = wp_count_terms( array(
			'taxonomy'   => 'course-category',
			'hide_empty' => true,
		) );
		$stats[] = array(
			'value' => (int) $cat_count,
			'label' => $label_categories,
			'icon'  => '📂',
		);
	}

	if ( empty( $stats ) ) {
		return '';
	}

	$color_style = tutorblock_color_style( $primary_color, $secondary_color );

	ob_start();
	?>
	<div class="tutorblock-course-stats is-layout-<?php echo esc_attr( $layout ); ?> is-style-<?php echo esc_attr( $style ); ?>"
		style="<?php echo esc_attr( $color_style ); ?>">
		<div class="tutorblock-stats-inner">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="tutorblock-stat-item<?php echo $show_icons ? ' has-icon' : ''; ?>"
					<?php if ( $animated ) : ?>
						data-target="<?php echo (int) $stat['value']; ?>"
						data-animated="true"
					<?php endif; ?>>
					<?php if ( $show_icons && 'emoji' === $icon_style ) : ?>
						<div class="tutorblock-stat-icon"><?php echo esc_html( $stat['icon'] ); ?></div>
					<?php endif; ?>
					<span class="tutorblock-stat-number">
						<?php echo esc_html( number_format_i18n( (int) $stat['value'] ) ); ?>
					</span>
					<span class="tutorblock-stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
