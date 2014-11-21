<?php


class PmMarkAsRead
{

    function PmMarkAsRead()
    {
        $this->Operators = array( 'pm_mark_as_read' );
    }

    function operatorList()
    {
        return array( 'pm_mark_as_read' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'pm_mark_as_read' => array ( 'msg_id'  => array( "type" => "integer",
                                                                       "required" => true),
                                                   'sender'  => array( "type" => "integer",
                                                                       "required" => true),
        										   'recipient'  => array( "type" => "integer",
                                                                       	  "required" => true),
        										   'send_date'  => array( "type" => "integer",
                                                                       	  "required" => true)
        									 ));
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'pm_mark_as_read':
                $msg_id = $namedParameters['msg_id'];
                $sender = $namedParameters['sender'];
                $recipient = $namedParameters['recipient'];
                $send_date = $namedParameters['send_date'];
                $db = eZDB::instance();
                $db->begin();
				$db->arrayQuery("UPDATE xrowforum_pm_messages SET read_state = '1' WHERE sender = $sender AND recipient = $recipient AND send_date = $send_date");
				$db->commit();
            break;
        }
    }
    
}
?>