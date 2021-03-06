<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\Mapping\AnnotationDriver;

use Darvin\Utils\Mapping\Annotation\Transliteratable;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

/**
 * Transliteratable annotation driver
 */
class TransliteratableDriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function readMetadata(ClassMetadata $doctrineMeta, array &$meta)
    {
        if (!isset($meta['transliteratable'])) {
            $meta['transliteratable'] = [];
        }
        foreach ($doctrineMeta->getReflectionClass()->getProperties() as $reflectionProperty) {
            $transliteratableAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, Transliteratable::class);

            if ($transliteratableAnnotation instanceof Transliteratable) {
                if (!$doctrineMeta->hasField($reflectionProperty->getName())) {
                    throw $this->createPropertyAnnotationInvalidException(
                        Transliteratable::class,
                        $doctrineMeta->getName(),
                        $reflectionProperty->getName(),
                        'property must be mapped field'
                    );
                }

                $meta['transliteratable'][$reflectionProperty->getName()] = get_object_vars(
                    $transliteratableAnnotation
                );
            }
        }
    }
}
