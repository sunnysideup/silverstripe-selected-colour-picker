<?php

namespace Sunnysideup\SelectedColourPicker\Forms;

use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\ArrayData;
use Sunnysideup\SelectedColourPicker\ViewableData\SelectedColourPickerFormFieldSwatches;
use SilverStripe\Forms\Validator;
use SilverStripe\Forms\DropdownField;

class SelectedColourPickerFormFieldDropdown extends DropdownField
{

    public function setLimitedToOptions(bool $bool)
    {
        $this->limitedToOptions = $bool;

        return $this;
    }

    public function setIsBgColour(bool $bool)
    {
        $this->isBgColour = $bool;

        return $this;
    }

    /**
     * Validate this field.
     *
     * @param Validator $validator
     *
     * @return bool
     */
    public function validate($validator)
    {
        if ($this->limitedToOptions && $this->value && ! isset($this->colourOptions[$this->value])) {
            $validator->validationError(
                $this->name,
                'Please selected from suggested options only',
                'validation'
            );

            return false;
        }

        return true;
    }

    public function Field($properties = [])
    {
        $this->setDescription(
            DBField::create_field(
                'HTMLText',
                SelectedColourPickerFormFieldSwatches::get_swatches_html(
                    $this->name,
                    $this->value,
                    $this->getListMap($this->source),
                    $this->isBgColour
                )
            )
        );

        return parent::Field();
    }

}

// <span
//     class="color-cms"
//     style="display: inline-block; vertical-align: bottom; width: 20px; height: 20px; border-radius: 10px; background-color: '.$this->value.'"
// >
// </span>
