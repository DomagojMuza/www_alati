import { __ } from '@wordpress/i18n';

export const np_cart_checkout = [
    {
        label: __('Try again another day', 'shipping-workshop'),
        value: 'try-again',
    },
    {
        label: __('Try again another day 2', 'shipping-workshop'),
        value: 'try-again 2',
    },
    {
        label: __('other', 'shipping-workshop'),
        value: 'other',
    }
    /**
     * [frontend-step-01]
     * 📝 Add more options using the same format as above. Ensure one option has the key "other".
     */
];