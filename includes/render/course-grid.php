<?php
/**
 * Render callback: tutorblock/course-grid
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
 * Render the course grid block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Inner content (unused).
 * @return string
 */
function tutorblock_render_course_grid( array $attributes, string $content = '' ): string {
	$num_courses     = max( 1, (int) ( $attributes['numberOfCourses'] ?? 9 ) );
	$columns         = max( 1, min( 6, (int) ( $attributes['columns'] ?? 3 ) ) );
	$columns_tablet  = max( 1, min( 4, (int) ( $attributes['columnsTablet'] ?? 2 ) ) );
	$show_filter     = (bool) ( $attributes['showFilterBar'] ?? false );
	$show_pagination = (bool) ( $attributes['showPagination'] ?? false );
	$show_rating     = (bool) ( $attributes['showRating'] ?? true );
	$show_price      = (bool) ( $attributes['showPrice'] ?? true );
	$show_instructor = (bool) ( $attributes['showInstructor'] ?? true );
	$show_enroll     = (bool) ( $attributes['showEnrollButton'] ?? true );
	$show_meta       = (bool) ( $attributes['showMeta'] ?? true );
	$primary_color   = sanitize_hex_color( $attributes['primaryColor'] ?? '#2563eb' ) ?: '#2563eb';
	$border_radius   = max( 0, (int) ( $attributes['cardBorderRadius'] ?? 8 ) );
	$layout          = in_array( $attributes['layout'] ?? 'card', array( 'card', 'list', 'minimal' ), true )
		? $attributes['layout'] : 'card';
	$heading         = sanitize_text_field( $attributes['heading'] ?? '' );
	$subheading      = sanitize_text_field( $attributes['subheading'] ?? '' );

	// Pagination.
	$current_page = max( 1, get_query_var( 'paged', 1 ) );
	$query_args   = tutorblock_build_course_query( $attributes, $num_courses );

	if ( $show_pagination ) {
		$query_args['posts_per_page'] = $num_courses;
		$query_args['paged']          = $current_page;
	}

	$query = new WP_Query( $query_args );

	$card_opts = array(
		'show_rating'     => $show_rating,
		'show_price'      => $show_price,
		'show_instructor' => $show_instructor,
		'show_enroll_btn' => $show_enroll,
		'show_meta'       => $show_meta,
		'border_radius'   => $border_radius,
		'primary_color'   => $primary_color,
	);

	// Build grid column CSS.
	$grid_style = sprintf(
		'grid-template-columns:repeat(%d,1fr);',
		$columns
	);

	ob_start();
	?>
	<div class="tutorblock-course-grid is-layout-<?php echo esc_attr( $layout ); ?>"
		 style="<?php echo esc_attr( tutorblock_color_style( $primary_color ) ); ?>">

		<?php if ( $heading || $subheading ) : ?>
			<div class="tutorblock-grid-header">
				<?php if ( $heading ) : ?>
					<h2><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $subheading ) : ?>
					<p><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $show_filter ) : ?>
			<?php
			$all_cats = get_terms( array( 'taxonomy' => 'course-category', 'hide_empty' => true ) );
			if ( $all_cats && ! is_wp_error( $all_cats ) ) :
			?>
				<div class="tutorblock-filter-bar" data-filter-target="tutorblock-grid-<?php echo uniqid(); ?>">
					<button class="tutorblock-filter-btn is-active" data-filter="*">
						<?php esc_html_e( 'All Courses', 'tutorblock' ); ?>
					</button>
					<?php foreach ( $all_cats as $term ) : ?>
						<button class="tutorblock-filter-btn"
							data-filter="<?php echo esc_attr( $term->slug ); ?>">
							<?php echo esc_html( $term->name ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( $query->have_posts() ) : ?>
			<div class="tutorblock-grid-inner" style="<?php echo esc_attr( $grid_style ); ?>">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					$post = get_post();

					// For filter functionality, attach category slugs as data attribute.
					$cats  = get_the_terms( $post, 'course-category' );
					$slugs = ( $cats && ! is_wp_error( $cats ) )
						? implode( ' ', wp_list_pluck( $cats, 'slug' ) )
						: '';

					echo '<div class="tutorblock-grid-item" data-category="' . esc_attr( $slugs ) . '">';
					echo tutorblock_render_course_card( $post, $card_opts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '</div>';
				}
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $show_pagination && $query->max_num_pages > 1 ) : ?>
				<nav class="tutorblock-pagination" aria-label="<?php esc_attr_e( 'Courses navigation', 'tutorblock' ); ?>">
					<?php
					echo paginate_links( array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'total'     => $query->max_num_pages,
						'current'   => $current_page,
						'type'      => 'list',
						'prev_text' => '&laquo;',
						'next_text' => '&raquo;',
						'before_page_number' => '<span class="tutorblock-page-btn">',
						'after_page_number'  => '</span>',
					) );
					?>
				</nav>
			<?php endif; ?>

		<?php else : ?>
			<div class="tutorblock-no-courses">
				<?php esc_html_e( 'No courses found. Try adjusting your filters.', 'tutorblock' ); ?>
			</div>
		<?php endif; ?>
	</div>
	<?php

	return ob_get_clean();
}
