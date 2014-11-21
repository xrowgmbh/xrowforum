<?php

/*
 * how to run(from ez root): php extension/xrowforum/bin/convert_pm_db.php
 * 
 * INFO: Make sure your new tables are empty!
 * 
 * This script converts the data from ezpm tables into xrowforum_pm tables
 * Don't run the script unless you know what you do.
 * The old Tables will be deleted at the end of the script.
 * convert from: http://projects.ez.no/ezpm to http://projects.ez.no/xrowforum
 */

/*
 * Schema explanation:
 * 
 * ezpm | xrowforum_pm_messages
 * ============================
 * id = msg_id
 * subject = pm_subject
 * text = pm_content
 * data_read = dropped because not used in my extension!
 * date_sent = send_date
 * sender_id = sender
 * sender_name = dropped because its to unsafe in case a user changes his nick name!
 * recipient_id = recipient
 * recipient_name = dropped because its to unsafe in case a user changes his nick name!
 * owner_user_id = owner_id
 * remote_id = dropped because not used in my extension!
 * 1 = read_state (sets all old data to "read")
 * 
 * ezpm_blacklist | xrowforum_pm_network
 * =====================================
 * user_id = user1
 * blocked_user_id = user2
 * 2 = state
 * auto_inc = id
 * 
 * ezpm_contact | xrowforum_pm_network
 * ===================================
 * user_id = user1
 * contact_user_id = user2
 * 1 = state
 * auto_inc = id
 * 
 */

require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => ( "Convert Pms Script" ),
                                     'use-session' => true,
                                     'use-modules' => true,
                                     'use-extensions' => true,
                                     'user' => true ) );

$script->startup();
$script->initialize();
$db = eZDB::instance();
$now = time();

#Transfer Messages
$old_messages = $db->arrayQuery("SELECT * FROM ezpm");
$old_messages_count = count($old_messages);
$cli->output( $old_messages_count. ' messages found' );

if($old_messages_count >= 0)
{
	$cli->output( 'starting transfer from ezpm to xrowforum_pm_messages' );	
	
	$db->begin();
	foreach ($old_messages as $message)
	{
		$msg_content = $db->escapeString($message['text']);
		$msg_subject = $db->escapeString($message['subject']);
		if ($message['date_read'] == 0 AND $message[sender_id] != $message[owner_user_id])
		{	
			#fix if someone sent PMs to himself
			if($message[sender_id] == $message[recipient_id])
			{
				$read = "1";
			}
			else
			{
				$read = "0";
			}
		}
		else
		{
			$read = "1";
		}
		$db->arrayQuery("INSERT INTO xrowforum_pm_messages (msg_id, sender, recipient, pm_subject, pm_content, send_date, read_state, owner_id) values ( $message[id], $message[sender_id], $message[recipient_id], '$msg_subject', '$msg_content' , $message[date_sent], $read, $message[owner_user_id] );");
	}
	$db->commit();
	
	$cli->output( 'finished the message transfer successfully' );	
}

#Transfer Blocked User
$old_blocked = $db->arrayQuery("SELECT * FROM ezpm_blacklist");
$old_blocked_count = count($old_blocked);
$cli->output( $old_blocked_count . ' blocked user found' );
if($old_blocked_count >= 0)
{
	$cli->output( 'starting transfer from ezpm_blacklist to xrowforum_pm_network' );	
	$blocked_state = 2;
	
	$db->begin();
	foreach ($old_blocked as $block)
	{
		$db->arrayQuery("INSERT INTO xrowforum_pm_network (user1, user2, state) values ($block[user_id], $block[blocked_user_id], $blocked_state );");
	}
	$db->commit();
	
	$cli->output( 'finished the blocked user transfer successfully' );
}

#Transfer Friends
$old_friends = $db->arrayQuery("SELECT * FROM ezpm_contact");
$old_friends_count = count($old_friends);
$cli->output( $old_friends_count . ' friends found' );

if($old_friends_count >= 0)
{
	$cli->output( 'starting transfer from ezpm_contact to xrowforum_pm_network' );	
	$friend_state = 1;
	
	$db->begin();
	foreach ($old_friends as $friends)
	{
		$db->arrayQuery("INSERT INTO xrowforum_pm_network (user1, user2, state) values ($friends[user_id], $friends[contact_user_id], $friend_state );");
	}
	$db->commit();
	
	$cli->output( 'finished the friends transfer successfully' );
}

$cli->output( 'all data has been transfered successfully' );

#Drop old Tables
$cli->output( 'dropping old tables..' );

$db->begin();
$db->arrayQuery("DROP TABLE ezpm;");
$db->arrayQuery("DROP TABLE ezpm_blacklist;");
$db->arrayQuery("DROP TABLE ezpm_contact;");
$db->commit();

$cli->output( '.. done!' );

$script->shutdown();

?>