<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2020 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Listener\ContaoHooks;

use Contao\Input;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Markocupic\EmployeeBundle\Vcard\VcardGenerator;

/**
 * Class GeneratePageListener.
 */
class GeneratePageListener
{
    public function generatePage(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular): void
    {
        // Trigger VCard download
        if ('true' === Input::get('downloadVCard') && '' !== Input::get('id')) {
            if (null !== ($objEmployee = EmployeeModel::findByPk(Input::get('id')))) {
                VcardGenerator::sendToBrowser($objEmployee);
            }
        }
    }
}
