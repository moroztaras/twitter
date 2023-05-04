<?php

namespace App\Components\VideoEmbed;

/**
 * @Annotation
 */
class VideoEmbedProvider implements VideoEmbedProviderInterface
{
    public $id;

    public function id()
    {
        return $this->id;
    }
}
