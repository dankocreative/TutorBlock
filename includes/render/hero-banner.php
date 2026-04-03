<?php
/**
 * Render callback: tutorblock/hero-banner
 *
 * Full-bleed cinematic hero section with background image/video,
 * overlay gradient, bold headline, and an overlaid CTA button.
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
 * Render the hero banner block.
 *
 * @param  array  $attributes Block attributes.
 * @param  string $content    Inner content (unused – server-side render).
 * @return string
 */
function tutorblock_render_hero_banner( array $attributes, string $content = '' ): string {

	// ── Attributes ────────────────────────────────────────────────────────────
	$bg_image_url       = esc_url_raw( $attributes['backgroundImageUrl'] ?? '' );
	$video_url          = esc_url_raw( $attributes['videoUrl'] ?? '' );
	$overlay_color      = sanitize_hex_color( $attributes['overlayColor'] ?? '#000000' ) ?: '#000000';
	$overlay_opacity    = max( 0, min( 95, (int) ( $attributes['overlayOpacity'] ?? 50 ) ) );
	$headline           = sanitize_text_field( $attributes['headline'] ?? __( 'Transform Your Knowledge', 'tutorblock' ) );
	$subheadline        = sanitize_textarea_field( $attributes['subheadline'] ?? '' );
	$tagline            = sanitize_text_field( $attributes['tagline'] ?? '' );
	$btn_text           = sanitize_text_field( $attributes['buttonText'] ?? __( 'Enroll Now', 'tutorblock' ) );
	$btn_url            = esc_url_raw( $attributes['buttonUrl'] ?? '' );
	$btn_color          = sanitize_hex_color( $attributes['buttonColor'] ?? '#ffffff' ) ?: '#ffffff';
	$btn_text_color     = sanitize_hex_color( $attributes['buttonTextColor'] ?? '#000000' ) ?: '#000000';
	$show_sec_btn       = (bool) ( $attributes['showSecondaryButton'] ?? false );
	$sec_btn_text       = sanitize_text_field( $attributes['secondaryButtonText'] ?? __( 'Watch Preview', 'tutorblock' ) );
	$sec_btn_url        = esc_url_raw( $attributes['secondaryButtonUrl'] ?? '' );
	$text_align         = in_array( $attributes['textAlign'] ?? 'center', array( 'left', 'center', 'right' ), true )
		? $attributes['textAlign'] : 'center';
	$text_color         = sanitize_hex_color( $attributes['textColor'] ?? '#ffffff' ) ?: '#ffffff';
	$min_height         = max( 300, (int) ( $attributes['minHeight'] ?? 600 ) );
	$content_width      = in_array( $attributes['contentWidth'] ?? 'medium', array( 'narrow', 'medium', 'wide', 'full' ), true )
		? $attributes['contentWidth'] : 'medium';
	$course_id          = max( 0, (int) ( $attributes['courseId'] ?? 0 ) );
	$show_course_stats  = (bool) ( $attributes['showCourseStats'] ?? false );
	$gradient_dir       = in_array( $attributes['gradientDirection'] ?? 'center', array( 'center', 'bottom', 'left', 'solid' ), true )
		? $attributes['gradientDirection'] : 'center';

	// ── Background ────────────────────────────────────────────────────────────
	$bg_style = '';
	if ( $bg_image_url ) {
		$bg_style = 'background-image:url(' . esc_attr( $bg_image_url ) . ');';
	}

	// ── Overlay ───────────────────────────────────────────────────────────────
	// Convert % opacity to 0-1 for rgba().
	$alpha          = round( $overlay_opacity / 100, 2 );
	$overlay_rgba   = tutorblock_hex_to_rgba( $overlay_color, $alpha );
	$overlay_style  = '';

	if ( 'solid' === $gradient_dir ) {
		$overlay_style = "background:rgba({$overlay_rgba},{$alpha});";
	}
	// For gradient variants the CSS class handles it; inline only adds a base tint.

	// ── Course data ───────────────────────────────────────────────────────────
	$stats_html = '';
	if ( $show_course_stats && $course_id > 0 && function_exists( 'tutor_utils' ) ) {
		$course = get_post( $course_id );
		if ( $course && 'courses' === $course->post_type ) {
			$enrolled = (int) tutor_utils()->get_total_enrolled( $course_id );
			$lessons  = (int) tutor_utils()->get_lesson_count_by_course( $course_id );
			$rating   = tutorblock_get_rating_html( $course_id );

			$stats_items = array();
			if ( $enrolled ) {
				$stats_items[] = '<div class="tutorblock-hero-stat"><span class="tutorblock-hero-stat-value">' . number_format_i18n( $enrolled ) . '</span><span class="tutorblock-hero-stat-label">' . esc_html__( 'Students', 'tutorblock' ) . '</span></div>';
			}
			if ( $lessons ) {
				$stats_items[] = '<div class="tutorblock-hero-stat"><span class="tutorblock-hero-stat-value">' . (int) $lessons . '</span><span class="tutorblock-hero-stat-label">' . esc_html__( 'Lessons', 'tutorblock' ) . '</span></div>';
			}
			if ( $stats_items ) {
				$stats_html = '<div class="tutorblock-hero-banner__stats">' . implode( '', $stats_items ) . '</div>';
			}
		}
	}

	// ── Video iframe ──────────────────────────────────────────────────────────
	$video_html = '';
	if ( $video_url ) {
		$embed_url = tutorblock_get_video_embed_url( $video_url, true );
		if ( $embed_url ) {
			$video_html = '<div class="tutorblock-hero-banner__video-bg" aria-hidden="true"><iframe src="' . esc_url( $embed_url ) . '" allow="autoplay; muted" title="" frameborder="0"></iframe></div>';
		}
	}

	// ── Secondary button – detect if it's a video for lightbox ───────────────
	$sec_btn_html = '';
	if ( $show_sec_btn && $sec_btn_text ) {
		$is_video_link = $sec_btn_url && tutorblock_is_video_url( $sec_btn_url );
		$data_attr     = $is_video_link ? ' data-tb-video="' . esc_attr( $sec_btn_url ) . '"' : '';
		$play_icon     = '<svg class="tutorblock-play-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>';
		$sec_btn_html  = sprintf(
			'<a href="%s" class="tutorblock-hero-btn is-secondary" style="color:%s"%s>%s%s</a>',
			$sec_btn_url ? esc_url( $sec_btn_url ) : '#',
			esc_attr( $text_color ),
			$data_attr,
			$play_icon,
			esc_html( $sec_btn_text )
		);
	}

	// ── Primary button ────────────────────────────────────────────────────────
	$primary_btn_html = '';
	if ( $btn_text && $btn_url ) {
		$primary_btn_html = sprintf(
			'<a href="%s" class="tutorblock-hero-btn" style="background:%s;color:%s">%s</a>',
			esc_url( $btn_url ),
			esc_attr( $btn_color ),
			esc_attr( $btn_text_color ),
			esc_html( $btn_text )
		);
	}

	ob_start();
	?>
	<div class="tutorblock-hero-banner" style="min-height:<?php echo (int) $min_height; ?>px;color:<?php echo esc_attr( $text_color ); ?>">

		<?php if ( $video_html ) : ?>
			<?php echo $video_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php elseif ( $bg_image_url ) : ?>
			<div class="tutorblock-hero-banner__bg has-image" style="<?php echo esc_attr( $bg_style ); ?>"></div>
		<?php else : ?>
			<div class="tutorblock-hero-banner__bg" style="background:#111827;"></div>
		<?php endif; ?>

		<?php if ( 'solid' !== $gradient_dir ) : ?>
			<div class="tutorblock-hero-banner__overlay gradient-<?php echo esc_attr( $gradient_dir ); ?>"
				style="opacity:<?php echo esc_attr( $alpha ); ?>"></div>
		<?php else : ?>
			<div class="tutorblock-hero-banner__overlay gradient-solid"
				style="background-color:<?php echo esc_attr( $overlay_color ); ?>;opacity:<?php echo esc_attr( $alpha ); ?>"></div>
		<?php endif; ?>

		<div class="tutorblock-hero-banner__content align-<?php echo esc_attr( $text_align ); ?>">
			<div class="tutorblock-hero-banner__inner width-<?php echo esc_attr( $content_width ); ?>">

				<?php if ( $tagline ) : ?>
					<span class="tutorblock-hero-banner__tagline"><?php echo esc_html( $tagline ); ?></span>
				<?php endif; ?>

				<?php if ( $headline ) : ?>
					<h2 class="tutorblock-hero-banner__headline"><?php echo esc_html( $headline ); ?></h2>
				<?php endif; ?>

				<?php if ( $subheadline ) : ?>
					<p class="tutorblock-hero-banner__subheadline"><?php echo esc_html( $subheadline ); ?></p>
				<?php endif; ?>

				<?php echo $stats_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<?php if ( $primary_btn_html || $sec_btn_html ) : ?>
					<div class="tutorblock-hero-banner__actions">
						<?php echo $primary_btn_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo $sec_btn_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php // Video lightbox modal ?>
		<div class="tutorblock-video-modal" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Video preview', 'tutorblock' ); ?>">
			<div class="tutorblock-video-modal__inner">
				<button class="tutorblock-video-modal__close" aria-label="<?php echo esc_attr__( 'Close video', 'tutorblock' ); ?>">&times;</button>
				<div class="tutorblock-video-modal__embed"></div>
			</div>
		</div>

	</div>
	<?php

	return ob_get_clean();
}

