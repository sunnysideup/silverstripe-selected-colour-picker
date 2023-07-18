## add colour to DataObject

Here is how to add a colour to a DataObject:

```php

    private static $db = [
        'MyColour' => 'Colour',
    ]

```
### Usage in SilverStripe template

```ss

<div style="background-color: $MyColour; color: $MyColour.ReadableColor" style="$MyColour.CssClass">

```


### customisation

```php
class MyDBColour extends DBColour
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
