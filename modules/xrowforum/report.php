<?php
    
$Module = $Params['Module'];
$current_user = eZUser::currentUser();
$current_user_obj = eZContentObject::fetch( $current_user->ContentObjectID );
$http = eZHTTPTool::instance();
$ini = eZINI::instance();

if( $http->hasPostVariable( 'comment' ) AND $http->hasPostVariable( 'node_id' ) AND $http->hasPostVariable( 'type' ) )
{
	if ($http->PostVariable( 'type' ) == "abuse")
	{
		$receiver_mail = $ini->variable( 'MailSettings', 'EmailSender' );
		if ( !$receiver_mail )
		{
			$receiver_mail = $ini->variable( 'MailSettings', 'AdminEmail' );
		}
		
		$mail = new eZMail();
		$mail->setSender( $current_user->Email );
		$mail->setReceiver( $receiver_mail );
		$mail->setSubject( 'Abuse reported on Node: ' . $http->PostVariable( 'node_id' ) );
		$mail->setBody( $current_user_obj->attribute('name') . '(' . $current_user->ContentObjectID . ') ' . 'has reported an incident on node: ' . $http->PostVariable( 'node_id' ) . ' please have a look at it.' . nl2br("\n\n", false) . 'comment:' . nl2br("\n", false) . '"' . $http->PostVariable( 'comment' ) . '"' );
		$mailResult = eZMailTransport::send( $mail );
	}
	$Module->redirectTo( "content/view/full/" . $http->PostVariable( 'node_id' ) );
}
else
{
	return $Module->handleError( 2, 'kernel' );
}
?>