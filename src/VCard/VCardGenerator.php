<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\VCard;

use Contao\File;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;

class VCardGenerator
{
    public const VCARD_TEMPLATE = 'partial_employee_vcard';

    public function __construct(
        private readonly Environment $twig,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function getVCard(EmployeeModel $objEmployee): \SplFileObject
    {
        $arrData = array_map(static fn ($value) => trim(utf8_decode(html_entity_decode((string) $value))), $objEmployee->row());

        // Add the file name
        $arrData['file_name'] = sprintf('%s %s %s', $arrData['title'], $arrData['firstname'], $arrData['lastname']);

        // Create temp file
        $objFile = new File('system/tmp/'.time().'.vcf');
        $objFile->append($this->twig->render('@MarkocupicEmployee/vcard/vcard.twig', $arrData));
        $objFile->close();

        return new \SplFileObject(Path::makeAbsolute($objFile->path, $this->projectDir));
    }
}
