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

namespace Markocupic\EmployeeBundle\Vcard;

use Contao\File;
use Contao\FrontendTemplate;
use Markocupic\EmployeeBundle\Model\EmployeeModel;

/**
 * Class VcardGenerator.
 */
class VcardGenerator
{
    const VCARD_TEMPLATE = 'partial_employee_vcard';

    /**
     * @throws \Exception
     */
    public static function sendToBrowser(EmployeeModel $objEmployee): void
    {
        $objTemplate = new FrontendTemplate(static::VCARD_TEMPLATE);

        // Set data from object
        $arrData = $objEmployee->row();
        $arrData = array_map('html_entity_decode', $arrData);
        $arrData = array_map('utf8_decode', $arrData);
        $arrData = array_map('trim', $arrData);

        $objTemplate->setData($arrData);

        // Set fname
        $objTemplate->fn = sprintf('%s %s %s',$row['title'], $row['firstname'], $row['lastname']);

        // Create tmp-file
        $objFile = new File('system/tmp/'.time().'.vcf', true);
        $objFile->append($objTemplate->parse());
        $objFile->close();
        $objFile->sendToBrowser(sprintf('vcard-%s-%s.vcf', $objEmployee->firstname, $objEmployee->lastname));
    }
}
