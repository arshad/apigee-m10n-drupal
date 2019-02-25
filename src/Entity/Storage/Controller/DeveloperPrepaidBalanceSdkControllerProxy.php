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
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public
 * License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

namespace Drupal\apigee_m10n\Entity\Storage\Controller;

use Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface;
use Apigee\Edge\Entity\EntityInterface;
use Drupal\apigee_m10n\ApigeeSdkControllerFactoryInterface;

/**
 * The developer prepaid balance sdk controller service.
 *
 * Responsible for proxying calls to the appropriate package rate plan
 * controllers. Rate plan controllers require a package ID for instantiation so
 * we sometimes need to get a controller at runtime for a given rate plan.
 */
class DeveloperPrepaidBalanceSdkControllerProxy implements DeveloperPrepaidBalanceSdkControllerProxyInterface {

  /**
   * @var \Drupal\apigee_m10n\ApigeeSdkControllerFactoryInterface
   */
  protected $sdkControllerFactory;

  /**
   * DeveloperPrepaidBalanceSdkControllerProxy constructor.
   *
   * @param \Drupal\apigee_m10n\ApigeeSdkControllerFactoryInterface $sdk_controller_factory
   *   The SDK controller factory.
   */
  public function __construct(ApigeeSdkControllerFactoryInterface $sdk_controller_factory) {
    $this->sdkControllerFactory = $sdk_controller_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function loadByDeveloperId(string $developer_id, \DateTimeImmutable $billingDate): array {
    return $this->sdkControllerFactory->developerBalanceController($developer_id)->getPrepaidBalance($billingDate);
  }

  public function create(EntityInterface $entity): void {
    // TODO: Implement create() method.
  }

  public function load(string $id): EntityInterface {
    // TODO: Implement load() method.
  }

  public function update(EntityInterface $entity): void {
    // TODO: Implement update() method.
  }

  public function delete(string $id): void {
    // TODO: Implement delete() method.
  }

  public function loadAll(): array {
    // TODO: Implement loadAll() method.
  }


}
