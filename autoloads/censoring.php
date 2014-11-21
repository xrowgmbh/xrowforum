<?php


class Censoring
{

    function Censoring()
    {
        $this->Operators = array( 'censoring' );
    }

    function operatorList()
    {
        return array( 'censoring' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'censoring' => array ());
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
		$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
		$censoringlist = $xrowForumINI->variable( 'Censoring', 'CensoringList' );
		$regexPattern = array();
		foreach( $censoringlist as $badWord ) 
		{
		  $chars = str_split( $badWord );
		  $regexPattern[] = '/\b(' . $badWord . ')\b/i';
		  $regexReplace[] = $chars[0] . "**" . $chars[ count($chars) -1 ];
		}
		$operatorValue = preg_replace( $regexPattern, $regexReplace, $operatorValue );		
    }
}

?>