<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\DevicesDetection\tests\Integration\Columns;

use DeviceDetector\DeviceDetector;
use Piwik\Container\StaticContainer;
use Piwik\DeviceDetector\DeviceDetectorFactory;
use Piwik\Plugins\DevicesDetection\Columns\ClientType;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visit\VisitProperties;
use Piwik\Tracker\Visitor;

use function Piwik\Plugins\DevicesDetection\getClientTypeMapping;

require_once PIWIK_INCLUDE_PATH . '/plugins/DevicesDetection/functions.php';

/**
 * @group DevicesDetection
 * @group ClientTypeTest
 * @group Plugins
 */
class ClientTypeTest extends IntegrationTestCase
{
    /**
     * @var ClientType
     */
    private $clientType;

    public function setUp(): void
    {
        parent::setUp();

        $this->clientType = new ClientType();
        $this->deviceDetectorMock = $this->getMockBuilder(DeviceDetector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $deviceDetectorFactoryMock = $this->createMock(DeviceDetectorFactory::class);
        $deviceDetectorFactoryMock->method('makeInstance')->willReturn($this->deviceDetectorMock);

        StaticContainer::getContainer()->set(DeviceDetectorFactory::class, $deviceDetectorFactoryMock);
    }

    /**
     * @dataProvider getOnNewVisitShouldDetectCorrectClientTypeTestData
     */
    public function testOnNewVisitShouldDetectCorrectClientType(
        string $deviceDetectorClientType,
        ?int $expectedClientType
    ): void {
        $this->deviceDetectorMock
            ->expects(self::once())
            ->method('getClient')
            ->with('type')
            ->willReturn($deviceDetectorClientType);

        $request = $this->getRequest(['ua' => 'mock user agent']);
        $visitor = $this->getNewVisitor();
        $detectedClientType = $this->clientType->onNewVisit($request, $visitor, null);

        $this->assertSame($expectedClientType, $detectedClientType);
    }

    public function getOnNewVisitShouldDetectCorrectClientTypeTestData(): iterable
    {
        foreach (getClientTypeMapping() as $internalId => $deviceDetectorValue) {
            yield $deviceDetectorValue => [$deviceDetectorValue, $internalId];
        }

        yield 'unknown' => [DeviceDetector::UNKNOWN, null];
    }

    /**
     * @dataProvider getOnNewVisitShouldUseSignatureForAIAgentDetectionTestData
     */
    public function testOnNewVisitShouldUseSignatureFromHeadersForAIAgentDetection(
        ?string $requestSignature,
        ?string $requestSignatureAgent,
        ?string $requestSignatureInput,
        bool $shouldDetectAIAgent
    ): void {
        $this->deviceDetectorMock
            ->expects($shouldDetectAIAgent ? self::never() : self::once())
            ->method('getClient')
            ->with('type')
            ->willReturn(DeviceDetector::UNKNOWN);

        $_SERVER[ClientType::HEADER_SIGNATURE] = $requestSignature;
        $_SERVER[ClientType::HEADER_SIGNATURE_AGENT] = $requestSignatureAgent;
        $_SERVER[ClientType::HEADER_SIGNATURE_INPUT] = $requestSignatureInput;

        $request = $this->getRequest(['ua' => 'mock user agent']);
        $visitor = $this->getNewVisitor();
        $detectedClientType = $this->clientType->onNewVisit($request, $visitor, null);
        $isAIAgent = 'ai agent' === (getClientTypeMapping()[$detectedClientType] ?? null);

        unset($_SERVER[ClientType::HEADER_SIGNATURE]);
        unset($_SERVER[ClientType::HEADER_SIGNATURE_AGENT]);
        unset($_SERVER[ClientType::HEADER_SIGNATURE_INPUT]);

        $this->assertSame($shouldDetectAIAgent, $isAIAgent);
    }

    /**
     * @dataProvider getOnNewVisitShouldUseSignatureForAIAgentDetectionTestData
     */
    public function testOnNewVisitShouldUseSignatureFromParametersForAIAgentDetection(
        ?string $requestSignature,
        ?string $requestSignatureAgent,
        ?string $requestSignatureInput,
        bool $shouldDetectAIAgent
    ): void {
        $this->deviceDetectorMock
            ->expects($shouldDetectAIAgent ? self::never() : self::once())
            ->method('getClient')
            ->with('type')
            ->willReturn(DeviceDetector::UNKNOWN);

        $request = $this->getRequest([
            'ua' => 'mock user agent',
            ClientType::PARAM_SIGNATURE => $requestSignature,
            ClientType::PARAM_SIGNATURE_AGENT => $requestSignatureAgent,
            ClientType::PARAM_SIGNATURE_INPUT => $requestSignatureInput,
        ]);

        $visitor = $this->getNewVisitor();
        $detectedClientType = $this->clientType->onNewVisit($request, $visitor, null);
        $isAIAgent = 'ai agent' === (getClientTypeMapping()[$detectedClientType] ?? null);

        $this->assertSame($shouldDetectAIAgent, $isAIAgent);
    }

    public function getOnNewVisitShouldUseSignatureForAIAgentDetectionTestData(): iterable
    {
        yield 'missing Signature header' => [null, 'sig-agent', 'sig-input', false];
        yield 'missing Signature Agent header' => ['sig', null, 'sig-input', false];
        yield 'missing Signature Input header' => ['sig', 'sig-agent', null, false];

        yield 'unhandled Signature Agent' => ['sig', 'sig-agent', 'sig-input', false];

        yield 'ChatGPT Agent' => [
            'Signature (value irrelevant)',
            '"https://chatgpt.com"',
            'Signature Input (value irrelevant)',
            true,
        ];
    }

    private function getRequest($params)
    {
        return new Request($params);
    }

    private function getNewVisitor()
    {
        return new Visitor(new VisitProperties());
    }
}
