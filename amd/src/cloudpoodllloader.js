define(['jquery','core/log','https://cdn.jsdelivr.net/gh/justinhunt/cloudpoodll@latest/amd/build/cloudpoodll.min.js'], function($,log,CloudPoodll){
    return {
        init: function(recorderid, thecallback){
            log.debug('aa');
            CloudPoodll.createRecorder(recorderid);
            CloudPoodll.theCallback = thecallback;
            CloudPoodll.initEvents();
        },
        autoCreateRecorders: function(){
            log.debug('bb');
            CloudPoodll.autoCreateRecorders();
            CloudPoodll.initEvents();
        }
}
});