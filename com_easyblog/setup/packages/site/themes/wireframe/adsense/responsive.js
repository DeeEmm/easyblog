<?php if ($this->config->get('integration_google_adsense_script')) { ?>
EasyBlog.require()
.script('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js')
.done(function() {
    (adsbygoogle = window.adsbygoogle || []).push({});
});
<?php } ?>