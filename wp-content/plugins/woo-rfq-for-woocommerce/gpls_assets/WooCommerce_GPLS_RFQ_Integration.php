<?php

namespace gpls_assets;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Class for integrating with WooCommerce Blocks
 */
class WooCommerce_GPLS_RFQ_Integration implements IntegrationInterface
{
    /**
     * The name of the integration.
     *
     * @return string
     */
    public function get_name()
    {
        return 'woo-rfq-for-woocommerce';
    }

    /**
     * When called invokes any initialization/setup for the integration.
     */
    public function initialize()
    {

    }

    /**
     * Returns an array of script handles to enqueue in the frontend context.
     *
     * @return string[]
     */
    public function get_script_handles()
    {

    }

    /**
     * Returns an array of script handles to enqueue in the editor context.
     *
     * @return string[]
     */
    public function get_editor_script_handles()
    {

    }

    /**
     * An array of key, value pairs of data made available to the block on the client side.
     *
     * @return array
     */
    public function get_script_data()
    {
        $woocommerce_example_plugin_data = some_expensive_serverside_function();
        return [
            'expensive_data_calculation' => $woocommerce_example_plugin_data
        ];
    }

    /**
     * Get the file modified time as a cache buster if we're in dev mode.
     *
     * @param string $file Local path to the file.
     * @return string The cache buster value to use for the given file.
     */
    protected function get_file_version($file)
    {

    }
}