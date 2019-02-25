<?php

/*
 * Copyright 2018 Google Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2 as published by the
 * Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNUnn General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

namespace Drupal\apigee_m10n\Entity\Storage;

use Drupal\apigee_edge\Entity\Controller\EdgeEntityControllerInterface;
use Drupal\apigee_edge\Entity\Storage\EdgeEntityStorageBase;
use Drupal\apigee_m10n\Entity\Storage\Controller\DeveloperPrepaidBalanceSdkControllerProxyInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Storage handler for prepaid balance entity.
 */
class DeveloperPrepaidBalanceStorage extends EdgeEntityStorageBase implements DeveloperPrepaidBalanceStorageInterface {

  /**
   * The controller proxy.
   *
   * @var \Drupal\apigee_edge\Entity\Controller\EdgeEntityControllerInterface
   */
  protected $controllerProxy;

  /**
   * DeveloperPrepaidBalanceStorage constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend to be used.
   * @param \Drupal\Core\Cache\MemoryCache\MemoryCacheInterface $memory_cache
   *   The memory cache.
   * @param \Drupal\Component\Datetime\TimeInterface $system_time
   *   The system time.
   * @param \Drupal\apigee_m10n\Entity\Storage\Controller\DeveloperPrepaidBalanceSdkControllerProxyInterface $controller_proxy
   *   The controller proxy.
   */
  public function __construct(EntityTypeInterface $entity_type, CacheBackendInterface $cache_backend, MemoryCacheInterface $memory_cache, TimeInterface $system_time, DeveloperPrepaidBalanceSdkControllerProxyInterface $controller_proxy) {
    parent::__construct($entity_type, $cache_backend, $memory_cache, $system_time);
    $this->controllerProxy = $controller_proxy;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('cache.apigee_edge_entity'),
      $container->get('entity.memory_cache'),
      $container->get('datetime.time'),
      $container->get('apigee_m10n.sdk_controller_proxy.developer_prepaid_balance')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function entityController(): EdgeEntityControllerInterface {
    return $this->controllerProxy;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function loadByDeveloperId(string $developer_id, \DateTimeImmutable $billingDate = NULL): array {
    // Load from cache.
    $ids = [$developer_id];
    if ($entities = $this->getFromPersistentCache($ids)) {
      return $entities[$developer_id];
    }

    // Set default for billing date.
    $billingDate = $billingDate ?? new \DateTimeImmutable('now');

    $entities = [];
    $this->withController(function (DeveloperPrepaidBalanceSdkControllerProxyInterface $controller) use ($developer_id, $billingDate, &$entities) {
      $sdk_entities = $controller->loadByDeveloperId($developer_id, $billingDate);
      // Convert the SDK entities to drupal entities.
      foreach ($sdk_entities as $sdk_entity) {
        $drupal_entity = $this->createNewInstance($sdk_entity);
        $entities[$drupal_entity->id()] = $drupal_entity;
      }
      $this->invokeStorageLoadHook($entities);
      $this->setPersistentCacheByOwner($developer_id, $entities);
    });

    return $entities;
  }

  protected function setPersistentCacheByOwner(string $owner_id, array $entities) {
    if (!$this->entityType->isPersistentlyCacheable()) {
      return;
    }

    $cache_tags = [];
    foreach ($entities as $entity) {
      $cache_tags = array_merge($cache_tags, $this->getPersistentCacheTagsByOwner($owner_id, $entity));
    }

    $this->cacheBackend->set($this->buildCacheId($owner_id), $entities, $this->getPersistentCacheExpiration(),$cache_tags);
  }

  /**
   * {@inheritdoc}
   */
  public function getPersistentCacheTagsByOwner(string $owner_id, EntityInterface $entity): array {
    return array_merge($this->getPersistentCacheTags($entity), [
      "{$this->entityTypeId}:{$owner_id}",
    ]);
  }

}
