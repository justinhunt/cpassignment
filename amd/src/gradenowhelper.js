define(['jquery','core/log','mod_cpassignment/cloudpoodllloader','mod_cpassignment/dialogs'], function($,log,cloudpoodll, dialogs) {
    "use strict"; // jshint ;_;

    log.debug('cpassignment grade now helper: initialising');

    return {
        controls: {},
        modulecssclass: null,

        init: function(props){
            this.modulecssclass = props.modulecssclass;
            this.prepare_html();
            this.register_events();
            //instantiate the recorders
            cloudpoodll.autoCreateRecorders();
        },
        prepare_html: function(){
            this.controls.arecbutton = $('#' + this.modulecssclass + '_afeedbackstart');
            this.controls.vrecbutton = $('#' + this.modulecssclass + '_vfeedbackstart');
            this.controls.afeedbackcontainer = $('#' + this.modulecssclass + '_arec_feedback_container');
            this.controls.vfeedbackcontainer = $('#' + this.modulecssclass + '_vrec_feedback_container');
            this.controls.arecbutton.show();
            this.controls.vrecbutton.show();
        },
        register_events: function(){
            var that =this;
            this.controls.arecbutton.click(function(){
                dialogs.openModal('#' + that.modulecssclass + '_arec_feedback_container');
            });
            this.controls.vrecbutton.click(function(){
                dialogs.openModal('#' + that.modulecssclass + '_vrec_feedback_container');
            });
        }
    };//end of return object

});