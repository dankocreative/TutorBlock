<?php
/**
 * TutorBlock — Shared helpers for render callbacks.
 *
 * @package TutorBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a WP_Query args array for course queries.
 *
 * @param array  $attr         Block attributes.
 * @param int    $num_posts    Override posts_per_page if needed.
 * @param string $cat_slugs    Comma-separated category slugs (overrides $attr['category']).
 * @return array
 */
function tutorblock_build_course_query( array $attr, int $num_posts = 0, string $cat_slugs = '' ): array {
	$number    = $num_posts > 0 ? $num_posts : ( (int) ( $attr['numberOfCourses'] ?? 9 ) );
	$order_by  = sanitize_key( $attr['orderBy'] ?? 'date' );
	$order     = strtoupper( sanitize_key( $attr['order'] ?? 'DESC' ) ) === 'ASC' ? 'ASC' : 'DESC';
	$cat_input = $cat_slugs !== '' ? $cat_slugs : ( $attr['category'] ?? '' );

	$args = array(
		'post_type'      => 'courses',
		'posts_per_page' => $number,
		'post_status'    => 'publish',
		'order'          => $order,
	);

	// Map custom order-by values to WP defaults.
	switch ( $order_by ) {
		case 'popularity':
			$args['meta_key'] = '_tutor_course_enrolled_by_meta_data';
			$args['orderby']  = 'meta_value_num';
			break;
		case 'rating':
			$args['meta_key'] = '_tutor_rating';
			$args['orderby']  = 'meta_value_num';
			break;
		case 'rand':
			$args['orderby'] = 'rand';
			break;
		default:
			$args['orderby'] = $order_by;
	}

	// Category tax query.
	if ( ! empty( $cat_input ) ) {
		$slugs = array_filter( array_map( 'trim', explode( ',', $cat_input ) ) );
		if ( ! empty( $slugs ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'course-category',
					'field'    => 'slug',
					'terms'    => $slugs,
				),
			);
		}
	}

	return $args;
}

/**
 * Render a single course card (shared across blocks).
 *
 * @param WP_Post $course     Course post object.
 * @param array   $options    Display options.
 * @return string
 */
function tutorblock_render_course_card( WP_Post $course, array $options = array() ): string {
	$defaults = array(
		'show_rating'       => true,
		'show_price'        => true,
		'show_instructor'   => true,
		'show_enroll_btn'   => true,
		'show_meta'         => true,
		'border_radius'     => 8,
		'primary_color'     => '#2563eb',
	);
	$opts = wp_parse_args( $options, $defaults );

	$course_id     = $course->ID;
	$title         = get_the_title( $course );
	$permalink     = get_permalink( $course );
	$thumbnail_url = get_the_post_thumbnail_url( $course, 'medium_large' ) ?: '';

	// Category.
	$categories = get_the_terms( $course, 'course-category' );
	$cat_name   = ( $categories && ! is_wp_error( $categories ) ) ? esc_html( $categories[0]->name ) : '';

	// Price.
	$price_html = '';
	if ( $opts['show_price'] ) {
		$price_html = tutorblock_get_price_html( $course_id );
	}

	// Rating.
	$rating_html = '';
	if ( $opts['show_rating'] ) {
		$rating_html = tutorblock_get_rating_html( $course_id );
	}

	// Instructor.
	$instructor_html = '';
	if ( $opts['show_instructor'] ) {
		$instructor_html = tutorblock_get_instructor_mini_html( $course_id );
	}

	// Meta (lessons, students).
	$meta_html = '';
	if ( $opts['show_meta'] ) {
		$meta_html = tutorblock_get_course_meta_html( $course_id );
	}

	// Badge (free/paid).
	$badge = '';
	if ( $opts['show_price'] ) {
		$price_type = get_post_meta( $course_id, '_tutor_course_price_type', true );
		if ( 'free' === $price_type || empty( $price_type ) ) {
			$badge = '<span class="tutorblock-card-badge is-free">' . esc_html__( 'Free', 'tutorblock' ) . '</span>';
		} else {
			$badge = '<span class="tutorblock-card-badge is-paid">' . esc_html__( 'Paid', 'tutorblock' ) . '</span>';
		}
	}

	$radius     = (int) $opts['border_radius'];
	$color      = esc_attr( $opts['primary_color'] );
	$enroll_btn = '';
	if ( $opts['show_enroll_btn'] ) {
		$enroll_btn = sprintf(
			'<a href="%s" class="tutorblock-enroll-btn" style="background:%s">%s</a>',
			esc_url( $permalink ),
			esc_attr( $color ),
			esc_html__( 'Enroll Now', 'tutorblock' )
		);
	}

	ob_start();
	?>
	<div class="tutorblock-course-card" style="border-radius:<?php echo $radius; ?>px;--tutorblock-primary:<?php echo $color; ?>">
		<a href="<?php echo esc_url( $permalink ); ?>" class="tutorblock-card-thumb" tabindex="-1" aria-hidden="true">
			<?php if ( $thumbnail_url ) : ?>
				<img src="<?php echo esc_url( $thumbnail_url ); ?>"
					 alt="<?php echo esc_attr( $title ); ?>"
					 loading="lazy" />
			<?php else : ?>
				<div class="tutorblock-card-no-thumb" style="background:<?php echo esc_attr( $color ); ?>1a;aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;font-size:3rem;">🎓</div>
			<?php endif; ?>
			<?php echo $badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>

		<div class="tutorblock-card-body">
			<?php if ( $cat_name ) : ?>
				<div class="tutorblock-card-category" style="color:<?php echo $color; ?>"><?php echo $cat_name; ?></div>
			<?php endif; ?>

			<h3 class="tutorblock-card-title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>

			<?php echo $rating_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $instructor_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $meta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<div class="tutorblock-card-footer">
			<?php if ( $opts['show_price'] ) : ?>
				<div class="tutorblock-card-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<?php echo $enroll_btn; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get formatted price HTML for a course.
 */
function tutorblock_get_price_html( int $course_id ): string {
	$price_type = get_post_meta( $course_id, '_tutor_course_price_type', true );

	if ( 'free' === $price_type || empty( $price_type ) ) {
		return '<span class="tutorblock-price-free">' . esc_html__( 'Free', 'tutorblock' ) . '</span>';
	}

	// Try TutorLMS price function.
	if ( function_exists( 'tutor_utils' ) ) {
		$price = tutor_utils()->get_raw_course_price( $course_id );
		if ( $price ) {
			$amount   = isset( $price->regular_price ) ? wc_price( $price->regular_price ) : '';
			$sale     = isset( $price->sale_price ) && $price->sale_price ? wc_price( $price->sale_price ) : '';
			$original = $sale ? '<span class="tutorblock-price-original">' . $amount . '</span>' : '';
			$display  = $sale ?: $amount;
			return '<span class="tutorblock-price-amount">' . $display . '</span>' . $original;
		}
	}

	// Fallback: check WooCommerce product.
	$product_id = get_post_meta( $course_id, '_tutor_course_product_id', true );
	if ( $product_id && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			return '<span class="tutorblock-price-amount">' . $product->get_price_html() . '</span>';
		}
	}

	return '<span class="tutorblock-price-free">' . esc_html__( 'Free', 'tutorblock' ) . '</span>';
}

