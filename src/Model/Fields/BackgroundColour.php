<?php

namespace Sunnysideup\SelectedColourPicker\Model\Fields\DBColour;

use SilverStripe\Forms\FormField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\FieldType\DBVarchar;

use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\FieldType\DBField;
use Sunnysideup\SelectedColourPicker\Forms\SelectedColourPickerFormField;
use Sunnysideup\SelectedColourPicker\Forms\SelectedColourPickerFormFieldDropdown;
use Sunnysideup\SelectedColourPicker\ViewableData\SelectedColourPickerFormFieldSwatches;
use TractorCow\Colorpicker\Color;

class DBColour extends DBColour
{
    /**
     * please set.
     *
     * @var bool
     */
    protected const IS_BG_COLOUR = true;


}
