<?php
namespace Academy\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Setup {
	const PAGE_ID = 'academy-setup';
	private $assets;
	public static function init() {
		$self = new self();
		$self->assets = new \Academy\Assets();
		if ( $self->is_current() ) {
			add_action( 'admin_init', [ $self, 'admin_init' ], 0 );
		}
		add_action( 'admin_menu', [ $self, 'register_admin_menu' ] );
	}

	public function register_admin_menu() {
		add_submenu_page(
			'options-writing.php', // make it hidden
			__( 'Academy Setup', 'academy' ),
			__( 'Academy Setup', 'academy' ),
			'manage_options',
			self::PAGE_ID
		);
	}


	public function admin_init() {
		do_action( 'academy/admin/setup/init', $this );

		$this->enqueue_assets();

		// Setup default heartbeat options
		// TODO: Enable heartbeat.
		add_filter( 'heartbeat_settings', function( $settings ) {
			$settings['interval'] = 15;
			return $settings;
		} );

		$this->render();
		die;
	}

	public function is_current() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( ! empty( $_GET['page'] ) && self::PAGE_ID === $_GET['page'] );
	}

	public function get_setup_scripts_data() {
		return apply_filters( 'academy/admin/setup_scripts_data', $this->assets->get_scripts_data() );
	}


	private function enqueue_assets() {
		$dependencies = include_once ACADEMY_ASSETS_DIR_PATH . sprintf( 'build/setup.%s.asset.php', ACADEMY_VERSION );
		wp_enqueue_style( 'academy-web-font', $this->assets->web_fonts_url( 'DM Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700|Inter:wght@300;400;500;600;700;800;900|Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap' ), array(), $dependencies['version'] );
		wp_enqueue_style( 'academy-icon', ACADEMY_ASSETS_URI . 'lib/css/academy-icon.css', array(), $dependencies['version'], 'all' );
		wp_enqueue_style( 'academy-admin-style', ACADEMY_ASSETS_URI . 'build/setup.css', array( 'wp-components' ), $dependencies['version'], 'all' );
		wp_enqueue_script(
			'academy-setup-scripts',
			ACADEMY_ASSETS_URI . sprintf( 'build/setup.%s.js', ACADEMY_VERSION ),
			$dependencies['dependencies'],
			$dependencies['version'],
			true
		);
		wp_localize_script( 'academy-setup-scripts', 'AcademyGlobal', array_merge( $this->get_setup_scripts_data(), array( 'admin_url'  => admin_url() ) ) );
	}

	private function render() {
		require ACADEMY_ROOT_DIR_PATH . 'includes/admin/views/setup.php';
	}
}
