<?php
use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
require_once(gpls_woo_rfq_DIR . 'includes/classes/gateway/wc-gateway-gpls-request-quote.php');
/**
 * Dummy Payments Blocks integration
 *
 * @since 1.0.3
 */
final class WC_Gateway_gpls_rfq_Blocks_Support extends AbstractPaymentMethodType {


	private $gateway;


	protected $name = 'gpls-rfq';

	/**
	 * Initializes the payment method type.
	 */
	public function initialize() {

		$this->settings = get_option( 'woocommerce_gpls-rfq_settings', [] );

        //	$this->gateway  = new WC_Gateway_Dummy();

        $this->gateway  = new WC_Gateway_GPLS_Request_Quote();
	}

	/**
	 * Returns if this payment method should be active. If false, the scripts will not be enqueued.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return $this->gateway->is_available();
	}

	/**
	 * Returns an array of scripts/handles to be registered for this payment method.
	 *
	 * @return array
	 */
	public function get_payment_method_script_handles() {
		$script_path       = '/assets/js/frontend/blocks.js';
		$script_asset_path = GPLS_WOO_RFQ::plugin_abspath() . 'assets/js/frontend/blocks.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require( $script_asset_path )
			: array(
				'dependencies' => array(),
				'version'      => '1.2.0'
			);
		$script_url        = GPLS_WOO_RFQ::plugin_url() . $script_path;

		wp_register_script(
			'wc-gpls-rfq-payments-blocks',
			$script_url,
			$script_asset[ 'dependencies' ],
			$script_asset[ 'version' ],
			true
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'wc-gpls_rfq-payments-blocks', 'woo-rfq-for-woocommerce', GPLS_WOO_RFQ::plugin_abspath() . 'languages/' );
		}

		return [ 'wc-gpls-rfq-payments-blocks' ];
	}

	/**
	 * Returns an array of key=>value pairs of data made available to the payment methods script.
	 *
	 * @return array
	 */
	public function get_payment_method_data() {
		return [
			'title'       => $this->get_setting( 'title' ),
			'description' => $this->get_setting( 'description' ),
			'supports'    => array_filter( $this->gateway->supports, [ $this->gateway, 'supports' ] )
		];
	}
}
