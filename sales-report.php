<?php
/**
 * Plugin Name: Sales Report by Province
 * Description: Add provincial filters to your sales analytics. Easily view and analyze sales reports by province directly within WooCommerce.
 * Version: 1.0
 * Author: Block Agency
 * Author URI: https://blockagency.co/
 * Text Domain: sales-report
 * Domain Path: /languages
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package extension
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAIN_PLUGIN_FILE' ) ) {
	define( 'MAIN_PLUGIN_FILE', __FILE__ );
}


$setup_path = plugin_dir_path( __FILE__ ) . 'includes/admin/Setup.php';
require_once $setup_path;



use SalesReport\Admin\Setup;

// $setup = new Setup();
// var_dump($setup );
// phpcs:disable WordPress.Files.FileName

/**
 * WooCommerce fallback notice.
 *
 * @since 0.1.0
 */
function sales_report_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Sales Report requires WooCommerce to be installed and active. You can download %s here.', 'sales_report' ), '<a href="https://woo.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

register_activation_hook( __FILE__, 'sales_report_activate' );

/**
 * Activation hook.
 *
 * @since 0.1.0
 */
function sales_report_activate() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'sales_report_missing_wc_notice' );
		return;
	}
}

if ( ! class_exists( 'sales_report' ) ) :
	/**
	 * The sales_report class.
	 */
	class sales_report {
		/**
		 * This class instance.
		 *
		 * @var \sales_report single instance of this class.
		 */
		private static $instance;

		/**
		 * Constructor.
		 */
		public function __construct() {
			if ( is_admin() ) {
				new Setup();
			}
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'sales_report' ), $this->version );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'sales_report' ), $this->version );
		}

		/**
		 * Gets the main instance.
		 *
		 * Ensures only one instance can be loaded.
		 *
		 * @return \sales_report
		 */
		public static function instance() {

			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
endif;

add_action( 'plugins_loaded', 'sales_report_init', 10 );

/**
 * Initialize the plugin.
 *
 * @since 0.1.0
 */
function sales_report_init() {
	load_plugin_textdomain( 'sales_report', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	add_action( 'init', 'add_shipping_states' );
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'sales_report_missing_wc_notice' );
		return;
	}

	sales_report::instance();

}

function add_shipping_states() {
	$country_code = 'CA'; // Replace with your country code
	$shipping_states = WC()->countries->get_states($country_code);
	$states = array();

	foreach ($shipping_states as $state_code => $state_name) {
		$states[] = array(
			'label' => __( $state_name, 'dev-blog-example' ),
			'value' => $state_code,
		);
	}

	// Add the shipping states to the data registry
	$data_registry = Automattic\WooCommerce\Blocks\Package::container()->get(
		Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class
	);

	$data_registry->add( 'shippingStates', $states );
}

// function apply_state_arg( $args ) {
//     // Handle state parameter
//     if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
//         $state = sanitize_text_field( wp_unslash( $_GET['shipping_states_is'] ) );
//         $args['shipping_states_is'] = $state;
//     }

   

//     return $args;
// }




// add_filter( 'woocommerce_analytics_orders_query_args', 'apply_state_arg' );
// add_filter( 'woocommerce_analytics_orders_stats_query_args', 'apply_state_arg' );

// function add_join_subquery( $clauses ) {
//     global $wpdb;

//     // Initialize WooCommerce Logger
//     // $logger = wc_get_logger();
//     // $context = array( 'source' => 'custom-join-filter' );

//     // Check if the 'state' filter is present
//     if ( isset( $_GET['shipping_states_is'] ) && !empty( $_GET['shipping_states_is']) ) {
//         $state = sanitize_text_field( wp_unslash( $_GET['shipping_states_is'] ) );

//         // Add the state join with a meta_key condition
//         $clauses[] = "JOIN {$wpdb->postmeta} AS state_postmeta 
//                       ON {$wpdb->prefix}wc_order_stats.order_id = state_postmeta.post_id";
//     }

//     // Log final JOIN clause
//   //  $logger->debug( 'Final JOIN clauses: ' . print_r( $clauses, true ), $context );

