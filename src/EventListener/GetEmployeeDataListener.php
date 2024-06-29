<?php

declare(strict_types=1);

/*
 * This file is part of Employee Bundle.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license LGPL-3.0+
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/employee-bundle
 */

namespace Markocupic\EmployeeBundle\EventListener;

use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Filesystem\FilesystemItem;
use Contao\CoreBundle\Filesystem\FilesystemUtil;
use Contao\CoreBundle\Filesystem\VirtualFilesystem;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\FigureBuilder;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\CoreBundle\Intl\Countries;
use Contao\Date;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Markocupic\EmployeeBundle\Event\PrepareEmployeeDataEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: PrepareEmployeeDataEvent::class, priority: 1000)]
final class GetEmployeeDataListener
{
    public function __construct(
        private readonly InsertTagParser $insertTagParser,
        #[Autowire('@contao.image.studio')]
        private readonly Studio $studio,
        #[Autowire('@contao.filesystem.virtual.files')]
        private readonly VirtualFilesystem $filesStorage,
        #[Autowire('@contao.intl.countries')]
        private readonly Countries $countries,
        #[Autowire('%contao.image.valid_extensions%')]
        private readonly array $validExtensions,
    ) {
    }

    public function __invoke(PrepareEmployeeDataEvent $event): void
    {
        // Load language file
        Controller::loadLanguageFile('tl_employee');

        $model = $event->getModel();
        $employee = $event->getEmployee();
        $dataEmployee = null !== $employee ? $employee->row() : [];
        $dataEmployee['countrycode_translation'] = $this->getCountryCodeTranslation($dataEmployee['country'] ?? '');
        $dataEmployee['dateOfBirth_formatted'] = Date::parse(Config::get('dateFormat'), $dataEmployee['dateOfBirth']);
        $dataEmployee['publications'] = $this->insertTagParser->replaceInline((string) $dataEmployee['publications']);
        $dataEmployee['interview'] = StringUtil::deserialize($dataEmployee['interview'] ?? null, true);
        $dataEmployee['businessHours'] = StringUtil::deserialize($dataEmployee['businessHours'] ?? null, true);
        $dataEmployee['href'] = false;

        $objJumpToPage = !empty($model->jumpTo) ? $this->getJumpToPage($model) : null;

        if ($objJumpToPage) {
            $strParam = sprintf('/%s', $dataEmployee['alias'] ?: $dataEmployee['id']);
            $dataEmployee['href'] = StringUtil::ampersand($objJumpToPage->getFrontendUrl($strParam));
        }

        // Add an image to the template
        $dataEmployee['hasImage'] = false;

        if ($model->addEmployeeImage && $dataEmployee['addImage']) {
            // Find the single image
            $filesystemItem = FilesystemUtil::listContentsFromSerialized($this->filesStorage, $employee->singleSRC)
                ->filter(fn ($item) => \in_array($item->getExtension(true), $this->validExtensions, true))
            ;

            $figureBuilder = $this->getFigureBuilder($model, $model->imgSize, (bool) $model->imgFullsize);
            $figure = $figureBuilder
                ->fromStorage($this->filesStorage, $filesystemItem->first()->getPath())
                ->buildIfResourceExists()
                ;

            if (null !== $figure) {
                $dataEmployee['hasImage'] = true;
                $dataEmployee['image'] = $figure;
                $dataEmployee['singleSRC'] = StringUtil::binToUuid(
                    FilesModel::findByPath($figure->getImage()->getFilePath())->uuid
                );
            }
        }

        // Add a gallery to the template
        $dataEmployee['hasGallery'] = false;
        $dataEmployee['multiSRC'] = [];

        if ($model->addEmployeeGallery && $dataEmployee['addGallery']) {
            // Find all images
            $filesystemItems = FilesystemUtil::listContentsFromSerialized($this->filesStorage, $employee->multiSRC)
                ->filter(fn ($item) => \in_array($item->getExtension(true), $this->validExtensions, true))
            ;

            $arrItems = iterator_to_array($filesystemItems);

            if (!empty($arrItems)) {
                $figureBuilder = $this->getFigureBuilder($model, $model->galSize, (bool) $model->galFullsize);

                $dataEmployee['images'] = array_filter(array_map(
                    fn (FilesystemItem $filesystemItem): Figure|null => $figureBuilder
                        ->fromStorage($this->filesStorage, $filesystemItem->getPath())
                        ->buildIfResourceExists(),
                    $arrItems,
                ));

                if (!empty($dataEmployee['images'])) {
                    $dataEmployee['hasGallery'] = true;

                    $dataEmployee['multiSRC'] = array_map(
                        static fn (Figure $figure): string => StringUtil::binToUuid(
                            FilesModel::findByPath($figure->getImage()->getFilePath())->uuid
                        ),
                        $dataEmployee['images'],
                    );
                }
            }
        }

        $event->setTemplateData(array_merge($event->getTemplateData(), $dataEmployee));
    }

    private function getFigureBuilder(ContentModel|ModuleModel $model, mixed $size, bool $fullsize): FigureBuilder
    {
        $figureBuilder = $this->studio->createFigureBuilder();

        $figureBuilder->setSize(StringUtil::deserialize($size));

        if ($fullsize) {
            $figureBuilder->setLightboxGroupIdentifier('lb_employee_list_'.$model->id);
            $figureBuilder->enableLightbox();
        }

        return $figureBuilder;
    }

    private function getJumpToPage(ContentModel|ModuleModel $model): PageModel|null
    {
        if (empty($model->jumpTo)) {
            return null;
        }

        $objPage = PageModel::findByPk($model->jumpTo);

        if (null !== $objPage) {
            return $objPage;
        }

        return null;
    }

    private function getCountryCodeTranslation(string $countryCode = ''): string
    {
        if (empty($countryCode)) {
            return '';
        }

        $arrTranslations = $this->countries->getCountries();

        if (empty($arrTranslations[$countryCode])) {
            return '';
        }

        return $arrTranslations[$countryCode];
    }
}
