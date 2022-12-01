<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2022 <m.cupic@gmx.ch>
 * @license LGPL-3.0+
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\Traits;

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Image\Studio\FigureBuilder;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Validator;
use Markocupic\EmployeeBundle\Model\EmployeeModel;

trait FrontendModuleTrait
{
    protected ?FigureBuilder $figureBuilder = null;

    public function getEmployeeDetails(EmployeeModel $employeeModel, ModuleModel $moduleModel, AbstractFrontendModuleController $frontendModuleInstance): array
    {
        $arrData = $employeeModel->row();

        $arrData['publications'] = $frontendModuleInstance->insertTagParser->replaceInline($arrData['publications']);
        $arrData['interview'] = StringUtil::deserialize($arrData['interview'] ?? null, true);
        $arrData['businessHours'] = StringUtil::deserialize($arrData['businessHours'] ?? null, true);
        $arrData['href'] = false;

        $objJumpToPage = $this->getJumpToPage($moduleModel);

        if ($objJumpToPage) {
            $params = (Config::get('useAutoItem') ? '/' : '/items/').($arrData['alias'] ?: $arrData['id']);
            $arrData['href'] = StringUtil::ampersand($objJumpToPage->getFrontendUrl($params));
        }

        // Add image to the template
        $arrData['hasImage'] = false;

        if ($moduleModel->addEmployeeImage && $arrData['addImage'] && Validator::isUuid($arrData['singleSRC'] ?? '')) {
            $objFile = FilesModel::findByUuid($arrData['singleSRC']);

            if (null !== $objFile && is_file($frontendModuleInstance->projectDir.'/'.$objFile->path)) {
                $arrData['hasImage'] = true;
                $arrData['singleSRC'] = StringUtil::binToUuid($objFile->uuid);

                $figureBuilder = $this->getFigureBuilder($moduleModel, $frontendModuleInstance->studio, $moduleModel->imgSize, (bool) $moduleModel->imgFullsize);
                $figure = $figureBuilder->fromUuid($objFile->uuid)->build();

                $arrData['figure'] = $frontendModuleInstance->twig->render(
                    '@ContaoCore/Image/Studio/figure.html.twig',
                    [
                        'figure' => $figure,
                    ]
                );
            }
        }

        // Add gallery to the template
        $arrData['hasGallery'] = false;
        $arrData['gallery'] = [];

        if ($moduleModel->addEmployeeGallery && $arrData['addGallery'] && Validator::isUuid($arrData['singleSRC'] ?? '')) {
            $arrMultiSrc = StringUtil::deserialize($arrData['multiSRC']);

            if (!empty($arrMultiSrc)) {
                foreach ($arrMultiSrc as $uuid) {
                    if (Validator::isBinaryUuid($uuid)) {
                        $objFile = FilesModel::findByUuid($arrData['singleSRC']);

                        if (null !== $objFile && is_file($frontendModuleInstance->projectDir.'/'.$objFile->path)) {
                            $arrData['hasImage'] = true;
                            $arrData['singleSRC'] = StringUtil::binToUuid($objFile->uuid);

                            $figureBuilder = $this->getFigureBuilder($moduleModel, $frontendModuleInstance->studio, $moduleModel->galSize, (bool) $moduleModel->galFullsize);
                            $figure = $figureBuilder->fromUuid($objFile->uuid)->build();

                            $arrData['hasGallery'] = true;
                            $arrData['gallery'][] = $frontendModuleInstance->twig->render(
                                '@ContaoCore/Image/Studio/figure.html.twig',
                                [
                                    'figure' => $figure,
                                ]
                            );
                        }
                    }
                }
            }
        }

        return $arrData;
    }

    public function getFigureBuilder(ModuleModel $moduleModel, Studio $studio, mixed $size, bool $fullsize): FigureBuilder
    {
        if ($this->figureBuilder) {
            return $this->figureBuilder;
        }

        $figureBuilder = $studio->createFigureBuilder();

        $figureBuilder->setSize(StringUtil::deserialize($size));

        if ($fullsize) {
            $figureBuilder->setLightboxGroupIdentifier('lb_employee_list_'.$moduleModel->id);
            $figureBuilder->enableLightbox();
        }

        return $figureBuilder;
    }

    protected function getJumpToPage(ModuleModel $moduleModel): ?PageModel
    {
        if (($objTarget = $moduleModel->getRelated('jumpTo')) instanceof PageModel) {
            return $objTarget;
        }

        return null;
    }
}