/**
 * Convert hex colour to comma-separated RGB components (for rgba()).
 *
 * @param string $hex     Hex colour string.
 * @param float  $alpha   Alpha value (unused – kept for compat).
 * @return string "r,g,b"
 */
function tutorblock_hex_to_rgba( string $hex, float $alpha = 1.0 ): string {
	$hex = ltrim( $hex, '#' );
	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	return "{$r},{$g},{$b}";
}

/**
 * Return an embeddable URL for YouTube / Vimeo, optimised for background play.
 *
 * @param string $url         Original video URL.
 * @param bool   $background  Whether this is a muted background video.
 * @return string|null
 */
function tutorblock_get_video_embed_url( string $url, bool $background = false ): ?string {
	// YouTube.
	if ( preg_match( '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		$params = $background
			? 'autoplay=1&mute=1&loop=1&playlist=' . $m[1] . '&controls=0&showinfo=0&rel=0&modestbranding=1'
			: 'autoplay=1&rel=0&modestbranding=1';
		return 'https://www.youtube.com/embed/' . $m[1] . '?' . $params;
	}

	// Vimeo.
	if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
		$params = $background
			? 'autoplay=1&muted=1&loop=1&background=1'
			: 'autoplay=1';
		return 'https://player.vimeo.com/video/' . $m[1] . '?' . $params;
	}

	// Direct video URL: return as-is (handled via <video> tag on frontend).
	if ( preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $url ) ) {
		return $url;
	}

	return null;
}

/**
 * Detect whether a URL is a video URL (YouTube / Vimeo / direct).
 *
 * @param string $url URL to test.
 * @return bool
 */
function tutorblock_is_video_url( string $url ): bool {
	return (bool) preg_match(
		'/(?:youtube\.com|youtu\.be|vimeo\.com|\.(mp4|webm|ogg)(\?.*)?$)/i',
		$url
	);
}
