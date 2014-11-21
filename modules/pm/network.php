<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

#constants:
#state 0 = pending friend
#state 1 = friend
#state 2 = block 

$Module = & $Params['Module'];
$http = eZHTTPTool::instance();
$namedParameters = $Module->NamedParameters;
$tpl = eZTemplate::factory();
$db = eZDB::instance();
$current_user = eZUser::currentUser();
$user_id = $current_user->ContentObjectID;
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
$user_class_id = $xrowForumINI->variable( 'ClassIDs', 'User' );
$path_strings = $xrowForumINI->variable( 'PrivateMessaging', 'SelectableUserPathString' );
$name_list_var = array();
$error_msg = array();
$success = "";
$subtree = "";

foreach($path_strings as $path_string)
{
	if ($subtree != "")
	{
		$subtree = $subtree . " OR ezcontentobject_tree.path_string LIKE '$path_string%'";
	}
	else
	{
		$subtree = "ezcontentobject_tree.path_string LIKE '$path_string%'";
	}
}

$name_list_sql = $db->arrayQuery("SELECT ezcontentobject.name FROM ezcontentobject_tree, ezcontentobject where ezcontentobject.id = ezcontentobject_tree.contentobject_id and ezcontentobject.contentclass_id = $user_class_id and ($subtree) and ezcontentobject.id != $user_id group by ezcontentobject.name order by name asc;");
foreach ($name_list_sql as $name)
{
	array_push($name_list_var, strtolower($name['name']));
}
$name_list = implode(",", $name_list_var);
if (count($name_list) >= 1)
{	
	$tpl->setVariable( 'name_list', $name_list);
}	
else
{
	$tpl->setVariable( 'name_list', "");
}

$friend_list = $db->arrayQuery("SELECT * FROM xrowforum_pm_network where (user1 = $user_id or user2 = $user_id) and state = 1;");
$request_list = $db->arrayQuery("SELECT * FROM xrowforum_pm_network where user2 = $user_id and state = 0;");
$block_list = $db->arrayQuery("SELECT * FROM xrowforum_pm_network where (user1 = $user_id or user2 = $user_id) and state = 2;");

