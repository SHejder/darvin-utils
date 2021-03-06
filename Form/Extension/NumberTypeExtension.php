<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\Form\Extension;

use Darvin\Utils\Form\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Number form type extension
 */
class NumberTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetViewTransformers();

        $builder->addViewTransformer(new NumberToLocalizedStringTransformer(
            $options['scale'],
            $options['grouping'],
            $options['rounding_mode']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return NumberType::class;
    }
}
