{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{if and($attribute|get_class|eq('ezinformationcollectionattribute'), $attribute.content.is_valid)}
{makedate( $attribute.content.month, $attribute.content.day, 1990)|datetime( 'custom', '%d %F' )} {$attribute.content.year}
{/if}
