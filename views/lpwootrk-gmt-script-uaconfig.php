<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<?php if (!empty($data->gaMeasurementId)): ?>
    <?php if ($data->isOptOut && !empty($data->optOutPropertyKey)): ?>
        <script>
            window['<?php echo esc_js($data->optOutPropertyKey); ?>'] = true;
        </script>
    <?php endif; ?>

    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($data->gaMeasurementId); ?>', {
            anonymize_ip: <?php echo $data->enableIpAnonymization ? 'true' : 'false'; ?>,
            link_attribution: <?php echo $data->enableEnhancedLinkAttribution ? 'true' : 'false'; ?>,
            allow_google_signals: <?php echo $data->disableAdvertisingFeatures ? 'true' : 'false'; ?>,
            <?php if (!empty($data->globalCurrency)): ?>
                currency: '<?php echo esc_js($data->globalCurrency); ?>'
            <?php endif; ?>

        });
    </script>
<?php endif; ?>