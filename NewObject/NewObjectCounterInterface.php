<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\NewObject;

/**
 * New object counter
 */
interface NewObjectCounterInterface
{
    /**
     * @param string $objectClass Object class
     *
     * @return int
     */
    public function count($objectClass);

    /**
     * @param string $objectClass Object class
     *
     * @return bool
     */
    public function isCountable($objectClass);
}
