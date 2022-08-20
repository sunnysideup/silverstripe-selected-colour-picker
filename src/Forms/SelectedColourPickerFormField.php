<?php

namespace Sunnysideup\SelectedColourPicker\Forms;

use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\ArrayData;
use Sunnysideup\SelectedColourPicker\ViewableData\SelectedColourPickerFormFieldSwatches;
use SilverStripe\Forms\Validator;

class SelectedColourPickerFormField extends TextField
{
    protected $inputType = 'color';

    protected $colourOptions = [];

    protected $limitedToOptions = true;

    protected $isBgColour = true;

    public function setColourOptions(array $array)
    {
        $this->colourOptions = $array;

        return $this;
    }

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
        $this->setAttribute('list', $this->ID() . '_List');
        $this->setDescription(
            DBField::create_field(
                'HTMLText',
                SelectedColourPickerFormFieldSwatches::get_swatches_html(
                    $this->name,
                    $this->value,
                    $this->colourOptions,
                    $this->isBgColour
                )
            )
        );

        return parent::Field();
    }

    public function ColourOptionsAsArrayList(): ArrayList
    {
        $al = new ArrayList();
        foreach ($this->colourOptions as $colour => $label) {
            $al->push(
                new ArrayData(
                    [
                        'Colour' => $colour,
                        'Label' => $label,
                    ]
                )
            );
        }

        return $al;
    }

    public function Type()
    {
        return 'selected-colour-picker';
    }
}

// <span
//     class="color-cms"
//     style="display: inline-block; vertical-align: bottom; width: 20px; height: 20px; border-radius: 10px; background-color: '.$this->value.'"
// >
// </span>
