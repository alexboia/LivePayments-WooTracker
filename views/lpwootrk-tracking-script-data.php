<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<script type="text/javascript">
    window['<?php echo 'lpwootrk_' . (!empty($data->trackingScriptDataName) ? $data->trackingScriptDataName : 'trackingScriptData'); ?>'] = <?php echo json_encode($data->trackingScriptData); ?>;
</script>
<?php if (!empty($data->trackingSupportData)): ?>
    <script type="text/javascript">
        window['lpwootrk_trackingSupportData'] = <?php echo json_encode($data->trackingSupportData); ?>;
    </script>
<?php endif; ?>