<?php

namespace Sunnysideup\SelectedColourPicker\ViewableData;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\LiteralField;
use SilverStripe\View\ViewableData;

use SilverStripe\ORM\FieldType\DBField;

class SelectedColourPickerFormFieldSwatches extends ViewableData
{
    public static function get_swatches_field(string $name, string $value, array $colours, bool $isBackgroundColour): LiteralField
    {
        return LiteralField::create(
            'SwatchesFor' . $name,
            self::get_swatches_html($name, $value, $colours, $isBackgroundColour)
        );
    }

    public static function colour_and_background_swatch(string $colour, string $bgColour, ?bool $asHTML = true)
    {
        $html = '<span style="color: '.$colour.'!important; background-color: '.$bgColour.'!important; padding: 4px; width: auto; text-align: center; border-radius: 4px; display: inline-block;">example</span>';
        if($asHTML) {
            return DBField::create_field('HTMLText', $html);
        }
    }

    public static function get_swatches_html(string $name, string $value, array $colours, bool $isBackgroundColour): string
    {
        $options = static::get_swatches_field_inner($value, $colours, $isBackgroundColour);
        $id = $name . rand(0, 99999999999);
        $js = Convert::raw2att('document.querySelector("#' . $id . '").style.display="block";');

        return '
            <div class="field ' . $name . '-class">
                <p>
                    Current Value: ' . ($colours[$value] ?? $value) . ', '.$value.'
                    <a onclick="' . $js . '" style="cursor: pointer;"><u>show available colours</u></a>
                </p>
                <div style="display: none" id="' . $id . '">' . implode('', $options) . '<hr style="clear: both; " /></div>
            </div>'
        ;
    }

    public static function hex_invert(string $color): string
    {
        $color = trim($color);
        $prependHash = false;
        if (false !== strpos($color, '#')) {
            $prependHash = true;
            $color = str_replace('#', '', $color);
        }

        $len = strlen($color);
        if (3 === $len || 6 === $len || 8 === $len) {
            if (8 === $len) {
                $color = substr($color, 0, 6);
            }

            if (3 === $len) {
                $color = preg_replace('#(.)(.)(.)#', '\\1\\1\\2\\2\\3\\3', $color);
            }
        } else {
            throw new \Exception("Invalid hex length ({$len}). Length must be 3 or 6 characters - colour is" . $color);
        }

        if (! preg_match('#^[a-f0-9]{6}$#i', $color)) {
            throw new \Exception(sprintf('Invalid hex string #%s', htmlspecialchars($color, ENT_QUOTES)));
        }

        $r = dechex(255 - hexdec(substr($color, 0, 2)));
        $r = (strlen($r) > 1) ? $r : '0' . $r;

        $g = dechex(255 - hexdec(substr($color, 2, 2)));
        $g = (strlen($g) > 1) ? $g : '0' . $g;

        $b = dechex(255 - hexdec(substr($color, 4, 2)));
        $b = (strlen($b) > 1) ? $b : '0' . $b;

        return ($prependHash ? '#' : '') . $r . $g . $b;
    }

    protected static function get_swatches_field_inner(string $value, array $colours, bool $isBackgrounColour): array
    {
        $ids = [];
        foreach ($colours as $colour => $name) {
            $invertColour = self::hex_invert($colour);
            if ($isBackgrounColour) {
                $styleA = 'background-color: ' . $colour . '; color: ' . $invertColour . '; ';
            } else {
                $styleA = 'color: ' . $colour . '; background-color: ' . $invertColour . ';';
            }

            $currentStyle = 'border: 4px solid transparent;';
            if ($colour === $value) {
                $currentStyle = 'border: 4px solid red!important; border-radius: 0!important; font-weight: strong!important;';
            }

            $ids[$colour] = '
                <div
                    style="float: left; margin-right: 10px; margin-bottom: 10px; width: auto; border-radius: 30px; font-size: 12px; overflow: hidden; ' . $currentStyle . '"
                    onMouseOver="this.style.borderRadius=\'0px\'"
                    onMouseOut="this.style.borderRadius=\'30px\'"
                >
                    <span style=" display: block; padding: 5px 15px; text-align: center; ' . $styleA . '">
                        ' . $name . ' (' . $colour . ')
                    </span>
                </div>
                ';
        }

        return $ids;
    }
}

// <span
//     class="color-cms"
//     style="display: inline-block; vertical-align: bottom; width: 20px; height: 20px; border-radius: 10px; background-color: '.$this->value.'"
// >
// </span>
