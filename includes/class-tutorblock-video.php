<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Video-focused LMS features.
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

	/**
	 * Permission callback for progress route.
	 *
	 * @return bool
	 */
	public function user_can_track_progress() {
		return is_user_logged_in();
	}

	/**
	 * Save learner video progress.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function save_progress( WP_REST_Request $request ) {
		$user_id   = get_current_user_id();
		$lesson_id = absint( $request->get_param( 'lessonId' ) );
		$progress  = min( 100, max( 0, (float) $request->get_param( 'progress' ) ) );

		if ( ! $lesson_id ) {
			return new WP_REST_Response(
				array( 'message' => __( 'Lesson ID is required.', 'tutorblock' ) ),
				400
			);
		}

		$stored = get_user_meta( $user_id, 'tutorblock_progress', true );
		$stored = is_array( $stored ) ? $stored : array();

		$stored[ $lesson_id ] = array(
			'progress'   => $progress,
			'updated_at' => current_time( 'mysql' ),
		);

		update_user_meta( $user_id, 'tutorblock_progress', $stored );

		return new WP_REST_Response(
			array(
				'lessonId' => $lesson_id,
				'progress' => $progress,
			),
			200
		);
	}

	/**
	 * Render video lesson player and controls.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public function render_video_lesson( $atts ) {
		$atts = shortcode_atts(
			array(
				'src'       => '',
				'lesson_id' => get_the_ID(),
				'title'     => __( 'Video Lesson', 'tutorblock' ),
			),
			$atts,
			'tutorblock_video_lesson'
		);

		if ( empty( $atts['src'] ) ) {
			return '<p>' . esc_html__( 'Please provide a video URL with the src attribute.', 'tutorblock' ) . '</p>';
		}

		ob_start();
		?>
		<div class="tutorblock-video" data-lesson-id="<?php echo esc_attr( $atts['lesson_id'] ); ?>">
			<h3 class="tutorblock-video__title"><?php echo esc_html( $atts['title'] ); ?></h3>
			<video class="tutorblock-video__player" controls preload="metadata">
				<source src="<?php echo esc_url( $atts['src'] ); ?>" type="video/mp4">
				<?php esc_html_e( 'Your browser does not support the video tag.', 'tutorblock' ); ?>
			</video>
			<div class="tutorblock-video__status" aria-live="polite">
				<?php esc_html_e( 'Progress: 0%', 'tutorblock' ); ?>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * Render aggregate progress.
	 *
	 * @return string
	 */
	public function render_course_progress() {
		if ( ! is_user_logged_in() ) {
			return '<p>' . esc_html__( 'Please log in to view course progress.', 'tutorblock' ) . '</p>';
		}

		$stored = get_user_meta( get_current_user_id(), 'tutorblock_progress', true );
		$stored = is_array( $stored ) ? $stored : array();

		if ( empty( $stored ) ) {
			return '<p>' . esc_html__( 'No lesson progress recorded yet.', 'tutorblock' ) . '</p>';
		}

		$sum = array_sum( array_map( static function( $item ) {
			return isset( $item['progress'] ) ? (float) $item['progress'] : 0;
		}, $stored ) );
		$avg = round( $sum / count( $stored ), 1 );

		return sprintf(
			'<div class="tutorblock-progress"><strong>%s</strong> %s%%</div>',
			esc_html__( 'Average video completion:', 'tutorblock' ),
			esc_html( (string) $avg )
		);
	}
}
