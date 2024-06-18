<?php
namespace Academy\Integration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Academy\Traits\Earning;

class Woocommerce {

	use Earning;
	public static function init() {
		$self = new self();
		/**
		 * After create new order
		 */
		add_action( 'woocommerce_new_order', array( $self, 'create_course_order_from_admin' ), 10, 3 );

		/**
		 * Order Status Hook
		 *
		 * Remove course from active courses if an order is cancelled or refunded
		 */
		add_action( 'woocommerce_order_status_changed', array( $self, 'enrolled_courses_status_change' ), 10, 3 );

		// save meta value
		add_filter( 'product_type_options', array( $self, 'add_academy_type_in_wc_product' ) );
		add_action( 'save_post_product', array( $self, 'save_wc_product_meta' ) );
		add_action( 'rest_after_insert_academy_courses', array( $self, 'update_product_meta' ), 10, 1 );

		// remove course product from shop page
		if ( \Academy\Helper::get_settings( 'hide_course_product_from_shop_page' ) ) {
			add_action( 'woocommerce_product_query', array( $self, 'remove_course_product_from_shop' ) );
		}

		/**
		 * Add Earning Data
		 */
		if ( \Academy\Helper::get_addon_active_status( 'multi_instructor' ) ) {
			add_action( 'woocommerce_new_order_item', array( $self, 'save_earning_data' ), 10, 3 );
			add_action( 'woocommerce_order_status_changed', array( $self, 'save_earning_data_status_change' ), 10, 3 );
		}

		/**
		 * Customer Order Details Page
		 */
		add_action( 'woocommerce_thankyou', array( $self, 'add_frontend_dashboard_menu_link' ) );

		/**
		 * Add Frontend Dashboard Menu Link
		 */
		add_filter( 'woocommerce_account_menu_items', array( $self, 'add_frontend_dashboard_menu_item' ) );
		add_filter( 'woocommerce_get_endpoint_url', array( $self, 'set_frontend_dashboard_menu_item_url' ), 10, 2 );
	}

	public function create_course_order_from_admin( $order_id ) {
		if ( ! is_admin() ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		$customer_id = $order->get_customer_id();
		if ( ! $customer_id ) {
			$customer_id = $this->create_user_by_order_details( $order );
		}
		foreach ( $order->get_items() as $item ) {
			$product_id    = $item->get_product_id();
			$if_has_course = \Academy\Helper::product_belongs_with_course( $product_id );
			if ( $if_has_course ) {
				$course_id   = $if_has_course->post_id;
				\Academy\Helper::do_enroll( $course_id, $customer_id, $order_id );
			}
		}
	}

	public function enrolled_courses_status_change( $order_id, $status_from, $status_to ) {
		global $wpdb;
		$course_enrolled_by_order = \Academy\Helper::get_course_enrolled_ids_by_order_id( $order_id );
		if ( $course_enrolled_by_order && is_array( $course_enrolled_by_order ) && count( $course_enrolled_by_order ) ) {
			foreach ( $course_enrolled_by_order as $enrolled_info ) {
				if ( ! is_admin() && self::is_order_will_be_automatically_completed( $order_id ) ) {
					$status_to = 'completed';
					self::order_mark_as_completed( $order_id );
				}
				$wpdb->update( $wpdb->posts, array( 'post_status' => $status_to ), array( 'ID' => $enrolled_info['enrolled_id'] ) );
				if ( 'completed' === $status_to ) {
					$enrolled_user_id = get_post_field( 'post_author', $enrolled_info['enrolled_id'] );
					do_action( 'academy/course/after_enroll', $enrolled_info['course_id'], $enrolled_info['enrolled_id'], $enrolled_user_id );
				}
			}
		} else {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$items = $order->get_items();
				foreach ( $items as $item ) {
					$product_id = $item->get_product_id();
					$has_course = \Academy\Helper::product_belongs_with_course( $product_id );
					if ( $has_course ) {
						if ( $order && in_array( $order->get_status(), array( 'processing', 'pending', 'completed', 'on-hold' ), true ) ) {
							$customer_id = $order->get_customer_id();
							if ( ! $customer_id ) {
								$customer_id = $this->create_user_by_order_details( $order );
							}
							$course_id = $has_course->post_id;
							$course_attach_product_id = $has_course->meta_value;
							if ( $course_id && $course_attach_product_id ) {
								$enroll_id = \Academy\Helper::do_enroll( $course_id, $customer_id, $order_id );
								// make order auto complete
								if ( ! is_admin() && self::is_order_will_be_automatically_completed( $order_id ) ) {
									$status_to = 'completed';
									self::order_mark_as_completed( $order_id );
								}
								$wpdb->update( $wpdb->posts, array( 'post_status' => $status_to ), array( 'ID' => $enroll_id ) );
								if ( 'completed' === $status_to ) {
									$enrolled_user_id = get_post_field( 'post_author', $enroll_id );
									do_action( 'academy/course/after_enroll', $course_id, $enroll_id, $enrolled_user_id );
								}
							}
						}//end if
					}//end if
				}//end foreach
			}//end if
		}//end if
	}

