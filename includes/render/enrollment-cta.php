<?php
/**
 * Render callback: tutorblock/enrollment-cta
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
 * Render the enrollment CTA block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Inner content (unused).
 * @return string
 */
function tutorblock_render_enrollment_cta( array $attributes, string $content = '' ): string {
	$course_id       = max( 0, (int) ( $attributes['courseId'] ?? 0 ) );
	$headline        = sanitize_text_field( $attributes['headline'] ?? __( 'Start Learning Today', 'tutorblock' ) );
	$subheadline     = sanitize_textarea_field( $attributes['subheadline'] ?? '' );
	$btn_text        = sanitize_text_field( $attributes['buttonText'] ?? __( 'Enroll Now', 'tutorblock' ) );
	$btn_url         = esc_url_raw( $attributes['buttonUrl'] ?? '' );
	$show_thumb      = (bool) ( $attributes['showCourseThumbnail'] ?? true );
	$show_title      = (bool) ( $attributes['showCourseTitle'] ?? true );
	$show_price      = (bool) ( $attributes['showPrice'] ?? true );
	$show_stats      = (bool) ( $attributes['showStats'] ?? true );
	$show_rating     = (bool) ( $attributes['showRating'] ?? true );
	$show_money_back = (bool) ( $attributes['showMoneyBack'] ?? false );
	$money_back_text = sanitize_text_field( $attributes['moneyBackText'] ?? __( '30-Day Money-Back Guarantee', 'tutorblock' ) );
	$layout          = in_array( $attributes['layout'] ?? 'horizontal', array( 'horizontal', 'vertical', 'inline' ), true )
		? $attributes['layout'] : 'horizontal';
	$primary_color   = sanitize_hex_color( $attributes['primaryColor'] ?? '#2563eb' ) ?: '#2563eb';
	$btn_text_color  = sanitize_hex_color( $attributes['buttonTextColor'] ?? '#ffffff' ) ?: '#ffffff';
	$bg_style        = in_array( $attributes['backgroundStyle'] ?? 'white', array( 'white', 'dark', 'primary', 'gradient' ), true )
		? $attributes['backgroundStyle'] : 'white';

	// Resolve course data.
	$course       = null;
	$course_title = '';
	$thumb_url    = '';
	$enrolled     = 0;
	$lessons      = 0;

	if ( $course_id > 0 ) {
		$course = get_post( $course_id );
		if ( $course && 'courses' === $course->post_type && 'publish' === $course->post_status ) {
			$course_title = get_the_title( $course );
			$thumb_url    = get_the_post_thumbnail_url( $course, 'large' ) ?: '';
			$btn_url      = $btn_url ?: get_permalink( $course );

			if ( function_exists( 'tutor_utils' ) ) {
				$enrolled = (int) tutor_utils()->get_total_enrolled( $course_id );
				$lessons  = (int) tutor_utils()->get_lesson_count_by_course( $course_id );
			}
		}
	}

	$color_style = tutorblock_color_style( $primary_color );

	ob_start();
	?>
	<div class="tutorblock-enrollment-cta is-layout-<?php echo esc_attr( $layout ); ?> bg-<?php echo esc_attr( $bg_style ); ?>"
		style="<?php echo esc_attr( $color_style ); ?>;--tutorblock-btn-text:<?php echo esc_attr( $btn_text_color ); ?>">

		<?php if ( $show_thumb && $thumb_url ) : ?>
			<div class="tutorblock-cta-image">
				<img src="<?php echo esc_url( $thumb_url ); ?>"
					 alt="<?php echo esc_attr( $course_title ); ?>"
					 loading="lazy" />
			</div>
		<?php endif; ?>

		<div class="tutorblock-cta-content">
			<?php if ( $show_title && $course_title ) : ?>
				<div class="tutorblock-cta-course-title"><?php echo esc_html( $course_title ); ?></div>
			<?php endif; ?>

			<?php if ( $headline ) : ?>
				<h2 class="tutorblock-cta-headline"><?php echo esc_html( $headline ); ?></h2>
			<?php endif; ?>

			<?php if ( $subheadline ) : ?>
				<p class="tutorblock-cta-subheadline"><?php echo esc_html( $subheadline ); ?></p>
			<?php endif; ?>

			<?php if ( $show_rating && $course_id > 0 ) : ?>
				<?php
				$rating_row = '';
				if ( function_exists( 'tutor_utils' ) ) {
					$r = tutor_utils()->get_course_rating( $course_id );
					if ( $r && $r->rating_avg > 0 ) {
						$avg     = round( (float) $r->rating_avg, 1 );
						$count   = (int) $r->rating_count;
						$filled  = floor( $avg );
						$half    = ( $avg - $filled ) >= 0.5 ? 1 : 0;
						$empty   = 5 - $filled - $half;
						$stars   = str_repeat( '★', $filled ) . ( $half ? '✫' : '' ) . str_repeat( '☆', $empty );
						$rating_row = sprintf(
							'<div class="tutorblock-cta-rating"><span class="tutorblock-stars">%s</span> <strong>%s</strong> (%d %s)</div>',
							esc_html( $stars ),
							esc_html( number_format( $avg, 1 ) ),
							$count,
							esc_html__( 'ratings', 'tutorblock' )
						);
					}
				}
				echo $rating_row; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			<?php endif; ?>

			<?php if ( $show_stats && ( $enrolled || $lessons ) ) : ?>
				<div class="tutorblock-cta-stats">
					<?php if ( $enrolled ) : ?>
						<div class="tutorblock-cta-stat">
							<span class="tutorblock-cta-stat-value"><?php echo number_format_i18n( $enrolled ); ?></span>
							<span class="tutorblock-cta-stat-label"><?php esc_html_e( 'Students', 'tutorblock' ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( $lessons ) : ?>
						<div class="tutorblock-cta-stat">
							<span class="tutorblock-cta-stat-value"><?php echo (int) $lessons; ?></span>
							<span class="tutorblock-cta-stat-label"><?php esc_html_e( 'Lessons', 'tutorblock' ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="tutorblock-cta-actions">
				<?php if ( $show_price && $course_id > 0 ) : ?>
					<div class="tutorblock-cta-price">
						<?php echo tutorblock_get_price_html( $course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
				<?php if ( $btn_url ) : ?>
					<a href="<?php echo esc_url( $btn_url ); ?>"
					   class="tutorblock-cta-enroll-btn"
					   style="background:<?php echo esc_attr( $primary_color ); ?>;color:<?php echo esc_attr( $btn_text_color ); ?>">
						<?php echo esc_html( $btn_text ); ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( $show_money_back ) : ?>
				<div class="tutorblock-cta-money-back"><?php echo esc_html( $money_back_text ); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
