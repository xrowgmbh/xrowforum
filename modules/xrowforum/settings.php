<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();
$db = eZDB::instance();
$Module = $Params['Module'];
$sys = eZSys::instance();
$error = array();
$success = false;

#create instance of xrowforum.ini
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );

#change variables if form sent with post vars
if( $http->hasPostVariable( "save_general_settings" ))
{
	if (is_numeric($http->postVariable( "forum_id" )) AND trim( $http->postVariable( "forum_id" ) ) != "")
	{
		$xrowForumINI->setVariable( 'IDs', 'ForumIndexPageNodeID' , $http->PostVariable( "forum_id" ));
	}
	else
	{
		array_push($error, "Please select a proper ForumIndexPageObjectID");
	}
    if (is_numeric($http->postVariable( "mod_id" )) AND trim( $http->postVariable( "mod_id" ) ) != "")
    {
        $xrowForumINI->setVariable( 'IDs', 'ModeratorGroupObjectID', $http->postVariable( "mod_id" ) );
    }
    else
    {
        array_push($error, "Please select a proper ModeratorGroupID");
    }
    if (is_numeric($http->postVariable( "user_class" )) AND trim( $http->postVariable( "user_class" ) ) != "")
    {
        $xrowForumINI->setVariable( 'ClassIDs', 'User', $http->postVariable( "user_class" ) );
    }
    else
    {
        array_push($error, "Please select a proper UserContentClassID");
    }
    if (is_numeric($http->postVariable( "topic_class" )) AND trim( $http->postVariable( "topic_class" ) ) != "")
    {
        $xrowForumINI->setVariable( 'ClassIDs', 'ForumTopic', $http->postVariable( "topic_class" ) );
    }
    else
    {
        array_push($error, "Please select a proper TopicContentClassID");
    }
    if (is_numeric($http->postVariable( "reply_class" )) AND trim( $http->postVariable( "reply_class" ) ) != "")
    {
        $xrowForumINI->setVariable( 'ClassIDs', 'ForumReply', $http->postVariable( "reply_class" ) );
    }
    else
    {
        array_push($error, "Please select a proper PostContentClassID");
    }
	if($http->haspostVariable( "checkbox_ranking" ))
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'Rankings', 'enabled' );
    }
    else
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'Rankings', 'disabled' );
    }
    if($http->haspostVariable( "checkbox_wti" ))
	{
		$xrowForumINI->setVariable( 'GeneralSettings', 'WordToImage', 'enabled' );
	}
	else
	{
		$xrowForumINI->setVariable( 'GeneralSettings', 'WordToImage', 'disabled' );
	}
	if($http->haspostVariable( "checkbox_censoring" ))
	{
		$xrowForumINI->setVariable( 'GeneralSettings', 'Censoring', 'enabled' );
	}
	else
	{
		$xrowForumINI->setVariable( 'GeneralSettings', 'Censoring', 'disabled' );
	}
    if (is_numeric($http->postVariable( "HotTopicNumber" )) AND trim( $http->postVariable( "HotTopicNumber" ) ) != "")
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'HotTopicNumber', $http->postVariable( "HotTopicNumber" ) );
    }
    else
    {
        array_push($error, "Please select a proper HotTopics value");
    }
    if (is_numeric($http->postVariable( "PostsPerPage" )) AND trim( $http->postVariable( "PostsPerPage" ) ) != "")
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'PostsPerPage', $http->postVariable( "PostsPerPage" ) );
    }
    else
    {
        array_push($error, "Please select a proper PostsPerPage value");
    }
    if (is_numeric($http->postVariable( "TopicsPerPage" )) AND trim( $http->postVariable( "TopicsPerPage" ) ) != "")
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'TopicsPerPage', $http->postVariable( "TopicsPerPage" ) );
    }
    else
    {
        array_push($error, "Please select a proper TopicsPerPage value");
    }
    if (is_numeric($http->postVariable( "PostHistoryLimit" )) AND trim( $http->postVariable( "PostHistoryLimit" ) ) != "")
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'PostHistoryLimit', $http->postVariable( "PostHistoryLimit" ) );
    }
    else
    {
        array_push($error, "Please select a proper PostHistoryLimit value");
    }
    #if (is_numeric($http->postVariable( "SignatureLength" )) AND trim( $http->postVariable( "SignatureLength" ) ) != "")
    #{
    #    $xrowForumINI->setVariable( 'GeneralSettings', 'SignatureLength', $http->postVariable( "SignatureLength" ) );
    #}
    #else
    #{
    #    array_push($error, "Please select a proper SignatureLength");
    #}
    if($http->haspostVariable( "checkbox_si" ))
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'SignatureImage', 'enabled' );
    }
    else
    {
        $xrowForumINI->setVariable( 'GeneralSettings', 'SignatureImage', 'disabled' );
    }
    #if (is_numeric($http->postVariable( "SignatureHeight" )) AND trim( $http->postVariable( "SignatureHeight" ) ) != "")
    #{
    #    $xrowForumINI->setVariable( 'MaxImageSizes', 'SignatureHeight', $http->postVariable( "SignatureHeight" ));
    #}
    #else
    #{
    #    array_push($error, "Please select a proper SignatureHeight");
    #}
    #if (is_numeric($http->postVariable( "SignatureWidth" )) AND trim( $http->postVariable( "SignatureWidth" ) ) != "")
    #{
    #    $xrowForumINI->setVariable( 'MaxImageSizes', 'SignatureWidth', $http->postVariable( "SignatureWidth" ) );
    #}
    #else
    #{
    #    array_push($error, "Please select a proper SignatureWidth");
    #}
    #if (is_numeric($http->postVariable( "EmbeddedImageHeight" )) AND trim( $http->postVariable( "EmbeddedImageHeight" ) ) != "")
    #{
    #    $xrowForumINI->setVariable( 'MaxImageSizes', 'EmbeddedImageHeight', $http->postVariable( "EmbeddedImageHeight" ) );
    #}
    #else
    #{
    #    array_push($error, "Please select a proper EmbeddedImageHeight");
    #}
    #if (is_numeric($http->postVariable( "EmbeddedImageWidth" )) AND trim( $http->postVariable( "EmbeddedImageWidth" ) ) != "")
    #{
    #    $xrowForumINI->setVariable( 'MaxImageSizes', 'EmbeddedImageWidth', $http->postVariable( "EmbeddedImageWidth" ) );
    #}
    #else
    #{
    #    array_push($error, "Please select a proper EmbeddedImageWidth");
    #}
    if (is_numeric($http->postVariable( "StatisticLimit" )) AND trim( $http->postVariable( "StatisticLimit" ) ) != "")
    {
       $xrowForumINI->setVariable( 'GeneralSettings', 'StatisticLimit', $http->postVariable( "StatisticLimit" ) );
    }
    else
    {
        array_push($error, "Please select a proper StatisticLimit");
    }
	if (is_numeric($http->postVariable( "KeepFlagDuration" )) AND trim( $http->postVariable( "KeepFlagDuration" ) ) != "")
    {
       $xrowForumINI->setVariable( 'GeneralSettings', 'KeepFlagDuration', $http->postVariable( "KeepFlagDuration" ) );
    }
    else
    {
        array_push($error, "Please select a proper KeepFlagDuration");
    }
	if($http->haspostVariable( "checkbox_mailnotification" ))
    {
        $xrowForumINI->setVariable( 'PrivateMessaging', 'AllowSendingNotificationMails', 'true' );
    }
    else
    {
        $xrowForumINI->setVariable( 'PrivateMessaging', 'AllowSendingNotificationMails', 'false' );
    }
	if (is_numeric($http->postVariable( "PmsPerPage" )) AND trim( $http->postVariable( "PmsPerPage" ) ) != "")
    {
        $xrowForumINI->setVariable( 'PrivateMessaging', 'PmsPerPage', $http->postVariable( "PmsPerPage" ) );
    }
    else
    {
        array_push($error, "Please select a proper PmsPerPage value");
    }
	if (empty ($error))
	{
	    $bbcodelist_fetch = $xrowForumINI->group( 'BB-Codes' );
	    $BBCodeListArray = array();
	    
	    foreach ($bbcodelist_fetch['BBCodeList'] as $BBCodeListKey => $BBCodeList)
	    {
	        $BBCodeListArray[$BBCodeListKey] = $http->postVariable( "radio_" . $BBCodeListKey );
	    }
	
	    $xrowForumINI->setVariables( array( 'BB-Codes' => array( 'BBCodeList' => $BBCodeListArray ) ) );
	}
	
    $xrowForumINI->save( false, false, 'append', false, true, true );
    eZCache::clearAll();
}

