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

{def $page_limit = ezini('GeneralSettings', 'TopicsPerPage', 'xrowforum.ini')
     $posts_limit = ezini('GeneralSettings', 'PostsPerPage', 'xrowforum.ini')
     $topic_list = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                  'limit', $page_limit,
                                                  'offset', $view_parameters.offset,
                                                  'sort_by', array( array( attribute, false(), 'forum_topic/sticky' ), array( 'modified_subnode', false() ), array( 'node_id', false() ) ) ) )
     $topic_count = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )
     $new_posts = ''
     $last_reply = ''
     $topic_reply_count = ''
     $topic_reply_pages = ''
     $topic_view_count = ''
     $hot_topic_number = ezini('GeneralSettings', 'HotTopicNumber', 'xrowforum.ini')}

{set-block variable=$paginator_and_create}
    {include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=$node.url_alias
         item_count=$topic_count
         view_parameters=$view_parameters
         item_limit=$page_limit}

     {if is_unset( $versionview_mode )}
        {if $node.object.can_create}
            {def $notification_access=fetch( 'user', 'has_access_to', hash( 'module', 'notification', 'function', 'use' ) )}
            <form method="post" action={"content/action/"|ezurl}>
                <input class="button forum-new-topic" type="submit" name="NewButton" value="{'New topic'|i18n( 'extension/xrowforum' )}" />
                <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
                <input type="hidden" name="ContentLanguageCode" value="{ezini( 'RegionalSettings', 'ContentObjectLocale', 'site.ini')}" />
                {if $notification_access }
                    <input class="button forum-keep-me-updated" type="submit" name="ActionAddToNotification" value="{'Keep me updated'|i18n( 'extension/xrowforum' )}" />
                {/if}
                <input type="hidden" name="NodeID" value="{$node.node_id}" />
                <input type="hidden" name="ClassIdentifier" value="forum_topic" />
            </form>
        {else}
            <p>
            {"You need to be logged in to get access to the forums. You can do so %login_link_start%here%login_link_end%"|i18n( "extension/xrowforum",,
             hash( '%login_link_start%', concat( '<a href=', '/user/login/'|ezurl, '>' ), '%login_link_end%', '</a>' ) )}
            </p>
        {/if}
    {/if}
{/set-block}

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-full">
    <div class="class-forum">

    <div class="attribute-header">
        <h1>{$node.name|wash()}</h1>
    </div>

    <div class="attribute-short">
        {attribute_view_gui attribute=$node.data_map.description}
    </div>

    {$paginator_and_create}

    <div class="content-view-children">

        <table class="list forum" cellspacing="0">
        <tr>
            <th colspan="2" class="topic">
                {"Topic"|i18n( "extension/xrowforum" )}
            </th>
            <th class="replies">
                {"Replies"|i18n( "extension/xrowforum" )}
            </th>
            <th class="views">
                {"Views"|i18n( "extension/xrowforum" )}
            </th>
            <th class="author">
                {"Author"|i18n( "extension/xrowforum" )}
            </th>
            <th class="lastreply">
                {"Last reply"|i18n( "extension/xrowforum" )}
            </th>
        </tr>

        {foreach $topic_list as $topic sequence array( 'bglight', 'bgdark' ) as $style}
            {set $topic_view_count = read_count($topic.contentobject_id)}
            {set $new_posts = like_operator($topic.path_string)}
            {set $topic_reply_count=fetch( 'content', 'tree_count', hash( parent_node_id, $topic.node_id ) )
                 $topic_reply_pages=sum( int( div( sum( $topic_reply_count, 1 ), $posts_limit ) ), cond( mod( sum( $topic_reply_count, 1 ), $posts_limit )|gt( 0 ), 1, 0 ) )}
        <tr class="{$style}">
            <td class="flag">
                {if and($topic.data_map.closed.content|eq(1),$topic_reply_count|ge($hot_topic_number),not($hot_topic_number|eq(0)))}
                    {* closed and hot - if hot activated *}
                    <img src={'forum/hot_closed_post.gif'|ezimage()} alt="closed and hot" title="closed and hot" />
                {elseif or(and($topic.data_map.closed.content|eq(1),$topic_reply_count|lt($hot_topic_number)),and($topic.data_map.closed.content|eq(1), $hot_topic_number|eq(0)))}
                     {* closed and not hot *}
                    <img src={'forum/closed_area.gif'|ezimage()} alt="closed and not hot" title="closed and not hot" />
                {elseif or(and($new_posts|count()|gt(0), $topic_reply_count|lt($hot_topic_number)),and($hot_topic_number|eq(0),$new_posts|count()|gt(0)))}
                     {* new and not hot *}
                    <a href="javascript: deflag('{$topic.node_id}')" title={"mark this forum as read"|i18n( "extension/xrowforum" )}>
                        <img src={'forum/new_post.gif'|ezimage()} alt="new and not hot" title="new and not hot" />
                    </a>
                {elseif and($new_posts|count()|gt(0), $topic_reply_count|ge($hot_topic_number),not($hot_topic_number|eq(0)))}
                    {* new and hot - if hot activated *}
                    <a href="javascript: deflag('{$topic.node_id}')" title={"mark this forum as read"|i18n( "extension/xrowforum" )}>
                        <img src={'forum/hot_new_post.gif'|ezimage()} alt="new and hot" title="new and hot" />
                    </a>
                {elseif or(and($new_posts|count()|eq(0), $topic_reply_count|lt($hot_topic_number)),and($new_posts|count()|eq(0),$hot_topic_number|eq(0)))}
                    {* old and not hot *}
                    <img src={'forum/old_post.gif'|ezimage()} alt="old and not hot" title="old and not hot" />
                {elseif and($new_posts|count()|eq(0), $topic_reply_count|ge($hot_topic_number),not($hot_topic_number|eq(0)))}
                    {* old and hot - if hot acitvated *}
                    <img src={'forum/hot_old_post.gif'|ezimage()} alt="old and hot" title="old and hot" />
                {/if}
            </td>
            <td class="topic">
                <p>
                    {if $topic.object.data_map.sticky.content}
                        <img class="forum-topic-sticky" src={"sticky-16x16-icon.gif"|ezimage} height="16" width="16" align="middle" alt="sticky" />
                    {/if}
                    <a href={$topic.url_alias|ezurl}>{$topic.object.name|wash()}</a>
                    {if $new_posts|count()|gt(1)}
                        ({$new_posts|count()} {'unread'|i18n( "extension/xrowforum" )})
                    {elseif $new_posts|count()|gt(0)}
                        ({"unread"|i18n( "extension/xrowforum" )})
                    {/if}
                </p>
                {if $topic_reply_pages|gt(1)}
                    <p>
                        {'Pages'|i18n( 'extension/xrowforum' )}:
                        {if $topic_reply_pages|gt( 5 )}
                            <a href={$topic.url_alias|ezurl}>1</a>
                            ...
                            <a href={concat( $topic.url_alias, '/(offset)/', mul( sub( $topic_reply_pages, 1 ), $posts_limit ) )|ezurl}>{$topic_reply_pages}</a>
                        {else}
                            <a href={$topic.url_alias|ezurl}>1</a>
                            {for 2 to $topic_reply_pages as $counter}
                                <a href={concat( $topic.url_alias, '/(offset)/', mul( sub( $counter, 1 ), $posts_limit ) )|ezurl}>{$counter}</a>
                            {/for}
                        {/if}
                    </p>
                {/if}
            </td>
            <td class="replies">
                <p>{$topic_reply_count}</p>
            </td>
            <td class="views">
                <p>{if $topic_view_count|count|eq(0)}0{else}{$topic_view_count.0.viewcount}{/if}</p>
            </td>
            <td class="author">
                <div class="attribute-byline">
                   <p class="author"><a href={$topic.object.owner.main_node.url_alias|ezurl()}>{$topic.object.owner.name|wash()}</a></p>
                </div>
            </td>
            <td class="lastreply">
                {set $last_reply=fetch('content','list',hash(   'parent_node_id', $topic.node_id,
                                                                'sort_by', array( array( 'published', false() ) ),
                                                                'limit', 1 ) )}
                {if $last_reply|count()|gt(0)}
                    {foreach $last_reply as $reply}
                        <div class="attribute-byline">
                            <p class="date">{$reply.object.published|l10n(shortdatetime)}</p>
                            <p class="author">
                                {if $topic_reply_count|gt( dec($page_limit) )}
                                    <a href={concat( $reply.parent.url_alias, '/(offset)/', sub( $topic_reply_count, mod( $topic_reply_count, $page_limit ) ) , '#msg', $reply.node_id )|ezurl}><br />{'by'|i18n( 'extension/xrowforum' )}:</a> 
                                {else}
                                    <a href={concat( $reply.parent.url_alias, '#msg', $reply.node_id )|ezurl}><br />{'by'|i18n( 'extension/xrowforum' )}:</a> 
                                {/if}                   
                                <a href={$reply.object.owner.main_node.url_alias|ezurl()}>{$reply.object.owner.name|wash()}</a>
                            </p>
                        </div>
                    {/foreach}
                {/if}
                <form name="form_{$topic.node_id}" id="form_{$topic.node_id}" method="post" action={$node.url_alias|ezurl()}>
                    <input name="subtree_delete" type="hidden" value="{$topic.path_string}" />
                </form>
           </td>
        </tr>
        {/foreach}
        </table>

    </div>

    </div>
</div>

{$paginator_and_create}

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>