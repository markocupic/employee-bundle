<?php

/**
 * SAC Pilatus web plugin
 * Copyright (c) 2008-2017 Marko Cupic
 * @package sacpilatus-bundle
 * @author Marko Cupic m.cupic@gmx.ch, 2017
 * @link    https://sac-kurse.kletterkader.com
 */

namespace Markocupic\SacpilatusBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Automator;


/**
 * Class InitializeSystem
 * @package Markocupic\SacpilatusBundle\EventListener
 */
class InitializeSystem
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;


    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Clear Script Cache when developing
     */
    public function purgeScriptCache()
    {
        $objAutomator = $this->framework->createInstance(Automator::class);
        $objAutomator->purgeScriptCache();
    }
}