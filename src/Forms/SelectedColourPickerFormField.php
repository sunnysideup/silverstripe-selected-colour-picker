<?php

namespace Sunnysideup\SelectedColourPicker\Forms;

use Override;
use SilverStripe\Forms\Validation\Validator;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Model\ArrayData;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBField;
use Sunnysideup\SelectedColourPicker\ViewableData\SelectedColourPickerFormFieldSwatches;

class SelectedColourPickerFormField extends TextField
{
    protected $inputType = 'color';

    protected $source = [];

    protected $limitedToOptions = true;

    protected $isBgColour = true;

    public function setOptions(array $array): static
    {
        $this->source = $array;

        return $this;
    }

    public function setLimitedToOptions(bool $bool): static
    {
        $this->limitedToOptions = $bool;

        return $this;
    }

    public function setIsBgColour(bool $bool): static
    {
        $this->isBgColour = $bool;

        return $this;
    }

    /**
     * Validate this field.
     *
     * @param Validator $validator
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
        $this->setAttribute('list', $this->ID() . '_List');
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
                    $this->source,
                    $this->isBgColour
                )
            )
        );

        return parent::Field();
    }

    public function ColourOptionsAsArrayList(): ArrayList
    {
        $al = ArrayList::create();
        foreach ($this->source as $colour => $label) {
            $al->push(
                ArrayData::create([
                    'Colour' => $colour,
                    'Label' => $label,
                ])
            );
        }

        return $al;
    }

    #[Override]
    public function Type()
    {
        return 'selected-colour-picker';
    }
}
