<?php

namespace App\Components\VideoEmbed\Providers;

use App\Components\VideoEmbed\PluginProviderInterface;
use App\Components\VideoEmbed\VideoEmbedProvider;

/**
 * Class Youtube.
 *
 * @VideoEmbedProvider(
 *   id = "youtube"
 * )
 */
class Youtube implements PluginProviderInterface
{
    private $id;

    private $input;

    public function __construct($input)
    {
        if (!static::getIdFromInput($input)) {
            throw new \Exception('Tried to create a video provider plugin with invalid input.');
        }
        $this->input = $input;
        $this->id = static::getIdFromInput($input);
    }

    public static function getIdFromInput($input)
    {
        preg_match('/^https?:\/\/(www\.)?((?!.*list=)youtube\.com\/watch\?.*v=|youtu\.be\/)(?<id>[0-9A-Za-z_-]*)/', $input, $matches);

        return isset($matches['id']) ? $matches['id'] : false;
    }

    public function renderEmbedCode($width = 670, $height = 380, $autoplay = false)
    {
        $embed_code = [
      'provider' => 'youtube',
      'url' => sprintf('https://www.youtube.com/embed/%s', $this->id),
      'query' => [
        'autoplay' => $autoplay,
        'rel' => '0',
      ],
      'attributes' => [
        'width' => $width,
        'height' => $height,
        'frameborder' => '0',
        'allowfullscreen' => 'allowfullscreen',
      ],
    ];

        return $embed_code;
    }
}
