<?php

namespace EightPoints\Bundle\GuzzleBundle;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\GuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler\EventHandlerCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @version   1.0
 * @since     2013-10
 */
class GuzzleBundle extends Bundle
{
    /**
     * Build GuzzleBundle
     *
     * @version 1.0
     * @since   2013-10
     *
     * @param   ContainerBuilder $container
     * @return  void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EventHandlerCompilerPass());

    }

    /**
     * Overwrite getContainerExtension
     *  - no naming convention of alias needed
     *  - extension class can be moved easily now
     *
     * @see     getContainerExtension
     *
     * @version 1.1
     * @since   2013-12
     *
     * @return  ExtensionInterface|null The container extension
     * @throws  \LogicException
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {

            $extension = new GuzzleExtension();

            if (!$extension instanceof ExtensionInterface) {

                $message = sprintf('%s is not a instance of ExtensionInterface', $extension->getClass());

                throw new \LogicException($message);
            }

            $this->extension = $extension;
        }

        return $this->extension;
    }
}
