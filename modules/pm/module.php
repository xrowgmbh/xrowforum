<?php

$Module = array( 'name' => 'pm' );

$ViewList = array();

$ViewList['inbox'] = array( 'functions' => array( 'inbox' ),
                            'script' => 'inbox.php',
                            'params' => array( 'sorting', 'direction' ));

$ViewList['outbox'] = array( 'functions' => array( 'outbox' ),
							 'script' => 'outbox.php',
							 'params' => array( 'sorting', 'direction' ) );
				
$ViewList['view'] = array( 'functions' => array( 'view' ),
                           'script' => 'view.php',
                           'params' => array( 'messageID' ));
						   
$ViewList['modify'] = array( 'functions' => array( 'modify' ),
							  'script' => 'modify.php',
							  'params' => array( 'type', 'messageID', 'where' ));			   
						   
$ViewList['create'] = array( 'functions' => array( 'create' ),
							 'script' => 'create.php',
							 'params' => array( 'RecipientID', 'ReplyID' ),
							 'single_post_actions' => array( 'PublishButton' => 'SendMessage',
															 'DiscardButton' => 'DiscardMessage')
						);

$ViewList['network'] = array( 'functions' => array( 'network' ),
							  'script' => 'network.php',
							  'params' => array( 'Type', 'RelatedUserID' ),
							  'single_post_actions' => array( 'RemoveBlockButton' => 'RemoveBlock',
															  'RemoveFriendButton' => 'RemoveFriend',
															  'ConfirmFriendButton' => 'ConfirmFriend',
															  'RejectFriendButton' => 'RejectFriend',
															  'NetworkActionButton' => 'NetworkAction',
															  'AddFriendButton' => 'AddFriend',
															  'AddBlockButton' => 'AddBlock')
													  );

$FunctionList = array();
$FunctionList['inbox'] = array( );
$FunctionList['outbox'] = array( );
$FunctionList['view'] = array( );
$FunctionList['modify'] = array( );
$FunctionList['create'] = array( );
$FunctionList['network'] = array( );

?>
