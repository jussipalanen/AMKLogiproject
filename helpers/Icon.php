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

    /**
     * Padlock, used for private albums.
     */
    public static function lock(): string
    {
        return '<svg class="amk-icon" viewBox="0 0 16 16" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false">'
            . '<path d="M8 1a3.5 3.5 0 0 0-3.5 3.5V7H4a1.5 1.5 0 0 0-1.5 1.5v5A1.5 1.5 0 0 0 4 15h8a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 12 7h-.5V4.5A3.5 3.5 0 0 0 8 1zm2 6H6V4.5a2 2 0 1 1 4 0V7z"/>'
            . '</svg>';
    }

    /**
     * Picture frame, used for empty albums.
     */
    public static function images(): string
    {
        return '<svg class="amk-icon" viewBox="0 0 16 16" width="1em" height="1em" fill="currentColor" aria-hidden="true" focusable="false">'
            . '<path d="M2.5 2h11A1.5 1.5 0 0 1 15 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 12.5v-9A1.5 1.5 0 0 1 2.5 2zm0 1a.5.5 0 0 0-.5.5v7.3l2.6-2.6a.5.5 0 0 1 .7 0L7.5 10.4l3.2-3.7a.5.5 0 0 1 .75 0L14 9.7V3.5a.5.5 0 0 0-.5-.5h-11zM5.5 4.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3z"/>'
            . '</svg>';
    }
}
