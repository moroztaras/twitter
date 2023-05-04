<?php

namespace App\Components\VideoEmbed;

interface PluginProviderInterface
{
    public static function getIdFromInput($input);

    public function renderEmbedCode($width, $height, $autoplay);
}
