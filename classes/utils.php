<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Grade Now for cpassignment plugin
 *
 * @package    mod_cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 namespace mod_cpassignment;
defined('MOODLE_INTERNAL') || die();

use \mod_cpassignment\constants;


/**
 * Event observer for mod_cpassignment
 *
 * @package    mod_cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils{

   // function cpassignment_editor_with_files_options($context){
    public static function editor_with_files_options($context){

        return array('maxfiles' => EDITOR_UNLIMITED_FILES,
                'noclean' => true, 'context' => $context,
                'subdirs' => true);
    }

    public static function editor_no_files_options($context){
        return array('maxfiles' => 0, 'noclean' => true,
                'context' => $context);
    }

    public static function select_attempt_as_submission($moduleinstance,$userid,$attemptid){
        global $DB;

        if (!$DB->record_exists(constants::M_USERTABLE, array('id'=>$attemptid))){
            print_error("Could not select attempt because it does not exist");
            return false;
        }

        // Clear all status fields for this user's attempts.
        $records = $DB->get_records(constants::M_USERTABLE, array('cpassignmentid'=>$moduleinstance->id, 'userid' => $userid));
        foreach ($records as $record) {
            // At the moment we are only using status graded and selected
            // therefore this will work, we won't get here if status is
            // graded.  If additional cases added, might have to check.
            $DB->set_field(constants::M_USERTABLE, 'status',
            constants::M_SUBMITSTATUS_UNKNOWN,
            array('id' => $record->id));
        }
        // Set status of this one to submitted.
        if (!$DB->set_field(constants::M_USERTABLE, 'status',
            constants::M_SUBMITSTATUS_SELECTED, array('id' => $attemptid))) {
            print_error("Could not unsubmit an attempt");
            return false;
        }
        return true;
    }

    //are we willing and able to transcribe submissions?
    public static function can_transcribe($instance)
    {
        //we default to true
        //but it only takes one no ....
        $ret = true;

        //currently only useast1 can transcribe
        switch($instance->region){
            case "useast1":
                break;
            default:
                $ret = false;
        }

        //if user disables ai, we do not transcribe
        if(!$instance->transcribe){
            $ret =false;
        }

        return $ret;
    }

    //we use curl to fetch transcripts from AWS and Tokens from cloudpoodll
    //this is our helper
   public static function curl_fetch($url,$postdata=false)
   {
       $ch = curl_init($url);
       curl_setopt($ch, CURLOPT_HEADER, false);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       if ($postdata) {
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        }

       $contents = curl_exec($ch);
       curl_close($ch);
       return $contents;
    }

    //We need a Poodll token to make this happen
    public static function fetch_token($apiuser, $apisecret)
    {

            $cache = \cache::make_from_params(\cache_store::MODE_APPLICATION, 'mod_cpassignment', 'token');
            $tokenobject = $cache->get('recentpoodlltoken');
            $tokenuser = $cache->get('recentpoodlluser');

            //if we got a token and its less than expiry time
            // use the cached one
            if($tokenobject && $tokenuser && $tokenuser==$apiuser){
                if($tokenobject->validuntil == 0 || $tokenobject->validuntil > time()){
                    return $tokenobject->token;
                }
            }

            // Send the request & save response to $token_response
            $token_url ="https://cloud.poodll.com/local/cpapi/poodlltoken.php";
            $postdata = array(
                'username' => $apiuser,
                'password' => $apisecret,
                'service'=>'cloud_poodll'
            );
            $token_response = self::curl_fetch($token_url,$postdata);
            if ($token_response) {
                $resp_object = json_decode($token_response);
                if($resp_object && property_exists($resp_object,'token')) {
                    $token = $resp_object->token;
                    //store the expiry timestamp and adjust it for diffs between our server times
                    if($resp_object->validuntil) {
                        $validuntil = $resp_object->validuntil - ($resp_object->poodlltime - time());
                    }else{
                        $validuntil = 0;
                    }

                    //cache the token
                    $tokenobject = new \stdClass();
                    $tokenobject->token = $token;
                    $tokenobject->validuntil = $validuntil;
                    $cache->set('recentpoodlltoken', $tokenobject);
                    $cache->set('recentpoodlluser', $apiuser);

                }else{
                    $token = '';
                    if($resp_object && property_exists($resp_object,'error')) {
                        //ERROR = $resp_object->error
                    }
                }
            }
            return $token;
    }

   public static function fetch_s3_file($url)
   {
       $s3_file = self::curl_fetch($url, false);
       if (!$s3_file || strpos($s3_file, "<Error><Code>AccessDenied</Code>") > 0) {
           return false;
       } else {
            return $s3_file;
       }
   }

  public static function get_region_options(){
      return array(
        "useast1" => get_string("useast1",'mod_cpassignment'),
          "tokyo" => get_string("tokyo",'mod_cpassignment'),
          "sydney" => get_string("sydney",'mod_cpassignment'),
          "dublin" => get_string("dublin",'mod_cpassignment')
      );
  }
    public static function get_recordertype_options(){
    return array(
        "bmr" => get_string("bmr",'mod_cpassignment'),
        "onetwothree" => get_string("onetwothree",'mod_cpassignment'),
        "once" => get_string("once",'mod_cpassignment')
    );
}

    public static function get_mediatype_options(){
        return array(
            "audio" => get_string("audio",'mod_cpassignment'),
            "video" => get_string("video",'mod_cpassignment')
        );
    }

  public static function get_expiredays_options(){
      return array(
          "1"=>"1",
          "3"=>"3",
          "7"=>"7",
          "30"=>"30",
          "90"=>"90",
          "180"=>"180",
          "365"=>"365",
          "730"=>"730",
          "9999"=>get_string('forever','mod_cpassignment')
      );
  }

    /**
     * The html part of the recorder (js is in the fetch_activity_amd)
     */
    public static function fetch_recorder($moduleinstance,$recorderid, $token, $update_control,$timelimit,$mediatype,$recordertype){
        global $CFG;

        $hints = new \stdClass();
        $string_hints = base64_encode (json_encode($hints));
        $can_transcribe = \mod_cpassignment\utils::can_transcribe($moduleinstance);
        $transcribe = $can_transcribe  ? "1" : "0";
        $subtitle=0;//$moduleinstance->subtitle
        switch($mediatype){
            case 'video':
                $width="450";
                $height="500";
                break;


            case 'audio':
            default:
                $mediatype = 'audio';//just in case we got something weird
                $width="360";
                $height="240";
                break;
        }


        $recorderdiv= \html_writer::div('', constants::M_CLASS  . '_center cloudpoodll',
            array('id'=>$recorderid,
                'data-id'=>$recorderid,
                'data-parent'=>$CFG->wwwroot,
                'data-localloader'=>'/mod/cpassignment/poodllloader.html',
                'data-media'=>$mediatype,
                'data-type'=>$recordertype,
                'data-width'=>$width,
                'data-height'=>$height,
                'data-updatecontrol'=>$update_control,
                'data-timelimit'=> $timelimit,
                'data-transcode'=>"1",
                'data-transcribe'=>$transcribe,
                'data-subtitle'=>$subtitle,
                //'data-language'=>$moduleinstance->language,
                'data-expiredays'=>$moduleinstance->expiredays,
                'data-region'=>$moduleinstance->region,
                'data-hints'=>$string_hints,
                'data-token'=>$token //localhost
                //'data-token'=>"643eba92a1447ac0c6a882c85051461a" //cloudpoodll
            )
        );

        //return it
        return $recorderdiv;
    }

    //puts the content passed in, into div containers that Bootstrap will recognize as a modal (hidden on load etc)
    public static function fetch_modal_container($modalcontent,$containertag){
        $containerid = constants::M_CLASS  . '_' . $containertag;
        //this is the modal container that hides it. The id is used to trigger the modal. The trigger code is not set up here.
        $modal_attributes = array('id'=>$containerid, 'role'=>'dialog','aria-hidden'=>'true','tab-index'=>'-1');
        $modal =  \html_writer::div($modalcontent, $containerid  . ' hidden modal fade',$modal_attributes);
        return $modal;
    }


    /**
     *  A template to make the content of a modal(header/content/footer(+buttons)
     */
    public static function fetch_modal_content($title,$content) {

        $modalcontent=  '<div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">';
        $modalcontent .=  $title;
        $modalcontent .= '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">';
        $modalcontent .=  $content;
        $modalcontent .=  '</div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>';

        return $modalcontent;
    }

