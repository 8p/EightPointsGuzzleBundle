<?php

namespace EightPoints\Bundle\GuzzleBundle;

use       Symfony\Component\HttpKernel\Bundle\Bundle,
          Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class GuzzleBundle
 *
 * @package   Webinterface\GuzzleBundle
 *
 * @copyright 8points IT
 * @author    Florian Preusner
 *
 * @version   1.0
 * @since     2013-10
 */
class GuzzleBundle extends Bundle {

    /**
     * Build GuzzleBundle
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   ContainerBuilder $container
     * @return  void
     */
    public function build(ContainerBuilder $container) {

        parent::build($container);
    } // end: build
} // end: GuzzleBundle