#read all existing variables from ini (for template output)
$forum_id = $xrowForumINI->variable( 'IDs', 'ForumIndexPageNodeID' );
$mod_id = $xrowForumINI->variable( 'IDs', 'ModeratorGroupObjectID' );
$user_class = $xrowForumINI->variable( 'ClassIDs', 'User' );
$topic_class = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
$reply_class = $xrowForumINI->variable( 'ClassIDs', 'ForumReply' );
$Rankings = $xrowForumINI->variable( 'GeneralSettings', 'Rankings' );
$Censoring = $xrowForumINI->variable( 'GeneralSettings', 'Censoring' );
$wordtoimage = $xrowForumINI->variable( 'GeneralSettings', 'WordToImage' );
$HotTopicNumber = $xrowForumINI->variable( 'GeneralSettings', 'HotTopicNumber' );
$PostsPerPage = $xrowForumINI->variable( 'GeneralSettings', 'PostsPerPage' );
$TopicsPerPage = $xrowForumINI->variable( 'GeneralSettings', 'TopicsPerPage' );
$PostHistoryLimit = $xrowForumINI->variable( 'GeneralSettings', 'PostHistoryLimit' );
#$SignatureLength = $xrowForumINI->variable( 'GeneralSettings', 'SignatureLength' );
$SignatureImage = $xrowForumINI->variable( 'GeneralSettings', 'SignatureImage' );
#$SignatureHeight = $xrowForumINI->variable( 'MaxImageSizes', 'SignatureHeight' );
#$SignatureWidth = $xrowForumINI->variable( 'MaxImageSizes', 'SignatureWidth' );
#$EmbeddedImageHeight = $xrowForumINI->variable( 'MaxImageSizes', 'EmbeddedImageHeight' );
#$EmbeddedImageWidth = $xrowForumINI->variable( 'MaxImageSizes', 'EmbeddedImageWidth' );
$StatisticLimit = $xrowForumINI->variable( 'GeneralSettings', 'StatisticLimit' );
$KeepFlagDuration = $xrowForumINI->variable( 'GeneralSettings', 'KeepFlagDuration' );
$AllowSendingNotificationMails = $xrowForumINI->variable( 'PrivateMessaging', 'AllowSendingNotificationMails' );
$PmsPerPage = $xrowForumINI->variable( 'PrivateMessaging', 'PmsPerPage' );
$bbcodelist = $xrowForumINI->group( 'BB-Codes' );

