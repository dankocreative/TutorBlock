<?php
/**
 * Render callback: tutorblock/course-carousel
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
 * Render the course carousel block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Block inner content (unused — SSR).
 * @return string
 */
function tutorblock_render_course_carousel( array $attributes, string $content = '' ): string {
	$num_courses      = max( 1, (int) ( $attributes['numberOfCourses'] ?? 9 ) );
	$slides_to_show   = max( 1, (int) ( $attributes['slidesToShow'] ?? 3 ) );
	$slides_tablet    = max( 1, (int) ( $attributes['slidesToShowTablet'] ?? 2 ) );
	$slides_mobile    = max( 1, (int) ( $attributes['slidesToShowMobile'] ?? 1 ) );
	$autoplay         = (bool) ( $attributes['autoplay'] ?? false );
	$autoplay_speed   = max( 500, (int) ( $attributes['autoplaySpeed'] ?? 3000 ) );
	$show_arrows      = (bool) ( $attributes['showArrows'] ?? true );
	$show_dots        = (bool) ( $attributes['showDots'] ?? true );
	$show_rating      = (bool) ( $attributes['showRating'] ?? true );
	$show_price       = (bool) ( $attributes['showPrice'] ?? true );
	$show_instructor  = (bool) ( $attributes['showInstructor'] ?? true );
	$show_enroll      = (bool) ( $attributes['showEnrollButton'] ?? true );
	$primary_color    = sanitize_hex_color( $attributes['primaryColor'] ?? '#2563eb' ) ?: '#2563eb';
	$border_radius    = max( 0, (int) ( $attributes['cardBorderRadius'] ?? 8 ) );
	$heading          = sanitize_text_field( $attributes['heading'] ?? '' );
	$subheading       = sanitize_text_field( $attributes['subheading'] ?? '' );

	$query_args = tutorblock_build_course_query( $attributes, $num_courses );
	$query      = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '<div class="tutorblock-course-carousel"><p class="tutorblock-no-courses">' .
			esc_html__( 'No courses found.', 'tutorblock' ) .
			'</p></div>';
	}

	// Unique ID for this carousel instance.
	static $carousel_count = 0;
	$carousel_count++;
	$carousel_id = 'tutorblock-carousel-' . $carousel_count;

	$card_opts = array(
		'show_rating'     => $show_rating,
		'show_price'      => $show_price,
		'show_instructor' => $show_instructor,
		'show_enroll_btn' => $show_enroll,
		'border_radius'   => $border_radius,
		'primary_color'   => $primary_color,
	);

	ob_start();
	?>
	<div
		id="<?php echo esc_attr( $carousel_id ); ?>"
		class="tutorblock-course-carousel"
		style="<?php echo esc_attr( tutorblock_color_style( $primary_color ) ); ?>"
		data-slides-desktop="<?php echo (int) $slides_to_show; ?>"
		data-slides-tablet="<?php echo (int) $slides_tablet; ?>"
		data-slides-mobile="<?php echo (int) $slides_mobile; ?>"
		data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>"
		data-autoplay-speed="<?php echo (int) $autoplay_speed; ?>"
	>
		<?php if ( $heading || $subheading ) : ?>
		<div class="tutorblock-carousel-header">
			<?php if ( $heading ) : ?>
				<h2 class="tutorblock-carousel-heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $subheading ) : ?>
				<p class="tutorblock-carousel-subheading"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<div class="tutorblock-carousel-outer" style="position:relative;padding:0 24px;">
			<?php if ( $show_arrows ) : ?>
				<button class="tutorblock-carousel-arrow tutorblock-arrow-prev"
					aria-label="<?php esc_attr_e( 'Previous', 'tutorblock' ); ?>"
					data-carousel="<?php echo esc_attr( $carousel_id ); ?>">&#8249;</button>
			<?php endif; ?>

			<div class="tutorblock-carousel-viewport">
				<div class="tutorblock-carousel-track">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<div class="tutorblock-carousel-slide">
							<?php echo tutorblock_render_course_card( get_post(), $card_opts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endwhile; ?>
				</div>
			</div>

			<?php if ( $show_arrows ) : ?>
				<button class="tutorblock-carousel-arrow tutorblock-arrow-next"
					aria-label="<?php esc_attr_e( 'Next', 'tutorblock' ); ?>"
					data-carousel="<?php echo esc_attr( $carousel_id ); ?>">&#8250;</button>
			<?php endif; ?>
		</div>

		<?php if ( $show_dots ) : ?>
			<div class="tutorblock-carousel-dots" role="tablist" aria-label="<?php esc_attr_e( 'Carousel navigation', 'tutorblock' ); ?>">
				<?php
				$num_slides  = $query->post_count;
				$num_pages   = max( 1, (int) ceil( $num_slides / $slides_to_show ) );
				for ( $i = 0; $i < $num_pages; $i++ ) :
				?>
					<button
						class="tutorblock-dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
						data-carousel="<?php echo esc_attr( $carousel_id ); ?>"
						data-index="<?php echo (int) $i; ?>"
						role="tab"
						aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Slide %d', 'tutorblock' ), $i + 1 ) ); ?>"
					></button>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	wp_reset_postdata();

	return ob_get_clean();
}
