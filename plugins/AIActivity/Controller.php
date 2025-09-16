<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\AIActivity;

use Piwik\Piwik;
use Piwik\Request;
use Piwik\Translation\Translator;

/**
 *
 */
class Controller extends \Piwik\Plugin\Controller
{
    /**
     * @var Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;

        parent::__construct();
    }

    public function getEvolutionGraph()
    {
        $this->checkSitePermission();

        $columns = Request::fromRequest()->getParameter('columns', false);

        if (false !== $columns) {
            $columns = Piwik::getArrayFromApiParameter($columns);
        }

        $view = $this->getLastUnitGraph(
            $this->pluginName,
            __FUNCTION__,
            'AIActivity.get'
        );

        $view->config->columns_to_display = [
            Metrics::METRIC_NB_VISITS_AI_AGENTS,
            Metrics::METRIC_NB_UNIQ_VISITORS_AI_AGENTS,
        ];

        $view->config->selectable_columns = [
            Metrics::METRIC_NB_VISITS_AI_AGENTS,
            Metrics::METRIC_NB_UNIQ_VISITORS_AI_AGENTS,
            Metrics::METRIC_NB_ACTIONS_AI_AGENTS,
            Metrics::METRIC_MAX_ACTIONS_AI_AGENTS,
            Metrics::METRIC_SUM_VISIT_LENGTH_AI_AGENTS,
        ];

        if (empty($view->config->columns_to_display)) {
            $view->config->columns_to_display = Metrics::METRIC_NB_VISITS_AI_AGENTS;
        }

        return $this->renderView($view);
    }
}
