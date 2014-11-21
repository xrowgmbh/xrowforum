{set-block scope=global variable=cache_ttl}0{/set-block}

{def $page_limit = ezini('GeneralSettings', 'PostsPerPage', 'xrowforum.ini')
     $reply_limit=cond( $view_parameters.offset|gt( 0 ), $page_limit, dec($page_limit) )
     $reply_offset=cond( $view_parameters.offset|gt( 0 ), dec($view_parameters.offset), $view_parameters.offset )
     $reply_list=fetch('content','list', hash( 'parent_node_id', $node.node_id,
                                               'limit', $reply_limit,
                                               'offset', $reply_offset,
                                               'sort_by', array( array( published, true() ) ) ) )
     $reply_count=fetch('content','list_count', hash( parent_node_id, $node.node_id ) )
     $previous_topic=fetch_alias( subtree, hash( 'parent_node_id', $node.parent_node_id,
                                                 'class_filter_type', include,
                                                 'class_filter_array', array( 'forum_topic' ),
                                                 'limit', 1,
                                                 'attribute_filter', array( and, array( 'modified_subnode', '<', $node.modified_subnode ) ),
                                                 'sort_by', array( array( 'modified_subnode', false() ), array( 'node_id', false() ) ) ) )
     $next_topic=fetch_alias( subtree, hash( 'parent_node_id', $node.parent_node_id,
                                             'class_filter_type', include,
                                             'class_filter_array', array( 'forum_topic' ),
                                             'limit', 1,
                                             'attribute_filter', array( and, array( 'modified_subnode', '>', $node.modified_subnode ) ),
                                             'sort_by', array( array( 'modified_subnode', true() ), array( 'node_id', true() ) ) ) ) 
     $topic_view_count = write_count($node.contentobject_id)
     $owner = $node.object.owner
     $owner_map = $owner.data_map
     $owner_id = $node.object.owner_id
     $node_path_array = array($node.path_string)
     $children_path_array = array()
     $forum = $node.parent
     $ranking = ezini('GeneralSettings', 'Rankings', 'xrowforum.ini')
     $mod_id = ezini('IDs', 'ModeratorGroupObjectID', 'xrowforum.ini')
     $current_user=fetch( 'user', 'current_user' )
     $LanguageCode = $node.object.current_language
     $close_access=fetch( 'user', 'has_access_to', hash( 'module',   'xrowforum',
                                                         'function', 'close',
                                                         'user_id',  $current_user.contentobject_id ) )
     $related=fetch( 'content', 'related_objects', hash(  'object_id', $current_user.contentobject_id,
                                                          'all_relations', true() ) )
     $moderator_here = false
     $policies=fetch( 'user', 'user_role', hash( 'user_id', $current_user.contentobject_id ) )
     $user_online = ''
}

{if $related|count()|gt(0)}
    {foreach $related as $rel_object}
        {if $node.path_string|begins_with($rel_object.main_node.path_string)}
            {set $moderator_here = true}
            {break}
        {/if}
    {/foreach}
{/if}

{if ezhttp_hasvariable( 'paths', 'session' )}
    {def $new_items = ezhttp( 'paths', 'session' )}
{/if}

{literal}
    <script type="text/javascript">
        function setID(id)
            {
                YAHOO.util.Cookie.set("quote_id", id, { path: "/" });
            }
    </script>
{/literal}

{if and(is_set($view_parameters.error),$view_parameters.error|eq('closed'))}
    <div class="message-error">
        <h2>{'Error'|i18n('extension/xrowforum')}</h2>
        <ul>
            <li>{'This Topic has been closed while you created a new post, your post has been deleted.'|i18n( "extension/xrowforum" )}</li>
        </ul>
    </div>
{/if}

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">
     
