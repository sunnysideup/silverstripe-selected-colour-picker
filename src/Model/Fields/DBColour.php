<?php

namespace Sunnysideup\SelectedColourPicker\Model\Fields;

use BimTheBam\NativeColorInput\Form\Field\ColorField;
use Fromholdio\ColorPalette\Fields\ColorPaletteField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\LiteralField;

use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\FieldType\DBField;
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
    private static $colours = [];

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
        // related colours
        'FontColour' => 'Varchar',
        'BackgroundColour' => 'Varchar',
        'ReadableColour' => 'Varchar',
        'RelatedColourByName' => 'Varchar',
        'Inverted' => 'Varchar',
        // css
        'CssVariableDefinition' => 'HTMLText',
        'CssClass' => 'Varchar',
        'CssClassAlternative' => 'Boolean',
        // booleans
        'IsDarkColour' => 'Boolean',
        'IsLightColour' => 'Boolean',

        'Nice' => 'HTMLText',
    ];


    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);
    }


    public static function my_colours(): array
    {
        return static::get_colour_as_db_field('')->getColours();
    }

    public static function get_swatches_field(string $name, string $value): LiteralField
    {
        return SelectedColourPickerFormFieldSwatches::get_swatches_field(
            (string) $name,
            (string) $value,
            static::my_colours(),
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
            ->setSource(static::my_colours())
            ->setLimitedToOptions(static::IS_LIMITED_TO_OPTIONS)
            ->setIsBgColour($isBackgroundColour);
        ;
    }


    public static function get_colours_for_dropdown(?bool $isBackgroundColour = null): ?array
    {
        if($isBackgroundColour === null) {
            $isBackgroundColour = static::IS_BG_COLOUR;
        }
        $colours = static::my_colours();
        if (!empty($colours)) {
            $array = [];

            foreach ($colours as $code => $label) {
                $textcolour = static::get_font_colour((string) $code) ;
                if($isBackgroundColour) {
                    $array[$code] = [
                        'label' => $label,
                        'background_css' => $code,
                        'colour_css' => $textcolour,
                        'sample_text' => 'Aa',
                    ];

                } else {
                    $array[$code] = [
                        'label' => $label,
                        'background_css' => $textcolour,
                        'colour_css' => $code,
                        'sample_text' => 'Aa',
                    ];
                }
            }

            return $array;
        }
        return null;
    }


    /**
     * Detects if the given colour is light
     * @param string $colour HEX colour code
     */
    public static function get_font_colour(?string $colour = null, ?string $name = '')
    {
        if(! $colour) {
            $colour = '#ffffff';
        }
        $colour = static::is_light_colour((string) $colour) ? '#000000' : '#ffffff';
        return static::get_colour_as_db_field($colour, $name);
    }

    /**
     * @param string $colour HEX colour code
     */
    public static function is_dark_colour(?string $colour = ''): bool
    {
        return static::is_light_colour((string) $colour) ? false : true;
    }

    /**
     * Detects if the given colour is light
     * @param string $colour HEX colour code
     */
    public static function is_light_colour(?string $colour = ''): bool
    {
        return static::get_colour_as_db_field($colour)
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
        $array = static::get_colours_for_dropdown();
        if(empty($array)) {
            return ColorField::create($this->name, $title);
        } else {
            return ColorPaletteField::create($this->name, $title, static::get_colours_for_dropdown());
        }
    }


    public function getReadableColour(): static
    {
        // Remove '#' if it's present
        return static::get_font_colour((string) $this->value, $this->name);
    }

    public function Inverted(): static
    {
        // Ensure the colour is 6 characters long
        $colour = str_pad(ltrim($this->value, "#"), 6, '0', STR_PAD_RIGHT);

        // Convert the colour to decimal
        $colour = hexdec($colour);

        // Invert the colour
        $colour = 0xFFFFFF - $colour;

        // Convert the colour back to hex
        $colour = dechex($colour);

        // Ensure the colour is 6 characters long
        $colour = str_pad($colour, 6, '0', STR_PAD_LEFT);

        return static::get_colour_as_db_field($colour, $this->name);
    }

    public function getRelatedColourByName(string $relatedName): static
    {
        $relatedColours = $this->getRelatedColours($this->value);
        $colour = $relatedColours[$relatedName] ?? 'error';
        return static::get_colour_as_db_field($colour, $this->name);
    }


    public function getCssVariableDefinition(?string $rootElement = ':root'): string
    {
        $style = PHP_EOL . '<style>';
        $style .= PHP_EOL.$rootElement;
        $style .= PHP_EOL. '{';
        $style .= $this->getCssVarLine();
        if(static::IS_BG_COLOUR) {
            $readableColourObj = $this->getReadableColour();
            $style .= $readableColourObj->getCssVarLine('-font');
        }
        foreach($this->getRelatedColours() as $name => $relatedColour) {
            $relatedColourObj = self::get_colour_as_db_field($relatedColour, $this->name);
            $style .= $relatedColourObj->getCssVarLine($name);
            $relatedColourObjReadable = $relatedColourObj->getReadableColour();
            $style .= $relatedColourObjReadable->getCssVarLine($name.'-font');
        }
        $style .= PHP_EOL . '}';
        $style .= PHP_EOL . '</style>';

        return $style;
    }

    public function getCssVarLine(?string $name = '', ?string $prefix = '--colour-'): string
    {
        $variableName = '    '.$prefix;
        $variableName .= $name ?: $this->kebabCase($this->getName());
        return PHP_EOL. $variableName . ': ' . $this->getValue() . ';';
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

    public function getIsLightColour(): bool
    {
        return static::is_light_colour($this->value);
    }

    public function getNice()
    {
        $html = '
            <div
            style="
                width: 40px;
                height: 40px;
                border-radius: 40px;
                background-color: ' . $this->getBackgroundColour() . '!important;
                color: '.$this->getFontColour().'!important;
                border: 1px solid '.$this->getFontColour().'!important;
                text-align: center;
                display: table-cell;
                display: flex;
                flex-direction: column;
                justify-content: center;
            "
            >Aa</div> ';
        return DBField::create_field('HTMLText', $html);
    }

    public function getIsDarkColour(): bool
    {
        return static::is_light_colour($this->value) ? false : true;
    }

    private function classCleanup(string $name): string
    {
        $name = str_replace('#', '', $name);
        $name = preg_replace('#[^A-Za-z0-9]#', '-', $name);

        return static::CSS_CLASS_PREFIX . '-' . trim(trim(strtolower($name), '-'));
    }




    protected function getColours(): array
    {
        return $this->Config()->get('colours');
    }

    protected function getMyColours(): array
    {
        $colours = $this->getColours();

        return empty($colours) ? static::DEFAULT_COLOURS : $colours;
    }

    protected function getRelatedColours(): array
    {
        $relatedColoursForAllColours = Config::inst()->get(static::class, 'linked_colours');
        return $relatedColoursForAllColours[$this->value] ?? [];
    }


    protected static $object_cache = [];

    protected static function get_colour_as_db_field(string $colour, ?string $name = '')
    {
        $cacheKey = $colour . '_'.  $name . '_'. static::class;
        if(!$colour || ! isset(static::$object_cache[$cacheKey])) {
            static::$object_cache[$cacheKey] = DBField::create_field(static::class, $colour, $name);
        }
        return static::$object_cache[$cacheKey];
    }

    public function getFontColour(): string
    {
        if(self::IS_BG_COLOUR) {
            return (string) self::get_font_colour($this->value);
        } else {
            return (string) $this->value;
        }
    }

    public function getBackgroundColour(): string
    {
        if(self::IS_BG_COLOUR) {
            return (string) $this->value;
        } else {
            return (string) self::get_font_colour($this->value);
        }
    }


    protected function kebabCase(string $string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }

}
