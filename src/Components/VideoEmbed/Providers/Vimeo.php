<?php

namespace App\Components\VideoEmbed\Providers;

use App\Components\VideoEmbed\PluginProviderInterface;
use App\Components\VideoEmbed\VideoEmbedProvider;

/**
 * Class Vimeo.
 *
 * @VideoEmbedProvider(
 *   id = "vimeo"
 * )
 */
class Vimeo implements PluginProviderInterface
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
        preg_match('/^https?:\/\/(www\.)?vimeo.com\/(channels\/[a-zA-Z0-9]*\/)?(?<id>[0-9]*)(\/[a-zA-Z0-9]+)?(\#t=(\d+)s)?$/', $input, $matches);

        return isset($matches['id']) ? $matches['id'] : false;
    }

    public function renderEmbedCode($width, $height, $autoplay)
    {
        $iframe = [
      'provider' => 'vimeo',
      'url' => sprintf('https://player.vimeo.com/video/%s', $this->id),
      'query' => [
        'autoplay' => $autoplay,
      ],
      'attributes' => [
        'width' => $width,
        'height' => $height,
        'frameborder' => '0',
        'allowfullscreen' => 'allowfullscreen',
      ],
    ];

        return $iframe;
    }
}
