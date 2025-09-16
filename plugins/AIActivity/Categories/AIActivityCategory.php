<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\AIActivity\Categories;

use Piwik\Category\Category;
use Piwik\Piwik;

class AIActivityCategory extends Category
{
    protected $id = 'AIActivity';
    protected $order = 100;
    protected $icon = 'icon-admin-platform';

    public function getDisplayName()
    {
        return Piwik::translate('AIActivity_AIActivity');
    }
}
