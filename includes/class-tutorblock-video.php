<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Video-focused LMS and course display features.
 */
class TutorBlock_Video {

	/**
	 * Singleton instance.
	 *
	 * @var TutorBlock_Video|null
	 */
	protected static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return TutorBlock_Video
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Register frontend shortcodes.
	 *
	 * @return void
	 */
	public function register_shortcodes() {
		add_shortcode( 'tutorblock_video_lesson', array( $this, 'render_video_lesson' ) );
		add_shortcode( 'tutorblock_course_progress', array( $this, 'render_course_progress' ) );
		add_shortcode( 'tutorblock_course_grid', array( $this, 'render_course_grid' ) );
		add_shortcode( 'tutorblock_course_masonry', array( $this, 'render_course_masonry' ) );
		add_shortcode( 'tutorblock_youtube_shorts', array( $this, 'render_youtube_shorts' ) );
	}

	/**
	 * Register REST endpoints.
	 *
	 * @return void
	 */
	public function register_rest_routes() {
		register_rest_route(
			'tutorblock/v1',
			'/progress',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_progress' ),
				'permission_callback' => array( $this, 'user_can_track_progress' ),
			)
		);
	}

	public function user_can_track_progress() {
		return is_user_logged_in();
	}

	public function save_progress( WP_REST_Request $request ) {
		$user_id   = get_current_user_id();
		$lesson_id = absint( $request->get_param( 'lessonId' ) );
		$progress  = min( 100, max( 0, (float) $request->get_param( 'progress' ) ) );

		if ( ! $lesson_id ) {
			return new WP_REST_Response( array( 'message' => __( 'Lesson ID is required.', 'tutorblock' ) ), 400 );
		}

		$stored = get_user_meta( $user_id, 'tutorblock_progress', true );
		$stored = is_array( $stored ) ? $stored : array();
		$stored[ $lesson_id ] = array(
			'progress'   => $progress,
			'updated_at' => current_time( 'mysql' ),
		);
		update_user_meta( $user_id, 'tutorblock_progress', $stored );

		return new WP_REST_Response( array( 'lessonId' => $lesson_id, 'progress' => $progress ), 200 );
	}

	public function render_video_lesson( $atts ) {
		$atts = shortcode_atts(
			array(
				'src'                  => '',
				'poster'               => '',
				'lesson_id'            => get_the_ID(),
				'title'                => __( 'Video Lesson', 'tutorblock' ),
				'accent_color'         => '#1d4ed8',
				'padding'              => 16,
				'border_radius'        => 8,
				'autoplay'             => false,
				'muted'                => false,
				'show_download_button' => false,
			),
			$atts,
			'tutorblock_video_lesson'
		);

		if ( empty( $atts['src'] ) ) {
			return '<p>' . esc_html__( 'Please provide a video URL with the src attribute.', 'tutorblock' ) . '</p>';
		}

		$accent_color  = sanitize_hex_color( $atts['accent_color'] ) ?: '#1d4ed8';
		$padding       = max( 0, min( 60, absint( $atts['padding'] ) ) );
		$border_radius = max( 0, min( 40, absint( $atts['border_radius'] ) ) );
		$autoplay      = filter_var( $atts['autoplay'], FILTER_VALIDATE_BOOLEAN );
		$muted         = filter_var( $atts['muted'], FILTER_VALIDATE_BOOLEAN );
		$show_download = filter_var( $atts['show_download_button'], FILTER_VALIDATE_BOOLEAN );
		$style         = sprintf( '--tutorblock-accent:%1$s;--tutorblock-padding:%2$dpx;--tutorblock-radius:%3$dpx;', esc_attr( $accent_color ), $padding, $border_radius );

		ob_start();
		?>
		<div class="tutorblock-video" data-lesson-id="<?php echo esc_attr( $atts['lesson_id'] ); ?>" style="<?php echo esc_attr( $style ); ?>">
			<h3 class="tutorblock-video__title"><?php echo esc_html( $atts['title'] ); ?></h3>
			<video class="tutorblock-video__player" controls preload="metadata" <?php echo $autoplay ? 'autoplay' : ''; ?> <?php echo $muted ? 'muted' : ''; ?> poster="<?php echo esc_url( $atts['poster'] ); ?>">
				<source src="<?php echo esc_url( $atts['src'] ); ?>" type="video/mp4">
			</video>
			<div class="tutorblock-video__status" aria-live="polite"><?php esc_html_e( 'Progress: 0%', 'tutorblock' ); ?></div>
			<?php if ( $show_download ) : ?>
				<p class="tutorblock-video__download"><a href="<?php echo esc_url( $atts['src'] ); ?>" download><?php esc_html_e( 'Download lesson video', 'tutorblock' ); ?></a></p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_course_progress( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'label'        => __( 'Average video completion:', 'tutorblock' ),
				'accent_color' => '#1d4ed8',
			),
			$atts,
			'tutorblock_course_progress'
		);

		if ( ! is_user_logged_in() ) {
			return '<p>' . esc_html__( 'Please log in to view course progress.', 'tutorblock' ) . '</p>';
		}

		$stored = get_user_meta( get_current_user_id(), 'tutorblock_progress', true );
		$stored = is_array( $stored ) ? $stored : array();
		if ( empty( $stored ) ) {
			return '<p>' . esc_html__( 'No lesson progress recorded yet.', 'tutorblock' ) . '</p>';
		}

		$sum = array_sum( array_map( static function( $item ) { return isset( $item['progress'] ) ? (float) $item['progress'] : 0; }, $stored ) );
		$avg = round( $sum / count( $stored ), 1 );
		$color = sanitize_hex_color( $atts['accent_color'] ) ?: '#1d4ed8';

		return sprintf( '<div class="tutorblock-progress" style="border-left-color:%1$s;"><strong>%2$s</strong> %3$s%%</div>', esc_attr( $color ), esc_html( $atts['label'] ), esc_html( (string) $avg ) );
	}

	public function render_course_grid( $atts ) {
		$atts = shortcode_atts(
			array(
				'count'    => 6,
				'columns'  => 3,
				'cta_text' => __( 'View Course', 'tutorblock' ),
			),
			$atts,
			'tutorblock_course_grid'
		);

		return $this->render_course_cards( $atts, 'grid' );
	}

	public function render_course_masonry( $atts ) {
		$atts = shortcode_atts(
			array(
				'count'    => 8,
				'columns'  => 3,
				'cta_text' => __( 'View Course', 'tutorblock' ),
			),
			$atts,
			'tutorblock_course_masonry'
		);

		return $this->render_course_cards( $atts, 'masonry' );
	}

	private function render_course_cards( $atts, $layout ) {
		$count = max( 1, min( 24, absint( $atts['count'] ) ) );
		$cols  = max( 1, min( 4, absint( $atts['columns'] ) ) );

		$q = new WP_Query(
			array(
				'post_type'      => 'courses',
				'posts_per_page' => $count,
				'post_status'    => 'publish',
			)
		);

		if ( ! $q->have_posts() ) {
			return '<p>' . esc_html__( 'No courses found.', 'tutorblock' ) . '</p>';
		}

		ob_start();
		?>
		<div class="tutorblock-courses tutorblock-courses--<?php echo esc_attr( $layout ); ?> columns-<?php echo esc_attr( $cols ); ?>">
			<?php while ( $q->have_posts() ) : $q->the_post(); ?>
				<article class="tutorblock-course-card">
					<a class="tutorblock-course-card__thumb" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large' ); } ?>
					</a>
					<h3 class="tutorblock-course-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<div class="tutorblock-course-card__excerpt"><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 18 ) ); ?></div>
					<a class="tutorblock-course-card__cta" href="<?php the_permalink(); ?>"><?php echo esc_html( $atts['cta_text'] ); ?></a>
				</article>
			<?php endwhile; ?>
		</div>
		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}

	public function render_youtube_shorts( $atts ) {
		$atts = shortcode_atts(
			array(
				'ids'   => '',
				'title' => __( 'Lesson Shorts', 'tutorblock' ),
			),
			$atts,
			'tutorblock_youtube_shorts'
		);

		$ids = array_filter( array_map( 'trim', explode( ',', (string) $atts['ids'] ) ) );
		if ( empty( $ids ) ) {
			return '<p>' . esc_html__( 'Provide YouTube Short IDs in the ids attribute.', 'tutorblock' ) . '</p>';
		}

		ob_start();
		?>
		<div class="tutorblock-shorts-wrap">
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<div class="tutorblock-shorts">
				<?php foreach ( $ids as $id ) : ?>
					<div class="tutorblock-shorts__item">
						<iframe src="<?php echo esc_url( 'https://www.youtube.com/embed/' . rawurlencode( $id ) ); ?>" title="YouTube short" allowfullscreen loading="lazy"></iframe>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
