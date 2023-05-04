<?php

namespace App\Components\VideoEmbed;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class VideoEmbedManager
{
    private $annotationReader;

    private $providers = [];

    private $kernel;

    private $twig;

    public function __construct(Reader $annotation_reader, KernelInterface $kernel, \Twig_Environment $twig_Environment)
    {
        $this->kernel = $kernel;
        $this->annotationReader = $annotation_reader;
        $this->twig = $twig_Environment;
    }

    private function discovery()
    {
        $path = $this->kernel->getRootDir().'/Components/VideoEmbed/Providers';
        $finder = new Finder();
        $finder->files()->in($path);
        $namespace = 'App\Components\VideoEmbed\Providers';
        foreach ($finder as $file) {
            $class = $namespace.'\\'.$file->getBasename('.php');

            /** @var VideoEmbedProviderInterface $annotation */
            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), 'App\Components\VideoEmbed\VideoEmbedProvider');

            if (!$annotation) {
                continue;
            }
            $this->providers[$annotation->id()] = $class;
        }
    }

    /**
     * @return array
     *
     * @throws \ReflectionException
     */
    public function getProviders()
    {
        if (empty($this->providers)) {
            $this->discovery();
        }

        return $this->providers;
    }

    public function getProviderInput($input)
    {
        /** @var PluginProviderInterface $provider */
        foreach ($this->getProviders() as $provider) {
            if ($provider::getIdFromInput($input)) {
                $class = new $provider($input);

                return $class;
            }
        }

        return null;
    }

    public function checkProviderInput($input)
    {
        return !is_null($this->getProviderInput($input));
    }

    /*
     * @param PluginProviderInterface $provider
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
//  public function renderProvider(PluginProviderInterface $provider){
//    $options = $provider->renderEmbedCode(670, 380, false);
//    $template = $this->twig->render('VideoEmbed/iframe.html.twig', $options);
//    return $template;
//  }
}
