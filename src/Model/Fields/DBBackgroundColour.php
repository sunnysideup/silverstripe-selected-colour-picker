<?php

namespace Sunnysideup\SelectedColourPicker\Model\Fields;

class DBBackgroundColour extends DBColour
{
    /**
     * please set.
     *
     * @var bool
     */
    protected const IS_BG_COLOUR = true;

    public static function get_colours_for_dropdown(?bool $isBackgroundColour = null): ?array
    {
        return parent::get_colours_for_dropdown(true);
    }
}
