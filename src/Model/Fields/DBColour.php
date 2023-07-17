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

class DBColour extends Color
{
    private static $colour_picker_field_class_name = SelectedColourPickerFormFieldDropdown::class;

    /**
     * please set
     * must be defined as #AABB99 (hex codes).
     * Needs to be set like this:
     * ```php
     *     [
     *         'schema1' => [
     *             '#fff000' => 'My Colour 1',
     *             '#fff000' => 'My Colour 2',
     *         ],
     *
     *     ]
     *
     * ```
     *
     * @var array
     */
    private static $colours = [
        'default' => [
            '#FF0000' => 'Red',
            '#0000FF' => 'Blue',
            '#00FF00' => 'Green',
        ]
    ];

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
        'CssClassAlternative' => 'Boolean',
        'ReadableColor' => 'Varchar'
    ];

    private static $schema = 'default';

    public function __construct($name = null, $schema = 'default', $options = [])
    {
        $this->schema = $schema;
        parent::__construct($name, $options);
    }

    public function CssClass(?bool $isTransparent = false): string
    {
        return $this->getCssClass();
    }

    public function getCssClass(?bool $isTransparent = false): string
    {
        $colous = $this->getColours();
        if($isTransparent) {
            $name = 'transparent';
        } else {
            $name = $colours[$this->value] ?? 'colour-error';
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

    public function ReadableColor(): string
    {
        return $this->getReadableColor();
    }
    public function getReadableColor(): string
    {
        // Remove '#' if it's present
        $hexColor = ltrim((string) $this->value, '#');

        // Check if the color is valid
        if(strlen($hexColor) == 6) {
            // Convert the color from hexadecimal to RGB
            $r = hexdec(substr($hexColor, 0, 2)) / 255;
            $g = hexdec(substr($hexColor, 2, 2)) / 255;
            $b = hexdec(substr($hexColor, 4, 2)) / 255;

            // Calculate the relative luminance
            $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

            // Return either black or white, depending on the luminance
            if($luminance > 0.5) {
                return '#000000'; // Black color
            } else {
                return '#ffffff'; // White color
            }
        } else {
            return false; // Invalid color
        }
    }
    private function classCleanup(string $name): string
    {
        $name = str_replace('#', '', $name);
        $name = preg_replace('#[^A-Za-z0-9]#', '-', $name);

        return static::CSS_CLASS_PREFIX . '-' . trim(trim(strtolower($name), '-'));
    }

    public static function my_colours_for_dropdown(): ?array
    {
        if ($colours = self::my_colours()) {
            $array = [];

            foreach ($colours as $code => $label) {
                $textColor = Helper::isColorLight($code) ? '#000' : '#FFF';

                $array[$code] = [
                    'label' => $label,
                    'background_css' => $code,
                    'color_css' => $textColor,
                    'sample_text' => 'Aa',
                ];
            }

            return $array;
        }
        return null;
    }


    /**
     * Detects if the given color is light
     * @param string $colour HEX color code
     */
    public static function fontColour($colour): string
    {
        return self::isColorLight((string) $colour) ? '#000000' : '#ffffff';
    }
    /**
     * @param string $colour HEX color code
     */
    public static function isDarkColour($colour): bool
    {
        return self::isColorLight((string) $colour) ? false : true;
    }

    /**
     * Detects if the given color is light
     * @param string $colour HEX color code
     */
    public static function isColorLight($colour): bool
    {
        if ($colour === 'transparent') {
            return true;
        }
        $colourWithoutHash = str_replace('#', '', (string) $colour);
        // Convert the color to its RGB values
        $rgb = sscanf($colourWithoutHash, "%02x%02x%02x");
        if (isset($rgb[0], $rgb[1], $rgb[2])) {
            // Calculate the relative luminance of the color using the formula from the W3C
            $luminance = 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];

            // If the luminance is greater than 50%, the color is considered light
            return $luminance > 128;
        }
        return true;
    }


    protected static function check_colour(?string $colour, ?bool $isBackgroundColour = false): string
    {
        if(! $colour || strlen($colour) !== 7) {
            if($isBackgroundColour) {
                $colour = '#ffffff';
            } else {
                $colour = '#000000';
            }
        }
        return $colour;
    }

    protected function getColours(): array
    {
        return $this->Config()->get('colours');
    }

    protected function getMyColours(): array
    {
        $colours = $this->getColours();

        return $colours[$this->schema] ?? $colours[$this->schema];
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
