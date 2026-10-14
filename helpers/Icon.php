<?php

declare(strict_types=1);

namespace app\helpers;

/**
 * Inline SVG icons; they inherit the text colour and size of the surrounding text.
 */
final class Icon
{
    /**
     * Speech bubble, used for new comments.
     */
    public static function comment(): string
    {
        return '<svg class="amk-icon" viewBox="0 0 16 16" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false">'
            . '<path d="M2.5 1.5h11A1.5 1.5 0 0 1 15 3v7a1.5 1.5 0 0 1-1.5 1.5H6.2L3 14.4a.5.5 0 0 1-.8-.4v-2.5h.3A1.5 1.5 0 0 1 1 10V3a1.5 1.5 0 0 1 1.5-1.5z"/>'
            . '</svg>';
    }
}
