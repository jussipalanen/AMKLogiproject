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

    /**
     * Trash can, used for deleting comments.
     */
    public static function trash(): string
    {
        return '<svg class="amk-icon" viewBox="0 0 16 16" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false">'
            . '<path d="M6 1.5h4a1 1 0 0 1 1 1V3h3a.5.5 0 0 1 0 1h-.6l-.8 9.6A1.5 1.5 0 0 1 11.1 15H4.9a1.5 1.5 0 0 1-1.5-1.4L2.6 4H2a.5.5 0 0 1 0-1h3v-.5a1 1 0 0 1 1-1zM6 3h4v-.5H6V3zM6.5 6a.5.5 0 0 0-.5.5v5.5a.5.5 0 0 0 1 0V6.5a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v5.5a.5.5 0 0 0 1 0V6.5a.5.5 0 0 0-.5-.5z"/>'
            . '</svg>';
    }
}