//     return $clauses;
// }



// add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', 'add_join_subquery' );
// add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', 'add_join_subquery' );
// add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', 'add_join_subquery');

// function add_where_subquery( $clauses ) {
//     global $wpdb;

    

//     // Filter by state
//     if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
//         $state = sanitize_text_field( wp_unslash( $_GET['shipping_states_is'] ) );
//         $state_clause = $wpdb->prepare( 
//             "AND state_postmeta.meta_key = '_shipping_state' AND state_postmeta.meta_value = %s", 
//             $state 
//         );
//         $clauses[] = $state_clause;

//         // Log the state filter
//       //  $logger->debug( "Added state filter: {$state_clause}", $context );
//     }

  
   

//     // Log the final clauses
//    // $logger->debug( 'Final WHERE clauses: ' . print_r( $clauses, true ), $context );

//     // Log SQL query
//     //$logger->debug( 'Last SQL query: ' . $wpdb->last_query, $context );

//     return $clauses;
// }





// add_filter( 'woocommerce_analytics_clauses_where_orders_subquery', 'add_where_subquery' );
// add_filter( 'woocommerce_analytics_clauses_where_orders_stats_total', 'add_where_subquery' );
// add_filter( 'woocommerce_analytics_clauses_where_orders_stats_interval', 'add_where_subquery' );

// function add_select_subquery( $clauses ) {
//     // Include additional fields in the SELECT clause if necessary
//     if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
//         $clauses[] = ', state_postmeta.meta_value AS state';
//     }

//     // if ( isset( $_GET['shipping_status'] ) && ! empty( $_GET['shipping_status'] ) ) {
//     //     $clauses[] = ', order_itemmeta.meta_value AS shipping_status';
//     // }

// 	// if ( isset( $_GET['order_status'] ) && ! empty( $_GET['order_status'] ) ) {
//     //     $clauses[] = ", {$wpdb->prefix}wc_order_stats.status AS order_status";
//     // }

//     return $clauses;
// }


// add_filter( 'woocommerce_analytics_clauses_select_orders_subquery', 'add_select_subquery' );
// add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total', 'add_select_subquery' );
// add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', 'add_select_subquery' );

// for variations



// Modify REST API query arguments to include `shipping_states_is`
function apply_shipping_states_variation( $args ) {
    if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
        $states = array_map( 'sanitize_text_field', wp_unslash( $_GET['shipping_states_is'] ) );
        $args['shipping_states_is'] = $states;
        //error_log( 'Shipping States Query Arg Applied: ' . print_r($states, true) );
    }

    if ( isset( $_GET['order_status'] ) && ! empty( $_GET['order_status'] ) ) {
        $order_status = sanitize_text_field( wp_unslash( $_GET['order_status'] ) );
        $args['order_status'] = $order_status;
        //error_log( 'Order Status Query Arg Applied: ' . $order_status );
    }

    return $args;
}

add_filter( 'woocommerce_analytics_variations_query_args', 'apply_shipping_states_variation' );
add_filter( 'woocommerce_analytics_variations_stats_query_args', 'apply_shipping_states_variation' );

// Modify the JOIN clause to include `_shipping_state`
function add_shipping_states_join_clause( $clauses ) {
    global $wpdb;

    if ( ! isset( $clauses['join'] ) ) {
        $clauses['join'] = '';
    }

    // if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
    //     $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS state_postmeta 
    //                           ON {$wpdb->prefix}wc_order_stats.order_id = state_postmeta.post_id 
    //                           AND state_postmeta.meta_key = '_shipping_state'";
    //     //error_log( 'JOIN Clause Updated for Shipping States: ' . $clauses['join'] );
    // }

	if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
        $clauses['join'] .= " INNER JOIN {$wpdb->postmeta} AS state_postmeta 
                              ON {$wpdb->prefix}wc_order_stats.order_id = state_postmeta.post_id 
                              AND state_postmeta.meta_key = '_shipping_state'";
        error_log( 'JOIN Clause Optimized: ' . $clauses['join'] );
    }

	

    return $clauses;
}






