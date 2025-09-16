<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\AIActivity\tests\Fixtures;

use MatomoTracker;
use Piwik\Date;
use Piwik\Filesystem;
use Piwik\Plugin;
use Piwik\Plugins\DevicesDetection\Columns\ClientType;
use Piwik\Tests\Framework\Fixture;

class AIActivity extends Fixture
{
    public $dateTime = '2022-11-30 00:00:00';
    public $idSite = 1;

    public function setUp(): void
    {
        parent::setUp();

        Plugin\Manager::getInstance()->loadPlugin('AIActivity');
        Plugin\Manager::getInstance()->installLoadedPlugins();
        Filesystem::deleteAllCacheOnUpdate();

        $this->setUpWebsite();
        $this->trackVisits();
    }

    private function setUpWebsite()
    {
        $this->idSite = self::createWebsite($this->dateTime);
    }

    private function trackVisits()
    {
        $t = self::getTracker($this->idSite, $this->dateTime);
        $t->enableBulkTracking();

        $baseDate = Date::factory($this->dateTime);

        // regular visitor
        $t->setForceVisitDateTime($baseDate->getDateTime());
        $t->setUrl('http://www.example.org/regular');
        $t->doTrackPageView('Regular User');

        $this->trackChatGPTAgentVisit($t, 1, 3, $baseDate->addHour(1));
        $this->trackChatGPTAgentVisit($t, 2, 1, $baseDate->addHour(2));
        $this->trackChatGPTAgentVisit($t, 3, 2, $baseDate->addDay(1));

        $t->doBulkTrack();
    }

    private function trackChatGPTAgentVisit(
        MatomoTracker $t,
        int $visitorNum,
        int $numberOfPageviews,
        Date $baseDate
    ): void {
        $t->setForceNewVisit();

        for ($i = 1; $i <= $numberOfPageviews; $i++) {
            $t->setCustomTrackingParameter(ClientType::PARAM_SIGNATURE, 'value irrelevant');
            $t->setCustomTrackingParameter(ClientType::PARAM_SIGNATURE_AGENT, '"https://chatgpt.com"');
            $t->setCustomTrackingParameter(ClientType::PARAM_SIGNATURE_INPUT, 'value irrelevant');

            $t->setForceVisitDateTime($baseDate->addHour($numberOfPageviews * 0.1)->getDatetime());
            $t->setUrl("http://www.example.org/chatgpt-agent/$visitorNum/$i");
            $t->doTrackPageView("ChatGPT Agent - $visitorNum - $i");
        }
    }
}