if ( ($Module->isCurrentAction('NetworkAction') OR $http->hasPostVariable( 'recipient_name' )) OR ($namedParameters['RelatedUserID'] AND $namedParameters['Type'] != ""))
{
	if ( ($http->hasPostVariable( 'recipient_name' ) and trim( $http->postVariable( "recipient_name" ) ) != "" ) OR ($namedParameters['RelatedUserID'] AND $namedParameters['Type'] != ""))
	{
		if ($http->hasPostVariable( 'recipient_name' ))
		{			
			$recipient_name = $http->postVariable( 'recipient_name' );
			$recipient_name = $db->escapeString($recipient_name);
			$action_type = $http->postVariable( 'action_type');
			$check_user = $db->arrayQuery("SELECT * FROM ezcontentobject where contentclass_id = $user_class_id and name = '$recipient_name';");
			if (count($check_user) >= 1 )
			{
				if(in_array(strtolower($recipient_name), $name_list_var))
				{
					$recipient_obj_id = $check_user[0]["id"];
					$check_user = true;
					$userObject = eZContentObject::fetch( $recipient_obj_id );
				}
				else
				{
					array_push($error_msg, "You are not allowed to contact this user");
				}
			}
			else
			{
				$check_user = false;
			}
			$tpl->setVariable( 'action_type', $action_type );
			$tpl->setVariable( 'recipient_name', $recipient_name );
		}
		else
		{
			$action_type = (int)$namedParameters['Type'];
			if($action_type != 0 or $action_type != 1)
			{
				array_push($error_msg, "Don't try to manipulate the URL!");	
			}
			$userObject = eZContentObject::fetch( $namedParameters['RelatedUserID'] );
			if (is_object ($userObject))
			{	
				if(in_array(strtolower($recipient_name), $name_list_var))
				{
					$recipient_name = $userObject->attribute('name');
					$tpl->setVariable( 'recipient_name', $recipient_name);
					$check_user = true;
				}
				else
				{
					array_push($error_msg, "You are not allowed to contact this user");
				}
			}
			else
			{
				$check_user = false;
			}
			$recipient_obj_id = $namedParameters['RelatedUserID'];
			$tpl->setVariable( 'action_type', $action_type );
		}
		$block_check = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_network where (user1 = $current_user->ContentObjectID AND user2 = $recipient_obj_id OR user1 = $recipient_obj_id AND user2 = $current_user->ContentObjectID ) AND state = 2;");
		if ($block_check[0]["count"] >= 1)
		{
			if ($action_type == 1 )
			{
				array_push($error_msg, "You are already in a blocked state with <b>" . $userObject->attribute('name') . "</b>");
			}
			else
			{
				array_push($error_msg, "You are in a blocked state with <b>" . $userObject->attribute('name') . "</b>");
			}
		}
		if ($action_type == 0)
		{		
			$friend_check = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_network where (user1 = $current_user->ContentObjectID AND user2 = $recipient_obj_id OR user1 = $recipient_obj_id AND user2 = $current_user->ContentObjectID ) AND state = 1;");
			if ($friend_check[0]["count"] >= 1)
			{
				array_push($error_msg, "You are already a friend of <b>" . $userObject->attribute('name') . "</b>");
			}
			$request_check = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_network where (user1 = $current_user->ContentObjectID AND user2 = $recipient_obj_id OR user1 = $recipient_obj_id AND user2 = $current_user->ContentObjectID ) AND state = 0;");
			if ($request_check[0]["count"] >= 1)
			{
				array_push($error_msg, "You have already made a friendrequest to <b>" . $userObject->attribute('name') . "</b>, please be patient.");
			}
		}
		if ($recipient_obj_id == $current_user->ContentObjectID)
		{
			array_push($error_msg, 'You may not have any actions with yourself.');
		}
		if ($check_user)
		{
			if (count($error_msg) == 0)
			{
				if ($action_type == 0)
				{	
					#if friendrequest
					$db->begin();
					$db->arrayQuery("INSERT INTO xrowforum_pm_network (user1, user2, state) values ( $current_user->ContentObjectID, $recipient_obj_id, 0);");
					$db->commit();
					$success = "The request to user " . $recipient_name . " has been done successful.";
					
					#mail sending
					$notification_setting = $xrowForumINI->variable( 'PrivateMessaging', 'AllowSendingNotificationMails' );
					if($notification_setting == "true")
					{
						#why does this not work!?
						#$user_pref = eZPreferences::value('pm_email_notification', false);	
						$user_pref = $db->arrayQuery( "SELECT value FROM ezpreferences WHERE user_id = $recipient_obj_id AND name = 'pm_email_notification'" );
						if (count($user_pref) >= 1)
						{
							if($user_pref[0]['value'] == "true")
							{
								$currentuserObject = eZContentObject::fetch( $current_user->ContentObjectID );
								$userDataMap = $userObject->DataMap();
								
								$firstName = $userDataMap['first_name']->Content();
								$ezuser = $userDataMap['user_account']->Content();
								$receiver_mail = $ezuser->attribute( 'email' );							
								$hostname = eZSys::hostname();
								
								$tpl->setVariable( 'sender', $currentuserObject->attribute('name'));
								$tpl->setVariable( 'hostname', $hostname );
								$tpl->setVariable( 'recipient_name', $userObject->Name );
							
								$ini = eZINI::instance();
								$emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
								if ( !$emailSender )
								{
									$emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
								}
							
								$templateResult = $tpl->fetch( 'design:pm/mail/friendadd_notification.tpl' );
							
								$subject = $tpl->variable( 'subject' );
							
								$mail = new eZMail();
								$mail->setSender( $emailSender );
								$mail->setReceiver( $receiver_mail );
								$mail->setSubject( $subject );
								$mail->setBody( $templateResult );
								$mailResult = eZMailTransport::send( $mail );
							}
						}
					}
				}
				else if($action_type == 1)
				{
					$db->begin();
					#add block
					$db->arrayQuery("INSERT INTO xrowforum_pm_network (user1, user2, state) values ( $current_user->ContentObjectID, $recipient_obj_id, 2);");
					#remove existing friend request					
					$db->arrayQuery("DELETE FROM xrowforum_pm_network where ( (user1 = $current_user->ContentObjectID and user2 = $recipient_obj_id) or (user1 = $recipient_obj_id and user2 = $current_user->ContentObjectID)) AND state = 0;");
					$db->commit();
					$success = "The user " . $recipient_name . " has been blocked successful.";
					$Module->redirectTo( '/pm/network' );
					#redirect just to avoid the caching problemy
				}
				else
				{
					return $Module->handleError( 1, 'kernel' );
				}
			}
		}
		else
		{
			array_push($error_msg, 'The user <b>' . $recipient_name . '</b> does not exist.');
		}
	}
	else
	{
		array_push($error_msg, 'Please enter a recipient.');
	}
}
else
{
	if( $namedParameters['Type'] != "" AND $namedParameters['RelatedUserID'] == NULL)
	{
		array_push($error_msg, "You forgot the UserID parameter in your URL");
	}
}
			
