<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

$Module = & $Params['Module'];
$http = eZHTTPTool::instance();
$namedParameters = $Module->NamedParameters;
$view_parameters = $Params['UserParameters'];
$tpl = eZTemplate::factory();

if($Params['UserParameters']['offset'])
{
	$offset = $Params['UserParameters']['offset'];
}
else
{
	$offset = 0;
}
if($namedParameters['sorting'])
{
	$sorting = $namedParameters['sorting'];
}
else
{
	$sorting = "send_date";
}
if($namedParameters['direction'])
{
	$direction = $namedParameters['direction'];
	$tpl->setVariable( 'direction', $direction );
}
else
{
	$direction = "desc";
}

$db = eZDB::instance();
$current_user = eZUser::currentUser();
$current_user_id = $current_user->ContentObjectID;
$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
$page_limit = $xrowForumINI->variable( 'PrivateMessaging', 'PmsPerPage' );

if ($http->hasPostVariable( 'pm_search' ))
{
	$searchtext = $http->PostVariable( 'pm_search' );
	$searchtext = $db->escapeString($searchtext);
	$total_msg = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_messages where (pm_subject LIKE '%$searchtext%' or pm_content LIKE '%$searchtext%') AND sender = $current_user_id AND owner_id = $current_user_id;");
	}
else
{
	$total_msg = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_messages where owner_id = $current_user_id AND sender = $current_user_id");
}

if($total_msg[0]['count'] >= 1)
{
	$tpl->setVariable( 'total_msg', $total_msg[0]["count"] );
	if ($http->hasPostVariable( 'pm_search' ))
	{
		$searchtext = $http->PostVariable( 'pm_search' );
		$searchtext = $db->escapeString($searchtext);
		$sent_mails = $db->arrayQuery("SELECT * FROM xrowforum_pm_messages where (pm_subject LIKE '%$searchtext%' or pm_content LIKE '%$searchtext%') AND sender = $current_user_id AND owner_id = $current_user_id order by $sorting $direction LIMIT $page_limit OFFSET $offset;;");
		$tpl->setVariable( 'search_on', 'true' );
	}
	else
	{
		$sent_mails = $db->arrayQuery("SELECT * FROM xrowforum_pm_messages where sender = $current_user_id and owner_id = $current_user_id order by $sorting $direction LIMIT $page_limit OFFSET $offset;");
	}
	$tpl->setVariable( 'sent_mails', $sent_mails );
}
else
{
	$tpl->setVariable( 'total_msg', 0 );
}

$tpl->setVariable( 'sorting', $sorting );
$tpl->setVariable( 'offset', $offset );
$tpl->setVariable( 'page_limit', $page_limit );
$tpl->setVariable( 'view_parameters', $view_parameters );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:pm/outbox.tpl' );
$Result['path'] = array( array( 'url' => "/",
                                'text' => ezpI18n::tr( 'extension/xrowpm', 'Home' ) ),
						 array( 'url' => false,
                                'text' => ezpI18n::tr( 'extension/xrowpm', 'Outbox' ) ) );
    
?>