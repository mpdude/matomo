<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\AIActivity;

class Metrics
{
    public const METRIC_MAX_ACTIONS_AI_AGENTS = 'max_actions_ai_agents';
    public const METRIC_NB_ACTIONS_AI_AGENTS = 'nb_actions_ai_agents';
    public const METRIC_NB_UNIQ_VISITORS_AI_AGENTS = 'nb_uniq_visitors_ai_agents';
    public const METRIC_NB_VISITS_AI_AGENTS = 'nb_visits_ai_agents';
    public const METRIC_SUM_VISIT_LENGTH_AI_AGENTS = 'sum_visit_length_ai_agents';
}
