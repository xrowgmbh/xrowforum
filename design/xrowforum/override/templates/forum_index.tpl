{* Forum Index - Full view *}

{set-block scope=global variable=cache_ttl}0{/set-block}
{def $page_limit = 10
     $children = array()
     $children_count = ''
     $current_user=fetch( 'user', 'current_user' )
	 $exclude_classes = array()}

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-full">
    <div class="class-folder">

        <div class="attribute-header">
            <h1>{attribute_view_gui attribute=$node.data_map.name}</h1>
        </div>
        
        {if $current_user.is_logged_in}   
            <div class="border-box">
				<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
				<div class="border-ml"><div class="border-mr"><div class="border-mc">
		            <div id="forum_top_left">
		            	<p>
		                {"Welcome"|i18n("extension/xrowforum")} {$current_user.contentobject.name}! <br />
		                {"Your last login was"|i18n("extension/xrowforum")}: {$current_user.last_visit|l10n( 'shortdatetime' )}<br />
		                <a class="bullet_item" href={"/xrowforum/removeflags"|ezurl}>{"mark all forums as read"|i18n("extension/xrowforum")}</a>
		                <a class="bullet_item" href={"xrowforum/overview"|ezurl}>{"see forum statistics"|i18n("extension/xrowforum")}</a>
		            	</p> 
		            </div>
		            <div id="forum_top_right">
		            	{include uri='design:pm/control_menu.tpl'}
		            </div>
	            </div></div></div>
				<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
			</div>
        {/if}
        
        {if $node.object.data_map.description.has_content}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/if}
        {set $children=fetch_alias( 'children', hash( 'parent_node_id', $node.node_id,
                                                      'offset', $view_parameters.offset,
                                                      'sort_by', $node.sort_array,
                                                      'class_filter_type', 'exclude',
                                                      'class_filter_array', $exclude_classes,
                                                      'limit', $page_limit ) )
             $children_count=fetch_alias( 'children_count', hash( 'parent_node_id', $node.node_id,
                                                                  'class_filter_type', 'exclude',
                                                                  'class_filter_array', $exclude_classes ) )}
    
        <div class="content-view-children">
            {foreach $children as $child }
                {node_view_gui view='full' content_node=$child other_param='line'}
            {/foreach}
        </div>
    
        {include name=navigator
                 uri='design:navigator/google.tpl'
                 page_uri=$node.url_alias
                 item_count=$children_count
                 view_parameters=$view_parameters
                 item_limit=$page_limit}

    </div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>