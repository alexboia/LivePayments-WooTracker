<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<?php if (!empty($data->missingPlugins)): ?>
    <div class="error notice">
        <p style="font-weight: bold; margin-bottom: 0px;"><?php echo esc_html__('The LivePayments – WooTracker plug-in requires the following plug-ins are currently missing:', 'livepayments-wootracker'); ?></p>
        <ul style="margin: 5px 0px 10px 0px;">
            <?php foreach ($data->missingPlugins as $p): ?>
                <li>- <?php echo esc_html($p); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>