<?php
namespace Academy\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PermalinkRewrite {
	public static function init() {
		$self = new self();
		add_action( 'generate_rewrite_rules', array( $self, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $self, 'gal_query_vars' ) );
	}
	public function add_rewrite_rules( $wp_rewrite ) {
		$permalinks = \Academy\Helper::get_permalink_structure();
		$course_rewrite_slug = $permalinks['course_rewrite_slug'];
		$new_rules         = [
			$course_rewrite_slug . '/(.+?)/lessons/(.+?)/?$' => 'index.php?source=lessons', // will be removed after migrate user v1.7.4
			$course_rewrite_slug . '/(.+?)/curriculums/(.+?)/?$' => 'index.php?source=curriculums',
			$course_rewrite_slug . '/(.+?)/certificate/(.+?)/?$' => 'index.php?source=certificate',
		];
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}
	public function gal_query_vars( $query_vars ) {
		$query_vars[] = 'source';
		return $query_vars;
	}
}
