<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\AIActivity;

class AIActivity extends \Piwik\Plugin
{
    public function registerEvents()
    {
        return [
            'CronArchive.getArchivingAPIMethodForPlugin' => 'getArchivingAPIMethodForPlugin',
        ];
    }

    public function getArchivingAPIMethodForPlugin(&$method, $plugin)
    {
        if ($plugin == 'AIActivity') {
            $method = 'AIActivity.getAIAgentsVisitsMetric';
        }
    }
}
