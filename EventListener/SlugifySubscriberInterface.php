<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\EventListener;

/**
 * Slugify event subscriber
 */
interface SlugifySubscriberInterface
{
    /**
     * @param object $entity Entity
     */
    public function blacklistEntity($entity);
}