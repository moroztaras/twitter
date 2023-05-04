<?php

namespace App\Components\VideoEmbed;

class VideoEmbedRenderer
{
    /**
     * @var \Twig_Environment;
     */
    private $twig;

    private $videoEmbedManager;

    public function __construct(\Twig_Environment $twig, VideoEmbedManager $videoEmbedManager)
    {
        $this->twig = $twig;
        $this->videoEmbedManager = $videoEmbedManager;
    }

    /**
     * @param $input
     *
     * @return string
     *
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($input)
    {
        $provider = $this->videoEmbedManager->getProviderInput($input);
        $options = $provider->renderEmbedCode(670, 380, false);
        $template = $this->twig->render('VideoEmbed/iframe.html.twig', $options);

        return $template;
    }
}