if ($Module->isCurrentAction('ConfirmFriend'))
{	
	if( $http->hasPostVariable( 'requester' ) AND trim($http->PostVariable( 'requester' )) != "")
	{
		$requester = $http->PostVariable( 'requester' );
		$db->begin();
		$db->arrayQuery("UPDATE xrowforum_pm_network SET state = '1' where user2 = $user_id and user1 = $requester and state = 0;");
		$db->commit();
		$success = "Your Friend has been added successful.";
		#mail notification confirm
		$notification_setting = $xrowForumINI->variable( 'PrivateMessaging', 'AllowSendingNotificationMails' );
		if($notification_setting == "true")
		{
			$user_pref = $db->arrayQuery( "SELECT value FROM ezpreferences WHERE user_id = $requester AND name = 'pm_email_notification'" );
			if (count($user_pref) >= 1)
			{
				if($user_pref[0]['value'] == "true")
				{
					$currentuserObject = eZContentObject::fetch( $current_user->ContentObjectID );
					$userObject = eZContentObject::fetch( $requester );
					$userDataMap = $userObject->DataMap();
					
					$firstName = $userDataMap['first_name']->Content();
					$ezuser = $userDataMap['user_account']->Content();
					$receiver_mail = $ezuser->attribute( 'email' );							
					$hostname = eZSys::hostname();
					
					$tpl->setVariable( 'friend', $currentuserObject->attribute('name'));
					$tpl->setVariable( 'hostname', $hostname );
					$tpl->setVariable( 'recipient_name', $userObject->Name );
				
					$ini = eZINI::instance();
					$emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
					if ( !$emailSender )
					{
						$emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
					}
				
					$templateResult = $tpl->fetch( 'design:pm/mail/friendaccept_notification.tpl' );
				
					$subject = $tpl->variable( 'subject' );
				
					$mail = new eZMail();
					$mail->setSender( $emailSender );
					$mail->setReceiver( $receiver_mail );
					$mail->setSubject( $subject );
					$mail->setBody( $templateResult );
					$mailResult = eZMailTransport::send( $mail );
				}
			}
		}
		$Module->redirectTo( '/pm/network'	);
		#that redirect is just to avoid a caching problem
	}
	else
	{
		array_push($error_msg, "User Not Found");
	}
}

