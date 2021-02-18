<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<script type="text/javascript">
    var lpwootrk_ajaxBaseUrl = '<?php echo esc_js($data->ajaxBaseUrl); ?>';
    var lpwootrk_saveSettingsNonce = '<?php echo esc_js($data->saveSettingsNonce); ?>';
    var lpwootrk_saveSettingsAction = '<?php echo esc_js($data->saveSettingsAction); ?>';
</script>

<div id="lpwootrk-settings-page">
    <form id="lpwootrk-settings-form" method="post">
        <h2><?php echo __('Livepayments WooTracker - Plugin Settings', 'livepayments-wootracker'); ?></h2>

        <div class="wrap lpwootrk-settings-container">
            <div id="lpwootrk-settings-save-result" 
                class="updated settings-error lpwootrk-settings-save-result" 
                style="display:none"></div>

            <table class="widefat" cellspacing="0">
                <thead>
                    <tr>
                        <th><h3><?php echo esc_html__('Setup your tracking', 'livepayments-wootracker'); ?></h3></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="form-table">
                                <?php if ($data->allowSettingGtmTrackingId): ?>
                                    <tr>
                                        <th scope="row">
                                            <label for="lpwootrk-gtm-tracking-id"><?php echo esc_html__('Enter the GTM Tracking Id', 'livepayments-wootracker'); ?>:</label>
                                        </th>
                                        <td>
                                            <input type="text" 
                                                name="gtmTrackingId" 
                                                id="lpwootrk-gtm-tracking-id"
                                                class="input-text regular-input"
                                                value="<?php echo esc_attr($data->settings->gtmTrackingId); ?>" /> 
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-ga-measurement-id"><?php echo esc_html__('Enter the GA Measurement Id', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="text" 
                                            name="gaMeasurementId" 
                                            id="lpwootrk-ga-measurement-id"
                                            class="input-text regular-input"
                                            value="<?php echo esc_attr($data->settings->gaMeasurementId); ?>" /> 
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-enable-ip-anonymization"><?php echo esc_html__('Enable IP Anonymization', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="enableIpAnonymization" 
                                            id="lpwootrk-enable-ip-anonymization" 
                                            value="1" 
                                            <?php echo $data->settings->enableIpAnonymization ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-enable-enhanced-link-attr"><?php echo esc_html__('Enable Enhanced Link Attribution', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="enableEnhancedLinkAttribution" 
                                            id="lpwootrk-enable-enhanced-link-attr" 
                                            value="1" 
                                            <?php echo $data->settings->enableEnhancedLinkAttribution ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-disable-advertising-feats"><?php echo esc_html__('Disable Advertising Features', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="disableAdvertisingFeatures" 
                                            id="lpwootrk-disable-advertising-feats" 
                                            value="1" 
                                            <?php echo $data->settings->disableAdvertisingFeatures ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="submit">
                                <input type="button" 
                                    id="lpwootrk-submit-settings-top" 
                                    name="lpwootrk-submit-settings" 
                                    class="button button-primary lpwootrk-form-submit-btn" 
                                    value="<?php echo esc_html__('Save settings', 'livepayments-wootracker'); ?>" />
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="widefat" cellspacing="0">
                <thead>
                    <tr>
                        <th><h3><?php echo esc_html__('Chose what to track', 'livepayments-wootracker'); ?></h3></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-track-order-received"><?php echo esc_html__('Track order received', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="trackOrderReceived" 
                                            id="lpwootrk-track-order-received" 
                                            value="1" 
                                            <?php echo $data->settings->trackOrderReceived ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-track-cart-item-added"><?php echo esc_html__('Track cart item added', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="trackCartItemAdded" 
                                            id="lpwootrk-track-cart-item-added" 
                                            value="1" 
                                            <?php echo $data->settings->trackCartItemAdded ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-track-cart-item-removed"><?php echo esc_html__('Track cart item removed', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="trackCartItemRemoved" 
                                            id="lpwootrk-track-cart-item-removed" 
                                            value="1" 
                                            <?php echo $data->settings->trackCartItemRemoved ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-track-checkout-begin"><?php echo esc_html__('Track begin checkout', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="trackCheckoutBegin" 
                                            id="lpwootrk-track-checkout-begin" 
                                            value="1" 
                                            <?php echo $data->settings->trackCheckoutBegin ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>

                                <tr>
                                    <th scope="row">
                                        <label for="lpwootrk-track-checkout-progress"><?php echo esc_html__('Track checkout progress', 'livepayments-wootracker'); ?>:</label>
                                    </th>
                                    <td>
                                        <input type="checkbox" 
                                            name="trackCheckoutProgress" 
                                            id="lpwootrk-track-checkout-progress" 
                                            value="1" 
                                            <?php echo $data->settings->trackCheckoutProgress ? 'checked="checked"' : ''; ?> /> 
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p class="submit">
                                <input type="button" 
                                    id="lpwootrk-submit-settings-top" 
                                    name="lpwootrk-submit-settings" 
                                    class="button button-primary lpwootrk-form-submit-btn" 
                                    value="<?php echo esc_html__('Save settings', 'livepayments-wootracker'); ?>" />
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>