<?php /* #?ini charset="utf8"?

[ClassIDs]
#contentclass id of the forum_topic class
ForumTopic=38
#contentclass id of the forum_reply class
ForumReply=39
#contentclass id of the user class
User=4

[IDs]
#the objectID of your forumindex object (required for statistic in admin interface)
ForumIndexPageNodeID=
#the objectID of your moderator usergroup
ModeratorGroupObjectID=

[GeneralSettings]
#set either to enabled or disabled to de or activate the ranking system
Rankings=enabled
#disabled deactivates the wordtoimage.ini
WordToImage=enabled
#set either to enabled or disabled to de or activate the censoring
Censoring=enabled
#allows images in the signature(either disabled or enabled)
SignatureImage=enable
#amount of maximum digets for the signature
#SignatureLength=500
#the amount of topics per page(displayed in the forum fullview)
TopicsPerPage=20
#the amount of posts per page(displayed in the forum topic fullview)
PostsPerPage=20
#the limit of displayed posts when adding a new post
PostHistoryLimit=20
#the amount of posts to make a topic a "hot topic"
HotTopicNumber=50
#the amount of maximum fetched entries for the statistic area
StatisticLimit=5
#the length the flag notifications will be stored in database (time by clean_flag.php cronjob) value is calculated in days
KeepFlagDuration=30

[BB-Codes]
#controls the javascript editor
#choose either disabled or enabled
BBCodeList[]
BBCodeList[big]=enabled
BBCodeList[italic]=enabled
BBCodeList[underline]=enabled
BBCodeList[quote]=enabled
BBCodeList[code]=enabled
BBCodeList[list]=enabled
BBCodeList[strike]=enabled
BBCodeList[img]=enabled
BBCodeList[url]=enabled
BBCodeList[fontcolor]=enabled
BBCodeList[fontsize]=enabled
BBCodeList[smilies]=enabled
#BBCodeList[youtube]=enabled

[MaxImageSizes]
#all sizes are in px
#signature max image size
#SignatureHeight=200
#SignatureWidth=400
#bbcode embedded pictures max sizes
#EmbeddedImageHeight=1000
#EmbeddedImageWidth=400

[MostUserOn]
Amount=0
Date=

[PrivateMessaging]
#set it either to true or false
AllowSendingNotificationMails=true
PmsPerPage=10
SelectableUserPathString[]
SelectableUserPathString[]=/1/5/12

[Censoring]
CensoringList[]
CensoringList[]=fuck
CensoringList[]=shit

*/ ?>