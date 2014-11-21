{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{if is_set($attribute_base)|not}{def $attribute_base='ContentObjectAttribute'}{/if}
{def $value=cond( is_set( $#collection_attributes[$attribute.id] ), $#collection_attributes[$attribute.id].content, $attribute.content )
    $date=currentdate()
	$byear=$date|datetime(custom,"%Y")}
<div class="block">
    <div class="element">
        <label>{"Year"|i18n("design/standard/content/datatype")}</label><div class="labelbreak"></div>
        <select name="{$attribute_base}_birthday_year_{$attribute.id}" title="{"Please enter the year"|i18n("design/standard/content/datatype")}">
            <option value=""></option>
        {for sub($byear,100) to $byear as $i}
             <option value="{$i}"{if eq($value.content.year,$i)} selected="selected"{/if}>{$i}</option>
        {/for}
        </select>
    </div>

    <div class="element">
        <label>{"Month"|i18n("design/standard/content/datatype")}</label><div class="labelbreak"></div>
        <select name="{$attribute_base}_birthday_month_{$attribute.id}" title="{"Please enter the month"|i18n("design/standard/content/datatype")}">
            <option value=""></option>
        {for 1 to 12 as $i}
            <option value="{$i}"{if eq($value.content.month,$i)} selected="selected"{/if} >{$i}</option>
        {/for}
        </select>
    </div>

    <div class="element">
        <label>{"Day"|i18n("design/standard/content/datatype")}</label><div class="labelbreak"></div>
        <option value=""></option>
        <select name="{$attribute_base}_birthday_day_{$attribute.id}" title="{"Please enter the day"|i18n("design/standard/content/datatype")}">
            <option value=""></option>
        {for 1 to 31 as $i}
            <option value="{$i}"{if eq($value.content.day,$i)} selected="selected"{/if}>{$i}</option>
        {/for}
        </select>
    </div>
</div>
<div class="break"></div>