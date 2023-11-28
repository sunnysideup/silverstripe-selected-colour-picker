<?php

namespace Sunnysideup\SelectedColourPicker\Forms;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\Validator;
use SilverStripe\ORM\FieldType\DBField;
use Sunnysideup\SelectedColourPicker\ViewableData\SelectedColourPickerFormFieldSwatches;

class SelectedColourPickerFormFieldDropdown extends DropdownField
{

    protected $limitedToOptions = true;

    protected $isBgColour = true;

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
        if ($this->limitedToOptions && $this->value && ! isset($this->source[$this->value])) {
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
                    (bool) $this->isBgColour
                )
            )
        );

        return parent::Field();
    }
}
