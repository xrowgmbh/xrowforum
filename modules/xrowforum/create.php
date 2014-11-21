<?php

include_once ( 'kernel/common/template.php' );

$Module = & $Params['Module'];
$http = eZHTTPTool::instance();
$user = eZUser::currentUser();
$user_id = $user->attribute("contentobject_id");
$node_id = 2;
$namedParameters = $Module->NamedParameters;
$tpl = eZTemplate::factory();
$error = array();
$errorstatus = false;
if( $namedParameters["LanguageCode"] )
{
	$languageCode = $namedParameters["LanguageCode"];
}
$languages = array( $languageCode, "eng-GB" );
$lang = new eZContentLanguage();
$l_prio = $lang->prioritizedLanguageCodes();
$lang->setPrioritizedLanguages( $languages );

if( is_numeric( $namedParameters["NodeID"] ) AND $namedParameters["NodeID"] > 2 )
{
	$node_id = $namedParameters["NodeID"];
    $node = eZContentObjectTreeNode::fetch( $node_id );
	
	#+1 for the topic
	$children_count = $node->childrenCount() + 1;
	$xrowForumINI = eZINI::instance( 'xrowforum.ini' );
	$PostsPerPage = $xrowForumINI->variable( 'GeneralSettings', 'PostsPerPage' );
	if($children_count > $PostsPerPage)
	{
		$offset = $PostsPerPage * ( floor($children_count / $PostsPerPage));
		$url = $node->urlAlias() . "/(offset)/" . $offset . "#last_from_page";
	}
	else
	{
		$url = $node->urlAlias()  . "#last_from_page";
	}
	
	if( $node instanceof eZContentObjectTreeNode )
    {
    	$class_ident =  $node->ClassIdentifier;
    	if($class_ident ==  'forum_topic')
    	{
    		$class = eZContentClass::fetchByIdentifier( $node->ClassIdentifier );
	        $newNodeObject = $node->attribute( 'object' );
	        $pc_id = eZContentClass::fetchByIdentifier( "forum_reply" );
	        $canCreate = $newNodeObject->checkAccess( 'create', $pc_id->attribute( 'id' ), $newNodeObject->attribute( 'contentclass_id' ) ) == 1;
	        if ($canCreate)
	        {
		        if( $languageCode )
				{
				    $obj_lang = eZContentLanguage::fetchByLocale($languageCode);
				    $lang_list = eZContentLanguage::fetchList();
				    if(in_array($obj_lang, $lang_list))
				    {
				        $tpl->setVariable( 'LanguageCode', $languageCode );
				        $tpl->setVariable( 'NodeID', $node_id );
				    }
				    else
				    {
				        return $Module->redirectTo( '/content/view/full/' . $node_id ); 
				    }
				}
				else
				{
				    return $Module->redirectTo( '/content/view/full/' . $node_id );
				}
	        }
	    	 else
	        {
	        	return $Module->redirectTo( '/content/view/full/' . $node_id );
	        }
    	}
	    else
		{     
			$node_id = 2;
		    return $Module->redirectTo( '/content/view/full/' . $node_id );
		}
    }
	else
	{
	    $node_id = 2;
	    return $Module->redirectTo( '/content/view/full/' . $node_id );
	}	
	
}
else
{
	return $Module->redirectTo( '/content/view/full/' . $node_id );
}

//wenn die Variable in Ordnung sind dann Prüfe ob gesendet oder gecancelt ist
if( $http->hasPostVariable( "PublishButton" ) )
{
	if( $http->hasPostVariable( "Subject" ) and trim( $http->postVariable( "Subject" ) ) != "" )
	{
		$subject = $http->postVariable( "Subject" );
		$tpl->setVariable( 'Subject', $subject );
	}
	else
	{
       array_push($error, "Please enter a subject!");
	   $tpl->setVariable( 'ErrorMSG', $error );
	   $errorstatus = true;
	}
    if( $http->hasPostVariable( "Message" ) and trim( $http->postVariable( "Message" ) ) != "" )
    {
        $message = $http->postVariable( "Message" );
        $tpl->setVariable( 'Message', $message );
    }
    else
    {
       array_push($error, "Please enter a message!");
       $tpl->setVariable( 'ErrorMSG', $error );
       $errorstatus = true;
    }
    //creation hier
    if (!$errorstatus)
    {
    	$data = array();
	    $data[0] = array( "subject" => $subject, "message" => $message);
		createReply ( "forum_reply" , $node_id, $languageCode, $data, $user_id );
	    $lang->setPrioritizedLanguages( $l_prio );
	    if( $http->hasPostVariable( 'RedirectURIERROR' ) )
	    {
	    	return $Module->redirectTo( '/content/view/full/' . $node_id . "/(error)/closed" );
	    }
	    else
	    {
	    	return $Module->redirectTo( $url );
	    }
    }
}
elseif ( $http->hasPostVariable( "DiscardButton" ) )
{
	$lang->setPrioritizedLanguages( $l_prio );
	return $Module->redirectTo( $url );
}


