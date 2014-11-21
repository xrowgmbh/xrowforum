<?php

$today = getdate();
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
$ini_status = $xrowForumINI->variable( 'MostUserOn', 'Amount' );
$reg_user_on = eZUser::fetchLoggedInCount();
$anonymous_on = eZUser::fetchAnonymousCount();
$user_on_now = $reg_user_on + $anonymous_on;

if ($ini_status <= $user_on_now)
{
	$xrowForumINI->setVariable( 'MostUserOn', 'Amount' , $user_on_now);
	$xrowForumINI->setVariable( 'MostUserOn', 'Date' , $today[0]);
	$xrowForumINI->save( false, false, 'append' );
}

?>