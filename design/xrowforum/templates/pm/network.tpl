<script type="text/javascript" src={'javascript/autocomplete.js'|ezdesign}></script>

{literal}
    <script type="text/javascript">
            function delete_item(name, userid, username)
            {
                Check = confirm('do your really want to remove ' + username + ' from your ' + name + ' list?');
                if (Check == true)
                {
					var form_name = name + "_form_" + userid;
					document.getElementById(form_name).submit();
                }
				else
				{
					return false;
				}
            }
			$(document).ready(function(){
				var data = {/literal}"{$name_list}"{literal}.split(",");
				$("#recipient_name").autocomplete(data);
			});
	</script>
{/literal}

{def $user = ""
	 $user_state = ""
	 $friend_list_count = count($friend_list)
	 $request_list_count  = count($request_list)
	 $block_list_count = count($block_list)
	 $friend_id = ""
	 $blocks_id = ""
}

<div class="border-box">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc float-break">

<div class="content-view-full">
    <div class="class-folder">
		<div id="pm_network">
			<div class="attribute-header">
				<h1>{'My Network'|i18n('extension/xrowpm')}</h1>
		    </div>
			{if $success}
				<div class="message-feedback">
					<h3>{$success}</h3>
				</div>
			{elseif $errors}
				<div class="message-error">
					<h3>{'Validation Error'|i18n('extension/xrowpm')}</h3>
					<ul>
						{foreach $errors as $error}
							<li>{$error}</li>
						{/foreach}
				   </ul>
				</div>
			{/if}
			<form name="friend_action" action="" method="post">
				<div class="block">
					<table class="list">
						<tr>
							<th>{'Name'|i18n('extension/xrowpm')}</th>
							<th>{'Type'|i18n('extension/xrowpm')}</th>
							<th>{'Action'|i18n('extension/xrowpm')}</th>
						</tr>
						<tr>
							<td><input class="box" id="recipient_name" type="text" name="recipient_name" value="{$recipient_name|wash()}" /></td>
							<td>
								<select name="action_type">
									<option value="0" {if $action_type|eq(0)}selected="selected"{/if}>{'Add as Friend'|i18n('extension/xrowpm')}</option>
									<option value="1" {if $action_type|eq(1)}selected="selected"{/if}>{'Block the User'|i18n('extension/xrowpm')}</option>
								</select>
							</td>
							<td><input type="submit" class="defaultbutton" name="NetworkActionButton" value="{'submit'|i18n('extension/xrowpm')}" /></td>
						</tr>
					</table>
				</div>
			</form>
			{if $request_list_count|gt(0)}
				<div class="attribute-header">
					<h2>{'Friend Requests'|i18n('extension/xrowpm')}({$request_list_count})</h2>
				</div>
				<table class="list">
					<tr>
						<th>{'Name'|i18n('extension/xrowpm')}</th>
						<th>{'Message'|i18n('extension/xrowpm')}</th>
						<th>{'State'|i18n('extension/xrowpm')}</th>
						<th>{'Action'|i18n('extension/xrowpm')}</th>
					<tr>
					{foreach $request_list as $request}
						<tr>
							{set $user = fetch( 'content', 'object', hash( 'object_id', $request.user1 ) )
								 $user_state = fetch( 'user', 'is_logged_in', hash( 'user_id', $request.user1 ) )}
							<td><a href={$user.main_node.url_alias|ezurl()}>{$user.name|wash()}</a></td>
							<td><a href={concat("/pm/create/", $request.user1)|ezurl()}>{'send PM'|i18n('extension/xrowpm')}</a></td>
							<td>
								{if $user_state}
									<p style="color:green">{'online'|i18n('extension/xrowpm')}</p>
								{else}
									<p style="color:red">{'offline'|i18n('extension/xrowpm')}</p>
								{/if}
							</td>
							<td>
								<form name="friend_handling" action="" method="post">
									<input type="hidden" name="requester" value="{$request.user1}" />
									<input type="submit" class="defaultbutton" name="ConfirmFriendButton" value="{'accept'|i18n('extension/xrowpm')}" />
									<input type="submit" class="defaultbutton" name="RejectFriendButton" value="{'decline'|i18n('extension/xrowpm')}" />
								</form>
							</td>
						</tr>
					{/foreach}
				</table>
			{/if}
			
			<div class="attribute-header">
				<h2>{'My Friends'|i18n('extension/xrowpm')}({$friend_list_count})</h2>
			</div>
			{if $friend_list_count|gt(0)}
				<table class="list">
					<tr>
						<th>{'Name'|i18n('extension/xrowpm')}</th>
						<th>{'Message'|i18n('extension/xrowpm')}</th>
						<th>{'State'|i18n('extension/xrowpm')}</th>
						<th>{'Action'|i18n('extension/xrowpm')}</th>
					</tr>
					{foreach $friend_list as $friend}
						<tr>
							{if $user_id|eq($friend.user1)}
								{set $friend_id = $friend.user2}
							{elseif $user_id|eq($friend.user2)}
								{set $friend_id = $friend.user1}
							{/if}
							{set $user_state = fetch( 'user', 'is_logged_in', hash( 'user_id', $friend_id ) )
								 $user = fetch( 'content', 'object', hash( 'object_id', $friend_id ) )}
							<td><a href={$user.main_node.url_alias|ezurl()}>{$user.name|wash()}</a></td>
							<td><a href={concat("/pm/create/", $friend_id)|ezurl()}>{'send PM'|i18n('extension/xrowpm')}</a></td>
							<td>
								{if $user_state}
									<p style="color:green">{'online'|i18n('extension/xrowpm')}</p>
								{else}
									<p style="color:red">{'offline'|i18n('extension/xrowpm')}</p>
								{/if}
							</td>
							<td>
							<form id="{concat("friend_form_", $friend_id)}" name="friend_form" action="" method="post">
								<input type="hidden" name="friend" value="{$friend_id}" />
								<input readonly="readonly" class="defaultbutton" onclick="delete_item('friend', {$friend_id}, '{$user.name|wash()}');" name="RemoveFriendButton" value="{'break friendship'|i18n('extension/xrowpm')}" />
								{* link alternative:
								<a href="#"><img src={'trash-icon-16x16.gif'|ezimage()} alt="{'remove friend'|i18n('extension/xrowpm')}" title="{'remove friend'|i18n('extension/xrowpm')}" border="0" onclick="delete_item('friend', {$friend_id}, '{$user.name|wash()}');" /></a>
								not working at the moment because the module works with "isCurrentAction"
								*}
							</form>
							</td>
						</tr>
					{/foreach}
				</table>
			{else}
				<div class="message-error">
					<p>{'no friends found'|i18n('extension/xrowpm')}</p>
				</div>
			{/if}
			
			{if $block_list_count|gt(0)}
				<div class="attribute-header">
					<h2>{'Blocked User'|i18n('extension/xrowpm')}({$block_list_count})</h2>
				</div>
				<table class="list">
					<tr>
						<th>{'Name'|i18n('extension/xrowpm')}</th>
						<th>{'Action'|i18n('extension/xrowpm')}</th>
					</tr>
					{foreach $block_list as $blocks}
						<tr>
							{if $user_id|eq($blocks.user1)}
								{set $blocks_id = $blocks.user2}
							{elseif $user_id|eq($blocks.user2)}
								{set $blocks_id = $blocks.user1}
							{/if}
							{set $user = fetch( 'content', 'object', hash( 'object_id', $blocks_id ) )}
							<td><a href={$user.main_node.url_alias|ezurl()}>{$user.name|wash()}</a></td>
							<td>
								{if $user_id|eq($blocks.user1)}
									<form id="{concat("block_form_", $blocks_id)}" name="block_form" action="" method="post">
										<input type="hidden" name="block_to" value="{$blocks_id}" />
										<input readonly="readonly" class="defaultbutton" onclick="delete_item('block', {$blocks_id}, '{$user.name|wash()}');" name="RemoveBlockButton" value="{'remove block'|i18n('extension/xrowpm')}" />
										{* link alternative:
										<a href="#"><img src={'trash-icon-16x16.gif'|ezimage()} alt="remove block" title="remove block" border="0" onclick="delete_item('block', {$blocks_id}, '{$user.name|wash()}');" /></a>
										not working at the moment because the module works with "isCurrentAction"
										*}
									</form>
								{/if}
							</td>
						</tr>
					{/foreach}
				</table>
			{/if}
			
		</div>
	</div>
</div>

</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>