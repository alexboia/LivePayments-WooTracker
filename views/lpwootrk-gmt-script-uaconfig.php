<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<?php if (!empty($data->gaMeasurementId)): ?>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($data->gaMeasurementId); ?>');
    </script>
<?php endif; ?>