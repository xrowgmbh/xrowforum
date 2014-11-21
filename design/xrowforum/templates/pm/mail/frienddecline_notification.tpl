{set-block scope=root variable=subject}{'Friendship with "%friend" rejected!'|i18n('extension/xrowpm', '', hash( '%friend', $friend ))}{/set-block}
{'We are sorry to let you know that your friendship request to "%friend" has been declined.'|i18n('extension/xrowpm', '', hash( '%friend', $friend ))}

http://{$hostname}