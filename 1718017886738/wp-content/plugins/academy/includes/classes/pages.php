<?php
namespace Academy\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Academy\Helper;

class Pages {
	private static function get_settings() {
		return json_decode( get_option( ACADEMY_SETTINGS_NAME, '{}' ), true );
	}
	private static function necessary_pages() {
		$page_lists = [
			'frontend_dashboard_page' => [
				'post_title'    => esc_html__( 'Dashboard', 'academy' ),
				'post_status'   => 'publish',
				'post_type' => 'page',
			],
			'course_page' => [
				'post_title'    => esc_html__( 'Courses', 'academy' ),
				'post_status'   => 'publish',
				'post_type' => 'page',
			],
			'frontend_student_reg_page' => [
				'post_title'    => esc_html__( 'Student Registration', 'academy' ),
				'post_status'   => 'publish',
				'post_type' => 'page',
			],
			'frontend_instructor_reg_page' => [
				'post_title'    => esc_html__( 'Instructor Registration', 'academy' ),
				'post_status'   => 'publish',
				'post_type'     => 'page',
			],
			'password_reset_page' => [
				'post_title'    => esc_html__( 'Password Reset', 'academy' ),
				'post_status'   => 'publish',
				'post_type'     => 'page',
			],
		];
		return apply_filters( 'academy/necessary_pages', $page_lists );
	}
	public static function get_necessary_pages() {
		$settings = self::get_settings();
		$necessary_pages = array();
		$page_lists = self::necessary_pages();
		foreach ( $page_lists as $key => $item ) {
			$item = ( $settings[ $key ] ? get_post( $settings[ $key ], ARRAY_A ) : $item );
			if ( isset( $item['ID'] ) ) {
				$item['permalink'] = get_the_permalink( $item['ID'] );
			}
			$item['settings'] = array(
				$key => $page_lists[ $key ]['post_title']
			);
			$necessary_pages[] = $item;
		}
		return $necessary_pages;
	}
	public static function regenerate_necessary_pages() {
		$settings = self::get_settings();
		$page_lists = self::necessary_pages();
		foreach ( $page_lists as $key => $page ) {
			$have_page = \Academy\Helper::get_page_by_title( $page['post_title'] );
			if ( $have_page ) {
				// check page status
				if ( 'publish' !== $have_page->post_status ) {
					$have_page->post_status = 'publish';
					wp_update_post( $have_page );
				}
				// assign page id inside academy settings
				if ( $settings[ $key ] !== $have_page->ID ) {
					$settings[ $key ] = $have_page->ID;
					// set page template
					update_post_meta( $have_page->ID, '_wp_page_template', 'academy-canvas.php' );
				}
			} else {
				$post_id = (string) wp_insert_post( $page );
				if ( $post_id && empty( $settings[ $key ] ) ) {
					$settings[ $key ] = $post_id;
					// set page template
					update_post_meta( $post_id, '_wp_page_template', 'academy-canvas.php' );
				}
			}
		}//end foreach
		update_option( ACADEMY_SETTINGS_NAME, wp_json_encode( $settings ), false );
		return true;
	}
}
