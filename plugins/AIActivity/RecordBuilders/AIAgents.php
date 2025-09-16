<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\AIActivity\RecordBuilders;

use Piwik\ArchiveProcessor;
use Piwik\ArchiveProcessor\Record;
use Piwik\Plugins\AIActivity\Archiver;
use Piwik\Plugins\AIActivity\Metrics;

use function Piwik\Plugins\DevicesDetection\getClientTypeMapping;

class AIAgents extends ArchiveProcessor\RecordBuilder
{
    public function getRecordMetadata(ArchiveProcessor $archiveProcessor): array
    {
        return [
            Record::make(
                Record::TYPE_NUMERIC,
                Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_MAX_ACTIONS_AI_AGENTS
            ),
            Record::make(
                Record::TYPE_NUMERIC,
                Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_NB_ACTIONS_AI_AGENTS
            ),
            Record::make(
                Record::TYPE_NUMERIC,
                Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_NB_UNIQ_VISITORS_AI_AGENTS
            ),
            Record::make(
                Record::TYPE_NUMERIC,
                Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_NB_VISITS_AI_AGENTS
            ),
            Record::make(
                Record::TYPE_NUMERIC,
                Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_SUM_VISIT_LENGTH_AI_AGENTS
            ),

        ];
    }

    protected function aggregate(ArchiveProcessor $archiveProcessor): array
    {
        $clientType = array_search('ai agent', getClientTypeMapping(), true);

        $query = $archiveProcessor->getLogAggregator()->queryVisitsByDimension(
            [],
            'log_visit.config_client_type = ' . $clientType,
            [
                'COUNT(log_visit.idvisit) as `' . Metrics::METRIC_NB_VISITS_AI_AGENTS . '`',
                'COUNT(DISTINCT log_visit.idvisitor) AS `' . Metrics::METRIC_NB_UNIQ_VISITORS_AI_AGENTS . '`',
                'SUM(log_visit.visit_total_actions) AS `' . Metrics::METRIC_NB_ACTIONS_AI_AGENTS . '`',
                'MAX(log_visit.visit_total_actions) AS `' . Metrics::METRIC_MAX_ACTIONS_AI_AGENTS . '`',
                'MAX(log_visit.visit_total_time) AS `' . Metrics::METRIC_SUM_VISIT_LENGTH_AI_AGENTS . '`',
            ],
            [],
            null
        );

        $data = $query->fetch();

        return [
            Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_MAX_ACTIONS_AI_AGENTS
                => $data[Metrics::METRIC_MAX_ACTIONS_AI_AGENTS],
            Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_NB_ACTIONS_AI_AGENTS
                => $data[Metrics::METRIC_NB_ACTIONS_AI_AGENTS],
            Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_NB_UNIQ_VISITORS_AI_AGENTS
                => $data[Metrics::METRIC_NB_UNIQ_VISITORS_AI_AGENTS],
            Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_NB_VISITS_AI_AGENTS
                => $data[Metrics::METRIC_NB_VISITS_AI_AGENTS],
            Archiver::NUMERIC_RECORD_PREFIX . Metrics::METRIC_SUM_VISIT_LENGTH_AI_AGENTS
                => $data[Metrics::METRIC_SUM_VISIT_LENGTH_AI_AGENTS],
        ];
    }
}
