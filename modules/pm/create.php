<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

$db = eZDB::instance();
$tpl = eZTemplate::factory();
$Module = $Params['Module'];
$http = eZHTTPTool::instance();
$user = eZUser::currentUser();
$namedParameters = $Module->NamedParameters;
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
$user_class_id = $xrowForumINI->variable( 'ClassIDs', 'User' );
$path_strings = $xrowForumINI->variable( 'PrivateMessaging', 'SelectableUserPathString' );
$error_msg = array();
$name_list_var = array();
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

$name_list_sql = $db->arrayQuery("SELECT ezcontentobject.name FROM ezcontentobject_tree, ezcontentobject where ezcontentobject.id = ezcontentobject_tree.contentobject_id and ezcontentobject.contentclass_id = $user_class_id and ($subtree) and ezcontentobject.id != $user->ContentObjectID group by ezcontentobject.name order by ezcontentobject.name asc;");
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

//before clicking send
if (!$http->hasPostVariable( 'recipient_name' ) AND $namedParameters['RecipientID'])
{
	if ($namedParameters['ReplyID'])
	{
		$reply_id = $namedParameters['ReplyID'];
		$reply_msg = $db->arrayQuery("SELECT * FROM xrowforum_pm_messages where msg_id = $reply_id");
		if(strrpos( $reply_msg[0]['pm_subject'] , "re:") !== false)
		{
			$subject_text = $reply_msg[0]['pm_subject'];
		}
		else
		{
			$subject_text = 're: ' . $reply_msg[0]['pm_subject'];
		}
		$tpl->setVariable( 'subject', $subject_text );
		$tpl->setVariable( 'content', $reply_msg[0]['pm_content']);
	}
	$object = eZContentObject::fetch( $namedParameters['RecipientID'] );
	if (is_object($object) AND $user_class_id == $object->ClassID)
	{
		$block_check = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_network where (user1 = $user->ContentObjectID AND user2 = $object->ID OR user1 = $object->ID AND user2 = $user->ContentObjectID ) AND state = 2;");
		if ($block_check[0]["count"] >= 1)
		{
			array_push($error_msg, "You are in a blocked state with <b>" . $object->attribute('name') . "</b>");
		}
		if ($object->ID == $user->ContentObjectID)
		{
			array_push($error_msg, 'You may not contact yourself.');
		}
		$tpl->setVariable( 'recipient_name', $object->attribute('name') );
	}
	else
	{
		array_push($error_msg, 'You have entered a incorrect UserID');
	}
	$tpl->setVariable( 'errors', $error_msg );
}
else
{
	if ($Module->isCurrentAction('SendMessage'))
	{
		if ($http->hasPostVariable( 'subject' ) and trim( $http->postVariable( "subject" ) ) != "" )
		{
			$subject = $pm_subject = $http->postVariable( 'subject' );
			$tpl->setVariable( 'subject', $subject );
		}
		else
		{
			array_push($error_msg, 'Please enter a Subject.');
		}
		if ($http->hasPostVariable( 'content' ) and trim( $http->postVariable( "content" ) ) != "" )
		{
			$content = $http->postVariable( 'content' );
			$tpl->setVariable( 'content', $content );
		}
		else
		{
			array_push($error_msg, 'Please enter Content.');
		}
		if ($http->hasPostVariable( 'recipient_name' ) and trim( $http->postVariable( "recipient_name" ) ) != "" )
		{
			$recipient_name = $http->postVariable( 'recipient_name' );
			$recipient_name = $db->escapeString($recipient_name);
			$check_user = $db->arrayQuery("SELECT * FROM ezcontentobject where contentclass_id = $user_class_id and name = '$recipient_name';");
			if (count($check_user) >= 1 )
			{
				if(in_array(strtolower($recipient_name), $name_list_var))
				{
					$recipient_obj_id = $check_user[0]["id"];
					$userObject = eZContentObject::fetch( $recipient_obj_id );
				}
				else
				{
					array_push($error_msg, "You are not allowed to contact this user");
				}
			}
			else
			{
				$recipient_obj_id = NULL;
			}
			if ($recipient_obj_id != NULL)
			{
				$tpl->setVariable( 'recipient_name', $recipient_name );
				$block_check = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_network where (user1 = $user->ContentObjectID AND user2 = $recipient_obj_id OR user1 = $recipient_obj_id AND user2 = $user->ContentObjectID ) AND state = 2;");
			}
			else
			{
				$block_check = 0;
			}
			if ($block_check[0]["count"] >= 1)
			{
				array_push($error_msg, "You are in a blocked state with <b>" . $userObject->attribute('name') . "</b>");
			}
			if ($recipient_obj_id == $user->ContentObjectID)
			{
				array_push($error_msg, 'You may not contact yourself.');
			}
			if (count($check_user) >= 1 )
			{
				if (count($error_msg) == 0)
				{
					foreach ($check_user as $user_child)
					{
						#pm sending						
						$time = time();
						$db->begin();
						$subject = $db->escapeString($subject);
						$content = $db->escapeString($content);
						$db->arrayQuery("INSERT INTO xrowforum_pm_messages (msg_id, sender, recipient, pm_subject, pm_content, send_date, read_state, owner_id) values ( NULL, $user->ContentObjectID, $recipient_obj_id, '$subject', '$content', '$time', 0, $user->ContentObjectID);");
						$db->arrayQuery("INSERT INTO xrowforum_pm_messages (msg_id, sender, recipient, pm_subject, pm_content, send_date, read_state, owner_id) values ( NULL, $user->ContentObjectID, $recipient_obj_id, '$subject', '$content', '$time', 0, $recipient_obj_id);");
						$msg_id = $db->lastSerialID();
						$db->commit();
						
						#mail sending
						$notification_setting = $xrowForumINI->variable( 'PrivateMessaging', 'AllowSendingNotificationMails' );
						if($notification_setting == "true")
						{
							#why does this not work!?
							$user_pref = $db->arrayQuery( "SELECT value FROM ezpreferences WHERE user_id = $recipient_obj_id AND name = 'pm_email_notification_disabled'" );
							if (count($user_pref) == 0)
							{
								$currentuserObject = eZContentObject::fetch( $user->ContentObjectID );
								$userDataMap = $userObject->DataMap();
								
								$firstName = $userDataMap['first_name']->Content();
								$ezuser = $userDataMap['user_account']->Content();
								$receiver_mail = $ezuser->attribute( 'email' );							
								$hostname = eZSys::hostname();
								
								$tpl->setVariable( 'sender', $currentuserObject->attribute('name'));
								$tpl->setVariable( 'pm_subject', $pm_subject );
								$tpl->setVariable( 'hostname', $hostname );
								$tpl->setVariable( 'recipient_name', $userObject->Name );
								$tpl->setVariable( 'messageID', $msg_id );
							
								$ini = eZINI::instance();
								$emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
								if ( !$emailSender )
								{
									$emailSender = $ini->variable( 'MailSettings', 'AdminEmail' );
								}
							
								$templateResult = $tpl->fetch( 'design:pm/mail/message_notification.tpl' );
							
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
					$Module->redirectTo( '/pm/outbox' );
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
		if (count($error_msg) != 0)
		{
			$tpl->setVariable( 'errors', $error_msg );
		}
	}
	else if ($Module->isCurrentAction('DiscardMessage'))
	{
		if( $http->hasPostVariable( 'RedirectURIAfterPublish' ) )
		{
			$Module->redirectTo( $http->PostVariable( 'RedirectURIAfterPublish' ) );
		}
		else
		{
			$Module->redirectTo( '/pm/inbox' );
		}
	}
	$tpl->setVariable( 'errors', $error_msg );
}

$Result = array();
$Result['content'] = $tpl->fetch( 'design:pm/create.tpl' );
$Result['path'] = array( array( 'url' => "/",
                                'text' => ezpI18n::tr( 'extension/xrowpm', 'Home' ) ),
							array( 
								'url' => false,
                                'text' => ezpI18n::tr( 'extension/xrowpm', 'New message' ) ) );
								
?>