#deliver variables to template
if (!empty ($error))
{
	$tpl->setVariable( 'ErrorMSG', $error );
}
elseif ($http->hasPostVariable( "save_general_settings" )) 
{
	$success = true;
	$tpl->setVariable( 'success', $success );
}
$tpl->setVariable( 'forum_id', $forum_id );
$tpl->setVariable( 'mod_id', $mod_id );
$tpl->setVariable( 'topic_class', $topic_class );
$tpl->setVariable( 'reply_class', $reply_class );
$tpl->setVariable( 'user_class', $user_class );
$tpl->setVariable( 'ranking', $Rankings );
$tpl->setVariable( 'censoring', $Censoring );
$tpl->setVariable( 'wordtoimage', $wordtoimage );
$tpl->setVariable( 'HotTopicNumber', $HotTopicNumber );
$tpl->setVariable( 'PostsPerPage', $PostsPerPage );
$tpl->setVariable( 'TopicsPerPage', $TopicsPerPage );
$tpl->setVariable( 'PostHistoryLimit', $PostHistoryLimit );
#$tpl->setVariable( 'SignatureLength', $SignatureLength );
$tpl->setVariable( 'SignatureImage', $SignatureImage );
#$tpl->setVariable( 'SignatureHeight', $SignatureHeight );
#$tpl->setVariable( 'SignatureWidth', $SignatureWidth );
#$tpl->setVariable( 'EmbeddedImageHeight', $EmbeddedImageHeight );
#$tpl->setVariable( 'EmbeddedImageWidth', $EmbeddedImageWidth );
$tpl->setVariable( 'StatisticLimit', $StatisticLimit );
$tpl->setVariable( 'KeepFlagDuration', $KeepFlagDuration );
$tpl->setVariable( 'AllowSendingNotificationMails', $AllowSendingNotificationMails );
$tpl->setVariable( 'PmsPerPage', $PmsPerPage );
$tpl->setVariable( 'bbcodelist', $bbcodelist['BBCodeList'] );

#tells php file what the template to deliver is and how it looks like
$Result = array();
$Result['left_menu'] = "design:admin/menu.tpl";
$Result['content'] = $tpl->fetch( "design:admin/settings.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'extension/xrowforum', 'xrowForum settings' ) ) );

?>