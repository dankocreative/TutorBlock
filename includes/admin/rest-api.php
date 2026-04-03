<?php
/**
 * TutorBlock — REST API helpers for the block editor.
 *
 * Registers lightweight REST endpoints so the editor JS can fetch live data
 * (course lists, category lists, instructor lists) for InspectorControl selects.
 *
 * @package TutorBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'tutorblock_register_rest_routes' );

/**
 * Register all REST routes.
 */
function tutorblock_register_rest_routes(): void {
	$namespace = 'tutorblock/v1';

	// GET /tutorblock/v1/courses — simple course list for selects.
	register_rest_route(
		$namespace,
		'/courses',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tutorblock_rest_get_courses',
			'permission_callback' => 'tutorblock_editor_permission',
			'args'                => array(
				'per_page' => array(
					'default'           => 50,
					'sanitize_callback' => 'absint',
				),
				'search' => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'category' => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);

	// GET /tutorblock/v1/categories — course category list.
	register_rest_route(
		$namespace,
		'/categories',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tutorblock_rest_get_categories',
			'permission_callback' => 'tutorblock_editor_permission',
		)
	);

	// GET /tutorblock/v1/instructors — instructor user list.
	register_rest_route(
		$namespace,
		'/instructors',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'tutorblock_rest_get_instructors',
			'permission_callback' => 'tutorblock_editor_permission',
		)
	);
}

/**
 * Permission check — must be able to edit posts to use these endpoints.
 */
function tutorblock_editor_permission(): bool {
	return current_user_can( 'edit_posts' );
}

/**
 * Return a list of courses for editor selects.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function tutorblock_rest_get_courses( WP_REST_Request $request ): WP_REST_Response {
	$per_page = min( 100, max( 1, (int) $request->get_param( 'per_page' ) ) );
	$search   = $request->get_param( 'search' );
	$category = $request->get_param( 'category' );

	$args = array(
		'post_type'      => 'courses',
		'posts_per_page' => $per_page,
		'post_status'    => 'publish',
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( $search ) {
		$args['s'] = $search;
	}

	if ( $category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'course-category',
				'field'    => 'slug',
				'terms'    => sanitize_key( $category ),
			),
		);
	}

	$courses = get_posts( $args );
	$data    = array();

	foreach ( $courses as $course ) {
		$data[] = array(
			'id'        => $course->ID,
			'title'     => get_the_title( $course ),
			'permalink' => get_permalink( $course ),
			'thumbnail' => get_the_post_thumbnail_url( $course, 'thumbnail' ) ?: null,
		);
	}

	return new WP_REST_Response( $data, 200 );
}

/**
 * Return course categories for editor selects.
 *
 * @return WP_REST_Response
 */
function tutorblock_rest_get_categories(): WP_REST_Response {
	$terms = get_terms( array(
		'taxonomy'   => 'course-category',
		'hide_empty' => true,
		'orderby'    => 'name',
	) );

	if ( is_wp_error( $terms ) ) {
		return new WP_REST_Response( array(), 200 );
	}

	$data = array();
	foreach ( $terms as $term ) {
		$data[] = array(
			'id'    => $term->term_id,
			'name'  => $term->name,
			'slug'  => $term->slug,
			'count' => $term->count,
		);
	}

	return new WP_REST_Response( $data, 200 );
}

/**
 * Return instructor users for editor selects.
 *
 * @return WP_REST_Response
 */
function tutorblock_rest_get_instructors(): WP_REST_Response {
	$users = get_users( array(
		'role__in' => array( 'tutor_instructor', 'administrator' ),
		'orderby'  => 'display_name',
		'order'    => 'ASC',
		'number'   => 100,
	) );

	$data = array();
	foreach ( $users as $user ) {
		$data[] = array(
			'id'     => $user->ID,
			'name'   => $user->display_name,
			'avatar' => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
		);
	}

	return new WP_REST_Response( $data, 200 );
}
