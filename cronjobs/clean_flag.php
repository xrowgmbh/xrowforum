<?php

$db = eZDB::instance();
$today = time();
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
$keep_duration_days = $xrowForumINI->variable( 'GeneralSettings', 'KeepFlagDuration' );
#60 seconds * 60 minutes * 24 hours = 86400
$keep_duration_timestamp = $keep_duration_days * 86400;
$max_flag_age = $today - $keep_duration_timestamp;

$db->begin();
$db->arrayQuery("DELETE FROM xrowforum_notification WHERE timestamp < $max_flag_age");
$db->commit();

?>