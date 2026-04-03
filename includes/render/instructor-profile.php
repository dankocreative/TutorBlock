<?php
/**
 * Render callback: tutorblock/instructor-profile
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
 * Render the instructor profile block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Inner content (unused).
 * @return string
 */
function tutorblock_render_instructor_profile( array $attributes, string $content = '' ): string {
	$instructor_id  = max( 0, (int) ( $attributes['instructorId'] ?? 0 ) );
	$layout         = in_array( $attributes['layout'] ?? 'card', array( 'card', 'horizontal', 'minimal' ), true )
		? $attributes['layout'] : 'card';
	$show_bio       = (bool) ( $attributes['showBio'] ?? true );
	$show_stats     = (bool) ( $attributes['showStats'] ?? true );
	$show_social    = (bool) ( $attributes['showSocialLinks'] ?? true );
	$show_courses   = (bool) ( $attributes['showCourses'] ?? true );
	$courses_num    = max( 1, (int) ( $attributes['coursesToShow'] ?? 3 ) );
	$show_ratings   = (bool) ( $attributes['showRatings'] ?? true );
	$primary_color  = sanitize_hex_color( $attributes['primaryColor'] ?? '#2563eb' ) ?: '#2563eb';
	$accent_color   = sanitize_hex_color( $attributes['accentColor'] ?? '#f59e0b' ) ?: '#f59e0b';

	if ( $instructor_id <= 0 ) {
		return '<div class="tutorblock-no-courses">' . esc_html__( 'Please set an Instructor User ID in the block settings.', 'tutorblock' ) . '</div>';
	}

	$instructor = get_user_by( 'id', $instructor_id );
	if ( ! $instructor ) {
		return '<div class="tutorblock-no-courses">' . esc_html__( 'Instructor not found.', 'tutorblock' ) . '</div>';
	}

	$name        = esc_html( $instructor->display_name );
	$avatar_url  = get_avatar_url( $instructor->ID, array( 'size' => 200 ) );
	$bio         = get_user_meta( $instructor->ID, 'description', true );
	$job_title   = get_user_meta( $instructor->ID, '_tutor_profile_job_title', true )
		?: get_user_meta( $instructor->ID, 'tutor_profile_job_title', true );

	// Stats.
	$total_students   = 0;
	$total_courses    = 0;
	$total_reviews    = 0;
	$avg_rating       = 0;
	$instructor_courses = array();

	if ( function_exists( 'tutor_utils' ) ) {
		$instructor_courses = get_posts( array(
			'post_type'      => 'courses',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'author'         => $instructor_id,
			'fields'         => 'ids',
		) );

		$total_courses = count( $instructor_courses );

		foreach ( $instructor_courses as $cid ) {
			$total_students += (int) tutor_utils()->get_total_enrolled( $cid );
			$r               = tutor_utils()->get_course_rating( $cid );
			$total_reviews  += (int) ( $r->rating_count ?? 0 );
			$avg_rating     += (float) ( $r->rating_avg ?? 0 );
		}
		if ( $total_courses > 0 ) {
			$avg_rating = round( $avg_rating / $total_courses, 1 );
		}
	}

	// Social links.
	$social_platforms = array(
		'twitter'   => array( 'label' => 'Twitter / X',  'icon' => '𝕏',  'meta' => '_tutor_profile_twitter' ),
		'facebook'  => array( 'label' => 'Facebook',     'icon' => 'f',  'meta' => '_tutor_profile_facebook' ),
		'linkedin'  => array( 'label' => 'LinkedIn',     'icon' => 'in', 'meta' => '_tutor_profile_linkedin' ),
		'youtube'   => array( 'label' => 'YouTube',      'icon' => '▶',  'meta' => '_tutor_profile_youtube' ),
		'website'   => array( 'label' => 'Website',      'icon' => '🌐', 'meta' => 'user_url' ),
	);

	$social_html = '';
	if ( $show_social ) {
		$links = '';
		foreach ( $social_platforms as $key => $platform ) {
			$url = 'website' === $key
				? $instructor->user_url
				: get_user_meta( $instructor->ID, $platform['meta'], true );
			if ( $url ) {
				$links .= sprintf(
					'<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s" title="%s">%s</a>',
					esc_url( $url ),
					esc_attr( $platform['label'] ),
					esc_attr( $platform['label'] ),
					esc_html( $platform['icon'] )
				);
			}
		}
		if ( $links ) {
			$social_html = '<div class="tutorblock-instructor-social">' . $links . '</div>';
		}
	}

	// Courses list.
	$courses_list_html = '';
	if ( $show_courses && ! empty( $instructor_courses ) ) {
		$display_courses = get_posts( array(
			'post_type'      => 'courses',
			'posts_per_page' => $courses_num,
			'post_status'    => 'publish',
			'author'         => $instructor_id,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		if ( $display_courses ) {
			$items = '';
			foreach ( $display_courses as $c ) {
				$thumb = get_the_post_thumbnail_url( $c, 'thumbnail' ) ?: '';
				$price = tutorblock_get_price_html( $c->ID );
				$items .= sprintf(
					'<li class="tutorblock-instructor-course-item">%s<a class="tutorblock-ic-title" href="%s">%s</a><span class="tutorblock-ic-price">%s</span></li>',
					$thumb ? '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( get_the_title( $c ) ) . '" loading="lazy">' : '',
					esc_url( get_permalink( $c ) ),
					esc_html( get_the_title( $c ) ),
					$price // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
			$courses_list_html = '<div class="tutorblock-instructor-courses"><p class="tutorblock-instructor-courses-title">' .
				esc_html__( 'Courses', 'tutorblock' ) .
				'</p><ul class="tutorblock-instructor-course-list">' . $items . '</ul></div>';
		}
	}

	// Overall rating stars.
	$rating_html = '';
	if ( $show_ratings && $avg_rating > 0 ) {
		$filled  = floor( $avg_rating );
		$half    = ( $avg_rating - $filled ) >= 0.5 ? 1 : 0;
		$empty   = 5 - $filled - $half;
		$stars   = str_repeat( '★', $filled ) . ( $half ? '✫' : '' ) . str_repeat( '☆', $empty );
		$rating_html = sprintf(
			'<div class="tutorblock-instructor-rating"><span class="tutorblock-stars" style="color:%s">%s</span> <span>%s (%d %s)</span></div>',
			esc_attr( $accent_color ),
			esc_html( $stars ),
			esc_html( number_format( $avg_rating, 1 ) ),
			(int) $total_reviews,
			esc_html__( 'reviews', 'tutorblock' )
		);
	}

	$color_style = tutorblock_color_style( $primary_color, $accent_color );

	ob_start();
	?>
	<div class="tutorblock-instructor-profile is-layout-<?php echo esc_attr( $layout ); ?>"
		style="<?php echo esc_attr( $color_style ); ?>">

		<?php if ( 'card' === $layout ) : ?>
			<div class="tutorblock-instructor-cover"></div>
		<?php endif; ?>

		<div class="tutorblock-instructor-avatar-wrap">
			<img class="tutorblock-instructor-avatar"
				 src="<?php echo esc_url( $avatar_url ); ?>"
				 alt="<?php echo esc_attr( $name ); ?>"
				 loading="lazy" />
		</div>

		<div class="tutorblock-instructor-info">
			<h3 class="tutorblock-instructor-name"><?php echo $name; ?></h3>
			<?php if ( $job_title ) : ?>
				<p class="tutorblock-instructor-title"><?php echo esc_html( $job_title ); ?></p>
			<?php endif; ?>

			<?php echo $rating_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php if ( $show_stats ) : ?>
				<div class="tutorblock-instructor-stats">
					<?php if ( $total_courses > 0 ) : ?>
						<div class="tutorblock-istat">
							<span class="tutorblock-istat-value"><?php echo (int) $total_courses; ?></span>
							<span class="tutorblock-istat-label"><?php esc_html_e( 'Courses', 'tutorblock' ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( $total_students > 0 ) : ?>
						<div class="tutorblock-istat">
							<span class="tutorblock-istat-value"><?php echo number_format_i18n( $total_students ); ?></span>
							<span class="tutorblock-istat-label"><?php esc_html_e( 'Students', 'tutorblock' ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( $total_reviews > 0 ) : ?>
						<div class="tutorblock-istat">
							<span class="tutorblock-istat-value"><?php echo number_format_i18n( $total_reviews ); ?></span>
							<span class="tutorblock-istat-label"><?php esc_html_e( 'Reviews', 'tutorblock' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_bio && $bio ) : ?>
				<p class="tutorblock-instructor-bio"><?php echo esc_html( $bio ); ?></p>
			<?php endif; ?>

			<?php echo $social_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $courses_list_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
