<?php
//This file is used for core configuration
//for now, lay out the tree of spaces
$core = array();
$core['config']= array();
$core['config']['permissions']= array();
$core['text'] = array();
$core['text']['panel'] = array();
$core['text']['user'] = array();
$core['text']['login'] = array();
$core['text']['chview'] = array();
$core['text']['testing'] = array();
$core['text']['testing']['modes'] = array();
$core['text']['editing'] = array();
$core['text']['presets'] = array();

//now include another file which fills in this information.
{//Permissions
$core['config']['permissions']['banned'] = 0;
$core['config']['permissions']['student'] = 1;
$core['config']['permissions']['stumod'] = 2;
$core['config']['permissions']['TA'] =7;
$core['config']['permissions']['teacher'] = 8;
$core['config']['permissions']['webmaster'] = 10;
}
{//User information
    $core['text']['user']['username'] = 'Username';
    $core['text']['user']['password'] = 'Password';
    $core['text']['user']['password2'] = 'Verify Password';
    $core['text']['user']['email'] = 'E-mail address';
    $core['text']['user']['sms'] = 'Phone E-mail address';
    $core['text']['user']['editprofile'] = 'Edit your profile';
    $core['text']['user']['editXProfile'] = 'Edit this profile';
    $core['text']['user']['update']['my'] = 'Update My profile';
    $core['text']['user']['update']['other'] = 'Update this profile';
    $core['text']['user']['title']['my'] = 'Edit your profile';
    $core['text']['user']['title']['this'] = 'Edit this profile';
}
{//Panel Section
    $core['text']['panel']['yoptions']['logout'] = 'Log Out';
    $core['text']['panel']['yoptions']['editprofile'] = 'Edit your profile';
    $core['text']['panel']['yoptions']['print'] = 'Print Cornell Notes';
    $core['text']['panel']['quote'] = 'Random Quote';
    $core['text']['panel']['chapters'] = 'Book Chapters';
    $core['text']['panel']['online'] = 'Who\'s on-line now?';
    $core['text']['panel']['admin']['title'] = 'Administration';
    $core['text']['panel']['admin']['createu'] = 'Create User';
    $core['text']['panel']['admin']['viewu'] = 'View Users';
    $core['text']['panel']['admin']['sendme'] = 'Send Mass Email';
    $core['text']['panel']['admin']['resetr'] = 'Reset Log in records';
    $core['text']['panel']['admin']['editc'] = 'Edit Calendar';
    $core['text']['panel']['admin']['editq'] = 'Edit Quotes';
    
}
{
    
}
?>