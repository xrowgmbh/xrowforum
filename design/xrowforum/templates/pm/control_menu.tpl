{def $new_msg = pm_message_check("inbox", "0")
	 $requests = count(pm_relation_fetch($current_user.contentobject_id, '0', 'all'))
	 $friends_on = count(pm_relation_fetch($current_user.contentobject_id, '1', 'online'))
}
<div class="pm_menu">
	<h2>{'Private Messaging'|i18n('extension/xrowpm')}</h2>
	<ul class="menu-list">
		<li>
			<div class="second_level_menu">
				<a href={"/pm/inbox"|ezurl()}>
					{'Inbox'|i18n('extension/xrowpm')}
					{if $new_msg|gt(0)}
						(<strong>{$new_msg}</strong>/{pm_message_check("inbox", 1)})
					{else}
						({pm_message_check("inbox", 1)})
					{/if}
				</a>
			</div>
		</li>
		<li>
			<div class="second_level_menu">
				<a href={"/pm/outbox"|ezurl()}>
					{'Sentbox'|i18n('extension/xrowpm')}
					({pm_message_check("outbox", "")})
				</a>
			</div>
		</li>
		<li>
			<div class="second_level_menu">
				<a href={"pm/network"|ezurl()}>{'my Network'|i18n('extension/xrowpm')}
					{if and($requests|gt(1), $friends_on|gt(0))}
						<strong>{$requests} {'requests'|i18n('extension/xrowpm')} / {$friends_on} {'online'|i18n('extension/xrowpm')} </strong>
					{elseif and($requests|eq(1), $friends_on|gt(0))}
						<strong>{$requests} {'request'|i18n('extension/xrowpm')} / {$friends_on} {'online'|i18n('extension/xrowpm')} </strong>
					{elseif $requests|eq(1)}
						(<strong>{$requests} {'request'|i18n('extension/xrowpm')} </strong>)
					{elseif $requests|gt(1)}
						(<strong>{$requests} {'requests'|i18n('extension/xrowpm')} </strong>)
					{elseif $friends_on|gt(0)}
						<strong>{$friends_on} {'online'|i18n('extension/xrowpm')} </strong>
					{/if}
				</a>
			</div>
		</li>
		<li><div class="second_level_menu"><a href={"/pm/create"|ezurl()}>{'New Message'|i18n('extension/xrowpm')}</a></div></li>
	</ul>
</div>