$tpl->setVariable( 'parentID', $node_id );
$Result['content'] = $tpl->fetch( "design:reply/create.tpl" );
$Result['path'] = array(  array( 'url' => false ,  'text' => ezpI18n::tr( 'extension/xrowforum', 'Reply Creation' )));

function createReply ( $classID, $NodeID, $languageCode, $data, $user_id )
{
    $result = array();
    $contentClassID = $classID;
        $class = eZContentClass::fetchByIdentifier( $classID );        
        if ( !is_object( $class ) )
            $class = eZContentClass::fetch( $contentClassID );

        foreach ( $data as $item )
        {
            $parentNodeID = $NodeID;

            $locale = eZLocale::instance();
            $datetime_create = new eZDateTime();
            $datetime_modify = new eZDateTime();
            $datetime_create->setLocale( $locale );
            $datetime_modify->setLocale( $locale );
            
            $parentContentObjectTreeNode = eZContentObjectTreeNode::fetch( $parentNodeID );
            if ( !is_object( $parentContentObjectTreeNode ) )
            {
            	eZDebug::writeError('No parent found or parent node is no object.');
                continue;
            }
            
            $parentContentObject = $parentContentObjectTreeNode->attribute("object");
            $sectionID = $parentContentObject->attribute( 'section_id' );
            
            // ================================================     
            // Create a new Version or Update an existing item
            //=================================================
            
            $version = null;
            $attribs = null;
            $contentObject = null;
            $logMessageStart = null;
            $remoteIdString = "CreateForumReply:" . md5( (string)mt_rand() . (string)time() );
            $owner = null;
            
            // set Owner im Moment nur Administrator, später dann current_user
            $owner = $user_id;
            
            // only can update if a remote id is set and the object exists
            # $contentObject = eZContentObject::fetchByRemoteID( $remoteIdString );

            // create new ContentObject
            
            
            
            $contentObject = $class->instantiate( $owner, $sectionID );
            #$contentObject = $class->instantiate( $owner, $sectionID, false );
            $version = $contentObject->currentVersion();
            
            $version->setAttribute( 'status',  eZContentObjectVersion::STATUS_DRAFT);
            $version->store();

            //default node settings
            $node_defaults = array();
            
            //create nodes
            $merged_node_array  =  array( 'contentobject_id' => $contentObject->attribute( 'id' ),
                                          'contentobject_version' => $contentObject->attribute( 'current_version' ),
		                                  'parent_node' => $parentContentObjectTreeNode->attribute( 'node_id' ),
		                                  'is_main' => 1
                                         );
                    
             $nodeAssignment = eZNodeAssignment::create( $merged_node_array );
             $nodeAssignment->store();
            
                    // if $item[EZ_IMPORT_PRESERVED_KEY_REMOTE_ID] == null ez will generate a remoteid
             $contentObject->setAttribute( 'remote_id', $remoteIdString );
                    //$contentObject->setAttribute( 'remote_id', $item[$remote_id] );

             // $contentObject->setAttribute( 'name', $item["subject"] );
                    
             $contentObject->setAttribute( 'modified', $datetime_modify->timeStamp() );
             $contentObject->setAttribute( 'published', $datetime_create->timeStamp() );
                //  $contentObject->setAttribute( 'owner_id', $owner );
                //  to generate a remot_id if needed
                //  $contentObject->store();
                // $contentObject->name();   
                
            // get all attributes and modify data if needed
            // ----------------------------------------------               
            $attribs = $contentObject->contentObjectAttributes();      
            for($i=0;$i<count($attribs);$i++){
                $ident = $attribs[$i]->attribute("contentclass_attribute_identifier");
                if ( array_key_exists( $ident , $item ) and $item[$ident] )
                {
                    // modify the input data
                    storeAttribute( $item[$ident], $attribs[$i]);
                }
            }
            
            $contentObject->setAttribute( 'modified', $datetime_modify->timeStamp() );
            $contentObject->setAttribute( 'published', $datetime_create->timeStamp() );
            $contentObject->setAttribute( 'status',  eZContentObjectVersion::STATUS_DRAFT );
            
            // set remote_id : if $item['remote_id']=null the system genereate a new remote id
            // e.g.   ezimport:namespace:remote_id          
            $contentObject->setAttribute( 'remote_id', $remoteIdString );
            $contentObject->store();
            
            
            $operationResult = eZOperationHandler::execute(
                'content', 'publish', array(
                            'object_id' => $contentObject->attribute( 'id' ),
                            'version' => $version->attribute('version') 
                ));

        }
        return $result;
}

