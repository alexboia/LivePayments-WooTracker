<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<?php if (!empty($data->gtmTrackingId)): ?>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });

            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer'
                    ? '&l=' + l
                    : '';

            j.async = true; 
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;

            f.parentNode.insertBefore(j,f);
        })(window, document, 'script', 'dataLayer', '<?php echo esc_js($data->gtmTrackingId); ?>');
    </script>
    <!-- End Google Tag Manager -->
<?php elseif (!empty($data->gaMeasurementId)): ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($data->gaMeasurementId); ?>"></script>
<?php endif; ?>