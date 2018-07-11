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

$string['modulename'] = 'CloudPoodll Assignment';
$string['modulenameplural'] = 'CloudPoodll Assignments';
$string['modulename_help'] = 'A cpassignment of the Poodll family';
$string['cpassignmentfieldset'] = 'Custom example fieldset';
$string['cpassignmentname'] = 'CloudPoodll Assignment';
$string['cpassignmentname_help'] = 'This is the content of the help tooltip associated with the cpassignmentname field. Markdown syntax is supported.';
$string['cpassignment'] = 'cpassignment';
$string['pluginadministration'] = 'CloudPoodll Assignment Administration';
$string['pluginname'] = 'CloudPoodll Assignment Activity';

// Admin Settings strings
$string['instructionslabel'] = 'Activity instructions';
$string['instructionslabel_details'] ='The default text to show in the activity area when creating a new CloudPoodll Assignment.';
$string['defaultinstructions'] = 'Instructions and resources for this CloudPoodll Assignment.';
$string['instructions_editor'] ='Activity description.';
$string['instructions_editor_help'] ='Add your assignment instructions and resources here.  You may include media such as images, audio and video.';
$string['completionlabel'] = 'Completed attempt message';
$string['completionlabel'] = 'completion Message';
$string['completionlabel_details'] ='The default text to show in the completion field when creating a new CloudPoodll Assignment.';
$string['completion_editor'] ='Activity completion message.';
$string['completion_editor_help'] ='Add your assignment completion message  here.  You may include media such as images, audio and video.';
$string['defaultcompletion'] = 'Thanks for your time.';
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
$string['once']='Once';
$string['mediatype']='Media Type';
$string['recordertype']='Recorder Type';

// Sections in instance settings
$string['cpassignmentsettings'] = 'cpassignment settings';
$string['attemptsettings'] = 'Attempt settings';
$string['feedbacksettings'] = 'Grader feedback settings';
$string['fblabel'] = 'Settings';
$string['fbdescription'] = 'Select default feedback method for grading. You can select additional feedback options on the grading page.';

// Checkboxes for grading choices - instance settings.
$string['feedbackaudiolabel'] ='Feedback (audio)';
$string['feedbacktextlabel'] ='Feedback (text)';
$string['feedbackvideolabel'] ='Feedback (video)';
$string['fbtext_details'] = 'Enable text feedback';
$string['fbaudio_details'] = 'Enable audio feedback';
$string['fbvideo_details'] = 'Enable video feedback';

// Capabilities and permissions.
$string['cpassignment:addinstance'] = 'Add a new CloudPoodll Assignment';
$string['cpassignment:view'] = 'View CloudPoodll Assignment';
$string['cpassignment:view'] = 'Preview CloudPoodll Assignment';
$string['cpassignment:itemview'] = 'View items';
$string['cpassignment:itemedit'] = 'Edit items';
$string['cpassignment:tts'] = 'Can use Text To Speech(tts)';
$string['cpassignment:manageattempts'] = 'Can manage CloudPoodll Assignment attempts';
$string['cpassignment:manage'] = 'Can manage CloudPoodll Assignment instances';
$string['cpassignment:preview'] = 'Can preview CloudPoodll Assignment activities';
$string['cpassignment:submit'] = 'Can submit CloudPoodll Assignment attempts';
$string['privacy:metadata'] = 'The Poodll CloudPoodll Assignment plugin does store personal data.';

$string['id']='ID';
$string['name']='Name';
$string['timecreated']='Time Created';
$string['basicheading']='Basic Report';
$string['attemptsheading']='Attempts Report';
$string['attemptsbyuserheading']='User Attempts Report';
$string['gradingheading']='Grading latest attempts for each user.';
$string['gradingbyuserheading']='Grading all attempts for: {$a}';
$string['totalattempts']='Attempts';
$string['overview']='Overview';
$string['overview_help']='Overview Help';
$string['view']='View';
$string['returncourse']='Finish';
$string['preview']='Preview';
$string['viewreports']='View Reports';
$string['reports']='Reports';
$string['viewgrading']='View Grading';
$string['grading']='Grading';
$string['gradenow']='Grade Now';
$string['cannotgradenow']=' - ';
$string['gradenowtitle']='Grading: {$a}';
$string['showingattempt']='Showing attempt for: {$a}';
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
$string['cpassignmenttask'] ='CloudPoodll Assignment Task';
$string['attempt_completed'] ='Finish';

//$string['passagelabel'] ='Reading Passage';

// Grading form labels (and checkboxes)
$string['feedbackaudiolabel'] ='Feedback (audio)';
$string['feedbacktextlabel'] ='Feedback (text)';
$string['feedbackvideolabel'] ='Feedback (video)';

$string['timelimit'] = 'Time Limit';
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
$string['notgradedyet'] = 'Your last submission has not been graded yet';
$string['language'] = 'TTS Language';
$string['deleteattemptconfirm'] = "Are you sure that you want to delete this attempt?";
$string['deletenow']='';
$string['itemsperpage']='Items per page';
$string['itemsperpage_details']='This sets the number of rows to be shown on reports or lists of attempts.';
$string['accuracy']='Accuracy';
$string['accuracy_p']='Acc(%)';
$string['mistakes']='Mistakes';
$string['grade']='Grade';
$string['grade_p']='Grade(%)';
