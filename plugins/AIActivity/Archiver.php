<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\AIActivity;

class Archiver extends \Piwik\Plugin\Archiver
{
    public const NUMERIC_RECORD_PREFIX = 'AIActivity_';
}
