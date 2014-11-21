<?php

class deflagAfterReadType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'deflagafterread';
	
    function deflagAfterReadType()
	{
		$this->eZWorkflowEventType( deflagAfterReadType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/xrowforum', 'Deflag' ) );
        $this->setTriggerTypes( array( 'content' => array( 'read' => array ( 'before' ) ) ) );
	}
	
	function execute( $process, $event )
	{
		$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
        $topic_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
        $post_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumReply' );
	    $parameters = $process->attribute( 'parameter_list' );
        $node_id = $parameters['node_id'];
        $current_userID = eZUser::currentUserID();
        $object = eZContentObject::fetchByNodeID($node_id);
        $object_contentclass_id = $object->ClassID;
        if ($object_contentclass_id == $post_class_id OR $object_contentclass_id == $topic_class_id AND $current_userID != 10)
        {
        	$db = eZDB::instance();
        	$objectID = $object->ID;
        	$obj = eZContentObjectTreeNode::fetchByContentObjectID( $objectID );
        	$path_string = $obj[0]->attribute('path_string');
        	#workaround because we dont have a content read after workflow
        	$save_path = $db->arrayQuery("SELECT path_string FROM xrowforum_notification WHERE user_id = $current_userID AND path_string like '$path_string%'");
        	if (count($save_path) >= 0)
        	{
                $save_paths = array();
                foreach ( $save_path as $save_path_items )
                {
                	$save_paths[] = $save_path_items['path_string'];
                }
	            $_SESSION['paths'] = $save_paths;
	            #fetch all users, except for the current user, with a limit and from ezuservisit to only fetch active users
	            $db->begin();
	            $db->query("DELETE FROM xrowforum_notification WHERE user_id = $current_userID AND path_string like '$path_string%'");
	            $db->commit();        		
        	}
        }
	   return eZWorkflowType::STATUS_ACCEPTED;
	}
}

eZWorkflowEventType::registerEventType( deflagAfterReadType::WORKFLOW_TYPE_STRING, 'deflagAfterReadType' );

?>