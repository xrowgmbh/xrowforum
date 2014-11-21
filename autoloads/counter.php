<?php


class topicViewCounter
{

    function topicViewCounter()
    {
        $this->Operators = array( 'read_count', 'write_count'  );
    }

    function operatorList()
    {
        return array( 'read_count', 'write_count' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'read_count' => array ( 'id'  => array( "type" => "string",
                                                                        "required" => true )  ),
                      'write_count' => array ( 'id'  => array( "type" => "string",
                                                                        "required" => true ) )
        
        );
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'read_count':
                $contentobject_id = $namedParameters['id'];
                // connects to database                 
                $db = eZDB::instance();
                $checkvalue = $db->arrayQuery("SELECT viewcount FROM xrowforum_viewcount WHERE contentobject_id = $contentobject_id"); 
                $operatorValue = $checkvalue;
            break;
            case 'write_count':
                $contentobject_id = $namedParameters['id'];
                // connects to database                 
                $db = eZDB::instance();
                $resultArray = array();
                $checkvalue = $db->arrayQuery("SELECT viewcount FROM xrowforum_viewcount WHERE contentobject_id = $contentobject_id"); 
                $checkvalue = $checkvalue[0]['viewcount'];
                if ($checkvalue == 0)
                {
                	$db->begin();        
		            $resultArray = $db->query("INSERT INTO xrowforum_viewcount ( contentobject_id, viewcount ) VALUES ( $contentobject_id, 1 );");
		            $db->commit();
                }
                elseif ($checkvalue >= 1)
                {
                	$checkvalue = $checkvalue +1;
                    $db->begin();
			        $db->query("UPDATE xrowforum_viewcount SET viewcount = $checkvalue WHERE contentobject_id = $contentobject_id;");
			        $db->commit();
                }
                $operatorValue = $resultArray;
            break;
        }
    }
    
}
?>