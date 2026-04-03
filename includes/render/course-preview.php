<?php
/**
 * Render callback: tutorblock/course-preview
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
 * Render the single course preview card.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Inner content (unused).
 * @return string
 */
function tutorblock_render_course_preview( array $attributes, string $content = '' ): string {
	$course_id      = max( 0, (int) ( $attributes['courseId'] ?? 0 ) );
	$layout         = in_array( $attributes['layout'] ?? 'horizontal', array( 'horizontal', 'vertical', 'hero', 'cinematic' ), true )
		? $attributes['layout'] : 'horizontal';
	$img_position   = in_array( $attributes['imagePosition'] ?? 'left', array( 'left', 'right' ), true )
		? $attributes['imagePosition'] : 'left';
	$show_desc      = (bool) ( $attributes['showDescription'] ?? true );
	$show_instructor = (bool) ( $attributes['showInstructor'] ?? true );
	$show_stats     = (bool) ( $attributes['showStats'] ?? true );
	$show_rating    = (bool) ( $attributes['showRating'] ?? true );
	$show_reqs      = (bool) ( $attributes['showRequirements'] ?? false );
	$show_curriculum = (bool) ( $attributes['showCurriculum'] ?? false );
	$show_enroll    = (bool) ( $attributes['showEnrollButton'] ?? true );
	$btn_text       = sanitize_text_field( $attributes['enrollButtonText'] ?? __( 'Enroll Now', 'tutorblock' ) );
	$primary_color  = sanitize_hex_color( $attributes['primaryColor'] ?? '#2563eb' ) ?: '#2563eb';
	$accent_color   = sanitize_hex_color( $attributes['accentColor'] ?? '#f59e0b' ) ?: '#f59e0b';

	if ( $course_id <= 0 ) {
		return '<div class="tutorblock-no-courses">' . esc_html__( 'Please select a course in the block settings.', 'tutorblock' ) . '</div>';
	}

	$course = get_post( $course_id );
	if ( ! $course || 'courses' !== $course->post_type || 'publish' !== $course->post_status ) {
		return '<div class="tutorblock-no-courses">' . esc_html__( 'Course not found or not published.', 'tutorblock' ) . '</div>';
	}

	$title         = get_the_title( $course );
	$permalink     = get_permalink( $course );
	$thumbnail_url = get_the_post_thumbnail_url( $course, 'large' ) ?: '';
	$description   = $show_desc ? get_the_excerpt( $course ) : '';
	if ( ! $description && $show_desc ) {
		$description = wp_trim_words( $course->post_content, 40 );
	}

	// Category.
	$categories = get_the_terms( $course, 'course-category' );
	$cat_name   = ( $categories && ! is_wp_error( $categories ) ) ? esc_html( $categories[0]->name ) : '';

	// Rating.
	$rating_html = $show_rating ? tutorblock_get_rating_html( $course_id ) : '';

	// Price.
	$price_html = tutorblock_get_price_html( $course_id );

	// Instructor.
	$instructor_html = '';
	if ( $show_instructor ) {
		$instructors = array();
		if ( function_exists( 'tutor_utils' ) ) {
			$instructors = tutor_utils()->get_instructors_by_course( $course_id );
		}
		if ( empty( $instructors ) ) {
			$instructors = array( get_user_by( 'id', $course->post_author ) );
		}
		if ( $instructors && $instructors[0] ) {
			$inst       = $instructors[0];
			$avatar_url = get_avatar_url( $inst->ID, array( 'size' => 56 ) );
			$inst_name  = esc_html( $inst->display_name );
			$inst_bio   = esc_html( get_user_meta( $inst->ID, 'description', true ) );
			$instructor_html = sprintf(
				'<div class="tutorblock-preview-instructor"><img src="%s" alt="%s" width="40" height="40"><div><div class="tutorblock-instructor-name">%s</div>%s</div></div>',
				esc_url( $avatar_url ),
				esc_attr( $inst_name ),
				$inst_name,
				$inst_bio ? '<div class="tutorblock-instructor-role">' . wp_trim_words( $inst_bio, 12 ) . '</div>' : ''
			);
		}
	}

	// Stats.
	$stats_html = '';
	if ( $show_stats && function_exists( 'tutor_utils' ) ) {
		$enrolled = tutor_utils()->get_total_enrolled( $course_id );
		$lessons  = tutor_utils()->get_lesson_count_by_course( $course_id );
		$duration = get_post_meta( $course_id, '_course_duration', true );
		$level    = get_post_meta( $course_id, '_tutor_course_level', true );

		$stats_items = array();
		if ( $enrolled ) {
			$stats_items[] = sprintf(
				'<div class="tutorblock-stat"><span class="tutorblock-stat-value">%s</span><span class="tutorblock-stat-label">%s</span></div>',
				number_format_i18n( (int) $enrolled ),
				esc_html__( 'Students', 'tutorblock' )
			);
		}
		if ( $lessons ) {
			$stats_items[] = sprintf(
				'<div class="tutorblock-stat"><span class="tutorblock-stat-value">%d</span><span class="tutorblock-stat-label">%s</span></div>',
				(int) $lessons,
				esc_html__( 'Lessons', 'tutorblock' )
			);
		}
		if ( $duration ) {
			$stats_items[] = sprintf(
				'<div class="tutorblock-stat"><span class="tutorblock-stat-value">%s</span><span class="tutorblock-stat-label">%s</span></div>',
				esc_html( $duration ),
				esc_html__( 'Duration', 'tutorblock' )
			);
		}
		if ( $level ) {
			$stats_items[] = sprintf(
				'<div class="tutorblock-stat"><span class="tutorblock-stat-value">%s</span><span class="tutorblock-stat-label">%s</span></div>',
				esc_html( ucfirst( $level ) ),
				esc_html__( 'Level', 'tutorblock' )
			);
		}
		if ( $stats_items ) {
			$stats_html = '<div class="tutorblock-preview-stats">' . implode( '', $stats_items ) . '</div>';
		}
	}

	// Requirements.
	$requirements_html = '';
	if ( $show_reqs ) {
		$reqs = get_post_meta( $course_id, '_tutor_course_requirements', true );
		if ( $reqs && is_array( $reqs ) ) {
			$items = array_map(
				function ( $req ) {
					return '<li>' . esc_html( $req ) . '</li>';
				},
				array_filter( $reqs )
			);
			if ( $items ) {
				$requirements_html = '<div class="tutorblock-preview-requirements"><h4>' . esc_html__( 'Requirements', 'tutorblock' ) . '</h4><ul>' . implode( '', $items ) . '</ul></div>';
			}
		}
	}

	// Curriculum preview (topics).
	$curriculum_html = '';
	if ( $show_curriculum ) {
		$topics = get_posts( array(
			'post_type'      => 'topics',
			'post_parent'    => $course_id,
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		) );
		if ( $topics ) {
			$topic_items = '';
			foreach ( $topics as $topic ) {
				$lesson_count = get_posts( array(
					'post_type'      => 'lesson',
					'post_parent'    => $topic->ID,
					'posts_per_page' => -1,
					'fields'         => 'ids',
				) );
				$topic_items .= sprintf(
					'<div class="tutorblock-topic"><div class="tutorblock-topic-title">%s <span style="color:#9ca3af;font-weight:400">(%d %s)</span></div></div>',
					esc_html( $topic->post_title ),
					count( $lesson_count ),
					esc_html__( 'lessons', 'tutorblock' )
				);
			}
			$curriculum_html = '<div class="tutorblock-preview-curriculum"><h4>' . esc_html__( 'Curriculum Preview', 'tutorblock' ) . '</h4>' . $topic_items . '</div>';
		}
	}

	// Enroll button.
	$enroll_btn = '';
	if ( $show_enroll ) {
		$enroll_btn = sprintf(
			'<a href="%s" class="tutorblock-enroll-btn" style="background:%s">%s</a>',
			esc_url( $permalink ),
			esc_attr( $primary_color ),
			esc_html( $btn_text )
		);
	}

	$color_style = tutorblock_color_style( $primary_color, $accent_color );

	// ── Cinematic layout: image fills card, text+button overlaid ──────────────
	if ( 'cinematic' === $layout ) {
		ob_start();
		?>
		<div class="tutorblock-course-preview is-layout-cinematic"
			style="<?php echo esc_attr( $color_style ); ?>">

			<?php if ( $thumbnail_url ) : ?>
				<div class="tutorblock-preview-cinematic-bg"
					style="background-image:url(<?php echo esc_url( $thumbnail_url ); ?>);"
					role="img"
					aria-label="<?php echo esc_attr( $title ); ?>">
				</div>
			<?php else : ?>
				<div class="tutorblock-preview-cinematic-bg tutorblock-preview-cinematic-bg--empty"></div>
			<?php endif; ?>

			<div class="tutorblock-preview-cinematic-overlay"></div>

			<div class="tutorblock-preview-cinematic-content">
				<?php if ( $cat_name ) : ?>
					<div class="tutorblock-preview-category"><?php echo esc_html( $cat_name ); ?></div>
				<?php endif; ?>

				<h2 class="tutorblock-preview-title">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
				</h2>

				<?php echo $rating_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php if ( $description ) : ?>
					<p class="tutorblock-preview-description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>

				<div class="tutorblock-preview-actions">
					<div class="tutorblock-preview-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php echo $enroll_btn; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>

		</div>
		<?php
		return ob_get_clean();
	}

	ob_start();
	?>
	<div class="tutorblock-course-preview is-layout-<?php echo esc_attr( $layout ); ?><?php echo 'horizontal' === $layout ? ' image-' . esc_attr( $img_position ) : ''; ?>"
		style="<?php echo esc_attr( $color_style ); ?>">

		<?php if ( $thumbnail_url ) : ?>
			<div class="tutorblock-preview-image">
				<img src="<?php echo esc_url( $thumbnail_url ); ?>"
					 alt="<?php echo esc_attr( $title ); ?>"
					 loading="lazy" />
			</div>
		<?php endif; ?>

		<div class="tutorblock-preview-content">
			<?php if ( $cat_name ) : ?>
				<div class="tutorblock-preview-category" style="color:<?php echo esc_attr( $primary_color ); ?>"><?php echo $cat_name; ?></div>
			<?php endif; ?>

			<h2 class="tutorblock-preview-title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
			</h2>

			<?php echo $rating_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<?php if ( $description ) : ?>
				<p class="tutorblock-preview-description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>

			<?php echo $instructor_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $stats_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $requirements_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo $curriculum_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<div class="tutorblock-preview-actions">
				<div class="tutorblock-preview-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php echo $enroll_btn; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}
