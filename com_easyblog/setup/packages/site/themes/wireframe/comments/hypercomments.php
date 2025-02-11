<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="hypercomments_widget"></div>
<script type="text/javascript">
    _hcwp = window._hcwp || [];
    var widgetId = <?php echo $widgetId;?>;
    var blogId = <?php echo $blog->id;?>;
    _hcwp.push({widget:"Stream", widget_id: widgetId, xid: blogId});
    (function() {
        if ("HC_LOAD_INIT" in window) return;
        HC_LOAD_INIT = true;
        var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage || "en").substr(0,2).toLowerCase();
        var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
        hcc.src = ("https:" == document.location? "https":"http")+"://w.hypercomments.com/widget/hc/"+widgetId+"/"+lang+"/widget.js";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hcc, s.nextSibling);
    })();
</script>
