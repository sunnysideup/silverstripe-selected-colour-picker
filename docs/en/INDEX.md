## add colour to DataObject

Here is how to add a colour to a DataObject:

```php

    private static $db = [
        'MyColour' => 'Colour',
        'MyColour2' => 'DifferentDBColourSchema',
    ]

```

### Settings in yml:

```yml
Sunnysideup\SelectedColourPicker\Model\Fields\DBColour:
  colours:
    '#ff2233': 'Brand Colour 1'
    '#88aadd': 'Brand Colour 2'
  linked_colours:
    '#ff2233': 
      'font': '#123312',
      'background': '#775544',
      'somethingelse': '#000000',
```

### Usage in SilverStripe template

```ss

<div style="background-color: $MyColour; color: $MyColour.ReadableColor" style="$MyColour.CssClass">

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

```php
class DifferentDBColourSchema extends DBColour
{
    private static $colour_picker_field_class_name = MyFormField::class;

    protected const DEFAULT_COLOURS = [
        '#FF0000' => 'Red',
        '#0000FF' => 'Blue',
        '#00FF00' => 'Green',
    ];


    /**
     * please set.
     *
     * @var string`
     */
    protected const CSS_CLASS_PREFIX = 'db-colour';

    /**
     * please set.
     *
     * @var bool
     */
    protected const IS_LIMITED_TO_OPTIONS = true;

    /**
     * please set.
     *
     * @var bool
     */
    protected const IS_BG_COLOUR = true;
}
```
