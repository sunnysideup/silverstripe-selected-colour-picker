<?php

namespace Sunnysideup\SelectedColourPicker\Model\Fields;

class DBFontColour extends DBColour
{
    /**
     * please set.
     *
     * @var bool
     */
    protected const IS_BG_COLOUR = false;
    public static function get_colours_for_dropdown(?bool $isBackgroundColour = null): ?array
    {
        return parent::get_colours_for_dropdown(false);
    }
}