function storeAttribute( $data, $contentObjectAttribute )
    {
        $contentClassAttribute = $contentObjectAttribute->attribute( 'contentclass_attribute' );
        $dataTypeString = $contentClassAttribute->attribute( 'data_type_string' );

        switch( $dataTypeString )
        {
            case 'ezstring' :
                $contentObjectAttribute->setAttribute( 'data_text', $data );
                $contentObjectAttribute->store();
                break;
            case 'eztext' :
                $contentObjectAttribute->setAttribute( 'data_text', $data );
                $contentObjectAttribute->store();
                break;
            case 'ezxmltext' :
                if ( $data instanceof DOMDocument or $data instanceof DOMNode )
                {
                    $contentObjectAttribute->setAttribute( "data_text", eZXMLTextType::domString( $data ) );
                    
                    $linkNodes = $data->getElementsByTagName( 'link' );
                    $links = $data->getElementsByTagName( 'link' );
                    eZXMLTextType::transformLinksToRemoteLinks( $links );
                    foreach ( $linkNodes as $linkNode )
                    {
                        $href = $linkNode->getAttribute( 'href' );
                        if ( ! $href )
                            continue;
                        $urlObj = eZURL::urlByURL( $href );
                        
                        if ( ! $urlObj )
                        {
                            $urlObj = eZURL::create( $href );
                            $urlObj->store();
                        }
                        
                        $linkNode->removeAttribute( 'href' );
                        $linkNode->setAttribute( 'url_id', $urlObj->attribute( 'id' ) );
                        $urlObjectLink = eZURLObjectLink::create( $urlObj->attribute( 'id' ), $contentObjectAttribute->attribute( 'id' ), $contentObjectAttribute->attribute( 'version' ) );
                        $urlObjectLink->store();
                    }
                }
                else
                {
                	$contentObjectID = $contentObjectAttribute->attribute( 'contentobject_id' );
					$contentObjectAttributeID = $contentObjectAttribute->attribute( 'id' );
					$contentObjectAttributeVersion = $contentObjectAttribute->attribute('version');
					if ( $data )
					{
						$data = $data;

						// Set original input to a global variable
						$originalInput = 'originalInput_' . $contentObjectAttributeID;
						$GLOBALS[$originalInput] = $data;

						// Set input valid true to a global variable
						$isInputValid = 'isInputValid_' . $contentObjectAttributeID;
						$GLOBALS[$isInputValid] = true;

						//include_once( 'kernel/classes/datatypes/ezxmltext/handlers/input/ezsimplifiedxmlinputparser.php' );

						$text = $data;

						$text = preg_replace('/\r/', '', $text);
						$text = preg_replace('/\t/', ' ', $text);

						// first empty paragraph
						$text = preg_replace('/^\n/', '<p></p>', $text );

						eZDebugSetting::writeDebug( 'kernel-datatype-ezxmltext', $text, 'eZSimplifiedXMLInput::validateInput text' );

						$parser = new eZSimplifiedXMLInputParser( $contentObjectID, true, eZXMLInputParser::ERROR_ALL, true );
						$document = $parser->process( $text );

						if ( !is_object( $document ) )
						{
							$GLOBALS[$isInputValid] = false;
							$errorMessage = implode( ' ', $parser->getMessages() );
							$contentObjectAttribute->setValidationError( $errorMessage );
							return eZInputValidator::STATE_INVALID;
						}

						$classAttribute = $contentObjectAttribute->contentClassAttribute();
						if ( $classAttribute->attribute( 'is_required' ) == true )
						{
							$root = $document->documentElement;
							if ( !$root->hasChildNodes() )
							{
								$contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
																					 'Content required' ) );
								return eZInputValidator::STATE_INVALID;
							}
						}
						$contentObjectAttribute->setValidationLog( $parser->getMessages() );

						$xmlString = eZXMLTextType::domString( $document );

						$urlIDArray = $parser->getUrlIDArray();

						if ( count( $urlIDArray ) > 0 )
						{
							$this->updateUrlObjectLinks( $contentObjectAttribute, $urlIDArray );
						}

						$contentObject = $contentObjectAttribute->attribute( 'object' );
						$contentObject->appendInputRelationList( $parser->getRelatedObjectIDArray(), eZContentObject::RELATION_EMBED );
						$contentObject->appendInputRelationList( $parser->getLinkedObjectIDArray(), eZContentObject::RELATION_LINK );
						$contentObjectAttribute->setAttribute( 'data_text', $xmlString );
					}
                }
				$contentObjectAttribute->setAttribute( 'data_int', eZXMLTextType::VERSION_TIMESTAMP );
				$contentObjectAttribute->store();
			break;
            default :
                $contentObjectAttribute->setContent( $data );
                $contentObjectAttribute->store();
        }
    }
    
?>