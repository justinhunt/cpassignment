define(['jquery', 'core/log'], function($,log) {
    "use strict"; // jshint ;_;

    log.debug('cpassignment one submission: initialising');

    return {

        controls: {},
        selectedattempt: false,
        audioplayerclass: 'mod_cpassignment_oneattempt_audio',
        videoplayerclass: 'mod_cpassignment_oneattempt_video',
        attemptstableclass: 'mod_cpassignment_myattemptstable',
        attemptrowclass: 'mod_cpassignment_attemptrow',
        selectattemptbuttonclass: 'mod_cpassignment_selectattempt',
        selectedattemptlabelclass: 'mod_cpassignment_selectedattemptlabel',
        selectedattemptclass: 'selected_attempt',

        init: function (opts) {

            this.selectedattempt = opts['selectedattempt'];
            this.register_controls();
            this.register_events();
            if(this.selectedattempt) {
                var selectedrow = $('.' + this.attemptrowclass + '[data-attemptid=' + this.selectedattempt + ']');
                this.doLayout(selectedrow);
            }
        },

        register_controls: function(){

            this.controls.audioplayer = $('#' + this.audioplayerclass);
            this.controls.videoplayer = $('#' + this.videoplayerclass);
            this.controls.attemptstable = $('.' + this.attemptstableclass);
            this.controls.selectattemptbutton = $('.' + this.selectattemptbuttonclass);

        },

        register_events: function(){
            var that = this;

            //handle the button click
            this.controls.attemptstable.on('click','.' + this.attemptrowclass, function(e){
                that.doLayout(this);
            });

        },

        doLayout: function(selectedrow){
            //highlight de-highlight rows
            $('.' + this.attemptrowclass).removeClass(this.selectedattemptclass);
            $(selectedrow).addClass(this.selectedattemptclass);


            //fetch media url and media type
            var mediasrc = $(selectedrow).attr('data-mediasource');
            var mediatype = 'none';
            if(mediasrc) {
                mediatype = mediasrc.toLowerCase().endsWith('.mp4') ? 'video' : 'audio';
            }
            switch(mediatype){
                case 'none':
                    this.controls.audioplayer.hide();
                    this.controls.videoplayer.hide();
                    this.controls.audioplayer.attr('src', '');
                    this.controls.videoplayer.attr('src', '');

                case 'video':
                    this.controls.audioplayer.hide();
                    this.controls.videoplayer.show();
                    this.controls.audioplayer.attr('src', '');
                    this.controls.videoplayer.attr('src', mediasrc);
                    break;

                case 'audio':
                default:
                    this.controls.videoplayer.hide();
                    this.controls.audioplayer.show();
                    this.controls.audioplayer.attr('src', mediasrc);
                    this.controls.videoplayer.attr('src', '');
            }
            this.selectedattempt =  $(selectedrow).attr('data-attemptid');
            this.controls.selectattemptbutton.attr('data-attemptid',this.selectedattempt);
            this.controls.selectattemptbutton.show();
        }

    };//end of return object

});