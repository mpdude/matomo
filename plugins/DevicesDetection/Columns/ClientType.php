<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\DevicesDetection\Columns;

use Piwik\Metrics\Formatter;
use Piwik\Request as PiwikRequest;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;
use Piwik\Tracker\Action;

use function Piwik\Plugins\DevicesDetection\getClientTypeLabel;
use function Piwik\Plugins\DevicesDetection\getClientTypeMapping;

class ClientType extends Base
{
    public const HEADER_SIGNATURE = 'HTTP_SIGNATURE';
    public const HEADER_SIGNATURE_AGENT = 'HTTP_SIGNATURE_AGENT';
    public const HEADER_SIGNATURE_INPUT = 'HTTP_SIGNATURE_INPUT';

    public const PARAM_SIGNATURE = 'dd_ct_s';
    public const PARAM_SIGNATURE_AGENT = 'dd_ct_sa';
    public const PARAM_SIGNATURE_INPUT = 'dd_ct_si';

    protected $columnName = 'config_client_type';
    protected $columnType = 'TINYINT( 1 ) NULL DEFAULT NULL';
    //protected $segmentName = 'clientType';
    protected $type = self::TYPE_ENUM;
    protected $nameSingular = 'DevicesDetection_ClientType';
    protected $namePlural = 'DevicesDetection_ClientTypes';

    public function __construct()
    {
        $clientTypes = getClientTypeMapping();
        $clientTypeList = implode(", ", $clientTypes);

        $this->acceptValues = $clientTypeList;
    }

    public function formatValue($value, $idSite, Formatter $formatter)
    {
        return getClientTypeLabel($value);
    }

    public function getEnumColumnValues()
    {
        return getClientTypeMapping();
    }

    /**
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     * @return mixed
     */
    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        if ($this->isAIAgent($request)) {
            $clientType = 'ai agent';
        } else {
            $parser = $this->getUAParser($request->getUserAgent(), $request->getClientHints());
            $clientType = $parser->getClient('type');
        }

        $clientTypes = getClientTypeMapping();

        return array_search($clientType, $clientTypes, true) ?: null;
    }

    /**
     * @param Request $request
     * @param Visitor $visitor
     * @param Action|null $action
     * @return mixed
     */
    public function onAnyGoalConversion(Request $request, Visitor $visitor, $action)
    {
        return $visitor->getVisitorColumn($this->columnName);
    }

    private function isAIAgent(Request $trackerRequest): bool
    {
        // cannot use \Piwik\Tracker\Request::getParam() for the custom parameters
        $matomoRequest = new PiwikRequest($trackerRequest->getParams());

        $signature = $matomoRequest->getStringParameter(
            self::PARAM_SIGNATURE,
            $_SERVER[self::HEADER_SIGNATURE] ?? ''
        );

        $signatureAgent = $matomoRequest->getStringParameter(
            self::PARAM_SIGNATURE_AGENT,
            $_SERVER[self::HEADER_SIGNATURE_AGENT] ?? ''
        );

        $signatureInput = $matomoRequest->getStringParameter(
            self::PARAM_SIGNATURE_INPUT,
            $_SERVER[self::HEADER_SIGNATURE_INPUT] ?? ''
        );

        return (
            '' !== $signature
            && '' !== $signatureInput
            // the value of the Signature-Agent header is wrapped in double quotes!
            && '"https://chatgpt.com"' === $signatureAgent
        );
    }
}
