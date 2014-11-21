<?php

include_once ( 'kernel/common/template.php' );
include_once( 'lib/ezdb/classes/ezdb.php' );

$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();

$Result = array();
$Result['left_menu'] = "design:admin/menu.tpl";
$Result['content'] = $tpl->fetch( "design:admin/permissions.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr( 'extension/xrowforum', 'xrowForum permissions' ) ) );
    
?>