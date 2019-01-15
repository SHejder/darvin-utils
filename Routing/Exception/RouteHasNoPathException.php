<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\Routing\Exception;

/**
 * Route has no path exception
 */
class RouteHasNoPathException extends \Exception
{
    /**
     * @param string $routeName Route name
     * @param string $locale    Locale
     *
     * @return RouteHasNoPathException
     */
    public static function create($routeName, $locale)
    {
        return new self(sprintf('Route "%s" has no path for locale "%s".', $routeName, $locale));
    }
}