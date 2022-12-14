<?php

namespace Sunnysideup\SelectedColourPicker\Model\Fields;

use SilverStripe\Forms\FormField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\FieldType\DBVarchar;

use SilverStripe\Core\Config\Config;
use Sunnysideup\SelectedColourPicker\Forms\SelectedColourPickerFormField;
use Sunnysideup\SelectedColourPicker\Forms\SelectedColourPickerFormFieldDropdown;
use Sunnysideup\SelectedColourPicker\ViewableData\SelectedColourPickerFormFieldSwatches;
use TractorCow\Colorpicker\Color;

class DBColour extends DBVarchar
{

    private static $colour_picker_field_class_name = SelectedColourPickerFormFieldDropdown::class;

    /**
     * please set
     * must be defined as #AABB99 (hex codes).
     *
     * @var array
     */
    protected const COLOURS = [];

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

    private static $casting = [
        'CssClass' => 'Varchar',
    ];

    public function __construct($name = null, $size = 9)
    {
        parent::__construct($name, $size);
    }

    public function CssClass(?bool $isTransparent = false): string
    {
        return $this->getCssClass();
    }

    public function getCssClass(?bool $isTransparent = false): string
    {
        if($isTransparent) {
            $name = 'transparent';
        } else {
            $name = static::COLOURS[$this->value] ?? 'colour-error';
        }

        return $this->classCleanup($name);
    }

    public function CssClassAlternative(?bool $isTransparent = false): string
    {
        return $this->getCssClassAlternative();
    }

    public function getCssClassAlternative(?bool $isTransparent = false): string
    {
        if($isTransparent) {
            $name = 'ffffff00';
        } else {
            $name = $this->value ?: 'no-colour';
        }
        return $this->classCleanup($name);
    }

    public function scaffoldFormField($title = null, $params = null)
    {
        return static::get_dropdown_field($this->name, $title);
    }

    /**
     *
     * @param  string $name
     * @param  string $title
     * @return FormField
     */
    public static function get_dropdown_field(string $name, ?string $title = '')
    {
        $className = Config::inst()->get(DBColour::class, 'colour_picker_field_class_name');
        return $className::create(
            $name,
            $title
        )
            ->setSource(static::COLOURS)
            ->setLimitedToOptions(static::IS_LIMITED_TO_OPTIONS)
            ->setIsBgColour(static::IS_BG_COLOUR);
        ;
        return $field;
    }

    public static function get_swatches_field(string $name, string $value): LiteralField
    {
        return SelectedColourPickerFormFieldSwatches::get_swatches_field((string) $name, (string) $value, static::COLOURS, static::IS_BG_COLOUR);
    }

    private function classCleanup(string $name): string
    {
        $name = str_replace('#', '', $name);
        $name = preg_replace('#[^A-Za-z0-9]#', '-', $name);

        return static::CSS_CLASS_PREFIX . '-' . trim(trim(strtolower($name), '-'));
    }
}

//
// public static function get_dropdown_field_old(?string $name = 'TextColour', ?string $title = 'Text Colour'): SelectedColourPickerFormField
// {
//     $field = SelectedColourPickerFormField::create(
//         $name,
//         $title,
//         static::COLOURS
//     );
//     $js = '
//         jQuery("#TextAndBackgroudColourExample").css("color", jQuery(this).val());
//     ';
//     $field->setAttribute('onchange', $js);
//     $field->setAttribute('onhover', $js);
//     $field->setAttribute('onclick', $js);
//     $field->setAttribute('onfocus', $js);
//
//     return $field;
// }
//
