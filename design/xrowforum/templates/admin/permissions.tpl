
{def $mod_forums = ''
     $mod_objectid = ezini( 'IDs', 'ModeratorGroupObjectID', 'xrowforum.ini' )
     $mod_node=fetch( 'content', 'object', hash( 'object_id', $mod_objectid ) )}

<div class="context-block">
{if not($mod_node.main_node_id|is_numeric())}
        <div class="message-error">
            <h2>{'Error'|i18n('extension/xrowforum')}</h2>
            <ul>
                <li>{'Please select a Proper value for your ModeratorGroupObjectID settings'|i18n('extension/xrowforum')}</li>
            </ul>
        </div>
{else}
    {def $mods = fetch( 'content', 'list', hash( 'parent_node_id', $mod_node.main_node_id, 'sort_by', array( 'name', false() ) ) )}
{/if}

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'Moderator Permissions'|i18n( 'extension/xrowforum' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{foreach $mods as $moderator}
    <table class="list">
        <thead>
            <tr>
                <th width="10px"></th>   
                <th>{'Name'|i18n('extension/xrowforum')}</th>
                <th width="10px"></th>
            </tr>
        </thead>
        <tr>
            <td width="10px">{$moderator.class_identifier|class_icon( small, 'Click on the icon to display a context-sensitive menu.'|i18n( 'extension/xrowforum' ) )}</td>
            <td>{$moderator.name|wash()}</td>
            <td width="10px">
                {section show=$moderator.can_edit}
                    <a href={concat( 'content/edit/', $moderator.contentobject_id )|ezurl}><img src={'edit.gif'|ezimage} alt="{'Edit'|i18n( 'extension/xrowforum' )}" title="{'Edit <%child_name>.'|i18n( 'extension/xrowforum',, hash( '%child_name', $child_name ) )|wash}" /></a>
                {section-else}
                    <img src={'edit-disabled.gif'|ezimage} alt="{'Edit'|i18n( 'extension/xrowforum' )}" title="{'You do not have permission to edit <%child_name>.'|i18n( 'extension/xrowforum',, hash( '%child_name', $child_name ) )|wash}" />
                {/section}
            </td>
        </tr>
    </table>
    {if $moderator.data_map.moderator.content.relation_list|count()|eq(0)}
        <ul>
            <li>{'no Forums to moderate selected'|i18n('extension/xrowforum')}</li>
        </ul>
    {else}
        <ul>
            {foreach $moderator.data_map.moderator.content.relation_list as $forums}
                {set $mod_forums=fetch( 'content', 'node', hash( 'node_id', $forums.node_id ) )}
                <li><a href={$mod_forums.url_alias|ezurl()}>{$mod_forums.name|wash()}</a></li>
            {/foreach}
        </ul>
    {/if}
{/foreach}


{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>