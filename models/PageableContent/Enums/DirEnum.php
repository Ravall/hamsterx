<?php

namespace app\models\PageableContent\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class DirEnum
 * @package app\models\PageableContent\Enums
 *
 * @method static static ASC()
 * @method static static DESC()
 */
final class DirEnum extends Enum
{
    private const ASC = 'asc';
    private const DESC = 'desc';
}