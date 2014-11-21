<?php

class flagAfterPublishType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'flagafterpublish';
    
    function flagAfterPublishType()
    {
        $this->eZWorkflowEventType( flagAfterPublishType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/xrowforum', 'Flag' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array ( 'after' ) ) ) );
    }
    
    function execute( $process, $event )
    {
    	$db = eZDB::instance();
    	$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
        $topic_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
		$user_active = $xrowForumINI->variable( 'GeneralSettings', 'KeepFlagDuration' );
    	#60 seconds * 60 minutes * 24 hours = 86400
		$user_active = $user_active * 86400;
		$now_time = time();
		#limit set to fetch "active user" only
        $limit = $now_time - $user_active;
        $current_userID = eZUser::currentUserID();
    	$parameters = $process->attribute( 'parameter_list' );
    	$objectID = $parameters['object_id'];
    	$obj = eZContentObjectTreeNode::fetchByContentObjectID( $objectID );
    	$parentobj = $obj[0]->fetchParent();
    	$path_string = $obj[0]->attribute('path_string');
    	$parent_contentobject = eZContentObject::fetch( $parentobj->attribute("contentobject_id") );
        $parent_dm = $parent_contentobject->dataMap();
        #fetch all users, except for the current user, with a limit and from ezuservisit to only fetch active users
        $users = $db->arrayQuery("SELECT user_id, last_visit_timestamp FROM ezuservisit WHERE user_id != $current_userID AND last_visit_timestamp > $limit");
        foreach ($users as $user_item)
        {
            $id  = $user_item['user_id'];
     		$db->begin();
		    $db->arrayQuery("INSERT INTO xrowforum_notification ( user_id, path_string, timestamp ) VALUES ( $id, '$path_string', $now_time );");
		    $db->commit();
        }
        if ( $parent_contentobject->ClassID == $topic_class_id )
        {
        	if ($parent_dm["closed"]->content() == 1)
        	{
             	$del_obj = eZContentObject::fetch($objectID);
				$del_obj->remove();
                $http = eZHTTPTool::instance();
                $http->setPostVariable( 'RedirectURIERROR', true );
        	}
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( flagAfterPublishType::WORKFLOW_TYPE_STRING, 'flagAfterPublishType' );

?>