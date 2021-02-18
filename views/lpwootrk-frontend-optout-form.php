<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<script type="text/javascript">
    var lpwootrk_ajaxBaseUrl = '<?php echo esc_js($data->ajaxBaseUrl); ?>';
    var lpwootrk_optoutNonce = '<?php echo esc_js($data->optoutNonce); ?>';
    var lpwootrk_optoutAction = '<?php echo esc_js($data->optoutAction); ?>';
</script>

<div id="lpwootrk-optout-container" class="lpwootrk-optout-container">
    <?php if ($data->isOptOut): ?>
        <p id="lpwootrk-optout-content" class="lpwootrk-is-optout">
            <?php echo esc_html__('You have already opted out from Google Analytics tracking.', 'livepayments-wootracker'); ?>
        </p>
    <?php else: ?>
        <p id="lpwootrk-optout-content" class="lpwootrk-is-not-optout">
            <span id="lpwootrk-optout-info"><?php echo esc_html__('Google Analytics tracking is currently active.', 'livepayments-wootracker'); ?></span>
            <a id="lpwootrk-optout-link" href="javascript:void(0);"><?php echo esc_html__('Click here to opt-out from Google Analytics tracking.', 'livepayments-wootracker'); ?></a>
        </p>
        <p id="lpwootrk-optout-result" style="display: none;"></p>
    <?php endif; ?>
</div>