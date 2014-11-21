<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

$Module = & $Params['Module'];
$http = eZHTTPTool::instance();
$namedParameters = $Module->NamedParameters;
$node_id = $namedParameters['NodeID'];
$obj = eZContentObject::fetchByNodeID( $node_id );
$obj_with_dm = $obj->dataMap();
$close_status = $obj_with_dm["closed"]->content();

if ($close_status == 0)
{
	$obj_with_dm["closed"]->setAttribute("data_int", 1);
    $obj_with_dm["closed"]->setAttribute("sort_key_int", 1);
    $obj_with_dm["closed"]->store();	
}
elseif ($close_status == 1)
{
    $obj_with_dm["closed"]->setAttribute("data_int", 0);
    $obj_with_dm["closed"]->setAttribute("sort_key_int", 0);
    $obj_with_dm["closed"]->store();    
}

return $Module->redirectTo( '/content/view/full/' . $node_id ); 
    
?>