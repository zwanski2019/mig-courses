<?php
namespace AcademyQuizzes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Academy\Interfaces\AddonInterface;

final class Quizzes implements AddonInterface {
	private $addon_name = 'quizzes';
	private function __construct() {
		$this->define_constants();
		$this->init_addon();
	}
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}
	public function define_constants() {
		/**
		 * Defines CONSTANTS for Whole Addon.
		 */
		define( 'ACADEMY_QUIZZES_VERSION', '1.0' );
		define( 'ACADEMY_QUIZZES_VERSION_NAME', 'academy_quizzes_version' );
		define( 'ACADEMY_QUIZZES_INCLUDES_DIR_PATH', ACADEMY_ROOT_DIR_PATH . 'includes/addons/quizzes/includes/' );
	}
	public function init_addon() {
		// fire addon activation hook
		add_action( "academy/addons/activated_{$this->addon_name}", array( $this, 'addon_activation_hook' ) );
		// if disable then stop running addons
		if ( ! \Academy\Helper::get_addon_active_status( $this->addon_name ) ) {
			return;
		}

		Database::init();
		API::init();
		Ajax::init();
		Miscellaneous::init();
	}

	public function addon_activation_hook() {
		Installer::init();
		flush_rewrite_rules();
	}
}
