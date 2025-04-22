define(['jquery','core/log','mod_cpassignment/cloudpoodll'], function($,log,CloudPoodll){
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