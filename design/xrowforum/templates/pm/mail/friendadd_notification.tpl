{set-block scope=root variable=subject}{'New Friend on %hostname'|i18n('extension/xrowpm', '', hash( '%hostname', $hostname ))}{/set-block}
{'Hello'|i18n('extension/xrowpm')} {$recipient_name},

{'"%sender" wants to be your friend.'|i18n('extension/xrowpm', '', hash( '%sender', $sender ))}
{'You may want to decide about that.'|i18n('extension/xrowpm')}

{'Click the following URL to access your Network Center'|i18n('extension/xrowpm')}:
http://{$hostname}{concat( 'pm/network/')|ezurl(no)}