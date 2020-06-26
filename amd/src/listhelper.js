define(['jquery','core/config','core/log','core/ajax','core/templates','core/modal_factory','core/str','core/modal_events',
        'mod_cpassignment/cloudpoodllloader','mod_cpassignment/dialogs','mod_cpassignment/datatables','core/notification'],
    function($,cfg,log,Ajax, templates, ModalFactory, str, ModalEvents, cloudpoodll, dialogs, datatables,notification) {
    "use strict"; // jshint ;_;

    log.debug('cpassignment list helper: initialising');

    return {
        controls: {},
        modulecssclass: null,
        cmid: null,
        moduleid: 0,
        strings: [],
        authmode: 'normal',
        accesskey: '',

        init: function(props){
            this.modulecssclass = props.modulecssclass;
            this.moduleid = props.moduleid;
            this.cmid = props.cmid;
            if(props.hasOwnProperty('authmode')){
                this.authmode=props.authmode;
            }
            if(props.hasOwnProperty('accesskey')){
                this.accesskey=props.accesskey;
            }
            this.prepare_html();
            this.register_events();
            //instantiate the recorders
            this.init_recorder(this.audiorecid);
            //cloudpoodll.autoCreateRecorders();
        },

        init_strings: function(){
            var that = this;
           var strings=['deletedialogtitle','deletedialogquestion','deletelabel'];
           for(var i=0; i<strings.length; i++) {
               str.get_string(strings[i],'mod_cpassignment').then(function (stringdata) {
                   that.strings[i]=stringdata;
               });
           }
        },

        prepare_html: function(){
            this.controls.arecstartbutton = $('#' + this.modulecssclass + '_listaudiorecstart');
            this.controls.shareboxbutton = $('#' + this.modulecssclass + '_listshareboxstart');
            this.controls.shareboxresetkeybutton = $('#' + this.modulecssclass + '_sharebox_resetkey_button');
            this.controls.sharebox = $('#' + this.modulecssclass + '_sharebox');
            this.controls.shareboxcopybutton = $('#' + this.modulecssclass + '_sharebox_copy_button');

            this.controls.areccontainer = $('#' + this.modulecssclass + '_arec_container');
            this.controls.rectable = $('#' + this.modulecssclass + '_itemstable__opts_9999');
            this.controls.itemnamefield = $('.itemform_itemname');
            this.controls.itemidfield = $('.itemform_itemid');
            this.controls.itemfilenamefield =$('.itemform_itemfilename');
            this.controls.itemsubidfield =$('.itemform_itemsubid');
            this.controls.dialogdownloadlink=$('#cp_assignment_download_link');
            this.controls.dialogdownloadbutton=$('#cp_assignment_download_button');
            this.controls.dialogdownloadname=$('#cp_assignment_download_name');
            this.audiorecid = 'therecorderid_mod_cpassignment_listaudiorec';
            this.controls.deletebutton = $('#' + this.modulecssclass + '_itemstable__opts_9999 a[data-type="delete"]');
            this.controls.downloadbutton = $('#' + this.modulecssclass + '_itemstable__opts_9999 a[data-type="download"]');
            this.controls.arecstartbutton.show();
            this.controls.shareboxbutton.show();
            this.controls.thedatatable = datatables.getDataTable(this.modulecssclass + '_itemstable__opts_9999');

        },

        register_events: function(){
            var that =this;

            //recorder dialog show link
            this.controls.arecstartbutton.click(function(){
                //clear fields
                that.controls.itemfilenamefield.val("");
                that.controls.itemidfield.val("");
                that.controls.itemnamefield.val("");
                that.controls.itemsubidfield.val("0");
                dialogs.openModal('#' + that.modulecssclass + '_arec_container');
            });

            //sharebox
            this.controls.shareboxbutton.click(function(){
                that.show_sharebox();
            });

            //sharebox
            this.controls.shareboxresetkeybutton.click(function(){
                ModalFactory.create({
                    type: ModalFactory.types.SAVE_CANCEL,
                    title: 'Reset the Public Link',
                    body: 'Resetting the public link will change it and the previous link will no longer work anymore. Are you sure that you want to do this?'
                })
                .then(function(modal) {
                    modal.setSaveButtonText('RESET');
                    var root = modal.getRoot();
                    root.on(ModalEvents.save, function() {
                        that.do_resetkey(that, that.moduleid);
                    });
                    modal.show();
                });
                return false;
            });

            this.controls.shareboxcopybutton.click(function(){
                var copyText = that.controls.sharebox[0];
                /* Select the text field */
                copyText.select();
                copyText.setSelectionRange(0, 99999); /*For mobile devices*/

                /* Copy the text inside the text field */
                document.execCommand("copy");
            });

            //download links
            this.controls.rectable.on('click','a[data-type="download"]',function(e){
                    var clickedLink = $(e.currentTarget);
                    var elementid = clickedLink.data('id');
                    that.show_download(that, elementid);
                    return false;
            });

            //delete linkc
            this.controls.rectable.on('click','a[data-type="delete"]',function(e){
                        var clickedLink = $(e.currentTarget);
                        var elementid = clickedLink.data('id');
                        var audiotitle = $('td.itemname span[data-itemid="'+ elementid+ '"]').data('value');
                        ModalFactory.create({
                            type: ModalFactory.types.SAVE_CANCEL,
                            title: 'Delete Media',
                            body: 'Do you really want to delete audio? <i>' + audiotitle + '</i>',
                        })
                            .then(function(modal) {
                                modal.setSaveButtonText('DELETE');
                                var root = modal.getRoot();
                                root.on(ModalEvents.save, function() {
                                    that.controls.thedatatable.row( clickedLink.parents('tr')).remove().draw();
                                    //$('tr[data-id="' +elementid+ '"]').remove();
                                    that.do_delete(elementid);
                                });
                                modal.show();
                            });
                        return false;
            });
        },

        show_download(that, elementid){
            var audiotitle = $('td.itemname span[data-itemid="'+ elementid+ '"]').data('value');
            var audiolink = $('td.item audio[data-id="'+ elementid+ '"]').attr('src');
            that.controls.dialogdownloadlink.val(audiolink);
            that.controls.dialogdownloadbutton.attr("href",audiolink);
            that.controls.dialogdownloadname.html('<h3>Download: ' +  audiotitle + '</h3>');
            dialogs.openModal('#' + that.modulecssclass + '_download_container');
        },

        show_sharebox(){
            var that =this;
            /*
            var audiotitle = $('td.itemname span[data-itemid="'+ elementid+ '"]').data('value');
            var audiolink = $('td.item audio[data-id="'+ elementid+ '"]').attr('src');
            that.controls.dialogdownloadlink.val(audiolink);
            that.controls.dialogdownloadbutton.attr("href",audiolink);
            that.controls.dialogdownloadname.html('<h3>Download: ' +  audiotitle + '</h3>');
            */
            dialogs.openModal('#' + that.modulecssclass + '_sharebox_container');
        },

        do_resetkey(that, moduleid){

            Ajax.call([{
                methodname: 'mod_cpassignment_reset_key',
                args: {
                    moduleid: moduleid,
                },
                done: function (ajaxresult) {
                    var payloadobject = JSON.parse(ajaxresult);
                    if (payloadobject) {
                        switch(payloadobject.success) {
                            case true:
                                var accesskey = payloadobject.message;
                                that.controls.sharebox.val(cfg.wwwroot + '/mod/cpassignment/k.php?k=' + accesskey);
                                break;

                            case false:
                            default:
                                if (payloadobject.message) {
                                    log.debug('message: ' + payloadobject.message);
                                }
                        }
                    }
                },
                fail: notification.exception
            }]);

        },

        do_delete(itemid){

            Ajax.call([{
                methodname: 'mod_cpassignment_remove_rec',
                args: {
                    itemid: itemid,
                },
                done: function (ajaxresult) {
                    var payloadobject = JSON.parse(ajaxresult);
                    if (payloadobject) {
                        switch(payloadobject.success) {
                            case true:
                                //all good do nothing
                                break;

                            case false:
                            default:
                                if (payloadobject.message) {
                                    log.debug('message: ' + payloadobject.message);
                                }
                        }
                    }
                },
                fail: notification.exception
            }]);

        },

        init_recorder: function(recorderid){
            var that = this;
            cloudpoodll.init(recorderid,

                function(message){
                    console.log(message);
                    switch(message.type){
                        case 'recording':
                            break;

                        case 'awaitingprocessing':
                            //awaitingprocessing fires often, but we only want to post once
                            if(that.status!='posted') {
                                //do something
                            }
                            that.status='posted';
                            break;

                        case 'filesubmitted':
                            that.controls.itemfilenamefield.val(message.mediaurl);
                            var filename=that.controls.itemfilenamefield.val();
                            var itemid = that.controls.itemidfield.val();
                            var itemname = that.controls.itemnamefield.val();
                            var subid = that.controls.itemsubidfield.val();
                            that.send_submission(subid,filename,itemid,itemname);
                            break;
                    }
                }
            );
        },

        re_init_recorder: function(that,recorderid){
            var rec_div = $('#' + recorderid);
            rec_div.empty();
            rec_div.attr('data-alreadyparsed','false');
            //the initially applied callback lives, so we just do a blank one here
            var callback = false;
            cloudpoodll.init(recorderid,callback);
        },

        insert_new_item: function(that,item){

            templates.render('mod_cpassignment/itemrow',item).then(
                function(html,js){
                   // that.controls.rectable.find('tbody').prepend(html);
                    that.controls.thedatatable.row.add($(html)[0]).draw();
                }
            );
        },

        send_submission: function(subid,filename, itemid, itemname ){
            var that=this;
            var args = {
                    subid: subid,
                    filename: filename,
                    itemname: itemname,
                    itemid: itemid,
                    cmid: that.cmid
                };
            if(authmode==='guest'){
                args.accesskey=that.accesskey;
            }else{
                args.accesskey='none';
            }

            Ajax.call([{
                methodname: 'mod_cpassignment_submit_rec',
                args: args,
                done: function (ajaxresult) {
                    var payloadobject = JSON.parse(ajaxresult);
                    if (payloadobject) {
                        switch(payloadobject.success) {
                            case true:
                                var item = payloadobject.item;
                                that.insert_new_item(that,item);
                                dialogs.closeModal('#' + that.modulecssclass + '_arec_container');
                                that.re_init_recorder(that,that.audiorecid);
                                break;

                            case false:
                            default:
                                if (payloadobject.message) {
                                    log.debug('message: ' + payloadobject.message);
                                }
                                dialogs.closeModal('#' + that.modulecssclass + '_arec_container');
                                that.clear_recorder();
                                that.re_init_recorder(that.audiorecid);

                        }
                    }
                },
                fail: notification.exception
            }]);

        },
    };//end of return object

});