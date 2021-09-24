<?php

namespace Grogu\Acf\Transformers;

use Grogu\Acf\FieldSet;

class VideoLink extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return FieldSet
     */
    public function execute()
    {
        $link = $this->value;

        $defaults = [
            'original' => $link,
            'embed'    => '',
            'platform' => '',
            'id'       => '',
        ];

        if (preg_match('/^.*(youtu.be\/|v\/|e\/|u\/\w+\/|embed\/|v=)([^#\&\?]*).*/', $link, $matches)) {
            return new FieldSet([
                'embed'    => sprintf('https://www.youtube.com/embed/%s', $matches[2]),
                'platform' => 'youtube',
                'id'       => $matches[2],
            ] + $defaults);
        }

        if (preg_match('/^.*(vimeo.com\/)([^#\&\?]*).*/', $link, $matches)) {
            return new FieldSet([
                'embed'    => sprintf('https://player.vimeo.com/video/%s', $matches[2]),
                'platform' => 'vimeo',
                'id'       => $matches[2],
            ] + $defaults);
        }

        return new FieldSet($defaults);
    }
}
