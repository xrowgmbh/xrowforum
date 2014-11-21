{set-block scope=root variable=subject}{'Friendship with "%friend" accepted!'|i18n('extension/xrowpm', '', hash( '%friend', $friend ))}{/set-block}
{'Congratulations %recipient_name, your friendship with "%friend" has been accepted.'|i18n('extension/xrowpm', '', hash( '%friend', $friend, '%recipient_name', $recipient_name ))}

http://{$hostname}