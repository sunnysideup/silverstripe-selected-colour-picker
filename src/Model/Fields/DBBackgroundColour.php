<?php

declare(strict_types=1);

namespace Sunnysideup\SelectedColourPicker\Model\Fields;

use Override;

class DBBackgroundColour extends DBColour
{
    /**
     * please set.
     *
     * @var bool
     */
    protected const IS_BG_COLOUR = true;

    #[Override]
    public static function get_colours_for_dropdown(?bool $isBackgroundColour = null): ?array
    {
        return parent::get_colours_for_dropdown(true);
    }
}