<div class="content-view-full">
    <div class="class-forum-topic">
        <div class="attribute-header">
            <h1>{$node.name|wash()} {if $new_items|contains($node_path_array.0)}<span class="red_new">- {'NEW'|i18n( "extension/xrowforum" )}</span>{/if}</h1>
        </div>
        
        {if is_unset( $versionview_mode )}
            <div class="content-navigator">
                {if $previous_topic}
                    <div class="content-navigator-previous">
                        <div class="content-navigator-arrow">&laquo;&nbsp;</div><a href={$previous_topic[0].url_alias|ezurl} title="{$previous_topic[0].name|wash()}">{'Previous topic'|i18n( 'extension/xrowforum' )}</a>
                    </div>
                {else}
                    <div class="content-navigator-previous-disabled">
                        <div class="content-navigator-arrow">&laquo;&nbsp;</div>{'Previous topic'|i18n( 'extension/xrowforum' )}
                    </div>
                {/if}
    
                {if $previous_topic}
                    <div class="content-navigator-separator">|</div>
                {else}
                    <div class="content-navigator-separator-disabled">|</div>
                {/if}
    
                <div class="content-navigator-forum-link"><a href={$forum.url_alias|ezurl}>{$forum.name|wash()}</a></div>
    
                {if $next_topic}
                    <div class="content-navigator-separator">|</div>
                {else}
                    <div class="content-navigator-separator-disabled">|</div>
                {/if}
                
                {if $next_topic}
                    <div class="content-navigator-next">
                        <a href={$next_topic[0].url_alias|ezurl} title="{$next_topic[0].name|wash()}">{'Next topic'|i18n( 'extension/xrowforum' )}</a><div class="content-navigator-arrow">&nbsp;&raquo;</div>
                    </div>
                {else}
                    <div class="content-navigator-next-disabled">
                        {'Next topic'|i18n( 'extension/xrowforum' )}<div class="content-navigator-arrow">&nbsp;&raquo;</div>
                    </div>
                {/if}
            </div>
            
            {set-block variable=$paginator_and_create}
            
                 {if $node.data_map.closed.content|eq(1)}
                    <p>This Topic has been closed</p>
                    <div class="float-break"></div>
                {else}
                    {if $node.object.can_create}
                        {def $notification_access=fetch( 'user', 'has_access_to', hash( 'module', 'notification', 'function', 'use' ) )}
                        <form method="post" action={concat('/xrowforum/create/', $node.node_id, '/', $LanguageCode)|ezurl}>
                            <input class="button forum-new-reply" type="submit" name="NewButton" value="{'New reply'|i18n( 'extension/xrowforum' )}" />
                            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                            <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
                            <input type="hidden" name="NodeID" value="{$node.node_id}" />
                            <input type="hidden" name="ClassIdentifier" value="forum_reply" />
                            <input type="hidden" name="ContentLanguageCode" value="{ezini( 'RegionalSettings', 'ContentObjectLocale', 'site.ini')}" />
                        </form>
                        {if $notification_access}
                       		<form method="post" action={"content/action/"|ezurl}>
                        		<input class="button forum-keep-me-updated" type="submit" name="ActionAddToNotification" value="{'Keep me updated'|i18n( 'extension/xrowforum' )}" />
                        		<input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
								<input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
                        	</form>
                        {/if}
                    {else}
                       <p>
                        {"You need to be logged in to get access to the forums. You can do so %login_link_start%here%login_link_end%"|i18n( "extension/xrowforum",,
                        hash( '%login_link_start%', concat( '<a href=', '/user/login/'|ezurl, '>' ), '%login_link_end%', '</a>' ) )}
                       </p>
                    {/if}
            
                {/if}
                {include name=navigator
                         uri='design:navigator/google.tpl'
                         page_uri=$node.url_alias
                         item_count=inc($reply_count)
                         view_parameters=$view_parameters
                         item_limit=$page_limit}
            {/set-block}
       
        {/if}
        {$paginator_and_create}
        <table class="list forum" cellspacing="0">
        <tr>
            <th class="author">
                {"Author"|i18n("extension/xrowforum")}
            </th>
            <th class="message">
                {"Message"|i18n("extension/xrowforum")}
             </th>
        </tr>
        {if $view_parameters.offset|lt( 1 )}
            <tr class="bglight">
                <td class="author">
                    <p class="author">
                    	<a href={$owner.main_node.url_alias|ezurl()}>{$owner.name|wash()}</a>
                    	{set $user_online=fetch( 'user', 'is_logged_in', hash( 'user_id', $owner.id ) )}
                    	{if $user_online}
                    		(online)
                    	{else}
                    		(offline)
                    	{/if}
                    </p>
                    {if $ranking|eq('enabled')}
                        <p class="rank">{request_rank($owner.id)}</p>
                    {elseif is_set($owner.title)}
                        <p class="rank">{$owner.title|wash()}</p>
                    {/if}
                    {if $owner_map.image.has_content}
                        <div class="authorimage">
                            {attribute_view_gui attribute=$owner_map.image image_class=small}
                        </div>
                    {/if}
                    <div class="user_state">
	                    {if pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2)}
	                    	 <p class="blocked_user">User Blocked</p>
	                    {elseif pm_is_inRelation( $current_user.contentobject_id, $owner.id, 1)}
	                    	 <p class="friend_user">Your Friend</p>
	                    {/if}
                    </div>
                     <div class="post_count_author">
                        <p>{object_by_id($owner.id)} {'posts'|i18n( "extension/xrowforum" )}</p>
                     </div>
                    {if is_set( $owner_map.location )}
                       {attribute_view_gui attribute=$owner_map.location}
                    {/if}
                    {if or(and($node.object.can_edit,or($owner.id|eq($current_user.contentobject_id),$moderator_here|eq(true))),and($policies.0.moduleName|eq('*'),$policies.0.functionName|eq('*')))}
                        <form method="post" action={"content/action/"|ezurl}>
                            <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                            <input class="button forum-account-edit" type="submit" name="EditButton" value="{'Edit'|i18n('extension/xrowforum')}" />
                            <input type="hidden" name="ContentObjectLanguageCode" value="{$node.object.current_language}" />
                            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
							<input type="hidden" name="RedirectURIAfterPublish" value={$node.url_alias|ezurl()} />
							<input type="hidden" name="RedirectIfDiscarded" value={$node.url_alias|ezurl()} />
                        </form>
                    {/if}
                    {if or(and($node.object.can_remove, $moderator_here|eq(true)),and($policies.0.moduleName|eq('*'),$policies.0.functionName|eq('*')))}
                        <form method="post" action={"content/action/"|ezurl}>
                            <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                            <input class="button" type="submit" name="ActionRemove" value="{'Remove'|i18n( 'extension/xrowforum' )}" title="{'Remove this item.'|i18n( 'extension/xrowforum' )}" />
                        </form>
                    {/if}
                    {if or(and($node.object.can_move, $moderator_here|eq(true)),and($policies.0.moduleName|eq('*'),$policies.0.functionName|eq('*')))}
                        <form method="post" action={"content/action/"|ezurl}>
                            <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                            <input type="hidden" name="ContentObjectLanguageCode" value="{$node.object.current_language}" />
                            <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                            <input class="button" name="MoveNodeButton" value="{'Move'|i18n( 'extension/xrowforum' )}" title="Move this item to another location." type="submit" />
                        </form>
                    {/if}
                    {if or(and($close_access, $moderator_here|eq(true)),and($policies.0.moduleName|eq('*'),$policies.0.functionName|eq('*')))}
                        <form method="post" action={concat('/xrowforum/close/', $node.node_id)|ezurl}>
                            <input class="button" type="submit" value="{if $node.data_map.closed.content|eq(1)}{'Open'|i18n( 'extension/xrowforum' )}{else}{'Close'|i18n( 'extension/xrowforum' )}{/if}" />
                            <input type="hidden" name="NodeID" value="{$node.node_id}" />
                        </form>
                    {/if}
                    {if $owner.id|ne($current_user.contentobject_id)}
						{if not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2))}
							<form action={concat('/pm/create/', $owner.id )|ezurl()} method="post" >
								<input class="defaultbutton" type="submit" name="ReplyButton" value="{'send PM'|i18n('extension/xrowpm')}" />
							</form>
						{/if}
						{if and(
								not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 1)),
								not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 0)),
								not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2))
							    )}
							<form action={"pm/network"|ezurl()} method="post">
								<input class="box" type="hidden" name="recipient_name" value="{$owner.name|wash()}" />
								<input class="box" type="hidden" name="action_type" value="0" />						
								<input class="defaultbutton" type="submit" name="NetworkActionButton" value="{'friendship request'|i18n('extension/xrowpm')}" />
							</form>
						{/if}
						{if not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2))}
							<form action={"pm/network"|ezurl()} method="post">
								<input class="box" type="hidden" name="recipient_name" value="{$owner.name|wash()}" />
								<input class="box" type="hidden" name="action_type" value="1" />	
								<input class="defaultbutton" type="submit" name="NetworkActionButton" value="{'block user'|i18n('extension/xrowpm')}" />
							</form>
						{/if}
					{/if}
                </td>
                <td class="message">
                    <p class="date">
                        {$node.object.published|l10n(datetime)}
                        {if $new_items|contains($node_path_array.0)}
                            <span class="red_new">- {'NEW'|i18n( "extension/xrowforum" )}</span>
                        {/if}
                        {if and($current_user.is_logged_in, $node.data_map.closed.sort_key_int|eq('0'))}
                            <form method="post" action={concat('/xrowforum/create/', $node.node_id, '/', $LanguageCode)|ezurl} onsubmit="setID('{$node.node_id}');">
                                <input class="button" type="submit" name="NewButton" value="{'Quote it'|i18n( 'extension/xrowforum' )}" />
                                <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                                <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                                <input type="hidden" name="NodeID" value="{$node.node_id}" />
                                <input type="hidden" name="ClassIdentifier" value="forum_reply" />
                                <input type="hidden" name="ContentLanguageCode" value="{$node.object.current_language}" />                         
                            </form>
                        {/if}
                        {if $current_user.is_logged_in}
                        	<input class="button" type="button" onclick="report_abuse('{$node.node_id}')" value="{'Report abuse'|i18n( 'extension/xrowforum' )}" />
                        {/if}
                    </p>
                    <div class="content_block">
                    <p>
                        {operator_merge(content, $node.data_map.message.content)}
                    </p>
                    </div>
                    {if $node.contentobject_version|ne('1')}
                        <div class="modified_block">
                            <p>{"modified"|i18n( "extension/xrowforum" )}: {$node.object.modified|l10n(datetime)} -  <a href={$node.object.current.creator.main_node.url_alias|ezurl}>{$node.object.current.creator.name|wash}</a></p>
                        </div>
                    {/if} 
                    {if $owner_map.signature.has_content}
                        <div class="content_block">
                            <p class="author-signature">
                                {operator_merge(signature, $owner_map.signature.content)}
                            </p>
                        </div>
                    {/if}
                </td>
            </tr>
        {/if}

        {foreach $reply_list as $reply sequence array( 'bgdark', 'bglight' ) as $style }
            {set $children_path_array = array( $reply.path_string )}
            <tr class="{$style}">
               <td class="author">
                    {set $owner = $reply.object.owner
                         $owner_map = $owner.data_map}
                   
                    <p class="author"><a href={$owner.main_node.url_alias|ezurl()}>{$owner.name|wash()}</a>
                        {set $user_online=fetch( 'user', 'is_logged_in', hash( 'user_id', $owner.id ) )}
                    	{if $user_online}
                    		(online)
                    	{else}
                    		(offline)
                    	{/if}
                    </p>
					{if $ranking|eq('enabled')}
                    	<p class="rank">{request_rank($owner.id)}</p>
                    {elseif is_set($owner_map.title)}
                    	<p class="rank">{$owner_map.title|wash()}</p>             
                    {/if}

                    {if $owner_map.image.has_content}
                        <div class="authorimage">
                            {attribute_view_gui attribute=$owner_map.image image_class=small}
                        </div>
                    {/if}
                    <div class="user_state">
	                    {if pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2)}
	                    	 <p class="blocked_user">User Blocked</p>
	                    {elseif pm_is_inRelation( $current_user.contentobject_id, $owner.id, 1)}
	                    	 <p class="friend_user">Your Friend</p>
	                    {/if}
                    </div>
                    
                     <div class="post_count_children">
                        <p>{object_by_id($owner.id)} {'posts'|i18n( "extension/xrowforum" )}</p>
                     </div>

                    {if is_set( $owner_map.location )}
                        {attribute_view_gui attribute=$owner_map.location}
                    {/if}
                    {if or(and($reply.object.can_edit,or($owner.id|eq($current_user.contentobject_id),$moderator_here|eq(true))),and($policies.0.moduleName|eq('*'),$policies.0.functionName|eq('*')))}
                        <form method="post" action={"content/action/"|ezurl}>
                            <input type="hidden" name="ContentObjectID" value="{$reply.object.id}" />
                            <input class="button" type="submit" name="EditButton" value="{'Edit'|i18n('extension/xrowforum')}" />
                            <input type="hidden" name="ContentObjectLanguageCode" value="{$node.object.current_language}" />
							{if $view_parameters.offset|ne(0)}
								<input type="hidden" name="RedirectURIAfterPublish" value="{concat($node.url_alias, "/(offset)/", $view_parameters.offset, "#msg", $reply.node_id )}" />
								<input type="hidden" name="RedirectIfDiscarded" value="{concat($node.url_alias, "/(offset)/", $view_parameters.offset, "#msg", $reply.node_id )}" />
							{else}
								<input type="hidden" name="RedirectURIAfterPublish" value="{concat($node.url_alias, "#msg", $reply.node_id )}" />
								<input type="hidden" name="RedirectIfDiscarded" value="{concat($node.url_alias, "#msg", $reply.node_id )}" />
							{/if}
                        </form>
                    {/if}
                    {if or(and($reply.object.can_remove, $moderator_here|eq(true)),and($policies.0.moduleName|eq('*'),$policies.0.functionName|eq('*')))}
                        <form method="post" action={"content/action/"|ezurl}>
                            <input type="hidden" name="ContentObjectID" value="{$reply.object.id}" />
                            <input type="hidden" name="ContentNodeID" value="{$reply.node_id}" />
                            <input class="button" type="submit" name="ActionRemove" value="{'Remove'|i18n( 'extension/xrowforum' )}" title="{'Remove this item.'|i18n( 'extension/xrowforum' )}" />
                        </form>
                    {/if}
                    {if or(and($reply.object.can_move, $moderator_here|eq(true)),and($policies.0.moduleName|eq('*'),$policies.0.functionName|eq('*')))}
                        <form method="post" action={"content/action/"|ezurl}>
                            <input type="hidden" name="ContentObjectID" value="{$reply.object.id}" />
                            <input type="hidden" name="ContentObjectLanguageCode" value="{$node.object.current_language}" />
                            <input type="hidden" name="ContentObjectID" value="{$reply.object.id}" />
                            <input type="hidden" name="ContentNodeID" value="{$reply.node_id}" />    
                            <input class="button" name="MoveNodeButton" value="{'Move'|i18n( 'extension/xrowforum' )}" title="Move this item to another location." type="submit" />
                        </form>
                    {/if}
                    {if $owner.id|ne($current_user.contentobject_id)}
						{if not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2))}
							<form action={concat('/pm/create/', $owner.id )|ezurl()} method="post" >
								<input class="defaultbutton" type="submit" name="ReplyButton" value="{'send PM'|i18n('extension/xrowpm')}" />
							</form>
						{/if}
						{if and(
								not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 1)),
								not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 0)),
								not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2))
							    )}
							<form action={"pm/network"|ezurl()} method="post">
								<input class="box" type="hidden" name="recipient_name" value="{$owner.name|wash()}" />
								<input class="box" type="hidden" name="action_type" value="0" />						
								<input class="defaultbutton" type="submit" name="NetworkActionButton" value="{'friendship request'|i18n('extension/xrowpm')}" />
							</form>
						{/if}
						{if not(pm_is_inRelation( $current_user.contentobject_id, $owner.id, 2))}
							<form action={"pm/network"|ezurl()} method="post">
								<input class="box" type="hidden" name="recipient_name" value="{$owner.name|wash()}" />
								<input class="box" type="hidden" name="action_type" value="1" />	
								<input class="defaultbutton" type="submit" name="NetworkActionButton" value="{'block user'|i18n('extension/xrowpm')}" />
							</form>
						{/if}
					{/if}
                </td>
                <td class="message">
                    <p class="date">
                        {$reply.object.published|l10n( datetime )}
                        {if $new_items|contains($children_path_array.0)}
                            <span class="red_new">- {'NEW'|i18n( "extension/xrowforum" )}</span>
                        {/if}
                        {if and($current_user.is_logged_in, $node.data_map.closed.sort_key_int|eq('0'))}
                            <form method="post" action={concat('/xrowforum/create/', $node.node_id, '/', $LanguageCode)|ezurl} onsubmit="setID('{$reply.node_id}');">
                                <input class="button greeninp" type="submit" name="NewButton" value="{'Quote it'|i18n( 'extension/xrowforum' )}" />
                                <input type="hidden" name="ContentObjectID" value="{$reply.object.id}" />
                                <input type="hidden" name="ContentNodeID" value="{$reply.node_id}" />
                                <input type="hidden" name="NodeID" value="{$node.node_id}" />
                                <input type="hidden" name="ClassIdentifier" value="forum_reply" />
                                <input type="hidden" name="ContentLanguageCode" value="{$node.object.current_language}" />                         
                            </form>
                        {/if}
                        {if $current_user.is_logged_in}
                        	<input class="button" type="button" onclick="report_abuse('{$reply.node_id}')" value="{'Report abuse'|i18n( 'extension/xrowforum' )}" />
                        {/if}
                    </p>
                    <a id="msg{$reply.node_id}"></a>
                    <div class="content_block">
                        <p>
                            {operator_merge(content, $reply.object.data_map.message.content)}
                        </p>
                    </div>
                    {if $reply.contentobject_version|ne('1')}
                        <div class="modified_block">
                            <p>{"modified"|i18n( "extension/xrowforum" )}: {$reply.object.modified|l10n(datetime)} -  <a href={$reply.object.current.creator.main_node.url_alias|ezurl}>{$reply.object.current.creator.name|wash}</a></p>
                        </div>
                    {/if} 
                    {if $owner_map.signature.has_content}
                        <div class="content_block">
                            <p class="author-signature">
                                {operator_merge(signature, $owner_map.signature.content)}
                            </p>
                        </div>
                    {/if}
                </td>
            </tr>
        {/foreach}
		<a id="last_from_page"></a>
        </table>
        {$paginator_and_create}
    </div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>