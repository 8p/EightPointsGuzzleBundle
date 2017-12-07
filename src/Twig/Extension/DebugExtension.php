<?php

namespace EightPoints\Bundle\GuzzleBundle\Twig\Extension;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class DebugExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction(
                'eight_points_guzzle_dump',
                [$this, 'dump'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $env
     * @param $value
     *
     * @throws \Exception
     *
     * @return bool|string
     */
    public function dump(\Twig_Environment $env, $value)
    {
        $cloner = new VarCloner();

        $dump = fopen('php://memory', 'r+b');
        $dumper = new HtmlDumper($dump, $env->getCharset());

        $dumper->dump($cloner->cloneVar($value));
        rewind($dump);

        return stream_get_contents($dump);
    }
}
