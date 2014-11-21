<?php

class updateAfterPublishType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'updateafterpublish';
    
    function updateAfterPublishType()
    {
        $this->eZWorkflowEventType( updateAfterPublishType::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/xrowforum', 'Update modified TS' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array ( 'after' ) ) ) );
    }
    
    function execute( $process, $event )
    {
    	$http = eZHTTPTool::instance();
    	$old_ts = $http->sessionVariable( 'old_modifiedsubnode_ts' );
    	$http->removeSessionVariable( 'old_modifiedsubnode_ts' );
    	$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
        $topic_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = $parameters['object_id'];
        $obj = eZContentObject::fetch($objectID);
        $obj_t = eZContentObjectTreeNode::fetchByContentObjectID( $objectID );
        if ($obj->ClassID == $topic_class_id)
        {
        	$obj_t[0]->setAttribute( "modified_subnode", $old_ts );
            eZContentObjectTreeNode::storeObject( $obj_t[0] );
                 
        }
        else
        {
        	$parentobj = $obj_t[0]->fetchParent();
        	$obj_t1 = eZContentObjectTreeNode::fetchByContentObjectID( $parentobj->ContentObjectID );
        	$obj_t1[0]->setAttribute( "modified_subnode", $old_ts );
        	eZContentObjectTreeNode::storeObject( $obj_t1[0] );
        }

        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( updateAfterPublishType::WORKFLOW_TYPE_STRING, 'updateAfterPublishType' );

?>