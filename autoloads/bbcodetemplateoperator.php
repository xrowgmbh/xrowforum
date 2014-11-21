<?php
//
// BBCode Template Operator
// Template operators with BBCode functionality 
//
// The template operators are using:
// - HTML_BBCodeParser ( http://pear.php.net/package/HTML_BBCodeParser )
//
// Copyright (C) 2004 Vathanan Kumarathurai <vathanan@vathanan.com>
//

#include_once( 'kernel/classes/ezurlalias.php' );
include_once( 'kernel/classes/ezcontentobjectattribute.php' );
include_once( "lib/ezutils/classes/ezini.php" );

class BBCodeTemplateOperator
{
	/*!
	*/
	function BBCodeTemplateOperator()
	{
		$this->Operators = array( 'bbcode2xhtml', 'xhtml2bbcode' );
	} // function BBCodeTemplateOperator()

	/*!
		\return an array with the template operator name.
	*/
	function operatorList()
	{
		return $this->Operators;
	} //  function operatorList()

	/*!
		\return true to tell the template engine that the parameter list exists per operator type,
		this is needed for operator classes that have multiple operators.
	*/
	function namedParameterPerOperator()
	{
		return true;
	} // function namedParameterPerOperator()

	/*!
		Executes the PHP function for the operator cleanup and modifies \a $operatorValue.
	*/
	function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
	{
		switch ( $operatorName )
		{
			case 'bbcode2xhtml':
			{
				$operatorValue = $this->bbcode2xhtml( $operatorValue );                
			}
			break;
			case 'xhtml2bbcode':
			{
				$operatorValue = $this->xhtml2bbcode( $operatorValue );                
			}
			break;
		}
	 } // function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )

	/*!
	
	*/
	function bbcode2xhtml( $text )
	{
		include_once( "extension/xrowforum/classes/BBCodeParser.php" );
		return HTML_BBCodeParser::staticQparse( $text );
	} // function bbcode2xhtml( $text )
	
	/*!
	
	*/
	function xhtml2bbcode( $text )
	{
		return "";
	} // function xhtml2bbcode( $text )
	
	
	var $Operators;

} // class BBCodeTemplateOperator

?>