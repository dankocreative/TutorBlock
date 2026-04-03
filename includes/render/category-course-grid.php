<?php
/**
 * Render callback: tutorblock/category-course-grid
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
 * Render category-organized course grid.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Inner content (unused).
 * @return string
 */
function tutorblock_render_category_course_grid( array $attributes, string $content = '' ): string {
	$cat_input       = sanitize_text_field( $attributes['categories'] ?? '' );
	$per_cat         = max( 1, (int) ( $attributes['coursesPerCategory'] ?? 4 ) );
	$columns         = max( 1, min( 6, (int) ( $attributes['columns'] ?? 4 ) ) );
	$columns_tablet  = max( 1, min( 4, (int) ( $attributes['columnsTablet'] ?? 2 ) ) );
	$show_cat_title  = (bool) ( $attributes['showCategoryTitle'] ?? true );
	$show_cat_desc   = (bool) ( $attributes['showCategoryDescription'] ?? false );
	$show_cat_count  = (bool) ( $attributes['showCategoryCourseCount'] ?? true );
	$show_view_all   = (bool) ( $attributes['showViewAllLink'] ?? true );
	$view_all_text   = sanitize_text_field( $attributes['viewAllText'] ?? __( 'View All Courses', 'tutorblock' ) );
	$show_rating     = (bool) ( $attributes['showRating'] ?? true );
	$show_price      = (bool) ( $attributes['showPrice'] ?? true );
	$show_enroll     = (bool) ( $attributes['showEnrollButton'] ?? true );
	$primary_color   = sanitize_hex_color( $attributes['primaryColor'] ?? '#2563eb' ) ?: '#2563eb';
	$border_radius   = max( 0, (int) ( $attributes['cardBorderRadius'] ?? 8 ) );
	$section_spacing = max( 16, (int) ( $attributes['sectionSpacing'] ?? 48 ) );

	// Fetch categories.
	$tax_args = array(
		'taxonomy'   => 'course-category',
		'hide_empty' => true,
		'orderby'    => 'name',
	);

	if ( ! empty( $cat_input ) ) {
		$slugs              = array_filter( array_map( 'trim', explode( ',', $cat_input ) ) );
		$tax_args['slug']   = $slugs;
	}

	$categories = get_terms( $tax_args );

	if ( is_wp_error( $categories ) || empty( $categories ) ) {
		return '<div class="tutorblock-no-courses">' . esc_html__( 'No course categories found.', 'tutorblock' ) . '</div>';
	}

	$card_opts = array(
		'show_rating'     => $show_rating,
		'show_price'      => $show_price,
		'show_instructor' => false,
		'show_enroll_btn' => $show_enroll,
		'show_meta'       => false,
		'border_radius'   => $border_radius,
		'primary_color'   => $primary_color,
	);

	$grid_style = sprintf( 'grid-template-columns:repeat(%d,1fr);', $columns );

	ob_start();
	?>
	<div class="tutorblock-category-course-grid"
		style="<?php echo esc_attr( tutorblock_color_style( $primary_color ) ); ?>;--tutorblock-section-spacing:<?php echo (int) $section_spacing; ?>px;">

		<?php foreach ( $categories as $category ) : ?>
			<?php
			// Query courses for this category.
			$course_args = array(
				'post_type'      => 'courses',
				'posts_per_page' => $per_cat,
				'post_status'    => 'publish',
				'orderby'        => sanitize_key( $attributes['orderBy'] ?? 'date' ),
				'order'          => strtoupper( sanitize_key( $attributes['order'] ?? 'DESC' ) ) === 'ASC' ? 'ASC' : 'DESC',
				'tax_query'      => array(
					array(
						'taxonomy' => 'course-category',
						'field'    => 'term_id',
						'terms'    => $category->term_id,
					),
				),
			);

			$query = new WP_Query( $course_args );
			if ( ! $query->have_posts() ) {
				continue;
			}

			$cat_link = get_term_link( $category );
			?>
			<section class="tutorblock-category-section">
				<?php if ( $show_cat_title ) : ?>
					<div class="tutorblock-category-header">
						<div class="tutorblock-category-title-wrap">
							<div class="tutorblock-category-accent"></div>
							<h3>
								<?php if ( $show_view_all ) : ?>
									<a href="<?php echo esc_url( $cat_link ); ?>" style="color:inherit;text-decoration:none;">
										<?php echo esc_html( $category->name ); ?>
									</a>
								<?php else : ?>
									<?php echo esc_html( $category->name ); ?>
								<?php endif; ?>
								<?php if ( $show_cat_count ) : ?>
									<span class="tutorblock-cat-count">&mdash; <?php echo sprintf(
										/* translators: %d: number of courses */
										esc_html( _n( '%d course', '%d courses', $category->count, 'tutorblock' ) ),
										(int) $category->count
									); ?></span>
								<?php endif; ?>
							</h3>
							<?php if ( $show_cat_desc && $category->description ) : ?>
								<p class="tutorblock-category-description"><?php echo esc_html( $category->description ); ?></p>
							<?php endif; ?>
						</div>

						<?php if ( $show_view_all ) : ?>
							<a href="<?php echo esc_url( $cat_link ); ?>" class="tutorblock-view-all-link"
								style="color:<?php echo esc_attr( $primary_color ); ?>">
								<?php echo esc_html( $view_all_text ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="tutorblock-category-grid" style="<?php echo esc_attr( $grid_style ); ?>">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						echo tutorblock_render_course_card( get_post(), $card_opts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
	<?php

	return ob_get_clean();
}
