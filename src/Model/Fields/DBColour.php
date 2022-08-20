<?php

namespace Sunnysideup\SelectedColourPicker\Model\Fields;

use TractorCow\Colorpicker\Color;

use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\DropdownField;

use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBVarchar;

use Sunnysideup\SelectedColourPicker\Forms\SelectedColourPickerFormField;

class DBColour extends DBVarchar
{

    /**
     * please set
     * must be defined as #AABB99 (hex codes)
     * @var array
     */
    private const COLOURS = [];

    /**
     * please set
     * @var string`
     */
    private const CSS_CLASS_PREFIX = 'db-colour';

    /**
     * please set
     * @var string
     */
    private const IS_BG_COLOUR = true;

    /**
     * please set
     * @var string
     */
    private const IS_LIMITED_TO_OPTIONS = true;

    private static $casting = [
        'CssClass' => 'Varchar',
    ];

    public function CssClass() : string
    {
        return $this->getCssClass();
    }

    public function getCssClass() : string
    {
        $name = static::COLOURS[$this->value] ?? 'no-colour';
        return $this->classCleanup($name);
    }

    public function CssClassAlternative() : string
    {
        return $this->getCssClassAlternative();
    }

    public function getCssClassAlternative() : string
    {
        $name = $this->value ?: 'no-colour';
        return $this->classCleanup($name);
    }

    private function classCleanup(string $name) : string
    {
        $name = str_replace('#', '', $name);
        $name = preg_replace("/[^A-Za-z0-9]/", '-', $name);
        return static::CSS_CLASS_PREFIX . '-'.trim(trim(strtolower($name),'-'));
    }

    public function scaffoldFormField($title = null, $params = null)
    {
        return static::get_dropdown_field($this->name, $title);
    }


    public static function get_dropdown_field(string $name, string $title): SelectedColourPickerFormField
    {
        return SelectedColourPickerFormField::create(
            $name,
            $title
        )
            ->setColourOptions(static::COLOURS)
            ->setLimitedToOptions(static::IS_LIMITED_TO_OPTIONS)
        ;
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
    public static function get_swatches_field($name, $value): LiteralField
    {
        $options = static::get_swatches_field_inner(static::COLOURS, $value);

        return LiteralField::create(
            $name . 'SwatchesFor',
            '<div class="field ' . $name . '-class">
                <h5 onclick="alert(\'show colours\')">Available Colours</h5>
                <div style="display: none" id="">' . implode('', $options) . '<hr style="clear: both; " />
            </div>'
        );
    }

    protected static function get_swatches_field_inner($colours, ?string $value = '') : array
    {
        $ids = [];
        foreach ($colours as $colour => $name) {
            if (static::IS_BG_COLOUR) {
                $styleA = 'background-color: ' . $colour . '; color: #eee;';
                $styleB = 'background-color: ' . $colour . '; color: #111;';
            } else {
                $styleA = 'color: ' . $colour . '; background-color: #eee;';
                $styleB = 'color: ' . $colour . '; background-color: #111;';
            }

            $currentStyle = 'border: 2px solid #000;';
            if ($colour === $value) {
                $currentStyle = 'border: 2px solid red;';
            }

            $ids[$colour] = '
                <div
                    style="float: left; margin-right: 10px; margin-bottom: 10px; width: auto; border-radius: 15px; font-size: 12px; overflow: hidden; ' . $currentStyle . '"
                    onMouseOver="this.style.borderRadius=\'0px\'"
                    onMouseOut="this.style.borderRadius=\'15px\'"
                >
                    <span style=" display: block; padding: 5px; text-align: center; ' . $styleA . '">
                        ' . $name . ' (' . $colour . ')
                    </span>
                    <span style=" display: block; padding: 5px; text-align: center; ' . $styleB . '">
                        ' . $name . ' (' . $colour . ')
                    </span>
                </div>
                ';
        }

        return $ids;
    }
}
