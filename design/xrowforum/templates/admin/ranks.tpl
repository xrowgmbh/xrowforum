{def $timestamp=currentdate()
     $user_name = ''
     $rank_count = 0}

<div class="context-block">

    {if $ErrorMSG}
        <div class="message-error">
            <h2>{'Validation Error'|i18n('extension/xrowforum')}</h2>
            <ul>
                {foreach $ErrorMSG as $error}
                    <li>{$error}</li>
                {/foreach}
            </ul>
        </div>
    {/if}
    {if $success}
        <div class="message-feedback">
            <h2><span class="time">{$timestamp|l10n( 'shortdatetime' )}</span> {'The input was successfully safed.'|i18n('extension/xrowforum')}</h2>
        </div>
    {/if}

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'User Ranks'|i18n( 'extension/xrowforum' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">


    <form name="rank_add" method="post" action={'xrowforum/ranks'|ezurl}>
        
        {* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
        
        <div class="context-toolbar">
            <div class="block">
                <div class="break"></div>
                <p>{'add a new rank'|i18n( 'extension/xrowforum' )}</p>
            </div>
        </div>
            
            <table class="list">
                <tr class="bglight">
                    <th scope="row" width="150px;">{'Rank Text'|i18n( 'extension/xrowforum' )}</th>
                    <th scope="row" width="150px;">{'Rank Category'|i18n( 'extension/xrowforum' )}</th>
                    <th scope="row" width="150px;">{'Rank Condition'|i18n( 'extension/xrowforum' )}</th>
                    <th scope="row" width="10px;">{'active'|i18n( 'extension/xrowforum' )}</th>
                </tr>
                <tr>
                    <td>
                        <input id="rank_text" name="rank_text" type="text" value="{if is_set($rank_text)}{$rank_text}{/if}" />
                    </td>
                    <td>
                        <select name="rank_category" id="select_contact">
                            <option value="0" {if $rank_category|eq(0)}selected="selected"{/if}>{'Normal Rank'|i18n('extension/xrowforum')}</option>
                            <option value="1" {if $rank_category|eq(1)}selected="selected"{/if}>{'Special Rank'|i18n('extension/xrowforum')}</option>
                        </select>
                    </td>
                    <td>
                        <input id="rank_condition" name="rank_condition" type="text" value="{if is_set($rank_condition)}{$rank_condition}{else}enter Posts or UserID{/if}" />
                    </td>
                    <td>
                        <input type="checkbox" name="active_create" value="{$active_create}" {if or(not($active_create|eq(0)),not(is_set($active_create)))}checked="checked"{/if} />
                    </td>
                </tr>
            </table>
                        
            {* DESIGN: Content END *}</div></div></div>
            
            <div class="controlbar">
            
                {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
                
                    <div class="block">
                        <input class="button" name="CreateNewRank" type="submit" value={'Execute'|i18n('extension/xrowforum')} />
                        <input class="button" type="submit" name="Cancel" value={'Clean'|i18n('extension/xrowforum')} />
                    </div>
                
                {* DESIGN: Control bar END *}</div></div></div></div></div></div>
           
            </div>
    </form>
    
<div class="context-toolbar">
    <div class="block">
        <div class="break"></div>
        <p>{'Ranklist'|i18n( 'extension/xrowforum' )}({$ranks|count})</p>
    </div>
</div>

    <table class="list">
        <tr class="bglight">
            <th scope="row" width="10px;"></th>
            <th scope="row" width="150px;">{'Rank Text'|i18n( 'extension/xrowforum' )}</th>
            <th scope="row" width="150px;">{'Rank Category'|i18n( 'extension/xrowforum' )}</th>
            <th scope="row" width="150px;">{'Rank Condition'|i18n( 'extension/xrowforum' )}</th>
            <th scope="row" width="10px;">{'active'|i18n( 'extension/xrowforum' )}</th>
            <th scope="row" width="10px;"></th>
        </tr>
        {foreach $ranks as $rank}
            {set $rank_count = inc($rank_count)}
            {if $rank.rank_category|eq(1)}
                {set $user_name=fetch( 'content', 'object', hash( 'object_id', $rank.rank_condition ) )}
            {/if}
            <tr>
                <td>{$rank_count}</td>
                <td>{$rank.rank_name}</td>
                <td>
                    {if $rank.rank_category|eq(0)}
                        {'Normal Rank'|i18n('extension/xrowforum')}
                    {else}
                        {'Special Rank'|i18n('extension/xrowforum')}
                    {/if}
                </td>
                <td>
                    {if $rank.rank_category|eq(0)}
                        {$rank.rank_condition} (Posts)
                    {else}
                        <a href={$user_name.main_node.url_alias|ezurl()}>{$user_name.name|wash()}</a> (UserID-{$rank.rank_condition})
                    {/if}
                </td>
                <td>
                    <form id="switch_{$rank.rank_index}" name="rank_switch" action={'xrowforum/ranks'|ezurl} method="post">
                        <input class="inline" name="rank_switch" type="hidden" size="30" value="{$rank.rank_index}" />
                        <input id="checkbox_{$rank.rank_index}" type="checkbox" name="acitve_list" value="{$rank.active}" {if $rank.active|eq(1)}checked="checked"{/if} onchange="check_change('{$rank.rank_index}');"/>
                    </form>
                </td>
                <td>
                    <form id="delete_{$rank.rank_index}" name="rank_delete" action={'xrowforum/ranks'|ezurl} method="post">
                        <input class="inline" name="delete_rank" type="hidden" size="30" value="{$rank.rank_index}" />
                        <a href="#"><img src={'trash-icon-16x16.gif'|ezimage()} alt="remove rank" title="remove rank" border="0" onclick="delete_item('{$rank.rank_index}', '{$rank.rank_name}');"/></a>
                    </form>
                </td>
            </tr>
        {/foreach}
    </table>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
<br />
{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>
{literal}
    <script type="text/javascript">
            function delete_item(id, name)
            {
                Check = confirm('do your really want to delete ' + name);
                if (Check == true)
                {
                   var form_delete_name = 'delete_' + id;
                   document.getElementById(form_delete_name).submit();
                }
            }
            function check_change(id)
            {
                var form_switch_name = 'switch_' + id;
                document.getElementById(form_switch_name).submit();
            }
    </script>
{/literal}