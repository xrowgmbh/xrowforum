<?php


class deflagSubtree
{

    function deflagSubtree()
    {
        $this->Operators = array( 'deflag_subtree'  );
    }

    function operatorList()
    {
        return array( 'deflag_subtree' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'deflag_subtree' => array ( 'path_string'  => array( "type" => "string",
                                                                           "required" => true,
                                                                           "default" => 0 ),
                                                  'limit'  => array( "type" => "integer",
                                                                     "required" => false,
                                                                     "default" => 100  ) ));
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'deflag_subtree':
                $current_userID = eZUser::currentUserID();
                $path_string = $namedParameters['path_string'];
                $limit = $namedParameters['limit'];
                // connects to database                 
                $db = eZDB::instance();
                $resultArray = array();
                $resultArray = $db->arrayQuery("DELETE FROM xrowforum_notification WHERE user_id = $current_userID AND path_string like '$path_string%' limit $limit;"); 
                $operatorValue = $resultArray;
            break;
        }
    }
    
}
?>