<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();
$db = eZDB::instance();
$Module = $Params['Module'];
$error = array();
$success = false;

if( $http->hasPostVariable( "rank_switch" ))
{
	$url_number = $http->PostVariable( "rank_switch");
    $old_content = $db->arrayQuery("SELECT active FROM xrowforum_rank WHERE rank_index = '$url_number';");
    $old_value = $old_content[0]['active'];
    if ($old_value == 1)
    {
        $db->begin();
        $db->arrayQuery("UPDATE xrowforum_rank SET active = '0' WHERE rank_index = '$url_number';");
        $db->commit();
    }
    else
    {
    	$db->begin();
        $db->arrayQuery("UPDATE xrowforum_rank SET active = '1' WHERE rank_index = '$url_number';");
        $db->commit();
    }
}

if( $http->hasPostVariable( "delete_rank" ))
{
	$del_object = $http->postVariable( "delete_rank" );
    $db->begin();        
    $db->arrayQuery("DELETE FROM xrowforum_rank WHERE rank_index='$del_object';");
    $db->commit();
}

if( $http->hasPostVariable( "CreateNewRank" ))
{
        if ($http->hasPostVariable( "rank_text" ) AND trim( $http->postVariable( "rank_text" ) ) != "")
        {
            $rank_text = $http->PostVariable( "rank_text" );
            $tpl->setVariable( 'rank_text', $rank_text );
        }
        else
        {
            array_push($error, "Please select a Rank Text");
        }
        
        $rank_category = $http->PostVariable( "rank_category" );
        $tpl->setVariable( 'rank_category', $rank_category );
        
        if ($http->hasPostVariable( "rank_condition" ) AND is_numeric($http->postVariable( "rank_condition" )))
        {
            $rank_condition = $http->PostVariable( "rank_condition" );
            $tpl->setVariable( 'rank_condition', $rank_condition );
        }
        else
        {
        	array_push($error, "The Rank Condition is not a number");
        }
        
        if ($http->hasPostVariable( "active_create" ))
        {
            $active_create = '1';
            $tpl->setVariable( 'active_create', $active_create );
        }
        else
        {
            $active_create = '0';
            $tpl->setVariable( 'active_create', $active_create );
        }
        
        if ( count($error) == 0 AND $http->hasPostVariable( "CreateNewRank" ))
        {
            $db->begin();        
            $db->arrayQuery("INSERT INTO xrowforum_rank ( rank_name, rank_category, rank_condition, active ) VALUES ( '$rank_text' , '$rank_category', '$rank_condition', '$active_create' );");
            $db->commit();
            $success = true;
            $tpl->setVariable( 'success', $success );
        }
        else 
        {
             $tpl->setVariable( 'ErrorMSG', $error );
        }
}

$ranks = $db->arrayQuery("SELECT * FROM xrowforum_rank;");
$tpl->setVariable( 'ranks', $ranks );

$Result = array();
$Result['left_menu'] = "design:admin/menu.tpl";
$Result['content'] = $tpl->fetch( "design:admin/ranks.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'extension/xrowforum', 'xrowForum ranks' ) ) );
    
?>