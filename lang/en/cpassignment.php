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
 * English strings for cpassignment
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_cpassignment
 * @copyright  2015 Justin Hunt (poodllsupport@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Cloud Poodll Assignment';
$string['modulenameplural'] = 'Cloud Poodll Assignments';
$string['modulename_help'] = 'A cpassignment of the Poodll family';
$string['cpassignmentfieldset'] = 'Custom example fieldset';
$string['cpassignmentname'] = 'Cloud Poodll Assignment';
$string['cpassignmentname_help'] = 'This is the content of the help tooltip associated with the cpassignmentname field. Markdown syntax is supported.';
$string['cpassignment'] = 'cpassignment';
$string['pluginadministration'] = 'Cloud Poodll Assignment Administration';
$string['pluginname'] = 'Cloud Poodll Assignment Activity';

// Admin Settings strings
$string['instructionslabel'] = 'Activity instructions';
$string['instructionslabel_details'] ='The default text to show in the activity area when creating a new Cloud Poodll Assignment.';
$string['defaultinstructions'] = 'Instructions and resources for this Cloud Poodll Assignment.';
$string['instructions_editor'] ='Activity description.';
$string['instructions_editor_help'] ='Add your assignment instructions and resources here.  You may include media such as images, audio and video.';
$string['finishedlabel'] = 'Completion attempt message';
$string['finishedlabel_details'] ='The default message to show when a Cloud Poodll Assignment has been completed.';
$string['finished_editor'] ='Activity completion message.';
$string['finished_editor_help'] ='Add your assignment completion message  here.  You may include media such as images, audio and video.';
$string['defaultfinished'] = 'Thanks for your time.  This recording has been submitted.  You may change the submitted recording before grading is completed.';
$string['apiuser']='Poodll API User ';
$string['apiuser_details']='The Poodll account username that authorises Poodll on this site.';
$string['apisecret']='Poodll API Secret ';
$string['apisecret_details']='The Poodll API secret. See <a href= "https://support.poodll.com/support/solutions/articles/19000083076-cloud-poodll-api-secret">here</a> for more details';
$string['transcribe']='Transcribe audio';
$string['transcribe_details']='';

$string['useast1']='US East';
$string['tokyo']='Tokyo, Japan';
$string['sydney']='Sydney, Australia';
$string['dublin']='Dublin, Ireland';
$string['forever']='Never expire';
$string['en-us']='English (US)';
$string['es-us']='Spanish (US)';
$string['awsregion']='AWS Region';
$string['region']='AWS Region';
$string['expiredays']='Days to keep file';

$string['audio']='Audio';
$string['video']='Video';
$string['onetwothree']='One Two Three';
$string['bmr']='Burnt Rose';
$string['fresh']='Fresh';
$string['once']='Once';
$string['mediatype']='Submission Type';
$string['recordertype']='Recorder Type';

// Sections in instance settings.
$string['cpassignmentsettings'] = 'cpassignment settings';
$string['attemptsettings'] = 'Attempt settings';
$string['feedbacksettings'] = 'Grader feedback settings';
$string['fblabel'] = 'Settings';
$string['fbdescription'] = 'Select default feedback method(s) for grading.';

// Checkboxes for grading choices - instance settings.
$string['feedbackaudiolabel'] ='Feedback (audio)';
$string['feedbacktextlabel'] ='Feedback (text)';
$string['feedbackvideolabel'] ='Feedback (video)';
$string['fbtext_details'] = 'Enable text feedback';
$string['fbaudio_details'] = 'Enable audio feedback';
$string['fbvideo_details'] = 'Enable video feedback';
$string['showgradelabel'] = 'Show grade to students';
$string['showgrade_details'] = 'Check to allow students to see their grade';

// Capabilities and permissions.
$string['cpassignment:addinstance'] = 'Add a new Cloud Poodll Assignment';
$string['cpassignment:view'] = 'View Cloud Poodll Assignment';
$string['cpassignment:view'] = 'Preview Cloud Poodll Assignment';
$string['cpassignment:itemview'] = 'View items';
$string['cpassignment:itemedit'] = 'Edit items';
$string['cpassignment:tts'] = 'Can use Text To Speech(tts)';
$string['cpassignment:manageattempts'] = 'Can manage Cloud Poodll Assignment attempts';
$string['cpassignment:manageownattempts'] = 'Can manage their own Cloud Poodll Assignment attempts';
$string['cpassignment:manage'] = 'Can manage Cloud Poodll Assignment instances';
$string['cpassignment:preview'] = 'Can preview Cloud Poodll Assignment activities';
$string['cpassignment:submit'] = 'Can submit Cloud Poodll Assignment attempts';
$string['privacy:metadata'] = 'The Poodll Cloud Poodll Assignment plugin does store personal data.';

// Reporting
$string['id']='ID';
$string['name']='Name';
$string['timecreated']='Time Created';
$string['basicheading']='Basic Report';
$string['userattemptsheading']='Attempts Report: {$a}';
$string['attemptsheading']='Attempts Report';
$string['attemptsbyuserheading']='User Attempts Report';
$string['gradingheading']='Grading selected attempt for each user.';
$string['viewingbyuserheading']='Viewing all attempts for: {$a}';
$string['submitbyuserheading']='{$a}: Your previous attempts.';
$string['submitbyuserconfirm']='Please confirm: submit this attempt?';
$string['alreadygraded'] = 'Graded: cannot submit';
$string['myattempts']='My Attempts';
$string['totalattempts']='Attempts';
$string['overview']='Overview';
$string['overview_help']='Overview Help';
$string['view']='View';
$string['submit']='Submit';
$string['action']='Action';
$string['returncourse']='Finish';
$string['returnview']='Return to assignment page';
$string['preview']='Preview';
$string['viewreports']='View Reports';
$string['reports']='Reports';
$string['viewgrading']='View Grading';
$string['grading']='Grading';
$string['gradenow']='Grade Now';
$string['cannotgradenow']=' - ';
$string['graded']='Graded';

$string['noselection']='No selection';
$string['viewall']='View all attempts';
$string['gradenowtitle']='Grading: {$a}';
$string['basicreport']='Basic Report';
$string['returntoreports']='Return to Reports';
$string['returntogradinghome']='Return to Grading Top';
$string['exportexcel']='Export to CSV';
$string['mingradedetails'] = 'The minimum grade required to "complete" this activity.';
$string['mingrade'] = 'Minimum Grade';
$string['deletealluserdata'] = 'Delete all user data';
$string['maxattempts'] ='Max. Attempts';
$string['unlimited'] ='unlimited';
$string['gradeoptions'] ='Grade Options';
$string['gradenone'] ='No grade';
$string['gradelowest'] ='lowest scoring attempt';
$string['gradehighest'] ='highest scoring attempt';
$string['gradehighest'] ='highest scoring attempt';
$string['gradelatest'] ='score of latest attempt';
$string['gradeaverage'] ='average score of all attempts';
$string['defaultsettings'] ='Default Settings';
$string['exceededattempts'] ='You have completed the maximum {$a} attempts.';
$string['cpassignmenttask'] ='Cloud Poodll Assignment Task';
$string['attempt_completed'] ='Finish';
$string['currentgrade'] = 'Current grade: ';
$string['status'] = 'Status';
$string['submitnow'] = 'Submit for grading';
$string['unknown'] = 'unknown';
$string['gradeunavailable'] = 'Grade not available';
$string['submitted'] = 'submitted';

//$string['passagelabel'] ='Reading Passage';

// Grading form labels (and checkboxes)
$string['feedbackaudiolabel'] ='Feedback (audio)';
$string['feedbacktextlabel'] ='Feedback (text)';
$string['feedbackvideolabel'] ='Feedback (video)';

$string['timelimit'] = 'Time Limit';
$string['timelimitdetails'] = 'An entry of 0 indicates no time limit.';
$string['gotnosound'] = 'We could not hear you. Please check the permissions and settings for microphone and try again.';
$string['done'] = 'Done';
$string['processing'] = 'Processing';
$string['feedbackheader'] = 'Finished';
$string['beginreading'] = 'Begin Reading';
$string['errorheader'] = 'Error';
$string['uploadconverterror'] = 'An error occured while posting your file to the server. Your submission has NOT been received. Please refresh the page and try again.';
$string['attemptsreport'] = 'Attempts Report';
$string['submitted'] = 'submitted';
$string['id'] = 'ID';
$string['username'] = 'User';
$string['mediafile'] = 'Submission';
$string['timecreated'] = 'Time Created';
$string['nodataavailable'] = 'No Data Available Yet';
$string['saveandnext'] = 'Save .... and next';
$string['reattempt'] = 'Try Again';
$string['notgradedyet'] = 'Your last submission has not been graded yet.';
$string['firstattempt'] = 'Start attempt';
$string['hasnopermission'] = 'You have no permission to view the activity.';
$string['hasnoattempts'] = 'You have used up all your attempts';
$string['unknown'] = 'An unknown error occurred';

// $string['language'] = 'TTS Language';
$string['deleteattemptconfirm'] = "Are you sure that you want to delete this attempt?";
$string['deletenow']='';
$string['itemsperpage']='Items per page';
$string['itemsperpage_details']='This sets the number of rows to be shown on reports or lists of attempts.';
$string['accuracy']='Accuracy';
$string['accuracy_p']='Acc(%)';
$string['mistakes']='Mistakes';
$string['grade']='Current grade (/{$a}):';
$string['grade_p']='Grade(%)';
$string['thesubmission']='The submission:';
$string['showcurrentfb']='Current feedback';
$string['showingattempt']='Grading a Cloud Poodll Assignment';
$string['attemptname']='Name: {$a}';
$string['showingtranscript']='Transcript: ';
$string['feedback']='Feedback';
$string['uploadsuccessmessage']='Your recording has been submitted. Thank you';
$string['listattempts'] = 'View all attempts';
$string['notokenincache']= "Refresh to see license information. Contact Poodll support if there is a problem.";

$string['normalmode'] = 'Normal mode';
$string['anonymousmode'] = 'Anonymous mode';
$string['mode'] = 'Mode';
$string['itemname'] = 'Name';
$string['itemid'] = 'ID';
$string['item'] = 'Item';
$string['timemodified'] = 'Date';
$string['edititem'] = 'Edit';
$string['deleteitem'] = 'Delete';

$string['listtop'] = '{$a} Audio Folder';
$string['listtopdetails'] = 'Your Audio Recordings are all displayed down here.';
$string['listrecaudiolabel'] = 'Record Audio';
$string['close'] = 'Close';
$string['saverecaudiolabel'] = 'Save';
$string['edititemname'] = 'Edit Name';
$string['edititemid'] = 'Edit Id';
$string['newnamefor'] = 'New name for:';
$string['deletedialogtitle'] = 'Delete Media';
$string['deletedialogquestion'] = 'Do you really want to delete audio? {$a}';
$string['deletelabel'] = 'DELETE';
$string['enteritemid'] = 'Enter audio ID';
$string['enteritemname'] = 'Enter audio name';
$string['listrecdownloadlabel'] = 'Download Audio';
$string['downloaditem'] = 'Download';
$string['listshareboxlabel'] = 'Get Public Link';
$string['listsharelabel'] = 'Get Public Link';
$string['resetkeylink'] = 'Reset Public Link';
$string['copysharelink'] = 'Copy Public Link';
$string['resetkeywarning'] = 'Resetting the public link will change it and the previous link will no longer work anymore. Are you sure that you want to do this?';
$string['copysharelinkinstructions'] = 'Copy the text below and distribute it to your users. From the linked location they will be able to record audio which will appear in your list of audio submissions,';
$string['receiptsheader'] = 'Submitted Audio';
$string['onereceiptheader'] = 'Audio Recieved!!';
$string['receiptsinfo'] = 'Receipts Info';

$string['noitemsheader'] = 'Nothing to Display (yet)';
$string['noitemsinfo'] = 'No recordings have been submitted yet. When they have you will see a list of them here';
$string['noitems'] = 'No Items';
$string['unauthtopdetails'] = 'Record audio using the recorder below. After you upload it will be submitted and processed.';

