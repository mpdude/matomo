<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\AIActivity\Reports;

use Piwik\Piwik;
use Piwik\Plugins\AIActivity\Metrics;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Evolution;
use Piwik\Report\ReportWidgetFactory;
use Piwik\Widget\WidgetsList;

class Get extends \Piwik\Plugin\Report
{
    protected function init()
    {
        parent::init();

        $this->categoryId    = 'AIActivity';
        $this->subcategoryId = 'General_Overview';
        $this->name          = Piwik::translate($this->categoryId);
        $this->order         = 200;

        $this->metrics  = [
            Metrics::METRIC_NB_VISITS_AI_AGENTS,
            Metrics::METRIC_NB_UNIQ_VISITORS_AI_AGENTS,
            Metrics::METRIC_NB_ACTIONS_AI_AGENTS,
            Metrics::METRIC_MAX_ACTIONS_AI_AGENTS,
            Metrics::METRIC_SUM_VISIT_LENGTH_AI_AGENTS,
        ];
    }

    public function configureWidgets(WidgetsList $widgetsList, ReportWidgetFactory $factory)
    {
        $widgetsList->addWidgetConfig(
            $factory->createWidget()
                ->setName('General_EvolutionOverPeriod')
                ->forceViewDataTable(Evolution::ID)
                ->setAction('getEvolutionGraph')
                ->setOrder(1)
        );
    }
}