add_filter( 'woocommerce_analytics_clauses_join_variations_subquery', 'add_shipping_states_join_clause' );
add_filter( 'woocommerce_analytics_clauses_join_variations_stats_total', 'add_shipping_states_join_clause' );
add_filter( 'woocommerce_analytics_clauses_join_variations_stats_interval', 'add_shipping_states_join_clause' );

// Modify the WHERE clause for `shipping_states_is`
function add_shipping_states_where_clause( $clauses ) {
    global $wpdb;

    if ( ! isset( $clauses['where'] ) ) {
        $clauses['where'] = '';
    }

   
   



    // if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
    //     $states = array_map( 'sanitize_text_field', wp_unslash( $_GET['shipping_states_is'] ) );
    //     $placeholders = implode( ',', array_fill( 0, count( $states ), '%s' ) );
    //     $clauses['where'] .= $wpdb->prepare(
    //         " AND state_postmeta.meta_value IN ($placeholders)",
    //         ...$states
    //     );

    //     //error_log( 'WHERE Clause Updated for Shipping States: ' . $clauses['where'] );
    //     //error_log( 'Shipping States Applied: ' . print_r($states, true) );
    // }
	

	if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
        $states = array_map( 'sanitize_text_field', wp_unslash( $_GET['shipping_states_is'] ) );
        $placeholders = implode( ',', array_fill( 0, count( $states ), '%s' ) );
        $clauses['where'] .= $wpdb->prepare(
            " AND state_postmeta.meta_value IN ($placeholders)",
            ...$states
        );
        //error_log( 'WHERE Clause Optimized for Shipping States: ' . $clauses['where'] );
    }



    // if ( isset( $_GET['order_status'] ) && ! empty( $_GET['order_status'] ) ) {
    //     $order_status = sanitize_text_field( wp_unslash( $_GET['order_status'] ) );
    //     $statuses = array_map( 'trim', explode( ',', $order_status ) );
    //     $placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
    //     $clauses['where'] .= $wpdb->prepare(
    //         " AND {$wpdb->prefix}wc_order_stats.status IN ($placeholders)",
    //         ...array_map( function( $status ) { return 'wc-' . $status; }, $statuses )
    //     );

    //     //error_log( 'Order Status WHERE Clause: ' . $clauses['where'] );
    // }
	// Filter by order status
    if ( isset( $_GET['order_status'] ) && ! empty( $_GET['order_status'] ) ) {
        $order_status = sanitize_text_field( wp_unslash( $_GET['order_status'] ) );

        // Use IN condition if multiple statuses are provided (e.g., via a comma-separated string)
        $statuses = array_map( 'trim', explode( ',', $order_status ) );
        $placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
        $status_clause = $wpdb->prepare(
            " AND {$wpdb->prefix}wc_order_stats.status IN ($placeholders)",
            ...array_map( function( $status ) { return 'wc-' . $status; }, $statuses )
        );

        // Add the status clause to the beginning of the array
        $clauses['where'] = $status_clause . $clauses['where'];
      //   error_log('Order status filter applied: ' . $order_status);
    }
    error_log( 'Original WHERE Clause: ' . print_r( $clauses, true ) );

    // Iterate through the clauses and replace `date_created` with `date_completed_cs`
    foreach ( $clauses as $key => $clause ) {
        // Check if the clause is a string and contains `date_created`
        if ( is_string( $clause ) && strpos( $clause, 'wp_wc_order_product_lookup.`date_created`' ) !== false ) {
            // Replace `date_created` with `date_completed_cs`
            $clauses[ $key ] = str_replace(
                'wp_wc_order_product_lookup.`date_created`',
                'wp_wc_order_product_lookup.`date_completed_cs`',
                $clause
            );

            // Log the modified clause
            error_log( "Modified Clause at Key [$key]: " . $clauses[ $key ] );
        }
    }

    // Log the modified WHERE clause
    error_log( 'Modified WHERE Clause (Using date_completed_cs): ' . print_r( $clauses, true ) );



    return $clauses;
}

