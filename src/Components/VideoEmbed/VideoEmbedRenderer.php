<?php

namespace App\Components\VideoEmbed;

class VideoEmbedRenderer
{
    public function __construct(
        private \Twig_Environment $twig,
        private VideoEmbedManager $videoEmbedManager)
    {
    }

    /**
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
        $template = $this->twig->render('web/videoEmbed/iframe.html.twig', $options);

        return $template;
    }
}