if ($Module->isCurrentAction('RejectFriend'))
{	
	if( $http->hasPostVariable( 'requester' ) AND trim($http->PostVariable( 'requester' )) != "")
	{
		$requester = $http->PostVariable( 'requester' );
		$db->begin();
		$db->arrayQuery("DELETE FROM xrowforum_pm_network where user2 = $user_id and user1 = $requester and state = 0;");
		$db->commit();
		$success = "The request has been rejected successful.";
		#mail notification reject
		$notification_setting = $xrowForumINI->variable( 'PrivateMessaging', 'AllowSendingNotificationMails' );
		if($notification_setting == "true")
		{
			$user_pref = $db->arrayQuery( "SELECT value FROM ezpreferences WHERE user_id = $requester AND name = 'pm_email_notification'" );
			if (count($user_pref) >= 1)
			{
				if($user_pref[0]['value'] == "true")
				{
					$currentuserObject = eZContentObject::fetch( $current_user->ContentObjectID );
					$userObject = eZContentObject::fetch( $requester );
					$userDataMap = $userObject->DataMap();
					
					$firstName = $userDataMap['first_name']->Content();
					$ezuser = $userDataMap['user_account']->Content();
					$receiver_mail = $ezuser->attribute( 'email' );							
					$hostname = eZSys::hostname();
					
					$tpl->setVariable( 'friend', $currentuserObject->attribute('name'));
					$tpl->setVariable( 'hostname', $hostname );
					$tpl->setVariable( 'recipient_name', $userObject->Name );
				
					$ini = eZINI::instance();
					$emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
					if ( !$emailSender )
					{
						$emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
					}
				
					$templateResult = $tpl->fetch( 'design:pm/mail/frienddecline_notification.tpl' );
				
					$subject = $tpl->variable( 'subject' );
				
					$mail = new eZMail();
					$mail->setSender( $emailSender );
					$mail->setReceiver( $receiver_mail );
					$mail->setSubject( $subject );
					$mail->setBody( $templateResult );
					$mailResult = eZMailTransport::send( $mail );
				}
			}
		}
		$Module->redirectTo( '/pm/network'	);
		#that redirect is just to avoid a caching problem
	}
	else
	{
		array_push($error_msg, "User Not Found");
	}
}

if ($Module->isCurrentAction('RemoveBlock'))
{	
	if( $http->hasPostVariable( 'block_to' ) AND trim($http->PostVariable( 'block_to' )) != "")
	{
		$blocked_user = $http->PostVariable( 'block_to' );
		$db->begin();
		$db->arrayQuery("DELETE FROM xrowforum_pm_network where user1 = $user_id and user2 = $blocked_user and state = 2;");
		$db->commit();
		$success = "The blocked state has been removed successful.";
		#mail notification sending not neccessary
		$Module->redirectTo( '/pm/network'	);
		#that redirect is just to avoid a caching problem
	}
	else
	{
		array_push($error_msg, "User Not Found");
	}
}


if ($Module->isCurrentAction('RemoveFriend'))
{	
	if( $http->hasPostVariable( 'friend' ) AND trim($http->PostVariable( 'friend' )) != "")
	{
		$friend = $http->PostVariable( 'friend' );
		$db->begin();
		$db->arrayQuery("DELETE FROM xrowforum_pm_network where (user1 = $user_id and user2 = $friend) OR (user1 = $friend and user2 = $user_id) AND state = 1;");
		$db->commit();
		$success = "The friend has been removed successful.";
		#mail notification sending not neccessary
		$Module->redirectTo( '/pm/network'	);
		#that redirect is just to avoid a caching problem
	}
	else
	{
		array_push($error_msg, "User Not Found");
	}
}
	
$tpl->setVariable( 'success', $success );
$tpl->setVariable( 'errors', $error_msg );
$tpl->setVariable( 'user_id', $user_id );
$tpl->setVariable( 'friend_list', $friend_list );
$tpl->setVariable( 'request_list', $request_list );
$tpl->setVariable( 'block_list', $block_list );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:pm/network.tpl' );
$Result['path'] = array( array( 'url' => "/",
                                'text' => ezpI18n::tr( 'extension/xrowpm', 'Home' ) ),
						 array( 'url' => false,
                                'text' => ezpI18n::tr( 'extension/xrowpm', 'My Network' ) ) );
    
?>