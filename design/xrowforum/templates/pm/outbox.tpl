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
				<h1>{'Outbox'|i18n('extension/xrowpm')}</h1>
		    </div>
			<div class="block">
				<a href={"/pm/create"|ezurl()}>{'new Message'|i18n('extension/xrowpm')}</a> {if $search_on|eq('true')} / <a href={"/pm/outbox"|ezurl()}>{'see all'|i18n('extension/xrowpm')}</a>{/if}
			</div>
			{if $total_msg|gt(0)}
				{def $recipient = ""}
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
							<th><a href={concat("pm/outbox/recipient/", $direction)|ezurl()}>{'to'|i18n('extension/xrowpm')}</a></th>
							<th><a href={concat("pm/outbox/pm_subject/", $direction)|ezurl()}>{'subject'|i18n('extension/xrowpm')}</a></th>
							<th><a href={concat("pm/outbox/send_date/", $direction)|ezurl()}>{'send date'|i18n('extension/xrowpm')}</a></th>
							<th><a href={concat("pm/outbox/read_state/", $direction)|ezurl()}>{'status'|i18n('extension/xrowpm')}</a></th>
						</tr>
						{foreach $sent_mails as $mail}
							<tr>
								{set $recipient = fetch( 'content', 'object', hash( 'object_id', $mail.recipient ) )}
								<td><input type="checkbox" name="DeleteIDArray[]" value="{$mail.msg_id}" title="{'Select the checkbox to remove the message'|i18n( 'extension/xrowpm' )}" /></td>
								<td><a href={$recipient.main_node.url_alias|ezurl()}>{$recipient.name|wash()}</a></td>
								<td><a href={concat("/pm/view/", $mail.msg_id)|ezurl()}>{$mail.pm_subject|wash()}</a></td>
								<td>{$mail.send_date|l10n( 'shortdatetime' )}</td>
								<td>
									{if $mail.read_state|eq(1)}
										{'read'|i18n("extension/xrowpm")}
									{elseif $mail.read_state|eq(0)}
										{'sent'|i18n("extension/xrowpm")}
									{else}
										{* should not happen *}
										{'unknown'|i18n("extension/xrowpm")}
									{/if}
								</td>
							</tr>
						{/foreach}
					</table>
					<div class="block">
						<input class="defaultbutton" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'extension/xrowpm' )}" title="{'Remove selected messages.'|i18n( 'extension/xrowpm' )}" />
						<input type="hidden" name="where" value="outbox" />
					</div>
				</form>
		
				{include name=navigator
						 uri='design:navigator/google.tpl'
						 page_uri=concat("/pm/outbox")
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