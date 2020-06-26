define(['jquery','core/log','https://cdn.jsdelivr.net/gh/justinhunt/cloudpoodll@latest/amd/build/cloudpoodll.min.js'], function($,log,CloudPoodll){
    return {
        init: function(recorderid, thecallback){
            CloudPoodll.createRecorder(recorderid);
            //if no callback was passed on, it might be a re-init, so we just skip
            if(thecallback !==false) {
                CloudPoodll.theCallback = thecallback;
                CloudPoodll.initEvents();
            }
        },
        autoCreateRecorders: function(){
            CloudPoodll.autoCreateRecorders();
            CloudPoodll.initEvents();
        }
}
});