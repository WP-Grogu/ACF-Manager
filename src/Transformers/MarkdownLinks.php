<?php

namespace Grogu\Acf\Transformers;

/**
 * Transforms a ACF link string into a video link represented by an array.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
class MarkdownLinks extends Transformer
{
    /**
     * The transformer action to execute.
     *
     * @return string
     */
    public function execute()
    {
        return $this->parseLinksFromText($this->value);
    }
    
    /**
     * Parse a string, searching for markdown format links changing them into html format.
     * eg: "[google](https://google.fr)" becomes "<a href="https://google.fr">google</a>"
     *
     * @param  string $text       String to parse
     * @param  bool   $clear_html Either if input should be cleared before parsing
     *
     * @return string Parsed string
     */
    public static function parseLinksFromText(string $text, bool $clear_html = true): string
    {
        $match_tag = "\[[a-zA-Z0-9áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\-\.\s]+\]";
        $match_url = "\((http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?\)";
    
        $match_association = "/" . $match_tag . $match_url . "/";
    
        if ($clear_html) {
            $text = htmlspecialchars($text);
        }
    
        if (preg_match($match_association, $text)) {
            $text = preg_replace_callback(
                $match_association,
                function ($matches) {
                    $match = $matches[0];
                    $match = ltrim($match, "[");
                    $match = rtrim($match, ")");
                    $match = explode('](', $match);
    
                    if (is_array($match) && count($match) === 2) {
                        return sprintf("<a href=\"%s\">%s</a>", $match[1], $match[0]);
                    }
                },
                $text
            );
        }
        return $text;
    }
}
