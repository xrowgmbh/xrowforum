{set-block scope=global variable=cache_ttl}0{/set-block}

{literal}
    <script language="JavaScript" type="text/javascript">
        <!--
            function deflag(id)
            {
                document.getElementById("form_" + id).submit();
            }
        -->
    </script>
{/literal}

{if ezhttp_hasvariable( 'subtree_delete', 'post' )}
    {def $delete_subtree = ezhttp( 'subtree_delete', 'post' )
         $deflag = deflag_subtree($delete_subtree)}
{/if}

{* Forums - Full view *}

{def $latest_item = ''
     $new_posts = ''
     $forum_id = ezini('IDs','ForumIndexPageNodeID', 'xrowforum.ini')
     $mod_node = ezini('IDs','ModeratorGroupObjectID', 'xrowforum.ini')
     $forum_has_moderator = false}
     
<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">
<div class="content-view-full">
    <div class="class-forums">
        <div class="attribute-header">
			{if and(is_set($other_param),$other_param|eq('line'))}
				<h2><a href={$node.url_alias|ezurl()}>{$node.name|wash()}</a></h2>
			{else}
				<h1>{$node.name|wash()}</h1>
			{/if}
        </div>

        {if $node.object.data_map.description.has_content}
            <div class="attribute-long">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/if}

        {def $children=fetch_alias( 'children', hash( parent_node_id, $node.node_id,
                                                         offset, $view_parameters.offset,
                                                         sort_by, $node.sort_array,
                                                         class_filter_type, include,
                                                         class_filter_array, array( 'forum' ),
                                                         limit, $page_limit ) )}
        <table class="list forum" cellspacing="0">
            <tr>
                <th colspan="2">{"Forum"|i18n( "extension/xrowforum" )}</th>
                <th class="topic">{"Topics"|i18n( "extension/xrowforum" )}</th>
                <th class="replies">{"Posts"|i18n( "extension/xrowforum" )}</th>
                <th class="lastreply">{"Last reply"|i18n( "extension/xrowforum" )}</th>
            </tr>
            {foreach $children as $child}
                {set $new_posts = like_operator($child.path_string)
                     $latest_item = fetch( 'content', 'tree', hash( 'parent_node_id', $child.node_id,
                                                                    'sort_by', array( 'published', false() ),
                                                                    'depth', 2,
                                                                    'limit', 1 ))}
                {def $mods = fetch( 'content', 'reverse_related_objects', hash( 'object_id', $child.contentobject_id, 'all_relations', true() ) )}
                <tr>
                    <td class="flag">
                        {if $new_posts|count()|gt(0)}
                            <a href="javascript: deflag('{$child.node_id}')" title={'mark this forum as read'|i18n( "extension/xrowforum" )}>
                                <img src={'forum/new_post.gif'|ezimage()} alt="new post" title="new post" />
                            </a>
                        {else}
                            <img src={'forum/old_post.gif'|ezimage()} alt="old post" title="old post" />
                        {/if}
                    </td>
                    <td class="forum">
                        <a href={$child.url_alias|ezurl}>{$child.name|wash()}{if $new_posts|count()|gt(0)} ({$new_posts|count()} {'unread'|i18n( "extension/xrowforum" )}){/if}</a><br />
                        {attribute_view_gui attribute=$child.data_map.description}
                        {if $mods|gt(0)}
                            <p>
                                {'Moderator'|i18n( "extension/xrowforum" )}:
                                {foreach $mods as $mod}
                                    <a href={$mod.main_node.url_alias|ezurl()}>{$mod.main_node.name|wash}</a>
                                    {delimiter},{/delimiter}
                                {/foreach}
                            </p>
                        {/if}
                    </td>
                    <td>{fetch('content','list_count',hash(parent_node_id,$child.node_id))}</td>
                    <td>{fetch('content','tree_count',hash(parent_node_id,$child.node_id))}</td>
                    <td class="last-reply">
                        {foreach $latest_item as $topic}
                            <a href={$topic.url_alias|ezurl}>{$topic.name}</a>
                            <p class="date">{$topic.object.published|l10n(shortdatetime)}</p>
                            <a href={$topic.object.owner.main_node.url_alias|ezurl()}>{$topic.object.owner.name|wash()}</a>
                        {/foreach}
                        {if and(is_set($other_param),$other_param|eq('line'))}
                            <form name="form_{$child.node_id}" id="form_{$child.node_id}" method="post" action={$node.parent.url_alias|ezurl()}>
                        {else}
                            <form name="form_{$child.node_id}" id="form_{$child.node_id}" method="post" action={$node.url_alias|ezurl()}>
                        {/if}
                                <input name="subtree_delete" type="hidden" value="{$child.path_string}" />
                            </form>
                    </td>
                </tr>
                {undef $mods}
            {/foreach}
        </table>
    </div>
</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>