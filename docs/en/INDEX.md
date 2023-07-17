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



