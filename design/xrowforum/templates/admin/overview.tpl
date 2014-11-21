<style type="text/css">
    @import url({'stylesheets/xrowforum_admin.css'|ezdesign});
</style>

{def $timestamp=currentdate()
     $forum_id = ezini('IDs','ForumIndexPageNodeID', 'xrowforum.ini')
     $url_var = ''
     $online_user = fetch( 'user', 'logged_in_users', hash( 'sort_by', array( array( 'login', true() ) ) ) )
     $forum_days_running_ts =  sub( $timestamp, $board_start)
     $forum_days_running = div(round(mul(sub(div($forum_days_running_ts,86400)),10)),10)
     $today=currentdate()|datetime(custom, '*%m-%d')
     $birthdayusers=fetch( 'content','tree', hash( 'parent_node_id',   5,
                                                   'main_node_only', true(),
                                                   'attribute_filter', array(array( 'user/birthday', 'like', $today))))}
                                                   
<div class="context-block">

{if $errormsg}
    <div class="message-error">
        <h2>{'Error'|i18n('extension/xrowforum')}</h2>
        <ul>
            {foreach $errormsg as $error}
                <li>{$error}</li>
            {/foreach}
        </ul>
    </div>
{/if}

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{'xrowForum Interface'|i18n( 'extension/xrowforum' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

            <div id="overview">
                <fieldset>
                    <legend>{'Information'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <tr>
                            <td width="200"><label>{'Board Started'|i18n('extension/xrowforum')}:</label></td>
                            <td>{$board_start|l10n('shortdate')} (running {$forum_days_running} days)</td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Topic Count'|i18n('extension/xrowforum')}:</label></td>
                            <td>{$topic_count}</td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Post Count'|i18n('extension/xrowforum')}:</label></td>
                            <td>{$post_count}</td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Total Count'|i18n('extension/xrowforum')}:</label></td>
                            <td>{sum($topic_count,$post_count)}</td>
                        </tr>
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>{'User'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <tr>
                            <td width="200"><label>{'User Count'|i18n('extension/xrowforum')}:</label></td>
                            <td>{$user_count}</td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Currently Online'|i18n('extension/xrowforum')} ({fetch( 'user', 'logged_in_count' )}):</label></td>
                            <td>
                                {if $online_user|count()|gt(0)}
                                    {foreach $online_user as $children}
                                        <a href={$children.contentobject.main_node.url_alias|ezurl}>{$children.contentobject.name|wash()}</a>
                                        {delimiter}, {/delimiter}
                                    {/foreach}
                                {else}
                                    {'no one online'|i18n('extension/xrowforum')}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Online In Past 24Hours'|i18n('extension/xrowforum')}:</label></td>
                           <td>
                                {if $online_past_24|count()|gt(0)}
                                    {foreach $online_past_24 as $children}
                                        {set $url_var=fetch( 'content', 'object', hash( 'object_id', $children.user_id ))}
                                        <a href={$url_var.main_node.url_alias|ezurl}>{$url_var.name|wash()}</a>{delimiter}, {/delimiter}
                                    {/foreach}
                                {else}
                                    {'no one online'|i18n('extension/xrowforum')}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Birthdays Today'|i18n('extension/xrowforum')}:</label></td>
                            <td>
                                {if $birthdayusers|count()|gt(0)}
                                    {foreach $birthdayusers as $child}
                                        <a href={$child.url_alias|ezurl}>{$child.name|wash()}({$child.data_map.birthday.content.age|wash()})</a>{delimiter}, {/delimiter}
                                    {/foreach}
                                {else}
                                    {'nobody'|i18n('extension/xrowforum')}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Most User Ever Online'|i18n('extension/xrowforum')}:</label></td>
                            <td>{$max_user_on} ({$max_user_on_date|l10n('shortdatetime')})</td>
                        </tr>
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>{'Statistics'|i18n('extension/xrowforum')}</legend>
                    
                    <table class="list">
                        <tr>
                            <td width="200"><label>{'Topics Per Day'|i18n('extension/xrowforum')}:</label></td>
                            <td>{div(round(mul(div($topic_count,$forum_days_running),10)),10)} (last24h: {$topiccount_y})</td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Posts Per Day'|i18n('extension/xrowforum')}:</label></td>
                            <td>{div(round(mul(div($post_count,$forum_days_running),10)),10)} (last24h: {$postcount_y})</td>
                        </tr>
                        <tr>
                            <td width="200"><label>{'Registrations Per Day'|i18n('extension/xrowforum')}:</label></td>
                            <td>{div(round(mul(div($user_count,$forum_days_running),10)),10)} (last24h: {$usercount_y})</td>
                        </tr>
                    </table>
                </fieldset>
                <fieldset>
                    <legend>{'latest'|i18n('extension/xrowforum')} {$stats_limit} {'registered User'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>{'Name'|i18n('extension/xrowforum')}</th>   
                                <th width="150">{'Date'|i18n('extension/xrowforum')}</th>
                            </tr>
                        </thead>
                        {foreach $latest_user as $children}
                            {set $url_var=fetch( 'content', 'object', hash( 'object_id', $children.id ) )}
                            <tr>
                                <td><a href={$url_var.main_node.url_alias|ezurl}>{$children.name|wash()}</a></td>
                                <td>{$children.published|l10n('shortdate')}</td>
                            </tr>
                        {/foreach}
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>{'latest'|i18n('extension/xrowforum')} {$stats_limit} {'Topics'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>{'Name'|i18n('extension/xrowforum')}</th>   
                                <th width="150">{'Date'|i18n('extension/xrowforum')}</th>
                            </tr>
                        </thead>
                        {foreach $latest_topics as $children}
                            {set $url_var=fetch( 'content', 'object', hash( 'object_id', $children.id ) )}
                            <tr>
                                <td><a href={$url_var.main_node.url_alias|ezurl}>{$children.name|wash()}</a></td>
                                <td>{$children.published|l10n('shortdate')}</td>
                            </tr>
                        {/foreach}
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>{'latest'|i18n('extension/xrowforum')} {$stats_limit} {'Posts'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>{'Name'|i18n('extension/xrowforum')}</th>   
                                <th width="150">{'Date'|i18n('extension/xrowforum')}</th>
                            </tr>
                        </thead>
                        {foreach $latest_posts as $children}
                            {set $url_var=fetch( 'content', 'object', hash( 'object_id', $children.id ) )}
                            <tr>
                                <td><a href={$url_var.main_node.url_alias|ezurl}>{$children.name|wash()}</a></td>
                                <td>{$children.published|l10n('shortdate')}</td>
                            </tr>
                        {/foreach}
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>{'hottest'|i18n('extension/xrowforum')} {$stats_limit} {'Topics (by views)'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>{'Name'|i18n('extension/xrowforum')}</th>   
                                <th width="150">{'views'|i18n('extension/xrowforum')}</th>
                            </tr>
                        </thead>
                        {foreach $hot_topic_by_view as $children}
                            {set $url_var=fetch( 'content', 'object', hash( 'object_id', $children.contentobject_id ) )}
                            <tr>
                                <td><a href={$url_var.main_node.url_alias|ezurl}>{$url_var.name|wash()}</a></td>
                                <td>{$children.viewcount}</td>
                            </tr>
                        {/foreach}
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>{'hottest'|i18n('extension/xrowforum')} {$stats_limit}  {'Topics (by replies)'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>{'Name'|i18n('extension/xrowforum')}</th>   
                                <th width="150">{'replies'|i18n('extension/xrowforum')}</th>
                            </tr>
                        </thead>
                        {foreach $hot_topic_by_replies as $children}
                            {set $url_var=fetch( 'content', 'node', hash( 'node_id', $children.parent_node_id    ) )}
                            <tr>
                                <td><a href={$url_var.url_alias|ezurl}>{$url_var.name|wash()}</a></td>
                                <td>{$children.kids}</td>
                            </tr>
                        {/foreach}
                    </table>
                </fieldset>

                <fieldset>
                    <legend>{'top'|i18n('extension/xrowforum')} {$stats_limit} {'activity User (by Comments)'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>{'Name'|i18n('extension/xrowforum')}</th>   
                                <th width="150">{'Amount'|i18n('extension/xrowforum')}</th>
                            </tr>
                        </thead>
                        {foreach $top_user_posts as $children}
                            {set $url_var=fetch( 'content', 'object', hash( 'object_id', $children.owner_id ) )}
                            <tr>
                                <td><a href={$url_var.main_node.url_alias|ezurl}>{$url_var.name|wash()}</a></td>
                                <td>{$children.counter}</td>
                            </tr>
                        {/foreach}
                    </table>
                </fieldset>
                
                <fieldset>
                    <legend>{'top'|i18n('extension/xrowforum')} {$stats_limit} {'activity User (by Logins)'|i18n('extension/xrowforum')}</legend>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>{'Name'|i18n('extension/xrowforum')}</th>   
                                <th width="150">{'Amount'|i18n('extension/xrowforum')}</th>
                            </tr>
                        </thead>
                        {foreach $top_user_login as $children}
                            {set $url_var=fetch( 'content', 'object', hash( 'object_id', $children.user_id ) )}
                            <tr>
                                <td><a href={$url_var.main_node.url_alias|ezurl}>{$url_var.name|wash()}</a></td>
                                <td>{$children.login_count}</td>
                            </tr>
                        {/foreach}
                    </table>
                </fieldset>
                
            </div>
            
            <div class="float-break"></div>
            
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>