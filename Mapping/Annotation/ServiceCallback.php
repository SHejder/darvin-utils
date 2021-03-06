<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Service callback annotation
 *
 * @Annotation
 * @Target("ANNOTATION")
 */
final class ServiceCallback
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $method;
}
