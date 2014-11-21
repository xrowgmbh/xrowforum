<?php

class setSessionBeforePublishType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'setsessionbeforepublish';
    
    function setSessionBeforePublishType()
    {
        $this->eZWorkflowEventType( setSessionBeforePublishType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/xrowforum', 'set Session' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array ( 'before' ) ) ) );
    }
    
    function execute( $process, $event )
    {
    	$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
        $topic_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
       	$parameters = $process->attribute( 'parameter_list' );
        $objectID = $parameters['object_id'];
        $obj = eZContentObject::fetch($objectID);
        $obj_t = eZContentObjectTreeNode::fetchByContentObjectID( $objectID );
        if ($obj->ClassID == $topic_class_id)
        {
        	$_SESSION['old_modifiedsubnode_ts'] = $obj_t[0]->ModifiedSubNode;
        }
        else
        {
        	$parentobj = $obj_t[0]->fetchParent();
        	$_SESSION['old_modifiedsubnode_ts'] = $parentobj->ModifiedSubNode;
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( setSessionBeforePublishType::WORKFLOW_TYPE_STRING, 'setSessionBeforePublishType' );

?>