<?php

namespace Sunnysideup\SelectedColourPicker\Forms;

use Override;
use SilverStripe\Forms\Validation\Validator;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\DropdownField;
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
    #[Override]
    public function validate(): ValidationResult
    {
        $result = parent::validate();
        if ($this->limitedToOptions && $this->value && ! isset($this->source[$this->value])) {
            $result->addError(
                'Please selected from suggested options only',
                'validation'
            );

        }

        return $result;
    }

    #[Override]
    public function Field($properties = [])
    {
        /**
         * @deprecated FormField::Value() has been deprecated. It will be replaced by getFormattedValue() and getValue().
         * See: https://docs.silverstripe.org/en/5/changelogs/5.4.0/#deprecated-api
         */
        $this->setDescription(
            DBField::create_field(
                'HTMLText',
                SelectedColourPickerFormFieldSwatches::get_swatches_html(
                    $this->name,
                    $this->getValue(),
                    $this->getListMap($this->source),
                    (bool) $this->isBgColour
                )
            )
        );

        return parent::Field();
    }
}
