<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\AIActivity\tests\System;

use Piwik\Plugins\AIActivity\tests\Fixtures\AIActivity as AIActivityFixture;
use Piwik\Tests\Framework\TestCase\SystemTestCase;

/**
 * @group AIActivity
 * @group APITest
 * @group Plugins
 */
class APITest extends SystemTestCase
{
    /**
     * @var AIActivityFixture
     */
    public static $fixture = null; // initialized below class definition

    /**
     * @dataProvider getApiForTesting
     */
    public function testApi($api, $params)
    {
        $this->runApiTests($api, $params);
    }

    public function getApiForTesting()
    {
        $api = ['AIActivity.get'];

        return [
            [
                $api,
                [
                    'idSite'     => self::$fixture->idSite,
                    'date'       => self::$fixture->dateTime,
                    'periods'    => ['day', 'week'],
                    'testSuffix' => '',
                ],
            ],
        ];
    }

    public static function getOutputPrefix()
    {
        return '';
    }

    public static function getPathToTestDirectory()
    {
        return dirname(__FILE__);
    }
}

APITest::$fixture = new AIActivityFixture();
