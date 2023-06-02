<?php

namespace App\Components\VideoEmbed\Providers;

use App\Components\VideoEmbed\PluginProviderInterface;
use App\Components\VideoEmbed\VideoEmbedProvider;

/**
 * Class Facebook.
 *
 * @VideoEmbedProvider(
 *   id = "facebook"
 * )
 */
class Facebook implements PluginProviderInterface
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
        preg_match('/^https?:\/\/(www\.)?facebook.com\/([\w-\.]*\/videos\/|video\.php\?v\=)(?<id>[0-9]*)\/?$/', $input, $matches);

        return isset($matches['id']) ? $matches['id'] : false;
    }

    public function renderEmbedCode($width, $height, $autoplay)
    {
        return [
      'provider' => 'facebook',
      'url' => sprintf('https://www.facebook.com/video/embed?video_id=%s', $this->id, $autoplay),
//      'url' => sprintf('https://www.facebook.com/plugins/video.php?href=%s', $this->input),
      'attributes' => [
        'width' => $width,
        'height' => $height,
        'frameborder' => '0',
        'allowfullscreen' => 'allowfullscreen',
        'style' => 'border:none;overflow:hidden',
      ],
    ];
    }
}
