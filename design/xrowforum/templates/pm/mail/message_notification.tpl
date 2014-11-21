{set-block scope=root variable=subject}{'New message from "%sender" in your inbox'|i18n('extension/xrowpm', '', hash( '%sender', $sender ))}{/set-block}
{'Hello'|i18n('extension/xrowpm')} {$recipient_name},
{'you have a new message: "%pm_subject"'|i18n('extension/xrowpm', '', hash( '%pm_subject', $pm_subject ))}

{'Click the following URL to access the PM'|i18n('extension/xrowpm')}:
http://{$hostname}{concat( 'pm/view/', $messageID )|ezurl(no)}