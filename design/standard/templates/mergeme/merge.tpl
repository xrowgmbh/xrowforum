{def $result=$content}
{foreach $types as $type}
    {switch match=$type}
        {case match='bbcode2xhtml'}
            {set $result=$result|bbcode2xhtml()}
        {/case}
        {case match='simpletags'}
            {set $result=$result|simpletags()}
        {/case}
        {case match='wordtoimage'}
            {set $result=$result|wordtoimage()}
        {/case}
        {case match='ezurl'}
            {set $result=$result|ezurl()}
        {/case}
        {case match='autolink'}
            {set $result=$result|autolink()}
        {/case}
        {case match='nl2br'}
            {set $result=$result|nl2br()}
        {/case}
		{case match='censoring'}
            {set $result=$result|censoring(xhtml)}
        {/case}
        {case match='wash'}
            {set $result=$result|wash(xhtml)}
        {/case}
    {/switch}
{/foreach}
{$result}