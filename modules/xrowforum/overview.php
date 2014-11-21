<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();
$db = eZDB::instance();
$Module = $Params['Module'];
$sys = eZSys::instance();
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
$ForumIndexPageNodeID = $xrowForumINI->variable( 'IDs', 'ForumIndexPageNodeID' );
$stats_limit = $xrowForumINI->variable( 'GeneralSettings', 'StatisticLimit' );
$topic_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
$post_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumReply' );
$user_class_id = $xrowForumINI->variable( 'ClassIDs', 'User' );
$max_user_on = $xrowForumINI->variable( 'MostUserOn', 'Amount' );
$max_user_on_date = $xrowForumINI->variable( 'MostUserOn', 'Date' );
$now_time = getdate();
$yesterday = $now_time[0]-86400;
$error = array();

if ($ForumIndexPageNodeID == '' or !is_numeric($ForumIndexPageNodeID))
{
	array_push($error, "Select a proper ForumIndexPageNodeID in the settings");
}

if ($stats_limit == '' or !is_numeric($stats_limit))
{
    array_push($error, "Select a proper StatisticLimit in the settings");
}

if ($topic_class_id == '' or !is_numeric($topic_class_id))
{
    array_push($error, "Select a proper ForumTopic ID in the settings");
}

if ($post_class_id == '' or !is_numeric($post_class_id))
{
    array_push($error, "Select a proper ForumReply ID in the settings");
}

if ($user_class_id == '' or !is_numeric($user_class_id))
{
    array_push($error, "Select a proper User ID in the settings");
}

if (count($error) ==0)
{
	#read database
	$forum_start = $db->arrayQuery("SELECT published FROM ezcontentobject, ezcontentobject_tree WHERE ezcontentobject_tree.contentobject_id = ezcontentobject.id AND ezcontentobject_tree.main_node_id = $ForumIndexPageNodeID;");
	$topiccount = $db->arrayQuery("SELECT COUNT(*)as Amount FROM ezcontentobject WHERE contentclass_id = $topic_class_id AND status='1';");
	$postcount = $db->arrayQuery("SELECT COUNT(*)as Amount FROM ezcontentobject WHERE contentclass_id = $post_class_id AND status='1';");
	$usercount = $db->arrayQuery("SELECT COUNT(*)as Amount FROM ezcontentobject WHERE contentclass_id = $user_class_id AND status='1';");
	$topiccount_y = $db->arrayQuery("SELECT COUNT(*)as Amount FROM ezcontentobject WHERE contentclass_id = $topic_class_id AND status='1' AND published > $yesterday;");
	$postcount_y = $db->arrayQuery("SELECT COUNT(*)as Amount FROM ezcontentobject WHERE contentclass_id = $post_class_id AND status='1' AND published > $yesterday;");
	$usercount_y = $db->arrayQuery("SELECT COUNT(*)as Amount FROM ezcontentobject WHERE contentclass_id = $user_class_id AND status='1' AND published > $yesterday;");
	$latest_posts = $db->arrayQuery("SELECT * FROM ezcontentobject WHERE contentclass_id = $post_class_id ORDER BY published DESC LIMIT $stats_limit;");
	$latest_topics = $db->arrayQuery("SELECT * FROM ezcontentobject WHERE contentclass_id = $topic_class_id ORDER BY published DESC LIMIT $stats_limit;");
	$hot_topic_by_view = $db->arrayQuery("SELECT contentobject_id, viewcount FROM xrowforum_viewcount ORDER BY viewcount DESC LIMIT $stats_limit;");
	$hot_topic_by_replies = $db->arrayQuery("SELECT ezcontentobject_tree.parent_node_id, count(ezcontentobject_tree.parent_node_id) as 'kids' FROM ezcontentobject_tree JOIN ezcontentobject ON ezcontentobject_tree.contentobject_id = ezcontentobject.id where ezcontentobject.contentclass_id = $post_class_id  GROUP BY ezcontentobject_tree.parent_node_id ORDER BY kids DESC LIMIT $stats_limit;");
	$latest_user = $db->arrayQuery("SELECT * FROM ezcontentobject WHERE contentclass_id = $user_class_id ORDER BY published DESC LIMIT $stats_limit;");
	$top_user_posts = $db->arrayQuery("SELECT COUNT(*) counter, owner_id FROM ezcontentobject WHERE status ='1' AND contentclass_id = $post_class_id OR contentclass_id = $topic_class_id GROUP BY owner_id ORDER BY counter desc limit $stats_limit;");
	$top_user_login = $db->arrayQuery("SELECT login_count, user_id FROM ezuservisit ORDER BY login_count DESC LIMIT $stats_limit ;");
	$online_past_24 = $db->arrayQuery("SELECT user_id FROM ezuservisit where last_visit_timestamp > $yesterday;");

	#format result
	$result_topiccount = $topiccount[0]['Amount'];
	$result_postcount = $postcount[0]['Amount'];
	$result_usercount = $usercount[0]['Amount'];
	$result_topiccount_y = $topiccount_y[0]['Amount'];
	$result_postcount_y = $postcount_y[0]['Amount'];
	$result_usercount_y = $usercount_y[0]['Amount'];
	$board_start = $forum_start[0]['published'];
	#-1 for 'anonymous user'
	$result_usercount = $result_usercount - 1;

	#deliver to template
	$tpl->setVariable( 'board_start', $board_start );
	$tpl->setVariable( 'topic_count', $result_topiccount );
	$tpl->setVariable( 'post_count', $result_postcount );
	$tpl->setVariable( 'user_count', $result_usercount );
	$tpl->setVariable( 'latest_posts', $latest_posts );
	$tpl->setVariable( 'latest_topics', $latest_topics );
	$tpl->setVariable( 'hot_topic_by_view', $hot_topic_by_view );
	$tpl->setVariable( 'hot_topic_by_replies', $hot_topic_by_replies );
	$tpl->setVariable( 'latest_user', $latest_user );
	$tpl->setVariable( 'top_user_posts', $top_user_posts );
	$tpl->setVariable( 'top_user_login', $top_user_login );
	$tpl->setVariable( 'stats_limit', $stats_limit );
	$tpl->setVariable( 'online_past_24', $online_past_24 );
	$tpl->setVariable( 'topiccount_y', $result_topiccount_y );
	$tpl->setVariable( 'postcount_y', $result_postcount_y );
	$tpl->setVariable( 'usercount_y', $result_usercount_y );
	$tpl->setVariable( 'max_user_on', $max_user_on );
	$tpl->setVariable( 'max_user_on_date', $max_user_on_date );
}
else
{
    $tpl->setVariable( 'errormsg', $error );
}

$Result = array();
$Result['left_menu'] = "design:admin/menu.tpl";
$Result['content'] = $tpl->fetch( "design:admin/overview.tpl" );
$Result['path'] = array( array( 'url' => "/",
                                'text' => ezpI18n::tr( 'extension/xrowpm', 'Home' ) ),
						array( 'url' => false,
                                'text' => ezpI18n::tr( 'extension/xrowforum', 'Statistics' ) ) );
    
?>