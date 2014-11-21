<?php

$Module = array( 'name' => 'xrowforum' );

$ViewList = array();

$ViewList['overview'] = array( 'functions' => array( 'overview' ),
                           'script' => 'overview.php',
                           'default_navigation_part' => 'ezxrownav',
                           'params' => array( 'NodeID' ));

$ViewList['settings'] = array( 'functions' => array( 'settings' ),
                           'script' => 'settings.php',
                           'default_navigation_part' => 'ezxrownav',
                           'params' => array( 'NodeID' ));

$ViewList['ranks'] = array( 'functions' => array( 'ranks' ),
                           'script' => 'ranks.php',
                           'default_navigation_part' => 'ezxrownav',
                           'params' => array( 'NodeID' ));

$ViewList['permissions'] = array( 'functions' => array( 'permissions' ),
                           'script' => 'permissions.php',
                           'default_navigation_part' => 'ezxrownav',
                           'params' => array( 'NodeID' ));

$ViewList['removeflags'] = array( 'functions' => array( 'removeflags' ),
                           'script' => 'removeflags.php',
                           'params' => array( 'NodeID' ));

$ViewList['create'] = array( 'functions' => array( 'create' ),
                        'script' => 'create.php',
                        'params' => array( 'NodeID', 'LanguageCode' ) );

$ViewList['close'] = array( 'functions' => array( 'close' ),
                        'script' => 'close.php',
                        'params' => array( 'NodeID' ) );
						
$ViewList['report'] = array( 'functions' => array( 'report' ),
                        'script' => 'report.php',
                        'params' => array( 'NodeID' ) );

$FunctionList = array();
$FunctionList['settings'] = array( );
$FunctionList['overview'] = array( );
$FunctionList['removeflags'] = array( );
$FunctionList['permissions'] = array( );
$FunctionList['ranks'] = array( );
$FunctionList['create'] = array( );
$FunctionList['close'] = array( );
$FunctionList['report'] = array( );

?>