	public function add_academy_type_in_wc_product( $types ) {
		$types['academy_product'] = array(
			'id'            => '_academy_product',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'For Academy LMS', 'academy' ),
			'description'   => __( 'This checkmark ensure that you will sell a specific course via this product.', 'academy' ),
			'default'       => 'no',
		);
		return $types;
	}

	public function save_wc_product_meta( $post_ID ) {
		$is_academy_product = sanitize_text_field( ( isset( $_POST['_academy_product'] ) ? $_POST['_academy_product'] : '' ) ); // phpcs:ignore input var ok, CSRF ok.
		if ( 'on' === $is_academy_product ) {
			update_post_meta( $post_ID, '_academy_product', 'yes' );
		} else {
			delete_post_meta( $post_ID, '_academy_product' );
		}
	}

	public function update_product_meta( $post ) {
		$product_id = (int) get_post_meta( $post->ID, 'academy_course_product_id', true );
		if ( $product_id ) {
			update_post_meta( $product_id, '_virtual', 'yes' );
			update_post_meta( $product_id, '_academy_product', 'yes' );
		}
	}

	public function remove_course_product_from_shop( $wp_query ) {
		$wp_query->set(
			'meta_query',
			array(
				array(
					'key'     => '_academy_product',
					'compare' => 'NOT EXISTS',
				),
			)
		);
		return $wp_query;
	}

	public function save_earning_data( $item_id, $item, $order_id ) {
		$is_enabled_earning = (bool) \Academy\Helper::get_settings( 'is_enabled_earning' );
		if ( ! \Academy\Helper::get_addon_active_status( 'multi_instructor' ) || ! $is_enabled_earning ) {
			return;
		}

		$item       = new \WC_Order_Item_Product( $item );
		$product_id = $item->get_product_id();
		$course     = \Academy\Helper::product_belongs_with_course( $product_id );
		if ( $course ) {
			$course_id    = $course->post_id;
			$user_id      = \Academy\Helper::get_user_id_from_course_id( $course_id );
			$order_status = \Academy\Helper::get_order_status_by_id( $order_id );

			if ( self::is_exists_user_earning_by_order( $course_id, $order_id, $user_id ) ) {
				return;
			}

			$total_price              = $item->get_total();
			$fees_deduct_data         = array();
			$is_enabled_fee_deduction = (bool) \Academy\Helper::get_settings( 'is_enabled_fee_deduction' );
			$course_price_grand_total = $total_price;
			if ( $is_enabled_fee_deduction ) {
				$fees_name   = \Academy\Helper::get_settings( 'fee_deduction_name' );
				$fees_amount = \Academy\Helper::get_settings( 'fee_deduction_amount' );
				$fees_type   = \Academy\Helper::get_settings( 'fee_deduction_type' );

				if ( $fees_amount > 0 ) {
					if ( 'percent' === $fees_type ) {
						$fees_amount = ( $total_price * $fees_amount ) / 100;
					}
					$course_price_grand_total = $total_price - $fees_amount;
				}

				$fees_deduct_data = array(
					'deduct_fees_amount' => $fees_amount,
					'deduct_fees_name'   => $fees_name,
					'deduct_fees_type'   => $fees_type,
				);
			}

			$instructor_rate = \Academy\Helper::get_settings( 'instructor_commission_percentage' );
			$admin_rate      = \Academy\Helper::get_settings( 'admin_commission_percentage' );

			$instructor_amount = 0;
			if ( $instructor_rate > 0 ) {
				$instructor_amount = ( $course_price_grand_total * $instructor_rate ) / 100;
			}

			$admin_amount = 0;
			if ( $admin_rate > 0 ) {
				$admin_amount = ( $course_price_grand_total * $admin_rate ) / 100;
			}

			$commission_type = 'percent';

			$earning_data = array(
				'user_id'                  => $user_id,
				'course_id'                => $course_id,
				'order_id'                 => $order_id,
				'order_status'             => $order_status,
				'course_price_total'       => $total_price,
				'course_price_grand_total' => $course_price_grand_total,
				'instructor_amount'        => $instructor_amount,
				'instructor_rate'          => $instructor_rate,
				'admin_amount'             => $admin_amount,
				'admin_rate'               => $admin_rate,
				'commission_type'          => $commission_type,
			);
			$data         = apply_filters( 'academy/integration/woocommerce/insert_earning_args', array_merge( $earning_data, $fees_deduct_data ) );
			self::insert_earning( $data );
		}//end if
	}
	public function save_earning_data_status_change( $order_id, $status_from, $status_to ) {
		if ( ! get_post_meta( $order_id, 'is_academy_order_for_course', true ) ) {
			return;
		}

		if ( ! is_admin() && self::is_order_will_be_automatically_completed( $order_id ) ) {
			$status_to = 'completed';
			self::order_mark_as_completed( $order_id );
		}

		if ( count( self::get_earning_by_order_id( $order_id ) ) ) {
			self::update_earning_status_by_order_id( $order_id, $status_to );
		}
	}

	public function add_frontend_dashboard_menu_item( $menu_links ) {
		$allow_fontend_dashbaord = (bool) \Academy\Helper::get_settings( 'is_enabled_fd_link_inside_woo_dashboard' );
		$fontend_dashbaord_label = (string) \Academy\Helper::get_settings( 'woo_dashboard_fd_link_label' );
		if ( ! $allow_fontend_dashbaord || empty( $fontend_dashbaord_label ) ) {
			return $menu_links;
		}

		$menu = [];
		foreach ( $menu_links as $key => $value ) {
			$menu[ $key ] = $value;
			if ( 'dashboard' === $key ) {
				$menu['academy_frontend_dashboard'] = esc_html( $fontend_dashbaord_label );
			}
		}
		return $menu;
	}

	public function set_frontend_dashboard_menu_item_url( $url, $endpoint ) {
		if ( 'academy_frontend_dashboard' === $endpoint ) {
			return esc_url( \Academy\Helper::get_page_permalink( 'frontend_dashboard_page' ) );
		}
		return $url;
	}

	public function add_frontend_dashboard_menu_link( $order_id ) {
		$allow_fontend_dashbaord = (bool) \Academy\Helper::get_settings( 'is_enabled_fd_link_inside_woo_order_page' );
		$fontend_dashbaord_label = (string) \Academy\Helper::get_settings( 'woo_order_page_fd_link_label' );
		if ( ! $allow_fontend_dashbaord || empty( $fontend_dashbaord_label ) ) {
			return;
		}

		$product_id = 0;
		$order = new \WC_Order( $order_id );
		$items = $order->get_items();
		foreach ( $items as $item ) {
			if ( isset( $item['product_id'] ) && ! empty( $item['product_id'] ) ) {
				$product_id = $item['product_id'];
				break;
			}
		}

		if ( 'yes' === get_post_meta( $product_id, '_academy_product', true ) ) {
			?>
				<div class="academy-customizer-backto-dashboard-wrap" style="margin: 50px; text-align: center;">
					<a class="button" href="<?php echo esc_url( \Academy\Helper::get_page_permalink( 'frontend_dashboard_page' ) ); ?>"><?php echo esc_html( $fontend_dashbaord_label ); ?></a>
				</div>
			<?php
		}
	}


	public static function is_order_will_be_automatically_completed( $order_id ) {
		$enable_woo_order_auto_complete     = (bool) \Academy\Helper::get_settings( 'woo_order_auto_complete' );
		if ( ! $enable_woo_order_auto_complete ) {
			return false;
		}
		$order         = wc_get_order( $order_id );
		$order_data    = is_object( $order ) && method_exists( $order, 'get_data' ) ? $order->get_data() : array();
		$payment_method = isset( $order_data['payment_method'] ) ? $order_data['payment_method'] : '';
		if ( $enable_woo_order_auto_complete && 'cod' !== $payment_method ) {
			return true;
		}
		return false;
	}

	public static function order_mark_as_completed( $order_id ) {
		global $wpdb;
		if ( 'yes' === get_option( 'woocommerce_custom_orders_table_enabled' ) ) {
			$is_update = $wpdb->update(
				"{$wpdb->prefix}wc_orders",
				array( 'status' => 'wc-completed' ),
				array( 'ID' => $order_id )
			);
		} else {
			$is_update = $wpdb->update(
				$wpdb->posts,
				array( 'post_status' => 'wc-completed' ),
				array( 'ID' => $order_id )
			);
		}
		return $is_update;
	}

	public static function create_user_by_order_details( $order ) {
		// Create a new user
		$billing_address = $order->get_address( 'billing' );

		$password = wp_generate_password();
		$email = $billing_address['email'];
		$username = strstr( $email, '@', true ); // Generate a unique username

		$user_id = wp_create_user( $username, $password, $email );
		// Send the new user notification email
		wp_new_user_notification( $user_id, null, 'both' );
		// Log in the user
		$user = get_user_by( 'id', $user_id );
		if ( $user ) {
			wp_set_current_user( $user_id, $user->user_login );
			wp_set_auth_cookie( $user_id );
		}

		// Set the created user ID as the customer ID in the order object
		$order->set_customer_id( $user_id );
		$order->save();
		return $user_id;
	}
}
