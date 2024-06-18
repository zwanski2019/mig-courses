<?php
namespace AcademyMultiInstructor\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Ajax {
	public static function init() {
		$self = new self();
		// withdraw
		add_action( 'wp_ajax_academy_multi_instructor/frontend/get_user_withdraw_info', array( $self, 'get_user_withdraw_info' ) );
		add_action( 'wp_ajax_academy_multi_instructor/frontend/get_user_withdraw_settings', array( $self, 'get_user_withdraw_settings' ) );
		add_action( 'wp_ajax_academy_multi_instructor/frontend/set_user_withdraw_settings', array( $self, 'set_user_withdraw_settings' ) );
		add_action( 'wp_ajax_academy_multi_instructor/frontend/withdraw_request_by_user', array( $self, 'withdraw_request_by_user' ) );
	}

	public function get_user_withdraw_info() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$user_id                     = get_current_user_id();
		$results                     = (object) \Academy\Helper::get_earning_by_user_id( $user_id );
		if ( \Academy\Helper::is_active_woocommerce() ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$results->withdrawCurrencySymbol = \get_woocommerce_currency_symbol( 'USD' );
		}
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$results->withdrawMethodType = get_user_meta( $user_id, 'academy_instructor_withdraw_method_type', true );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$results->minimumWithdrow    = \Academy\Helper::get_settings( 'instructor_minimum_withdraw_amount', 80 );
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$results->withdrowHistory    = \Academy\Helper::get_withdraw_history_by_user_id( $user_id );
		wp_send_json_success( $results );
		wp_die();
	}


	public function get_user_withdraw_settings() {
		check_ajax_referer( 'academy_nonce', 'security' );
		$user_id = get_current_user_id();
		$data    = [
			'withdrawMethodType' => get_user_meta( $user_id, 'academy_instructor_withdraw_method_type', true ),
			'paypalEmailAddress' => get_user_meta( $user_id, 'academy_instructor_withdraw_paypal_email', true ),
			'echeckAddress'      => get_user_meta( $user_id, 'academy_instructor_withdraw_echeck_address', true ),
			'bankAccountName'    => get_user_meta( $user_id, 'academy_instructor_withdraw_bank_acocunt_name', true ),
			'bankAccountNumber'  => get_user_meta( $user_id, 'academy_instructor_withdraw_bank_acocunt_number', true ),
			'bankName'           => get_user_meta( $user_id, 'academy_instructor_withdraw_bank_name', true ),
			'bankIBAN'           => get_user_meta( $user_id, 'academy_instructor_withdraw_bank_iban', true ),
			'bankSWIFTCode'      => get_user_meta( $user_id, 'academy_instructor_withdraw_bank_swiftcode', true ),
			'minimum_withdrow'   => \Academy\Helper::get_settings( 'instructor_minimum_withdraw_amount', 80 ),
		];
		wp_send_json_success( $data );
		wp_die();
	}

	public function set_user_withdraw_settings() {
		check_ajax_referer( 'academy_nonce', 'security' );
		if ( ! current_user_can( 'manage_academy_instructor' ) ) {
			wp_die();
		}

		$withdraw_method_type = ( isset( $_POST['withdrawMethodType'] ) ? sanitize_text_field( $_POST['withdrawMethodType'] ) : '' );
		$paypal_email_address = ( isset( $_POST['paypalEmailAddress'] ) ? sanitize_text_field( $_POST['paypalEmailAddress'] ) : '' );
		$check_address = ( isset( $_POST['echeckAddress'] ) ? sanitize_text_field( $_POST['echeckAddress'] ) : '' );
		$bank_account_name = ( isset( $_POST['bankAccountName'] ) ? sanitize_text_field( $_POST['bankAccountName'] ) : '' );
		$bank_account_number = ( isset( $_POST['bankAccountNumber'] ) ? sanitize_text_field( $_POST['bankAccountNumber'] ) : '' );
		$bank_name = ( isset( $_POST['bankName'] ) ? sanitize_text_field( $_POST['bankName'] ) : '' );
		$bank_iban = ( isset( $_POST['bankIBAN'] ) ? sanitize_text_field( $_POST['bankIBAN'] ) : '' );
		$bank_SWIFT_code = ( isset( $_POST['bankSWIFTCode'] ) ? sanitize_text_field( $_POST['bankSWIFTCode'] ) : '' );

		$user_id = get_current_user_id();
		update_user_meta( $user_id, 'academy_instructor_withdraw_method_type', $withdraw_method_type );
		update_user_meta( $user_id, 'academy_instructor_withdraw_paypal_email', $paypal_email_address );
		update_user_meta( $user_id, 'academy_instructor_withdraw_echeck_address', $check_address );
		update_user_meta( $user_id, 'academy_instructor_withdraw_bank_acocunt_name', $bank_account_name );
		update_user_meta( $user_id, 'academy_instructor_withdraw_bank_acocunt_number', $bank_account_number );
		update_user_meta( $user_id, 'academy_instructor_withdraw_bank_name', $bank_name );
		update_user_meta( $user_id, 'academy_instructor_withdraw_bank_iban', $bank_iban );
		update_user_meta( $user_id, 'academy_instructor_withdraw_bank_swiftcode', $bank_SWIFT_code );
		wp_send_json_success( [
			'withdrawMethodType' => $withdraw_method_type,
			'paypalEmailAddress' => $paypal_email_address,
			'echeckAddress' => $check_address,
			'bankAccountName' => $bank_account_name,
			'bankAccountNumber' => $bank_account_number,
			'bankName' => $bank_name,
			'bankIBAN' => $bank_iban,
			'bankSWIFTCode' => $bank_SWIFT_code,
		] );
		wp_die();
	}

	public function withdraw_request_by_user() {
		check_ajax_referer( 'academy_nonce', 'security' );

		$user_id = get_current_user_id();

		$withdraw_amount      = (int) ( isset( $_POST['withdrawAmount'] ) ? sanitize_text_field( $_POST['withdrawAmount'] ) : 0 );
		$withdraw_method_type = ( isset( $_POST['withdrawMethodType'] ) ? sanitize_text_field( $_POST['withdrawMethodType'] ) : '' );
		if ( get_user_meta( $user_id, 'academy_instructor_withdraw_method_type', true ) !== $withdraw_method_type ) {
			$message = apply_filters( 'academy/frontend/user_withdraw_wrong_method_type_message', esc_html__( 'Your Request withdraw method type isn\'t match', 'academy' ) );
			wp_send_json_error( array( 'message' => $message ) );
			wp_die();
		}
		$earning      = (object) \Academy\Helper::get_earning_by_user_id( $user_id );
		$min_withdraw = \Academy\Helper::get_settings( 'instructor_minimum_withdraw_amount' );

		if ( $withdraw_amount < $min_withdraw ) {
			$message = apply_filters( 'academy/frontend/user_minimum_withdraw_amount_message', sprintf( '%s %d', esc_html__( 'Minimum withdrawal amount is', 'academy' ), $min_withdraw ) );
			wp_send_json_error( array( 'message' => $message ) );
			wp_die();
		}

		if ( $earning->balance < $withdraw_amount ) {
			$insufficient_balence = apply_filters( 'academy/frontend/user_withdraw_insufficient_balance_message', __( 'Insufficient balance.', 'academy' ) );
			wp_send_json_error( array( 'message' => $insufficient_balence ) );
			wp_die();
		}

		$withdraw_args = apply_filters(
			'academy/frontend/withdraw_data_insert_args',
			array(
				'user_id'     => $user_id,
				'amount'      => $withdraw_amount,
				'method_data' => wp_json_encode( \Academy\Helper::get_user_withdraw_saved_info( $user_id, $withdraw_method_type ) ),
				'status'      => 'pending',
				'created_at'  => current_time( 'mysql' ),
			)
		);

		do_action( 'academy/frontend/before_withdraw_data_insert', $withdraw_args );
		$withdraw_id = \Academy\Helper::insert_withdraw( $withdraw_args );
		do_action( 'academy/frontend/after_withdraw_data_insert', $withdraw_id, $withdraw_args );
		wp_send_json_success( $withdraw_args );
		wp_die();
	}
}
