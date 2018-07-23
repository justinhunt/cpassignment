/* jshint ignore:start */
define(['jquery','jqueryui', 'core/log','mod_cpassignment/recorderhelper','mod_cpassignment/dialogs'], function($, jqui, log, recorderhelper,dialogs) {

    "use strict"; // jshint ;_;

    log.debug('Activity controller: initialising');

    return {

        cmid: null,
        activitydata: null,
        recorderid: null,
        successmessageid: null,
        sorryboxid: null,
        controls: null,
        moduleclass: null,

        //for making multiple instances
        clone: function(){
            return $.extend(true,{},this);
        },

        //init the activity page js and components
        init: function(props){
            var dd = this.clone();

            //pick up opts from html
            var theid='#amdopts_' + props.widgetid;
            var configcontrol = $(theid).get(0);
            if(configcontrol){
                dd.activitydata = JSON.parse(configcontrol.value);
                $(theid).remove();
            }else{
                //if there is no config we might as well give up
                log.debug('Read Aloud Test Controller: No config found on page. Giving up.');
                return;
            }

            dd.moduleclass = dd.activitydata.moduleclass;
            dd.cmid = props.cmid;
            dd.recorderid = dd.activitydata.recorderid;
            dd.sorryboxid = "sorryboxid-would-go-here"//props.widgetid + '_sorrybox';
            dd.successmessageid = dd.moduleclass  + '_uploadsuccess';
            dd.startbuttonid = dd.moduleclass  + '_startbutton';


            //if the browser doesn't support html5 recording.
            //then warn and do not go any further
            if(!dd.is_browser_ok()){
                $('#' + dd.sorryboxid).show();
                return;
            }

            dd.setup_recorder();
            dd.process_html(dd.activitydata);
            dd.register_events();
        },



        process_html: function(opts){

            var controls ={

                finishedcontainer: $('.' +  opts['finishedcontainer']), //the text showed after finish
                errorcontainer: $('.' +  opts['errorcontainer']), //any errors
                //passagecontainer: $('.' +  opts['passagecontainer']),
                recordingcontainer: $('.' +  opts['recordingcontainer']), //the recorder container
                recordercontainer: $('.' +  opts['recordercontainer']),  //the recorder container
                instructionscontainer: $('.' +  opts['instructionscontainer']), //the activity instructions container
                startbuttoncontainer: $('.' +  opts['moduleclass'] + '_startbuttoncontainer'),  //the start button container
                gradingattemptcontainer: $('.' +  opts['moduleclass'] + '_grading_attempt_cont'),  //the attempt data (current submission) container
                currentfeedbackcontainer: $('.' +  opts['moduleclass'] + '_current_feedback_cont'), // //the feedback container
                attemptstatuscontainer: $('.' +  opts['moduleclass'] + '_attempt_status_cont'), //the attempt status message containter
                startbutton: $('#' +  opts['moduleclass'] + '_startbutton') //the start button
            };
            this.controls = controls;

            switch(opts['pagemode']){
                case 'summary':
                    this.dosummarylayout();
                    break;
                case 'attempt':
                default:

            }
        },

        beginall: function(){
            var m = this;
           // m.dorecord();
            m.passagerecorded = true;
        },

        is_browser_ok: function(){
            return (navigator && navigator.mediaDevices
                && navigator.mediaDevices.getUserMedia);
        },

        setup_recorder: function(){
            var dd = this;

            //Set up the callback functions for the audio recorder

            //originates from the recording:started event
            //See https://api.poodll.com
            var on_recording_start= function(eventdata){
                //do something
            };

            //originates from the recording:ended event
            //See https://api.poodll.com
            var on_recording_end= function(eventdata){
                //do not do anything here
            };

            //data sent here originates from the awaiting_processing event
            //See https://api.poodll.com
           var on_audio_processing= function(eventdata){
                //at this point we know the submission has been uploaded and we know the fileURL
               //so we send the submission
               dd.send_submission(eventdata.mediaurl);
              //and let the user know that they are all done
               dd.dofinishedlayout();
            };

            //init the recorder
            recorderhelper.init(dd.activitydata,
                on_recording_start,
                on_recording_end,
                on_audio_processing);
        },

        register_events: function() {
            var dd = this;
			//events for other controls on the page
            //ie not recorders
            dd.controls.startbutton.click(function(){
                    dd.dorecordinglayout();
                }
            );
        },

        send_submission: function(filename){

            //set up our ajax request
            var xhr = new XMLHttpRequest();
            var that = this;

            //set up our handler for the response
            xhr.onreadystatechange = function(e){
                if(this.readyState===4){
                    if(xhr.status==200){
                        log.debug('ok we got an attempt submission response');
                        //get a yes or forgetit or tryagain
                        var payload = xhr.responseText;
                        var payloadobject = JSON.parse(payload);
                        if(payloadobject){
                            switch(payloadobject.success) {
                                case true:
                                    log.debug('attempted submission accepted');
                                    dialogs.openModal('#' + that.successmessageid);
                                    break;

                                case false:
                                default:
                                    log.debug('attempted item evaluation failure');
                                    if (payloadobject.message) {
                                        log.debug('message: ' + payloadobject.message);
                                    }
                            }
                        }
                     }else{
                        log.debug('Not 200 response:' + xhr.status);
                    }
                }
            };

            var params = "cmid=" + that.cmid + "&filename=" + filename;
            xhr.open("POST",M.cfg.wwwroot + '/mod/cpassignment/ajaxhelper.php', true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.setRequestHeader("Cache-Control", "no-cache");
            xhr.send(params);
        },


        dorecordinglayout: function(){
            var m = this;
            m.controls.instructionscontainer.show();
            m.controls.startbuttoncontainer.hide();
            m.controls.gradingattemptcontainer.hide();
            m.controls.attemptstatuscontainer.hide();
            m.controls.currentfeedbackcontainer.hide();
            m.controls.recordingcontainer.show();
        },


        dosummarylayout: function(){
            var m = this;
            m.controls.instructionscontainer.show();
            // m.controls.passagecontainer.hide();
            m.controls.recordingcontainer.hide();
            m.controls.finishedcontainer.hide();

            m.controls.startbuttoncontainer.show();
            m.controls.gradingattemptcontainer.show();
            m.controls.attemptstatuscontainer.show();
            m.controls.currentfeedbackcontainer.show()

        },

        dofinishedlayout: function(){
            var m = this;
            m.controls.instructionscontainer.hide();
           // m.controls.passagecontainer.hide();
            m.controls.recordingcontainer.show();
            m.controls.finishedcontainer.show();
            m.controls.startbuttoncontainer.hide();
            m.controls.gradingattemptcontainer.hide();
            m.controls.attemptstatuscontainer.hide();
            m.controls.currentfeedbackcontainer.hide();

        },
        doerrorlayout: function(){
            var m = this;
            //m.controls.passagecontainer.hide();
            m.controls.recordingcontainer.hide();
            m.controls.errorcontainer.show();
            m.controls.startbuttoncontainer.hide();
            m.controls.gradingattemptcontainer.hide();
            m.controls.attemptstatuscontainer.hide();
            m.controls.currentfeedbackcontainer.hide();
        }
    };//end of returned object
});//total end
