<?php

namespace EightPoints\Bundle\GuzzleBundle\Twig\Extension;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DebugExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'eight_points_guzzle_dump',
                [$this, 'dump'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @return bool|string
     *
     * @throws \Exception
     */
    public function dump(Environment $env, $value)
    {
        $cloner = new VarCloner();

        $dump = fopen('php://memory', 'r+b');
        $dumper = new HtmlDumper($dump, $env->getCharset());

        $dumper->dump($cloner->cloneVar($value));
        rewind($dump);

        return stream_get_contents($dump);
    }

    /**
     * This method is removed from interface in Twig v2.0
     *
     * @TODO Remove this method when drop support of Symfony < 5.0
     */
    public function getName(): string
    {
        return get_class($this);
    }
}
