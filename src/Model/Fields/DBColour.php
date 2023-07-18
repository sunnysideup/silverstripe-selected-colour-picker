<?php

namespace Sunnysideup\SelectedColourPicker\Model\Fields\DBColour;

use SilverStripe\Forms\FormField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\FieldType\DBVarchar;

use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\FieldType\DBField;
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
     *         '#fff000' => 'My Colour 1',
     *         '#fff000' => 'My Colour 2',
     *     ]
     *
     * ```
     *
     * @var array
     */
    private static $colours = self::DEFAULT_COLOURS;

    /**
     * You can link colours to other colours.
     * e.g.
     * ```php
     *     '#ffffff' => [
     *          'link' => '#000000',
     *          'foreground' => '#000000',
     *          'background' => '#000000',
     *     ],
     *     '#aabbcc' => [
     *          'link' => '#123123',
     *          'foreground' => '#123312',
     *          'somethingelse' => '#000000',
     *     ],
     * ```
     *
     * @var array
     */

    private static $linked_colours = [
    ];

    protected const DEFAULT_COLOURS = [
        '#FF0000' => 'Red',
        '#0000FF' => 'Blue',
        '#00FF00' => 'Green',
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
        'CssVariableDefinition' => 'Varchar',
        'CssClass' => 'Varchar',
        'CssClassAlternative' => 'Boolean',
        'ReadableColor' => 'Varchar',
        'RelatedColourByName' => 'Varchar',
        'IsDarkColour' => 'Boolean',
        'IsLightColour' => 'Boolean',
        'Inverted' => 'Boolean',
    ];


    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
    }

    public function getCssVariableDefinition(): string
    {
        return '--' . $this->getName() . ': ' . $this->getValue() . ';';
    }

    public static function my_colours(): array
    {
        return self::get_colour_as_db_field(DBColour::class)->getColours();
    }

    public static function get_swatches_field(string $name, string $value): LiteralField
    {
        return SelectedColourPickerFormFieldSwatches::get_swatches_field(
            (string) $name,
            (string) $value,
            self::my_colours(($name)),
            static::IS_BG_COLOUR
        );
    }

    /**
     *
     * @param  string $name
     * @param  string $title
     * @return FormField
     */
    public static function get_dropdown_field(string $name, ?string $title = '', ?bool $isBackgroundColour = null)
    {
        if($isBackgroundColour === null) {
            $isBackgroundColour = static::IS_BG_COLOUR;
        }
        $className = Config::inst()->get(static::class, 'colour_picker_field_class_name');
        return $className::create(
            $name,
            $title
        )
            ->setSource(self::my_colours($name))
            ->setLimitedToOptions(static::IS_LIMITED_TO_OPTIONS)
            ->setIsBgColour($isBackgroundColour);
        ;
    }


    public static function get_colours_for_dropdown(?bool $isBackgroundColour = null): ?array
    {
        if($isBackgroundColour === null) {
            $isBackgroundColour = static::IS_BG_COLOUR;
        }
        $colours = self::my_colours();
        if (!empty($colours)) {
            $array = [];

            foreach ($colours as $code => $label) {
                $textColor = self::get_font_colour($code) ;
                if($isBackgroundColour) {
                    $array[$code] = [
                        'label' => $label,
                        'background_css' => $code,
                        'color_css' => $textColor,
                        'sample_text' => 'Aa',
                    ];

                } else {
                    $array[$code] = [
                        'label' => $label,
                        'background_css' => $textColor,
                        'color_css' => $code,
                        'sample_text' => 'Aa',
                    ];
                }
            }

            return $array;
        }
        return null;
    }


    /**
     * Detects if the given color is light
     * @param string $colour HEX color code
     */
    public static function get_font_colour(?string $colour = ''): string
    {
        return self::is_light_colour((string) $colour) ? '#000000' : '#ffffff';
    }

    /**
     * @param string $colour HEX color code
     */
    public static function is_dark_colour(?string $colour = ''): bool
    {
        return self::is_light_colour((string) $colour) ? false : true;
    }

    /**
     * Detects if the given color is light
     * @param string $colour HEX color code
     */
    public static function is_light_colour(?string $colour = ''): bool
    {
        return DBField::create_field(DBColour::class, $colour)
            ->Luminance() > 0.5;
    }


    public static function check_colour(?string $colour, ?bool $isBackgroundColour = false): string
    {
        $colour = strtolower($colour);
        if($colour === 'transparent') {
            return 'transparent';
        }
        if(! strpos($colour, '#')) {
            $colour = '#' . $colour;
        }
        if(! $colour) {
            if($isBackgroundColour) {
                $colour = '#ffffff';
            } else {
                $colour = '#000000';
            }
        }
        return $colour;
    }

    public function scaffoldFormField($title = null, $params = null)
    {
        return static::get_dropdown_field($this->name, $title);
    }

    public function getCssClass(?bool $isTransparent = false): string
    {
        $colours = $this->getColours();
        if($isTransparent) {
            $name = 'transparent';
        } else {
            $name = $colours[$this->value] ?? 'colour-error';
        }

        return $this->classCleanup($name);
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


    public function getReadableColor(): string
    {
        // Remove '#' if it's present
        return $this->getFontColour();
    }

    public function getIsLightColour(): bool
    {
        return self::is_light_colour($this->value);
    }

    public function getIsDarkColour(): bool
    {
        return self::is_light_colour($this->value) ? false : true;
    }

    public function Inverted(): string
    {
        // Ensure the color is 6 characters long
        $color = str_pad(ltrim($this->value, "#"), 6, '0', STR_PAD_RIGHT);

        // Convert the color to decimal
        $color = hexdec($color);

        // Invert the color
        $color = 0xFFFFFF - $color;

        // Convert the color back to hex
        $color = dechex($color);

        // Ensure the color is 6 characters long
        $color = str_pad($color, 6, '0', STR_PAD_LEFT);

        return $color;
    }

    private function classCleanup(string $name): string
    {
        $name = str_replace('#', '', $name);
        $name = preg_replace('#[^A-Za-z0-9]#', '-', $name);

        return static::CSS_CLASS_PREFIX . '-' . trim(trim(strtolower($name), '-'));
    }


    public function getRelatedColourByName(string $name): string
    {
        $colours = Config::inst()->get(static::class, 'linked_colours');
        $colour = $colours($this->value)[$name] ?? 'error';
        return DBField::create_field(DBColour::class, $colour)->getValue();
    }


    protected function getColours(): array
    {
        return $this->Config()->get('colours');
    }

    protected function getMyColours(): array
    {
        $colours = $this->getColours();

        return empty($colours) ? self::DEFAULT_COLOURS : $colours;
    }


    protected static $object_cache = [];

    protected static function get_colour_as_db_field(string $colour)
    {
        if(! isset(self::$object_cache[$colour])) {
            self::$object_cache[$colour] = DBField::create_field(DBColour::class, $colour);
        }
        return self::$object_cache[$colour];
    }




}
