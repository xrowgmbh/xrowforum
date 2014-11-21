<?php


class PmRelationCheck
{

    function PmRelationCheck()
    {
        $this->Operators = array( 'pm_relation_fetch', 'pm_is_inRelation' );
    }

    function operatorList()
    {
        return array( 'pm_relation_fetch', 'pm_is_inRelation' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'pm_relation_fetch' => array ( 'user_id'  => array( "type" => "integer",
                                                                     	  "required" => true),
													 'state'  => array( "type" => "string",
                                                                     	  "required" => true),
												     'logged_condition' => array( "type" => "integer",
																				  "required" => true,
																				  "default" => "all")
														  ),
					  'pm_is_inRelation' => array ( 'user_id'  => array( "type" => "integer",
                                                                     	 "required" => true),
													'user2_id'  => array( "type" => "integer",
                                                                     	 "required" => true),
													'state'  => array( "type" => "string",
                                                                       "required" => false),
														  )
														  
														  
					);
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
		$db = eZDB::instance();
		$user_id = $namedParameters['user_id'];
		if( trim($user_id) == "" OR is_null($user_id))
		{
			$user = eZUser::currentUser();
			$user_id = $user->ContentObjectID;
		}
        switch ( $operatorName )
        {
			#returns a list of user for the different relations and online conditions
			case 'pm_relation_fetch':
				$state = $namedParameters['state'];
				$logged_condition = $namedParameters['logged_condition'];
				$value = array();
				if($state == 0)
				{
					$value = $db->arrayQuery("SELECT * FROM xrowforum_pm_network where user2 = $user_id and state = $state;");
				}
				else
				{
					if ($logged_condition == "all")
					{
						$value = $db->arrayQuery("SELECT * FROM xrowforum_pm_network where (user1 = $user_id or user2 = $user_id) and state = $state;");
					}
					else if ($logged_condition == "online")
					{
						$friendList = $db->arrayQuery("SELECT * FROM xrowforum_pm_network where (user1 = $user_id or user2 = $user_id) and state = $state;");
						if ( count($friendList) >= 1 )
						{
							foreach ( $friendList as $item ) 
							{
								if ($item['user1'] == $user_id )
								{
									$friend_id = $item['user2'];
								}
								else
								{
									$friend_id = $item['user1'];
								}
								if( eZUser::isUserLoggedIn( $friend_id ) )
								{
									array_push($value, $friend_id);
								}
							}
						}
					}
					else if ($logged_condition == "offline")
					{
						$friendList = $db->arrayQuery("SELECT * FROM xrowforum_pm_network where (user1 = $user_id or user2 = $user_id) and state = $state;");
						if ( count($friendList) >= 1 )
						{
							foreach ( $friendList as $item ) 
							{
								if ($item['user1'] == $user_id )
								{
									$friend_id = $item['user2'];
								}
								else
								{
									$friend_id = $item['user1'];
								}
								if( !eZUser::isUserLoggedIn( $friend_id ) )
								{
									array_push($value, $friend_id);
								}
							}
						}
					}
				}
				$operatorValue = $value;
            break;
			#checks if user1 or user2 are in a certain relation
			case 'pm_is_inRelation':
				$user2_id = $namedParameters['user2_id'];
				$state = $namedParameters['state'];
				$check = $db->arrayQuery("SELECT count(*) as 'count' FROM xrowforum_pm_network where (user1 = $user_id AND user2 = $user2_id OR user1 = $user2_id AND user2 = $user_id ) AND state = $state ;");
				if ($check[0]["count"] >= 1)
				{
					$operatorValue = true;
				}
				else
				{
					$operatorValue = false;
				}
			break;	
        }
    }
    
}
?>