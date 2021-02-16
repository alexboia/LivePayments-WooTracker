<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<?php $trackingScriptDataName = (!empty($data->trackingScriptDataName) 
    ? $data->trackingScriptDataName 
    : 'trackingScriptData'); ?>

<script type="text/javascript">
    window['<?php echo 'lpwootrk_' . $trackingScriptDataName; ?>'] = <?php echo json_encode($data->trackingScriptData); ?>;
</script>
<?php if (!empty($data->trackingSupportData)): ?>
    <script type="text/javascript">
        window['lpwootrk_<?php echo $trackingScriptDataName; ?>_trackingSupportData'] = <?php echo json_encode($data->trackingSupportData); ?>;
    </script>
<?php endif; ?>