<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

declare(strict_types=1);

namespace Piwik\Plugins\AIActivity;

use Piwik\Archive;
use Piwik\Piwik;
use Piwik\Plugin\ReportsProvider;

class API extends \Piwik\Plugin\API
{
    public function get($idSite, $period, $date, $segment = false, $columns = false)
    {
        Piwik::checkUserHasViewAccess($idSite);

        $report = ReportsProvider::factory('AIActivity', 'get');
        $archive = Archive::build($idSite, $period, $date, $segment);

        $requestedColumns = Piwik::getArrayFromApiParameter($columns);
        $columns = $report->getMetricsRequiredForReport(null, $requestedColumns);

        $databaseColumns = array_map(function ($column) {
            return Archiver::NUMERIC_RECORD_PREFIX . $column;
        }, $columns);

        $dataTable = $archive->getDataTableFromNumeric($databaseColumns);
        $dataTable->deleteColumns(array_diff($requestedColumns, $columns));

        $newNameMapping = array_combine($databaseColumns, $columns);
        $dataTable->filter('ReplaceColumnNames', [$newNameMapping]);

        $columnsToShow = $requestedColumns ?: $report->getAllMetrics();
        $dataTable->queueFilter('ColumnDelete', [[], $columnsToShow]);

        return $dataTable;
    }
}
