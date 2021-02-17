<?php
/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

    defined('LPWOOTRK_LOADED') or die;
?>

<script type="text/javascript">
    window['lpwootrk_<?php echo $data->trackingScriptDataName; ?>_trackingSupportData']['<?php echo $data->trackingSupportDataKey ?>']['<?php echo $data->trackingSupportDataItemId; ?>'] = <?php echo json_encode($data->trackingSupportDataItemValue); ?>;
</script>