/**
 * Get star rating HTML for a course.
 */
function tutorblock_get_rating_html( int $course_id ): string {
	$rating = array( 'rating_avg' => 0, 'rating_count' => 0 );

	if ( function_exists( 'tutor_utils' ) ) {
		$result = tutor_utils()->get_course_rating( $course_id );
		if ( $result ) {
			$rating['rating_avg']   = round( (float) ( $result->rating_avg ?? 0 ), 1 );
			$rating['rating_count'] = (int) ( $result->rating_count ?? 0 );
		}
	}

	if ( $rating['rating_avg'] <= 0 ) {
		return '';
	}

	$filled  = floor( $rating['rating_avg'] );
	$half    = ( $rating['rating_avg'] - $filled ) >= 0.5 ? 1 : 0;
	$empty   = 5 - $filled - $half;
	$stars   = str_repeat( '★', $filled ) . ( $half ? '✫' : '' ) . str_repeat( '☆', $empty );

	return sprintf(
		'<div class="tutorblock-card-rating"><span class="tutorblock-stars">%s</span><span class="tutorblock-rating-value">%s</span><span class="tutorblock-rating-count">(%d)</span></div>',
		esc_html( $stars ),
		esc_html( number_format( $rating['rating_avg'], 1 ) ),
		(int) $rating['rating_count']
	);
}

/**
 * Get mini instructor HTML (avatar + name).
 */
function tutorblock_get_instructor_mini_html( int $course_id ): string {
	$instructors = array();

	if ( function_exists( 'tutor_utils' ) ) {
		$instructors = tutor_utils()->get_instructors_by_course( $course_id );
	}

	if ( empty( $instructors ) ) {
		$post        = get_post( $course_id );
		$instructors = $post ? array( get_user_by( 'id', $post->post_author ) ) : array();
	}

	if ( empty( $instructors ) || ! $instructors[0] ) {
		return '';
	}

	$instructor  = $instructors[0];
	$avatar_url  = get_avatar_url( $instructor->ID, array( 'size' => 32 ) );
	$name        = esc_html( $instructor->display_name );

	return sprintf(
		'<div class="tutorblock-card-instructor"><img src="%s" alt="%s" width="24" height="24"><span>%s</span></div>',
		esc_url( $avatar_url ),
		esc_attr( $name ),
		$name
	);
}

/**
 * Get course meta HTML (lessons count, students, duration).
 */
function tutorblock_get_course_meta_html( int $course_id ): string {
	$items = array();

	// Lesson count.
	if ( function_exists( 'tutor_utils' ) ) {
		$lessons = tutor_utils()->get_lesson_count_by_course( $course_id );
		if ( $lessons > 0 ) {
			$items[] = sprintf(
				'<span class="tutorblock-meta-item">📚 %s</span>',
				sprintf(
					/* translators: %d: number of lessons */
					esc_html( _n( '%d Lesson', '%d Lessons', $lessons, 'tutorblock' ) ),
					(int) $lessons
				)
			);
		}

		// Total enrolled students.
		$enrolled = tutor_utils()->get_total_enrolled( $course_id );
		if ( $enrolled > 0 ) {
			$items[] = sprintf(
				'<span class="tutorblock-meta-item">👥 %s</span>',
				sprintf(
					/* translators: %s: formatted number of students */
					esc_html__( '%s Students', 'tutorblock' ),
					number_format_i18n( (int) $enrolled )
				)
			);
		}
	}

	if ( empty( $items ) ) {
		return '';
	}

	return '<div class="tutorblock-card-meta">' . implode( '', $items ) . '</div>';
}

/**
 * Inline CSS variables for color customization.
 *
 * @param string $primary_color    Hex color.
 * @param string $secondary_color  Optional secondary color.
 * @return string Style attribute string.
 */
function tutorblock_color_style( string $primary_color, string $secondary_color = '' ): string {
	$style = '--tutorblock-primary:' . esc_attr( $primary_color ) . ';';
	if ( $secondary_color ) {
		$style .= '--tutorblock-secondary:' . esc_attr( $secondary_color ) . ';';
	}
	return $style;
}
