<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\Utils\EventListener;

use Darvin\Utils\Event\SlugsUpdateEvent;
use Darvin\Utils\Mapping\MetadataFactoryInterface;
use Darvin\Utils\Slug\SlugException;
use Darvin\Utils\Slug\SlugHandlerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Slug event subscriber
 */
class SlugSubscriber extends AbstractOnFlushListener implements EventSubscriber
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Darvin\Utils\Mapping\MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \Darvin\Utils\Slug\SlugHandlerInterface[]
     */
    private $slugHandlers;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher  Event dispatcher
     * @param \Darvin\Utils\Mapping\MetadataFactoryInterface              $metadataFactory  Metadata factory
     * @param \Symfony\Component\PropertyAccess\PropertyAccessorInterface $propertyAccessor Property accessor
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        MetadataFactoryInterface $metadataFactory,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->metadataFactory = $metadataFactory;
        $this->propertyAccessor = $propertyAccessor;
        $this->slugHandlers = array();
    }

    /**
     * @param \Darvin\Utils\Slug\SlugHandlerInterface $slugHandler Slug handler
     */
    public function addSlugHandler(SlugHandlerInterface $slugHandler)
    {
        $this->slugHandlers[] = $slugHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        parent::onFlush($args);

        $updateSlugsCallback = array($this, 'updateSlugs');

        $this
            ->onInsert($updateSlugsCallback)
            ->onUpdate($updateSlugsCallback);
    }

    /**
     * @param object $entity    Entity
     * @param string $operation Operation type
     */
    protected function updateSlugs($entity, $operation)
    {
        $meta = $this->metadataFactory->getMetadata($this->em->getClassMetadata(ClassUtils::getClass($entity)));

        if (!isset($meta['slugs']) || empty($meta['slugs'])) {
            return;
        }

        $slugsChangeSet = array();

        foreach ($meta['slugs'] as $slugProperty => $params) {
            $sourcePropertyPaths = $params['sourcePropertyPaths'];

            $oldSlug = $this->getPropertyValue($entity, $slugProperty);

            $slugParts = $this->getSlugParts($entity, $slugProperty, $sourcePropertyPaths);

            $newSlug = $originalNewSlug = implode($params['separator'], $slugParts);
            $slugSuffix = $slugParts[count($slugParts) - 1];

            foreach ($this->slugHandlers as $slugHandler) {
                $slugHandler->handle($newSlug, $slugSuffix, $this->em);
            }
            if ($newSlug === $oldSlug) {
                continue;
            }

            $slugsChangeSet[$oldSlug] = $newSlug;

            if ($newSlug !== $originalNewSlug) {
                $suffixPropertyPath = $sourcePropertyPaths[count($sourcePropertyPaths) - 1];
                $this->setPropertyValue($entity, $suffixPropertyPath, $slugSuffix);
            }

            $this->setPropertyValue($entity, $slugProperty, $newSlug);
        }
        if (empty($slugsChangeSet)) {
            return;
        }

        $this->recomputeChangeSet($entity);

        if (AbstractOnFlushListener::OPERATION_UPDATE === $operation) {
            $this->eventDispatcher->dispatch(
                \Darvin\Utils\Event\Events::POST_SLUGS_UPDATE,
                new SlugsUpdateEvent($slugsChangeSet, $this->em)
            );
        }
    }

    /**
     * @param object $entity              Entity
     * @param string $slugProperty        Slug property
     * @param array  $sourcePropertyPaths Source property paths
     *
     * @return array
     * @throws \Darvin\Utils\Slug\SlugException
     */
    private function getSlugParts($entity, $slugProperty, array $sourcePropertyPaths)
    {
        $slugParts = array();

        foreach ($sourcePropertyPaths as $propertyPath) {
            if (false !== strpos($propertyPath, '.')) {
                $related = $this->getPropertyValue($entity, preg_replace('/\..*/', '', $propertyPath));

                if (empty($related)) {
                    continue;
                }
            }

            $slugPart = $this->getPropertyValue($entity, $propertyPath);

            if (!empty($slugPart)) {
                $slugParts[] = $slugPart;
            }
        }
        if (empty($slugParts)) {
            $message = sprintf(
                'Unable to generate slug "%s::$%s": unable to get any non empty slug parts using property paths "%s".',
                ClassUtils::getClass($entity),
                $slugProperty,
                implode('", "', $sourcePropertyPaths)
            );

            throw new SlugException($message);
        }

        return $slugParts;
    }

    /**
     * @param object $entity       Entity
     * @param string $propertyPath Property path
     * @param mixed  $value        Value
     *
     * @throws \Darvin\Utils\Slug\SlugException
     */
    private function setPropertyValue($entity, $propertyPath, $value)
    {
        if (!$this->propertyAccessor->isWritable($entity, $propertyPath)) {
            throw new SlugException(
                sprintf('Property "%s::$%s" is not writable.', ClassUtils::getClass($entity), $propertyPath)
            );
        }

        $this->propertyAccessor->setValue($entity, $propertyPath, $value);
    }

    /**
     * @param object $entity       Entity
     * @param string $propertyPath Property path
     *
     * @return mixed
     * @throws \Darvin\Utils\Slug\SlugException
     */
    private function getPropertyValue($entity, $propertyPath)
    {
        if (!$this->propertyAccessor->isReadable($entity, $propertyPath)) {
            throw new SlugException(
                sprintf('Property "%s::$%s" is not readable.', ClassUtils::getClass($entity), $propertyPath)
            );
        }

        return $this->propertyAccessor->getValue($entity, $propertyPath);
    }
}
