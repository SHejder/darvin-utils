<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2017, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\Routing;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Route cache warmer
 */
class RouteCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var \Darvin\Utils\Routing\CachedRouteManager
     */
    private $cachedRouteManager;

    /**
     * @param \Darvin\Utils\Routing\CachedRouteManager $cachedRouteManager Cached route manager
     */
    public function __construct(CachedRouteManager $cachedRouteManager)
    {
        $this->cachedRouteManager = $cachedRouteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $this->cachedRouteManager->cacheRoutes();
    }
}