add_filter( 'woocommerce_analytics_clauses_where_variations_subquery', 'add_shipping_states_where_clause' );
add_filter( 'woocommerce_analytics_clauses_where_variations_stats_total', 'add_shipping_states_where_clause' );
add_filter( 'woocommerce_analytics_clauses_where_variations_stats_interval', 'add_shipping_states_where_clause' );

// Modify the SELECT clause to include `_shipping_state`
function add_shipping_states_select_clause( $clauses ) {
    global $wpdb;

    if ( ! isset( $clauses['select'] ) ) {
        $clauses['select'] = '';
    }

    if ( isset( $_GET['shipping_states_is'] ) && ! empty( $_GET['shipping_states_is'] ) ) {
        $clauses['select'] .= ', state_postmeta.meta_value AS state';
        //error_log( 'SELECT Clause Updated for Shipping States: ' . $clauses['select'] );
    }

    if ( isset( $_GET['order_status'] ) && ! empty( $_GET['order_status'] ) ) {
        $clauses['select'] .= ', ' . $wpdb->prefix . 'wc_order_stats.status AS order_status';
    }

    return $clauses;
}

add_filter( 'woocommerce_analytics_clauses_select_variations_subquery', 'add_shipping_states_select_clause' );
add_filter( 'woocommerce_analytics_clauses_select_variations_stats_total', 'add_shipping_states_select_clause' );
add_filter( 'woocommerce_analytics_clauses_select_variations_stats_interval', 'add_shipping_states_select_clause' );



// add_action( 'admin_init', function() {
//     global $wpdb;

//     // Fetch all order_id and date_created from wc_order_product_lookup
//     $table_name = $wpdb->prefix . 'wc_order_product_lookup';
//     $results = $wpdb->get_results( "SELECT order_id, date_created, date_completed_cs FROM $table_name", ARRAY_A );

//     // Log the results to debug.log (optional)
//     error_log( 'Order Data: ' . print_r( $results, true ) );

//    // Display results in the admin (for debugging)
//     if ( current_user_can( 'manage_options' ) ) {
//         echo '<pre>';
//         print_r( $results );
//         echo '</pre>';
//     }
// });


add_action('init', function() {
    if ( isset($_GET['reset_wc_analytics']) && current_user_can('manage_options') ) {
       
        delete_option('woocommerce_admin_import_orders');
        delete_option('woocommerce_admin_import_customers');
        delete_option('woocommerce_admin_version');
        delete_option('woocommerce_admin_import_stats_enabled');

       
        global $wpdb;
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wc_order_stats");
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wc_order_product_lookup");
        $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wc_customer_lookup");

        echo 'WooCommerce Analytics has been reset. You can re-import historical data.';
        exit;
    }
});


add_action( 'admin_init', function() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'wc_order_product_lookup';
    $column_name = 'date_completed_cs';

    // Check if the column already exists
    $column_exists = $wpdb->get_results( $wpdb->prepare(
        "SHOW COLUMNS FROM $table_name LIKE %s",
        $column_name
    ) );

   


    // Add the column if it does not exist
    if ( empty( $column_exists ) ) {
        $wpdb->query( "ALTER TABLE $table_name ADD COLUMN date_completed_cs DATETIME NULL" );
      //  error_log( 'Column date_completed_cs added to table ' . $table_name );
    }
});



add_action( 'woocommerce_analytics_update_product', 'update_product_date_created_to_completed', 10, 2 );

function update_product_date_created_to_completed( $order_item_id, $order_id ) {
    global $wpdb;

   
    $order = wc_get_order( $order_id );
    $completed_date = $order->get_date_completed();

    if ( $completed_date ) {
        $date_completed = $completed_date->date( 'Y-m-d H:i:s' );

    
        $wpdb->update(
            $wpdb->prefix . 'wc_order_product_lookup',
            [ 'date_completed_cs' => $date_completed ], 
            [ 'order_item_id' => $order_item_id ], 
            [ '%s' ],                              
            [ '%d' ]                               
        );

       
     // error_log( 'Updated date_created for order item ID ' . $order_item_id . ' to ' . $date_completed );
    } 
    
   
}