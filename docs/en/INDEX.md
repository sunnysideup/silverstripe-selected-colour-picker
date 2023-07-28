## add colour to DataObject

Here is how to add a colour to a DataObject:

```php

    private static $db = [
        'MyColour' => 'Colour',
        'MyColour2' => 'BackgroundColour',
        'MyColour3' => 'FontColour',
        'MyColour4' => 'DifferentDBColourSchema',
    ]

```

### Settings in yml:

```yml
Sunnysideup\SelectedColourPicker\Model\Fields\DBColour:
  # REQUIRED
  colours:
    '#ff2233': 'Brand Colour 1'
    '#88aadd': 'Brand Colour 2'

  # OPTIONAL!
  linked_colours:
    '#ff2233': 
      'font': '#123312',
      'background': '#775544',
      'somethingelse': '#000000',
  # OPTIONAL!
  colour_picker_field_class_name: MyColourSelectionField
```

### Usage in SilverStripe template

```ss

<div style="background-color: $MyColour; color: $MyColour.ReadableColour" style="$MyColour.CssClass">

<br /><% if $MyColour.IsDarkColour %>Is Dark Colour <% end_if %>
<br /><% if $MyColour.IsLightColour %>Is Light Colour <% end_if %>
<br />Related font colour: $MyColour.RelatedColourByName(font) - as set in private static
<br />Related background colour: $MyColour.RelatedColourByName(background) - as set in private static
<br />Related something else colour: $MyColour.RelatedColourByName(somethingelse) - as set in private static
<br />Inverted colour: $MyColour.Inverted
<style>
MyColour.CssVariableDefinition
</style>

```


### customisation

```yml
SilverStripe\Core\Injector\Injector:
  MyColour:
    class: MyName\MyApp\Model\Fields\DifferentDBColourSchema
```

In PHP you can then add different approaches.

```php
class DifferentDBColourSchema extends DBColour
{
    private static $colour_picker_field_class_name = MyFormField::class;

    /**
     * please set.
     *
     * @var bool
     */
    protected const IS_BG_COLOUR = true;
}
```
