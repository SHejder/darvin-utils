<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\Transliteratable;

/**
 * Transliterator
 */
class Transliterator implements TransliteratorInterface
{
    private const REPLACEMENTS = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',  'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',  'н' => 'n', 'о' => 'o',  'п' => 'p',  'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',  'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh',
        'ъ' => '',  'ы' => 'y', 'ь' => '',  'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];

    /**
     * {@inheritdoc}
     */
    public function transliterate($text, bool $sanitize = true, array $allowedSymbols = [], string $separator = '-'): string
    {
        if (null === $text) {
            return '';
        }

        $text = (string)$text;

        if ('' === $text) {
            return '';
        }

        $transliterated = mb_strtolower($text);
        $transliterated = strtr($transliterated, self::REPLACEMENTS);
        $transliterated = \Transliterator::create('Latin-ASCII')->transliterate(\Transliterator::create('Any-Latin')->transliterate($transliterated));
        $transliterated = strtolower($transliterated);

        if (!$sanitize) {
            return $transliterated;
        }

        preg_match_all(sprintf('/[0-9a-zA-Z%s]+/', implode('', array_unique($allowedSymbols))), $transliterated, $matches);

        if (!isset($matches[0]) || empty($matches[0])) {
            return '';
        }

        return implode($separator, $matches[0]);
    }
}
