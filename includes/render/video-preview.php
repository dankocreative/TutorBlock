<?php
/**
 * Render callback: tutorblock/video-preview
 *
 * Video thumbnail with play button overlay, optional sign-up button overlay,
 * and video lightbox. Supports YouTube, Vimeo, and self-hosted video.
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
 * Render the video preview block.
 *
 * @param  array  $attributes Block attributes.
 * @param  string $content    Inner content (unused).
 * @return string
 */
function tutorblock_render_video_preview( array $attributes, string $content = '' ): string {

	// ── Attributes ────────────────────────────────────────────────────────────
	$video_url        = esc_url_raw( $attributes['videoUrl'] ?? '' );
	$thumbnail_url    = esc_url_raw( $attributes['thumbnailUrl'] ?? '' );
	$course_id        = max( 0, (int) ( $attributes['courseId'] ?? 0 ) );
	$title            = sanitize_text_field( $attributes['title'] ?? '' );
	$description      = sanitize_textarea_field( $attributes['description'] ?? '' );
	$show_overlay_btn = (bool) ( $attributes['showOverlayButton'] ?? true );
	$overlay_btn_text = sanitize_text_field( $attributes['overlayButtonText'] ?? __( 'Sign Up Free', 'tutorblock' ) );
	$overlay_btn_url  = esc_url_raw( $attributes['overlayButtonUrl'] ?? '' );
	$overlay_btn_bg   = sanitize_hex_color( $attributes['overlayButtonColor'] ?? '#ffffff' ) ?: '#ffffff';
	$overlay_btn_txt  = sanitize_hex_color( $attributes['overlayButtonTextColor'] ?? '#000000' ) ?: '#000000';
	$show_play        = (bool) ( $attributes['showPlayButton'] ?? true );
	$play_btn_color   = sanitize_hex_color( $attributes['playButtonColor'] ?? '#ffffff' ) ?: '#ffffff';
	$aspect_ratio     = in_array( $attributes['aspectRatio'] ?? '16/9', array( '16/9', '4/3', '21/9', '1/1' ), true )
		? $attributes['aspectRatio'] : '16/9';
	$overlay_color    = sanitize_hex_color( $attributes['overlayColor'] ?? '#000000' ) ?: '#000000';
	$overlay_opacity  = max( 0, min( 80, (int) ( $attributes['overlayOpacity'] ?? 30 ) ) );
	$caption_position = in_array( $attributes['captionPosition'] ?? 'below', array( 'below', 'overlay-bottom', 'overlay-center', 'none' ), true )
		? $attributes['captionPosition'] : 'below';
	$border_radius    = max( 0, min( 32, (int) ( $attributes['borderRadius'] ?? 12 ) ) );
	$autoplay_click   = (bool) ( $attributes['autoplayOnClick'] ?? true );

	// ── Resolve thumbnail ─────────────────────────────────────────────────────
	if ( ! $thumbnail_url && $course_id > 0 ) {
		$course = get_post( $course_id );
		if ( $course && 'courses' === $course->post_type ) {
			$thumbnail_url = get_the_post_thumbnail_url( $course, 'large' ) ?: '';
			if ( ! $title ) {
				$title = get_the_title( $course );
			}
			if ( ! $overlay_btn_url && $course_id > 0 ) {
				$overlay_btn_url = get_permalink( $course );
			}
		}
	}

	// ── No content fallback ───────────────────────────────────────────────────
	if ( ! $thumbnail_url && ! $video_url ) {
		return '<div class="tutorblock-no-courses">' .
			esc_html__( 'Add a video URL or select a thumbnail in the block settings.', 'tutorblock' ) .
			'</div>';
	}

	// ── Overlay ───────────────────────────────────────────────────────────────
	$alpha         = round( $overlay_opacity / 100, 2 );
	$overlay_style = "background-color:{$overlay_color};opacity:{$alpha};";

	// ── Embed URL ─────────────────────────────────────────────────────────────
	$embed_url = $video_url ? tutorblock_get_video_embed_url( $video_url, false ) : '';

	// Is it a self-hosted video?
	$is_direct_video = $video_url && preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $video_url );

	// ── Play button ───────────────────────────────────────────────────────────
	$play_svg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>';

	ob_start();
	?>
	<div class="tutorblock-video-preview caption-<?php echo esc_attr( $caption_position ); ?>"
		style="border-radius:<?php echo (int) $border_radius; ?>px;overflow:hidden;">

		<?php // ── Media wrapper ── ?>
		<div class="tutorblock-video-preview__media"
			style="aspect-ratio:<?php echo esc_attr( $aspect_ratio ); ?>;"
			<?php if ( $video_url && $autoplay_click ) : ?>
				data-tb-video-trigger="<?php echo esc_attr( $video_url ); ?>"
				role="button"
				tabindex="0"
				aria-label="<?php echo esc_attr( sprintf( __( 'Play video: %s', 'tutorblock' ), $title ?: __( 'preview', 'tutorblock' ) ) ); ?>"
			<?php endif; ?>>

			<?php if ( $thumbnail_url ) : ?>
				<img
					src="<?php echo esc_url( $thumbnail_url ); ?>"
					alt="<?php echo esc_attr( $title ); ?>"
					loading="lazy"
					style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;" />
			<?php endif; ?>

			<?php if ( $overlay_opacity > 0 ) : ?>
				<div class="tutorblock-video-preview__overlay"
					style="<?php echo esc_attr( $overlay_style ); ?>"></div>
			<?php endif; ?>

			<?php if ( $show_play && $video_url ) : ?>
				<div class="tutorblock-video-preview__play-btn"
					style="color:<?php echo esc_attr( $play_btn_color ); ?>;">
					<?php echo $play_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<?php if ( $show_overlay_btn && $overlay_btn_text && $overlay_btn_url ) : ?>
				<a href="<?php echo esc_url( $overlay_btn_url ); ?>"
					class="tutorblock-video-preview__overlay-btn"
					style="background:<?php echo esc_attr( $overlay_btn_bg ); ?>;color:<?php echo esc_attr( $overlay_btn_txt ); ?>;">
					<?php echo esc_html( $overlay_btn_text ); ?>
				</a>
			<?php endif; ?>

			<?php if ( in_array( $caption_position, array( 'overlay-bottom', 'overlay-center' ), true ) && ( $title || $description ) ) : ?>
				<div class="tutorblock-video-preview__caption">
					<?php if ( $title ) : ?>
						<h3 class="tutorblock-video-preview__title"><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>
					<?php if ( $description ) : ?>
						<p class="tutorblock-video-preview__description"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div><?php // .tutorblock-video-preview__media ?>

		<?php if ( 'below' === $caption_position && ( $title || $description ) ) : ?>
			<div class="tutorblock-video-preview__caption">
				<?php if ( $title ) : ?>
					<h3 class="tutorblock-video-preview__title"><?php echo esc_html( $title ); ?></h3>
				<?php endif; ?>
				<?php if ( $description ) : ?>
					<p class="tutorblock-video-preview__description"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php // Lightbox modal (shown only if there's a video) ?>
		<?php if ( $video_url ) : ?>
			<div class="tutorblock-video-modal"
				role="dialog"
				aria-modal="true"
				aria-label="<?php echo esc_attr( $title ?: __( 'Video preview', 'tutorblock' ) ); ?>">
				<div class="tutorblock-video-modal__inner">
					<button class="tutorblock-video-modal__close"
						aria-label="<?php echo esc_attr__( 'Close video', 'tutorblock' ); ?>">&times;</button>
					<div class="tutorblock-video-modal__embed"
						data-tb-embed-url="<?php echo esc_attr( $embed_url ?: $video_url ); ?>"
						data-tb-direct="<?php echo $is_direct_video ? '1' : '0'; ?>">
					</div>
				</div>
			</div>
		<?php endif; ?>

	</div>
	<?php

	return ob_get_clean();
}
