<?php

namespace SalesReport\Admin;

/**
 * SalesReport Setup Class
 */
class Setup {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_menu', array( $this, 'register_page' ) );
	}

	/**
	 * Load all necessary dependencies.
	 *
	 * @since 1.0.0
	 */
	public function register_scripts() {
		if ( ! method_exists( 'Automattic\WooCommerce\Admin\PageController', 'is_admin_or_embed_page' ) ||
		! \Automattic\WooCommerce\Admin\PageController::is_admin_or_embed_page()
		) {
			return;
		}

		$script_path       = '/build/index.js';
		$script_asset_path = dirname( MAIN_PLUGIN_FILE ) . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
		? require $script_asset_path
		: array(
			'dependencies' => array(),
			'version'      => filemtime( $script_path ),
		);
		$script_url        = plugins_url( $script_path, MAIN_PLUGIN_FILE );

		wp_register_script(
			'sales-report',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		// wp_register_style(
		// 	'sales-report',
		// 	plugins_url( '/build/index.css', MAIN_PLUGIN_FILE ),
		// 	// Add any dependencies styles may have, such as wp-components.
		// 	array(),
		// 	filemtime( dirname( MAIN_PLUGIN_FILE ) . '/build/index.css' )
		// );


        // Register and enqueue the CSS file
        $style_path = '/build/index.css';
        if ( file_exists( dirname( MAIN_PLUGIN_FILE ) . $style_path ) ) {
            wp_register_style(
                'sales-report-style',
                plugins_url( $style_path, MAIN_PLUGIN_FILE ),
                array(),
                filemtime( dirname( MAIN_PLUGIN_FILE ) . $style_path )
            );
            wp_enqueue_style( 'sales-report-style' );
        }



		wp_enqueue_script( 'sales-report' );
		wp_enqueue_style( 'sales-report' );
	}

	/**
	 * Register page in wc-admin.
	 *
	 * @since 1.0.0
	 */
	public function register_page() {

		if ( ! function_exists( 'wc_admin_register_page' ) ) {
			return;
		}

		wc_admin_register_page(
			array(
				'id'     => 'sales_report-example-page',
				'title'  => __( 'Sales Report', 'sales_report' ),
				'parent' => 'woocommerce',
				'path'   => '/sales-report',
			)
		);
	}
}
