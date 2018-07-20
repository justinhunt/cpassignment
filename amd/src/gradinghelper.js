define(['jquery', 'core/log','mod_cpassignment/dialogs'], function($,log,dialogs) {
    "use strict"; // jshint ;_;

    log.debug('cpassignment grading helper: initialising');

    return {

        controls: {},
        mediatype: null,
        hiddenplayer: 'mod_cpassignment_hidden_player',
        hiddenplayerbutton: 'mod_cpassignment_hidden_player_button',
        hiddenvideoplayer: 'mod_cpassignment_hidden_video_player',
        videoplayercontainer: 'mod_cpassignment_hiddenvideocontainer',
        activebutton: 'mod_cpassignment_hidden_player_button_active',
        activebuttonpaused: 'mod_cpassignment_hidden_player_button_paused',
        activebuttonplaying: 'mod_cpassignment_hidden_player_button_playing',

        init: function (opts) {
            this.mediatype = opts['mediatype'];
            this.hiddenplayer = opts['hiddenplayerclass'];
            this.hiddenplayerbutton = opts['hiddenplayerbuttonclass'];
            this.register_controls();
            this.register_events();
        },

        register_controls: function(){
            this.controls.hiddenplayer = $('.' + this.hiddenplayer);
            this.controls.hiddenvideoplayer = $('.' + this.hiddenvideoplayer);
            this.controls.hiddenplayerbutton = $('.' + this.hiddenplayerbutton);
        },

        register_events: function(){
            var that = this;
            var audioplayer = this.controls.hiddenplayer;
            //handle the button click
            this.controls.hiddenplayerbutton.click(function(e){
                switch(that.mediatype){

                    case 'video':
                        var mediasrc = $(this).attr('data-audiosource');
                        that.dohiddenvideoplay(mediasrc);
                        break;

                    case 'audio':
                    default:
                        var mediasrc = $(this).attr('data-audiosource');
                        if (mediasrc == audioplayer.attr('src') && !(audioplayer.prop('paused'))) {
                            that.dohiddenstop();
                        } else {
                            that.dohiddenaudioplay(mediasrc);
                        }
                }

            });

        },


        dohiddenaudioplay: function (mediasrc) {
            var m = this;//M.mod_cpassignment.gradinghelper;
            var audioplayer = m.controls.hiddenplayer;
            audioplayer.attr('src', mediasrc);
            audioplayer[0].pause();
            audioplayer[0].load();
            var pp = audioplayer[0].play();
            if (pp !== undefined) {
                pp.then(function() {
                    // Yay we are playing
                }).catch(function(error) {
                    // somethings up ... but we can ignore it
                });
            }
            m.dobuttonicons();
        },

        dohiddenvideoplay: function(mediasrc){

            var m = this;//M.mod_cpassignment.gradinghelper;
            var videoplayer = m.controls.hiddenvideoplayer;
            videoplayer.attr('src', mediasrc);
            videoplayer[0].pause();
            videoplayer[0].load();
            var pp = videoplayer[0].play();
            if (pp !== undefined) {
                pp.then(function() {
                    // Yay we are playing
                }).catch(function(error) {
                    // somethings up ... but we can ignore it
                });
            }
            dialogs.openModal('#' + m.videoplayercontainer);
            m.dobuttonicons();
        },

        dohiddenstop: function () {
            var m = this;// M.mod_cpassignment.gradinghelper;
            var audioplayer =  m.controls.hiddenplayer;
            audioplayer[0].pause();
            m.dobuttonicons();
        },

        dobuttonicons: function (theaudiosrc) {
            var m = this;//M.mod_cpassignment.gradinghelper;
            var audioplayer = m.controls.hiddenplayer;
            if (!theaudiosrc) {
                theaudiosrc = audioplayer.attr('src');
            }
            m.controls.hiddenplayerbutton.each(function (index) {
                var audiosrc = $(this).attr('data-audiosource');
                if (audiosrc == theaudiosrc) {
                    $(this).addClass(m.activebutton);
                    if (audioplayer.prop('paused')) {
                        $(this).removeClass(m.activebuttonplaying);
                        $(this).addClass(m.activebuttonpaused);
                        //for now we make it look like no button is selected
                        //later we can implement better controls
                        $(this).removeClass(m.activebutton);
                    } else {
                        $(this).removeClass(m.activebuttonpaused);
                        $(this).addClass(m.activebuttonplaying);
                    }
                } else {
                    $(this).removeClass(m.activebutton);
                    $(this).removeClass(m.activebuttonplaying);
                    $(this).removeClass(m.activebuttonpaused);
                }
            });
        }
    };//end of return object

});