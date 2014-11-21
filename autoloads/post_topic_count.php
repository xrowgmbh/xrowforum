<?php


class ObjectByIDOperator
{

    function ObjectByIDOperator()
    {
        $this->Operators = array( 'object_by_id' );
    }

    function operatorList()
    {
        return array( 'object_by_id' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'object_by_id' => array ( 'user_id'  => array( "type" => "string",
                                                                     "required" => true) ));
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'object_by_id':
                $user_id = $namedParameters['user_id'];
                $xrowForumINI = eZINI::instance( 'xrowforum.ini' );
				$topic_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
				$post_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumReply' );
                $db = eZDB::instance();
                $result = $db->arrayQuery("SELECT count(*) as counter FROM ezcontentobject WHERE ( contentclass_id=$topic_class_id OR contentclass_id=$post_class_id ) AND owner_id=$user_id and status=1;");
                $operatorValue = $result[0]["counter"];
            break;
        }
    }
    
}
?>