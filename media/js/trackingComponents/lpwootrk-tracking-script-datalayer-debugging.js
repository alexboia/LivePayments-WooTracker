/**
 * Copyright (c) 2021-2021 LiveDesign SRL
 */

(function($) {
    "use strict";

    $(document).ready(function() {
        lpwootrk.createDataLayerDebugger('dataLayer')
            .startWatcher(250);
    });
})(jQuery);