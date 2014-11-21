<?php


class OperatorMerge
{

    function OperatorMerge()
    {
        $this->Operators = array( 'operator_merge' );
    }

    function operatorList()
    {
        return array( 'operator_merge' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'operator_merge' => array ( 'type'  => array( "type" => "string",
                                                                     "required" => true),
                                                  'content'  => array( "type" => "string",
                                                                     "required" => true)));
    }

    function modify( &$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters )
    {
        switch ( $operatorName )
        {
            case 'operator_merge':
            	$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
                $BBCodeList = $xrowForumINI->variable( 'BB-Codes', 'BBCodeList' );
                $WordToImage = $xrowForumINI->variable( 'GeneralSettings', 'WordToImage' );
				$censoring = $xrowForumINI->variable( 'GeneralSettings', 'Censoring' );
                $op_array = array();
            	$type = $namedParameters['type'];
            	if( $type == "content" )
            	{
                    foreach ($BBCodeList as $BBitem)
                    {
                        if ($BBitem == 'enabled')
                        {
                            array_push($op_array, "bbcode2xhtml");
                            break 1;
                        }
                    }
            	    if($WordToImage == 'enabled')
                    {
                        array_push($op_array, "wordtoimage");
                    }
					if($censoring == 'enabled')
                    {
                        array_push($op_array, "censoring");
                    }
					array_push($op_array, "nl2br");
            	}
				if( $type == "preview" )
            	{
                    foreach ($BBCodeList as $BBitem)
                    {
                        if ($BBitem == 'enabled')
                        {
                            array_push($op_array, "bbcode2xhtml");
                            break 1;
                        }
                    }
            	    if($WordToImage == 'enabled')
                    {
                        array_push($op_array, "wordtoimage");
                    }
					if($censoring == 'enabled')
                    {
                        array_push($op_array, "censoring");
                    }
            	}
                elseif( $type == "signature" )
                {
                    $SignatureImage = $xrowForumINI->variable( 'GeneralSettings', 'SignatureImage' );
                    if($SignatureImage == 'enabled' AND $BBCodeList['img'] == 'enabled')
                    {
                        array_push($op_array, "bbcode2xhtml");
                    }
                    if($WordToImage == 'enabled')
                    {
                        array_push($op_array, "wordtoimage");
                    }
					if($censoring == 'enabled')
                    {
                        array_push($op_array, "censoring");
                    }
					array_push($op_array, "nl2br");
                }
                $tpl->setVariable( 'types', $op_array );
                $content = $namedParameters['content'];
            	$tpl->setVariable( 'content', $content );
            	$operatorValue = $tpl->fetch( 'design:mergeme/merge.tpl' );
            break;
        }
    }
}
?>