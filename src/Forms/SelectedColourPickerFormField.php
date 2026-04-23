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
        if ($this->limitedToOptions && $this->value && ! isset($this->source[$this->value])) {
            $valid->addError(
                'Please selected from suggested options only',
                'validation'
            );

            return false;
        }

        return true;
    }

    #[Override]
    public function Field($properties = [])
    {
        $this->setAttribute('list', $this->ID() . '_List');
        $this->setDescription(
            DBField::create_field(
                'HTMLText',
                SelectedColourPickerFormFieldSwatches::get_swatches_html(
                    $this->name,
                    $this->value,
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
