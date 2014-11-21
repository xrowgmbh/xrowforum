<?php
    
$db = eZDB::instance();
$Module = $Params['Module'];
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
$forum_id = $xrowForumINI->variable( 'IDs', 'ForumIndexPageNodeID' );
$current_userID = eZUser::currentUserID();
$db->begin();
$db->arrayQuery("DELETE FROM xrowforum_notification WHERE user_id = $current_userID");
$db->commit();

$Module->redirectTo( "content/view/full/" . $forum_id );

?>