{def $timestamp=currentdate()
}

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

<h1 class="context-title">{'Forum Settings'|i18n( 'extension/xrowforum' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div></div></div></div></div>

<form name="general_settings" method="post" action={'xrowforum/settings'|ezurl}>

    {* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

        <div class="context-toolbar">
            <div class="block">
                <div class="break"></div>
                <p>{'id settings'|i18n( 'extension/xrowforum' )}</p>
            </div>
        </div>
            
            <table class="list">
                <thead>
                    <tr>
                        <th width="300">{'Setting'|i18n('extension/xrowforum')}</th>
                        <th>ID</th>
                    </tr>
                </thead>
                <tr>
                    <td>ForumIndexPageNodeID</td>
                    <td><input name="forum_id" type="text" value="{$forum_id}" /></td>
                </tr>
                <tr>
                    <td>ModeratorGroupObjectID</td>
                    <td><input name="mod_id" type="text" value="{$mod_id}" /></td>
                </tr>
                <tr>
                    <td>UserContentClassID</td>
                    <td><input name="user_class" type="text" value="{$user_class}" /></td>
                </tr>
                <tr>
                    <td>TopicContentClassID</td>
                    <td><input name="topic_class" type="text" value="{$topic_class}" /></td>
                </tr>
                <tr>
                    <td>PostContentClassID</td>
                    <td><input name="reply_class" type="text" value="{$reply_class}" /></td>
                </tr>
            </table>
            
            <div class="context-toolbar">
                <div class="block">
                    <div class="break"></div>
                    <p>{'general settings'|i18n( 'extension/xrowforum' )}</p>
                </div>
            </div>
            
            
             <table class="list">
                <thead>
                    <tr>
                        <th width="300">{'Setting'|i18n('extension/xrowforum')}</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tr>
                    <td>{'activate'|i18n('extension/xrowforum')} Rankingsystem</td>
                    <td>
                        <input value="enabled" name="checkbox_ranking" type="checkbox"{if $ranking|eq('enabled')}checked="checked"{/if} />
                    </td>
                </tr>
                <tr>
                    <td>{'activate'|i18n('extension/xrowforum')} WordToImage</td>
                    <td>
                        <input value="enabled" name="checkbox_wti" type="checkbox"{if $wordtoimage|eq('enabled')}checked="checked"{/if} />
                    </td>
                </tr>
				<tr>
                    <td>{'activate'|i18n('extension/xrowforum')} Censoring</td>
                    <td>
                        <input value="enabled" name="checkbox_censoring" type="checkbox"{if $censoring|eq('enabled')}checked="checked"{/if} />
                    </td>
                </tr>
                <tr>
                    <td>HotTopics</td>
                    <td><input name="HotTopicNumber" type="text" value="{$HotTopicNumber}" /> (set value to 0 to deactivate hottopics)</td>
                </tr>
                <tr>
                    <td>PostsPerPage</td>
                    <td><input name="PostsPerPage" type="text" value="{$PostsPerPage}" /></td>
                </tr>
                <tr>
                    <td>TopicsPerPage</td>
                    <td><input name="TopicsPerPage" type="text" value="{$TopicsPerPage}" /></td>
                </tr>
                <tr>
                    <td>PostHistoryLimit</td>
                    <td><input name="PostHistoryLimit" type="text" value="{$PostHistoryLimit}" /></td>
                </tr>
                {*
                <tr>
                    <td>SignatureLength</td>
                    <td><input name="SignatureLength" type="text" value="{$SignatureLength}" /> digits</td>
                </tr>
                *}
                <tr>
                    <td>{'activate'|i18n('extension/xrowforum')} Signature Image</td>
                    <td>
                        <input name="checkbox_si" type="checkbox"{if $SignatureImage|eq('enabled')}checked="checked"{/if} />
                    </td>
                </tr>
                {*
                <tr>
                    <td>SignatureHeight</td>
                    <td><input name="SignatureHeight" type="text" value="{$SignatureHeight}" /> px</td>
                </tr>
                <tr>
                    <td>SignatureWidth</td>
                    <td><input name="SignatureWidth" type="text" value="{$SignatureWidth}" /> px</td>
                </tr>
                <tr>
                    <td>EmbeddedImageHeight</td>
                    <td><input name="EmbeddedImageHeight" type="text" value="{$EmbeddedImageHeight}" /> px</td>
                </tr>
                <tr>
                    <td>EmbeddedImageWidth</td>
                    <td><input name="EmbeddedImageWidth" type="text" value="{$EmbeddedImageWidth}" /> px</td>
                </tr>
                *}
                <tr>
                    <td>StatisticLimit</td>
                    <td><input name="StatisticLimit" type="text" value="{$StatisticLimit}" /> (Top X)</td>
                </tr>
				<tr>
                    <td>KeepFlagDuration</td>
                    <td><input name="KeepFlagDuration" type="text" value="{$KeepFlagDuration}" /> days</td>
                </tr>
            </table>
            
			<div class="context-toolbar">
                <div class="block">
                    <div class="break"></div>
                    <p>{'private messaging settings'|i18n( 'extension/xrowforum' )}</p>
                </div>
            </div>
            
             <table class="list">
                <thead>
                    <tr>
                        <th width="300">{'Setting'|i18n('extension/xrowforum')}</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tr>
                    <td>AllowSendingNotificationMails</td>
                    <td>
                        <input value="enabled" name="checkbox_mailnotification" type="checkbox"{if $AllowSendingNotificationMails|eq('true')}checked="checked"{/if} />
                    </td>
                </tr>
				<tr>
                    <td>PmsPerPage</td>
                    <td><input name="PmsPerPage" type="text" value="{$PmsPerPage}" /></td>
                </tr>
            </table>
			
            <div class="context-toolbar">
				<div class="block">
					<div class="break"></div>
					<p>{'BBCodes'|i18n( 'extension/xrowforum' )}</p>
				</div>
			</div>
        
            <table class="list">
                <thead>
                    <tr>
                        <th>{'name'|i18n('extension/xrowforum')}</th>
                        <th>{'active'|i18n('extension/xrowforum')}</th>
                        <th>{'inactive'|i18n('extension/xrowforum')}</th>
                    </tr>
                </thead>
                {foreach $bbcodelist as $children_key => $children}
                    <tr>
                        <td>{$children_key}</td>
                        <td><input value="enabled" name="radio_{$children_key}" type="radio" {if $children|eq('enabled')}checked="checked"{/if} /></td>
                        <td><input value="disabled" name="radio_{$children_key}" type="radio" {if $children|eq('disabled')}checked="checked"{/if}/></td>
                    </tr>
                {/foreach}
            </table>
            
    {* DESIGN: Content END *}</div></div></div>
    
    <div class="controlbar">
    {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
    
        <div class="block">
            <input class="button" name="save_general_settings" type="submit" value={'save'|i18n('extension/xrowforum')} />
        </div>
    
    {* DESIGN: Control bar END *}</div></div></div></div></div></div>
    </div>

</form>

</div>