/*
    public static function get_lang_options(){
       return array(
            'en-US'=>get_string('en-us','mod_cpassignment'),
           'es-US'=>get_string('es-us','mod_cpassignment')
       );

      return array(
			"none"=>"No TTS",
			"af"=>"Afrikaans",
			"sq"=>"Albanian",
			"am"=>"Amharic",
			"ar"=>"Arabic",
			"hy"=>"Armenian",
			"az"=>"Azerbaijani",
			"eu"=>"Basque",
			"be"=>"Belarusian",
			"bn"=>"Bengali",
			"bh"=>"Bihari",
			"bs"=>"Bosnian",
			"br"=>"Breton",
			"bg"=>"Bulgarian",
			"km"=>"Cambodian",
			"ca"=>"Catalan",
			"zh-CN"=>"Chinese (Simplified)",
			"zh-TW"=>"Chinese (Traditional)",
			"co"=>"Corsican",
			"hr"=>"Croatian",
			"cs"=>"Czech",
			"da"=>"Danish",
			"nl"=>"Dutch",
			"en"=>"English",
			"eo"=>"Esperanto",
			"et"=>"Estonian",
			"fo"=>"Faroese",
			"tl"=>"Filipino",
			"fi"=>"Finnish",
			"fr"=>"French",
			"fy"=>"Frisian",
			"gl"=>"Galician",
			"ka"=>"Georgian",
			"de"=>"German",
			"el"=>"Greek",
			"gn"=>"Guarani",
			"gu"=>"Gujarati",
			"xx-hacker"=>"Hacker",
			"ha"=>"Hausa",
			"iw"=>"Hebrew",
			"hi"=>"Hindi",
			"hu"=>"Hungarian",
			"is"=>"Icelandic",
			"id"=>"Indonesian",
			"ia"=>"Interlingua",
			"ga"=>"Irish",
			"it"=>"Italian",
			"ja"=>"Japanese",
			"jw"=>"Javanese",
			"kn"=>"Kannada",
			"kk"=>"Kazakh",
			"rw"=>"Kinyarwanda",
			"rn"=>"Kirundi",
			"xx-klingon"=>"Klingon",
			"ko"=>"Korean",
			"ku"=>"Kurdish",
			"ky"=>"Kyrgyz",
			"lo"=>"Laothian",
			"la"=>"Latin",
			"lv"=>"Latvian",
			"ln"=>"Lingala",
			"lt"=>"Lithuanian",
			"mk"=>"Macedonian",
			"mg"=>"Malagasy",
			"ms"=>"Malay",
			"ml"=>"Malayalam",
			"mt"=>"Maltese",
			"mi"=>"Maori",
			"mr"=>"Marathi",
			"mo"=>"Moldavian",
			"mn"=>"Mongolian",
			"sr-ME"=>"Montenegrin",
			"ne"=>"Nepali",
			"no"=>"Norwegian",
			"nn"=>"Norwegian(Nynorsk)",
			"oc"=>"Occitan",
			"or"=>"Oriya",
			"om"=>"Oromo",
			"ps"=>"Pashto",
			"fa"=>"Persian",
			"xx-pirate"=>"Pirate",
			"pl"=>"Polish",
			"pt-BR"=>"Portuguese(Brazil)",
			"pt-PT"=>"Portuguese(Portugal)",
			"pa"=>"Punjabi",
			"qu"=>"Quechua",
			"ro"=>"Romanian",
			"rm"=>"Romansh",
			"ru"=>"Russian",
			"gd"=>"Scots Gaelic",
			"sr"=>"Serbian",
			"sh"=>"Serbo-Croatian",
			"st"=>"Sesotho",
			"sn"=>"Shona",
			"sd"=>"Sindhi",
			"si"=>"Sinhalese",
			"sk"=>"Slovak",
			"sl"=>"Slovenian",
			"so"=>"Somali",
			"es"=>"Spanish",
			"su"=>"Sundanese",
			"sw"=>"Swahili",
			"sv"=>"Swedish",
			"tg"=>"Tajik",
			"ta"=>"Tamil",
			"tt"=>"Tatar",
			"te"=>"Telugu",
			"th"=>"Thai",
			"ti"=>"Tigrinya",
			"to"=>"Tonga",
			"tr"=>"Turkish",
			"tk"=>"Turkmen",
			"tw"=>"Twi",
			"ug"=>"Uighur",
			"uk"=>"Ukrainian",
			"ur"=>"Urdu",
			"uz"=>"Uzbek",
			"vi"=>"Vietnamese",
			"cy"=>"Welsh",
			"xh"=>"Xhosa",
			"yi"=>"Yiddish",
			"yo"=>"Yoruba",
			"zu"=>"Zulu"
		);

   }
*/
}
