<?php


class requestUserRank
{

    function requestUserRank()
    {
        $this->Operators = array( 'request_rank' );
    }

    function operatorList()
    {
        return array( 'request_rank' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'request_rank' => array ( 'user_id'  => array( "type" => "string",
                                                                     "required" => true) ));
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'request_rank':
                $user_id = $namedParameters['user_id'];
                // connects to database                 
                $db = eZDB::instance();
                $checkvalue = $db->arrayQuery("SELECT rank_name FROM xrowforum_rank WHERE rank_category = 1 AND rank_condition = $user_id AND active = 1 limit 1;");
                if(count($checkvalue) != 0)
                {
                    $checkvalue = $checkvalue[0]['rank_name'];
                }
                if(count($checkvalue) == 0)
                {
                	$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
					$topic_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumTopic' );
					$post_class_id = $xrowForumINI->variable( 'ClassIDs', 'ForumReply' );
                	$counter = $db->arrayQuery("SELECT count(*) as counter FROM ezcontentobject WHERE owner_id = $user_id AND (contentclass_id = $topic_class_id OR contentclass_id = $post_class_id);");
                	$counter = $counter[0]['counter'];
                	$checkvalue = $db->arrayQuery("SELECT rank_name FROM xrowforum_rank WHERE active = 1 AND rank_category = 0 AND $counter >= rank_condition ORDER BY rank_condition DESC limit 1;");
                    if(count($checkvalue) == 0)
                    {
                    	$checkvalue = 'no match';
                    }
                    else
                    {
                    	$checkvalue = $checkvalue[0]['rank_name'];
                    }
                }
                $operatorValue = $checkvalue;
            break;
        }
    }
    
}
?>