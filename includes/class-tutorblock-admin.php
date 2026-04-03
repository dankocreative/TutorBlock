<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin UX and settings.
 */
class TutorBlock_Admin {

	/**
	 * Singleton instance.
	 *
	 * @var TutorBlock_Admin|null
	 */
	protected static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return TutorBlock_Admin
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
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register admin page.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_options_page(
			__( 'TutorBlock Settings', 'tutorblock' ),
			__( 'TutorBlock', 'tutorblock' ),
			'manage_options',
			'tutorblock-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'tutorblock_settings_group',
			'tutorblock_settings',
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'tutorblock_video_section',
			__( 'Video Experience', 'tutorblock' ),
			'__return_false',
			'tutorblock-settings'
		);

		add_settings_field(
			'autoplay_next',
			__( 'Autoplay Next Lesson', 'tutorblock' ),
			array( $this, 'render_checkbox_field' ),
			'tutorblock-settings',
			'tutorblock_video_section',
			array(
				'key'         => 'autoplay_next',
				'description' => __( 'Automatically continue to the next lesson after video completion.', 'tutorblock' ),
			)
		);
	}

	/**
	 * Sanitize settings before save.
	 *
	 * @param array $values Raw values.
	 * @return array
	 */
	public function sanitize_settings( $values ) {
		return array(
			'autoplay_next' => ! empty( $values['autoplay_next'] ) ? 1 : 0,
		);
	}

	/**
	 * Render checkbox field.
	 *
	 * @param array $args Field args.
	 * @return void
	 */
	public function render_checkbox_field( $args ) {
		$options = get_option( 'tutorblock_settings', array() );
		$key     = $args['key'];
		$value   = ! empty( $options[ $key ] );
		?>
		<label>
			<input type="checkbox" name="tutorblock_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( $value ); ?> />
			<?php echo esc_html( $args['description'] ); ?>
		</label>
		<?php
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'TutorBlock Settings', 'tutorblock' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'tutorblock_settings_group' );
				do_settings_sections( 'tutorblock-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
