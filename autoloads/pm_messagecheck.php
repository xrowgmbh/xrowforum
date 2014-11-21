<?php


class PmMessageCheck
{

    function PmMessageCheck()
    {
        $this->Operators = array( 'pm_message_check' );
    }

    function operatorList()
    {
        return array( 'pm_message_check' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'pm_message_check' => array ( 'box_type'  => array( "type" => "string",
                                                                          "required" => true),
													'state'  => array( "type" => "string",
                                                                       "required" => true,
																	   "default" => "" ) ));
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
		$db = eZDB::instance();
		$current_user = eZUser::currentUser();
		$current_user_id = $current_user->ContentObjectID;
		$box_type = $namedParameters['box_type'];
		$state = $namedParameters['state'];
		if($box_type == "inbox")
		{
		
			if( $state == "" )
			{
					$checkvalue = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_messages where owner_id = $current_user_id and recipient = $current_user_id;");
			}
			else
			{
				$checkvalue = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_messages where owner_id = $current_user_id and recipient = $current_user_id AND read_state = $state;");
			}
		}
		else if($box_type == "outbox")
		{
			if( $state == "" )
			{
					$checkvalue = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_messages where owner_id = $current_user_id and sender = $current_user_id;");
			}
			else
			{
				$checkvalue = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_messages where owner_id = $current_user_id and sender = $current_user_id AND read_state = $state;");
			}
		}
		$operatorValue = $checkvalue[0]["count"];
    }
    
}
?>