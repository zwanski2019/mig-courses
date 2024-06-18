<?php
namespace  Academy\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Academy;
use WP_Query;

class Template {
	public static function init() {
		$self = new self();
		$self->dispatch_hook();
		Template\Loader::init();
	}

	public function dispatch_hook() {
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_action( 'template_redirect', array( $this, 'archive_course_template_redirect' ) );
		add_action( 'template_redirect', array( $this, 'frontend_dashboard_template_redirect' ) );
		add_filter( 'pre_get_document_title', array( $this, 'pre_get_archive_course_title' ), 30, 1 );
		add_filter( 'post_type_archive_title', array( $this, 'archive_course_document_title' ), 30, 2 );
	}

	/**
	 * Hook into pre_get_posts to do the main product query.
	 *
	 * @param WP_Query $q Query instance.
	 */
	public function pre_get_posts( $q ) {
		$per_page = (int) \Academy\Helper::get_settings( 'course_archive_courses_per_page', 12 );

		if ( $q->is_main_query() && ! $q->is_feed() && ! is_admin() ) {
			$queried_object = get_queried_object();
			if ( ! empty( $q->query['author_name'] ) && Academy\Helper::get_settings( 'is_show_public_profile' ) ) {
				$user = get_user_by( 'login', $q->query['author_name'] );
				if ( $user ) {
					if ( current( $user->roles ) === 'academy_instructor' || current( $user->roles ) === 'administrator' ) {
						$q->set( 'post_type', array( 'academy_courses' ) );
						$q->set( 'author', $q->query['author_name'] );
						$q->set( 'posts_per_page', $per_page );
					}
				}
			} elseif ( is_archive( 'academy_courses' ) && $queried_object instanceof \WP_Post && (int) \Academy\Helper::get_settings( 'course_page' ) === (int) $queried_object->ID ) {
				$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
				$orderby = ( get_query_var( 'orderby' ) ) ? get_query_var( 'orderby' ) : Academy\Helper::get_settings( 'course_archive_courses_order' );
				$q->set( 'post_type', apply_filters( 'academy/get_course_archive_post_types', array( 'academy_courses' ) ) );
				$q->set( 'posts_per_page', $per_page );
				$q->set( 'paged', $paged );
				$q->set( 'orderby', $orderby );
			}//end if
		}//end if
	}

	public function archive_course_template_redirect() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && (int) \Academy\Helper::get_settings( 'course_page' ) === absint( $_GET['page_id'] ) ) {
			$archive_link = $this->get_post_type_archive_link( 'academy_courses' );
			if ( $archive_link ) {
				wp_safe_redirect( $this->get_post_type_archive_link( 'academy_courses' ) );
				exit;
			}
		}
	}

	public function frontend_dashboard_template_redirect() {
		if ( ! is_user_logged_in() && (int) \Academy\Helper::get_settings( 'frontend_dashboard_page' ) === get_the_ID() ) {
			if ( ! \Academy\Helper::get_settings( 'is_enabled_academy_login', true ) && wp_safe_redirect( wp_login_url( get_the_permalink() ) ) ) {
				exit;
			}
		}
	}

	public function get_post_type_archive_link( $post_type ) {
		global $wp_rewrite;

		$post_type_obj = get_post_type_object( $post_type );
		if ( ! $post_type_obj ) {
			return false;
		}

		if ( 'post' === $post_type ) {
			$show_on_front  = get_option( 'show_on_front' );
			$page_for_posts = get_option( 'page_for_posts' );

			if ( 'page' === $show_on_front && $page_for_posts ) {
				$link = get_permalink( $page_for_posts );
			} else {
				$link = get_home_url();
			}
			/** This filter is documented in wp-includes/link-template.php */
			return apply_filters( 'post_type_archive_link', $link, $post_type );
		}

		if ( ! $post_type_obj->has_archive ) {
			return false;
		}

		if ( get_option( 'permalink_structure' ) && is_array( $post_type_obj->rewrite ) ) {
			$struct = ( true === $post_type_obj->has_archive ) ? $post_type_obj->rewrite['slug'] : $post_type_obj->has_archive;
			if ( $post_type_obj->rewrite['with_front'] ) {
				$struct = $wp_rewrite->front . $struct;
			} else {
				$struct = $wp_rewrite->root . $struct;
			}
			$link = home_url( user_trailingslashit( $struct, 'post_type_archive' ) );
		} else {
			$link = home_url( '?post_type=' . $post_type );
		}

		return apply_filters( 'academy/frontend/post_type_archive_link', $link, $post_type );
	}
	public function pre_get_archive_course_title( $title ) {
		if ( class_exists( 'RankMath' ) ) {
			$page_id = (int) get_queried_object_id();
			$course_page = (int) \Academy\Helper::get_settings( 'course_page' );
			if ( $page_id === $course_page ) {
				return;
			}
		}
		return $title;
	}
	public function archive_course_document_title( $name, $post_type ) {
		if ( 'academy_courses' === $post_type ) {
			$course_page = (int) \Academy\Helper::get_settings( 'course_page' );
			return get_the_title( $course_page );
		}
		return $name;
	}
}
