define(['jquery','core/log','mod_cpassignment/cloudpoodllloader'], function($,log,cloudpoodll) {
    "use strict"; // jshint ;_;

    log.debug('cpassignment grade now helper: initialising');

    return {
        init: function(){
             cloudpoodll.autoCreateRecorders();
        }
    };//end of return object

});