<?php

namespace App\Components\VideoEmbed\Twig;

use App\Components\VideoEmbed\VideoEmbedRenderer;

class VideoEmbedTwig extends \Twig_Extension
{
    private $videoEmbedRenderer;

    public function __construct(VideoEmbedRenderer $videoEmbedRenderer)
    {
        $this->videoEmbedRenderer = $videoEmbedRenderer;
    }

    public function getFilters()
    {
        return [
      new \Twig_SimpleFilter('video_embed_iframe', [$this, 'videoEmbedIframe'], ['is_safe' => ['html']]),
    ];
    }

    public function videoEmbedIframe($input)
    {
        return $this->videoEmbedRenderer->render($input);
    }
}
