<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-full">
    <div class="class-folder">
		<div id="pm_full">
		
		<div class="attribute-header">
			<h2>{'Private Message'|i18n('extension/xrowpm')}</h2>
	    </div>
		
		{if $message}
			{def $sender = fetch( 'content', 'object', hash( 'object_id', $message.0.sender ) )
				 $recipient = fetch( 'content', 'object', hash( 'object_id', $message.0.recipient ) )
				 $sender_on=fetch( 'user', 'is_logged_in', hash( 'user_id', $message.0.sender ) )
 				 $recipient_on=fetch( 'user', 'is_logged_in', hash( 'user_id', $message.0.recipient ) )}
			<table>
				<tr>
					<td><label>{'from'|i18n('extension/xrowpm')}:</label></td>
					<td>
						<a href={$sender.main_node.url_alias|ezurl()}>{$sender.name|wash()}</a>
						{if $sender_on}
							(online)
						{else}
							(offline)
						{/if}
					</td>
				</tr>
				<tr>
					<td><label>{'to'|i18n('extension/xrowpm')}:</label></td>
					<td>
						<a href={$recipient.main_node.url_alias|ezurl()}>{$recipient.name|wash()}</a>
						{if $recipient_on}
							(online)
						{else}
							(offline)
						{/if}
					</td>
				</tr>
				<tr>
					<td><label>{'sent date'|i18n('extension/xrowpm')}:</label></td>
					<td>{$message.0.send_date|l10n( 'shortdatetime' )}</td>
				</tr>
				<tr>
					<td><label>{'subject'|i18n('extension/xrowpm')}:</label></td>
					<td>{$message.0.pm_subject|wash()}</td>
				</tr>
				<tr>
					<td><label>{'text'|i18n('extension/xrowpm')}:</label></td>
					<td>{operator_merge(content, $message.0.pm_content)}</td>
				</tr>
			</table>
			<div class="buttonblock">
				{if $message.0.owner_id|ne($sender.id)}
					{if not(pm_is_inRelation( $message.0.recipient, $message.0.sender, 2))}
						<form action={concat('/pm/create/', $message.0.sender, '/', $message.0.msg_id )|ezurl()}" method="post" >
							<input class="defaultbutton" type="submit" name="ReplyButton" value="{'reply'|i18n('extension/xrowpm')}" />
						</form>
					{/if}
					<form action={concat("/pm/modify/remove/", $message.0.msg_id, "/inbox")|ezurl} method="post">
						<input type="hidden" name="DeleteIDArray[]" value="{$message.0.msg_id}" />
						<input class="defaultbutton" type="submit" name="DeleteButton" value="{'delete message'|i18n('extension/xrowpm')}" />
					</form>
					{if and(
							not(pm_is_inRelation( $message.0.recipient, $message.0.sender, 1)),
							not(pm_is_inRelation( $message.0.recipient, $message.0.sender, 0)),
							not(pm_is_inRelation( $message.0.recipient, $message.0.sender, 2))
						    )}
						<form action={"pm/network"|ezurl()} method="post">
							<input class="box" type="hidden" name="recipient_name" value="{$sender.name|wash()}" />
							<input class="box" type="hidden" name="action_type" value="0" />						
							<input class="defaultbutton" type="submit" name="NetworkActionButton" value="{'friendship request'|i18n('extension/xrowpm')}" />
						</form>
					{/if}
					{if not(pm_is_inRelation( $message.0.recipient, $message.0.sender, 2))}
						<form action={"pm/network"|ezurl()} method="post">
							<input class="box" type="hidden" name="recipient_name" value="{$sender.name|wash()}" />
							<input class="box" type="hidden" name="action_type" value="1" />	
							<input class="defaultbutton" type="submit" name="NetworkActionButton" value="{'block user'|i18n('extension/xrowpm')}" />
						</form>
					{/if}
					{* mark the PM as read when Inbox + not read yet = true*}
					{if $message.0.read_state|eq(0)}
						{pm_mark_as_read($message.0.msg_id, $message.0.sender, $message.0.recipient, $message.0.send_date )}
					{/if}
					| <a href={"/pm/inbox"|ezurl()}>{'back'|i18n('extension/xrowpm')}</a>
				{else}
					<form name="pm_list" action={concat("/pm/modify/remove/", $message.0.msg_id, "/outbox")|ezurl} method="post">
						<input type="hidden" name="DeleteIDArray[]" value="{$message.0.msg_id}" />
						<input class="defaultbutton" type="submit" name="DeleteButton" value="{'delete message'|i18n('extension/xrowpm')}" />
					</form>
					| <a href={"/pm/outbox"|ezurl()}>{'back'|i18n('extension/xrowpm')}</a>
				{/if}
				
			</div>
		{else}
			<div class="message-error">
				<h2>Error</h2>
				<ul>
					<li>{'You have entered a wrong MessageID.'|i18n('extension/xrowpm')}</li>
			   </ul>
			</div>
		{/if}
	</div>
	</div>
</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>