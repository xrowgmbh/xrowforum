{def $mails_allowed = ezini('PrivateMessaging','AllowSendingNotificationMails','xrowforum.ini')}

{if $direction}
	{if $direction|eq("asc")}
		{set $direction = "desc"}
	{elseif $direction|eq("desc")}
		{set $direction = "asc"}
	{/if}
{else}
	{def $direction = "desc"}
{/if}

{literal}
<script language="JavaScript" type="text/javascript">
<!--
	function check_all( formname, checkboxname )
	{
		with( formname )
		{
			for( var i=0; i<elements.length; i++ )
			{
				if( elements[i].type == 'checkbox' && elements[i].name == checkboxname && elements[i].disabled == "" )
				{
					if( elements[i].checked == true )
					{
						elements[i].checked = false;
					}
					else
					{
						elements[i].checked = true;
					}
				}
			}
		}
	}
	function check_change()
	{
		document.getElementById("switch_pref").submit();
	}

	
//-->
</script>
{/literal}	

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-full">
    <div class="class-folder">
		<div id="pm_outbox">
			<div class="attribute-header">
				<h1>{'Inbox'|i18n('extension/xrowpm')}</h1>
		    </div>
	    	{if $mails_allowed|eq("true")}
				<div class="block">
					<form id="switch_pref" name="switch_pref" action="" method="post">
						<label>{"Don't send notification"|i18n("extension/xrowpm")}</label>
						<input class="inline" name="switch_pref" type="hidden" size="30" value="{$pref}" />
						<input type="checkbox" value="{$pref}" {if $pref|eq(0)}checked="checked"{/if} onchange="check_change();"/>
					</form>
				</div>
			{/if}
			<div class="block">
				<a href={"/pm/create"|ezurl()}>{'New Message'|i18n('extension/xrowpm')}</a> {if $search_on|eq('true')} / <a href={"/pm/inbox"|ezurl()}>{'see all'|i18n('extension/xrowpm')}</a>{/if}
			</div>
			{if $total_msg|gt(0)}
				{def $sender = ""}
				<div class="block">
					<label>Suche</label>
					<form name="pm_search" action="" method="post">
						<input class="box" type="text" name="pm_search" value="" />
						<input class="defaultbutton" type="submit" name="SearchButton" value="{'Search'|i18n('extension/xrowpm')}" />
					</form>
				</div>
				
				<form name="pm_list" action={"/pm/modify/remove"|ezurl} method="post" >
					<table class="list">
						<tr>
							<th><img src={"toggle-button-16x16.gif"|ezimage()} alt="{'select all'|i18n( 'extension/xrowpm')}" onclick="check_all( document.pm_list, 'DeleteIDArray[]' ); return false;" /></th>
							<th><a href={concat("pm/inbox/sender/", $direction)|ezurl()}>{'from'|i18n('extension/xrowpm')}</a></th>
							<th><a href={concat("pm/inbox/pm_subject/", $direction)|ezurl()}>{'subject'|i18n('extension/xrowpm')}</a></th>
							<th><a href={concat("pm/inbox/send_date/", $direction)|ezurl()}>{'date'|i18n('extension/xrowpm')}</a></th>
							<th>&nbsp;</th>
						</tr>
						{foreach $sent_mails as $mail}
							<tr>
								{set $sender = fetch( 'content', 'object', hash( 'object_id', $mail.sender ) )}
								<td><input type="checkbox" name="DeleteIDArray[]" value="{$mail.msg_id}" title="{'Select the checkbox to remove the message'|i18n( 'extension/xrowpm' )}" /></td>
								{if $mail.read_state|eq(0)}
									<td><strong><a href={$sender.main_node.url_alias|ezurl()}>{$sender.name|wash()}</a></strong></td>
									<td><strong><a href={concat("/pm/view/", $mail.msg_id)|ezurl()}>{$mail.pm_subject|wash()}</a></strong></td>
									<td><strong>{$mail.send_date|l10n( 'shortdatetime' )}</strong></td>
								{else}
									<td><a href={$sender.main_node.url_alias|ezurl()}>{$sender.name|wash()}</a></td>
									<td><a href={concat("/pm/view/", $mail.msg_id)|ezurl()}>{$mail.pm_subject|wash()}</a></td>
									<td>{$mail.send_date|l10n( 'shortdatetime' )}</td>
								{/if}
								<td><a href={concat('/pm/create/', $sender.id, '/', $mail.msg_id )|ezurl()}>{'reply'|i18n('extension/xrowpm')}</a></td>
							</tr>
						{/foreach}
					</table>
					<div class="block">
						<input class="defaultbutton" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'extension/xrowpm' )}" title="{'Remove selected messages.'|i18n( 'extension/xrowpm' )}" />
						<input type="hidden" name="where" value="{'inbox'|i18n('extension/xrowpm')}" />
					</div>
				</form>
		
				{include name=navigator
						 uri='design:navigator/google.tpl'
						 page_uri=concat("/pm/inbox")
						 item_count=$total_msg
						 view_parameters=$view_parameters
						 item_limit=$page_limit}
			{else}
				<div class="message-error">
					<p>{'no messages found'|i18n('extension/xrowpm')}</p>
				</div>
			{/if}
		</div>
	</div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>