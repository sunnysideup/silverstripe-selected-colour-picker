<?php

namespace Sunnysideup\SelectedColourPicker\Forms;

use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;

use SilverStripe\ORM\ArrayList;

use SilverStripe\ORM\FieldType\DBField;

use SilverStripe\View\ArrayData;


class SelectedColourPickerFormField extends TextField
{

    protected $inputType = 'color';

    protected $colourOptions = [];

    protected $limitedToOptions = true;

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
    /**
     * Validate this field
     *
     * @param Validator $validator
     * @return bool
     */
    public function validate($validator)
    {
        if ($this->limitedToOptions && $this->value && ! isset($this->colourOptions[$this->value])) {
            $validator->validationError(
                $this->name,
                'Please selected from suggested options only',
                "validation"
            );
            return false;
        }
        return true;
    }

    public function Field($properties = [])
    {
        $this->setAttribute('list', $this->ID().'_List');
        if($this->value) {
            $description = '
                <span
                    class="color-cms"
                    style="display: inline-block; vertical-align: bottom; width: 20px; height: 20px; border-radius: 10px; background-color: '.$this->value.'"
                >
                </span>
                <span> / '.($this->colourOptions[$this->value] ?? $this->value).'</span>';
                $descriptionObject = DBField::create_field('HTMLText', $description);
            $this->setDescription($descriptionObject);
        }
        return parent::Field();
    }

    public function ColourOptionsAsArrayList() : ArrayList
    {
        $al = new ArrayList();
        foreach($this->colourOptions as $colour => $label) {
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
