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

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;
use Markocupic\EmployeeBundle\Event\GenerateVCardEvent;
use Markocupic\EmployeeBundle\Model\EmployeeModel;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class VCardGenerator
{
    public const VCARD_TEMPLATE = 'partial_employee_vcard';

    private readonly Adapter $stringUtilAdapter;

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly Environment $twig,
        private readonly EventDispatcherInterface $eventDispatcher,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
        #[Autowire('%markocupic_employee.vcard_template%')]
        private readonly string $vCardTemplate,
    ) {
        $this->stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);
    }

    public function getVCard(EmployeeModel $objEmployee, Request $request): \SplFileInfo
    {
        $arrData = array_map(static fn ($value) => trim(utf8_decode(html_entity_decode((string) $value))), $objEmployee->row());

        // Specify the formatted text corresponding to the name of the object the vCard represents.
        $arrData['fn'] = implode(' ', array_filter([$arrData['title'], $arrData['firstname'], $arrData['lastname']]));

        $vCardFileName = sprintf('%s.vcf', implode('_', array_filter([$arrData['title'], $arrData['firstname'], $arrData['lastname']])));
        $vCardFileName = $this->stringUtilAdapter->sanitizeFilename($vCardFileName);

        // Create and dispatch GenerateVCardEvent
        $event = new GenerateVCardEvent($request, $arrData, $objEmployee, $vCardFileName, $this->vCardTemplate);
        $this->eventDispatcher->dispatch($event);

        $vCardFilePath = Path::join($this->projectDir, 'system/tmp', $event->getFileName());

        $objSplFile = new \SplFileObject($vCardFilePath, 'w');
        $objSplFile->fwrite($this->twig->render($event->getTemplateName(), $arrData));
        $objSplFile->rewind();

        return new \SplFileInfo($vCardFilePath);
    }
}
