<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/06/16
 * Time: 19:31
 */

namespace mod_cpassignment;


class constants
{

const M_FRANKY='mod_cpassignment';
const M_FILEAREA_SUBMISSIONS='submission';
const M_FILEAREA_FEEDBACKTEXT='feedbacktext';
const M_FILEAREA_FINISHED='finished';
const M_FILEAREA_INSTRUCTIONS='instructions';
const M_LANG='mod_cpassignment';
const M_TABLE='cpassignment';
const M_USERTABLE='cpassignment_attempt';
const M_MODNAME='cpassignment';
const M_URL='/mod/cpassignment';
const M_CLASS='mod_cpassignment';
const M_UPDATE_CONTROL='mod_cpassignment_update_control';
const M_RECORDERID='therecorderid';
const M_RECORDING_CONTAINER='mod_cpassignment_recording_cont';
const M_RECORDER_CONTAINER='mod_cpassignment_recorder_cont';
const M_INSTRUCTIONS_CONTAINER='mod_cpassignment_instructions_cont';
const M_FINISHED_CONTAINER='mod_cpassignment_finished_cont';
const M_ERROR_CONTAINER='mod_cpassignment_error_cont';
const M_GRADING_MYATTEMPTS_CONTAINER='mod_cpassignment_myattempts_cont';
const M_GRADING_ATTEMPTS_CONTAINER='mod_cpassignment_attempts_cont';
const M_GRADING_PLAYER_CONTAINER='mod_cpassignment_grading_player_cont';
const M_GRADING_CURRENTFB_CONTAINER=
    'mod_cpassignment_current_feedback_cont';
const M_GRADING_CURRENTFBPLAYER_CONTAINER =
    'mod_cpassignment_current_player_cont';

const M_GRADING_PLAYER='mod_cpassignment_grading_player';
const M_GRADING_FORM_SESSIONTIME='mod_cpassignment_gradingform_sessiontime';
const M_GRADING_FORM_FEEDBACKAUDIO='mod_cpassignment_gradingform_feedbackaudio';
const M_GRADING_FORM_FEEDBACKVIDEO='mod_cpassignment_gradingform_feedbackvideo';
const M_GRADING_FORM_SESSIONSCORE='mod_cpassignment_gradingform_sessionscore';

const M_HIDDEN_PLAYER='mod_cpassignment_hidden_player';
const M_HIDDEN_PLAYER_BUTTON='mod_cpassignment_hidden_player_button';
const M_HIDDEN_PLAYER_BUTTON_ACTIVE='mod_cpassignment_hidden_player_button_active';
const M_HIDDEN_PLAYER_BUTTON_PAUSED='mod_cpassignment_hidden_player_button_paused';
const M_HIDDEN_PLAYER_BUTTON_PLAYING='mod_cpassignment_hidden_player_button_playing';

const M_HIDDEN_VIDEO_PLAYER='mod_cpassignment_hidden_video_player';

const M_GRADEHIGHEST= 0;
const M_GRADELOWEST= 1;
const M_GRADELATEST= 2;
const M_GRADEAVERAGE= 3;
const M_GRADENONE= 4;

const M_SUBMITSTATUS_UNKNOWN = 0;  // Can be used when user 'unsubmits' previously submitted
const M_SUBMITSTATUS_WAITING = 1;  // Waiting to be graded.
const M_SUBMITSTATUS_GRADED = 2;   // Graded by teacher.
const M_SUBMITSTATUS_SELECTED = 3; // Selected as submission by student.

const LANG_ENUS = 'en-US';
const LANG_ENGB = 'en-GB';
const LANG_ENAU = 'en-AU';
const LANG_ESUS = 'es-US';
const LANG_FRCA = 'fr-CA';
const LANG_FRFR = 'fr-FR';
const LANG_ITIT = 'it-IT';
const LANG_PTBR = 'pt-BR';
const LANG_KOKR = 'ko-KR';
const LANG_DEDE = 'de-DE';
const LANG_HIIN = 'hi-IN';
const LANG_ENIN = 'en-IN';
const LANG_ESES = 'es-ES';

const LANG_ARAE ='ar-AE';
const LANG_ARSA ='ar-SA';
const LANG_ZHCN ='zh-CN';
const LANG_NLNL ='nl-NL';
const LANG_ENIE ='en-IE';
const LANG_ENWL ='en-WL';
const LANG_ENAB ='en-AB';
const LANG_FAIR ='fa-IR';
const LANG_DECH ='de-CH';
const LANG_HEIL ='he-IL';
const LANG_IDID ='id-ID';
const LANG_JAJP ='ja-JP';
const LANG_MSMY ='ms-MY';
const LANG_PTPT ='pt-PT';
const LANG_RURU ='ru-RU';
const LANG_TAIN ='ta-IN';
const LANG_TEIN ='te-IN';
const LANG_TRTR ='tr-TR';

}