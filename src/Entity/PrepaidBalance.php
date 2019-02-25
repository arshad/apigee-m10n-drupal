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

namespace Drupal\apigee_m10n\Entity;

use Apigee\Edge\Api\Monetization\Entity\PrepaidBalance as ApiPrepaidBalance;
use Apigee\Edge\Api\Monetization\Entity\SupportedCurrencyInterface;
use Apigee\Edge\Entity\EntityInterface;
use Drupal\apigee_edge\Entity\EdgeEntityBase;

/**
 * Base class for prepaid balance entity.
 */
abstract class PrepaidBalance extends EdgeEntityBase implements PrepaidBalanceInterface {

  /**
   * PrepaidBalance constructor.
   *
   * @param array $values
   * @param string|null $entity_type
   * @param EntityInterface|null $decorated
   *
   * @throws \ReflectionException
   */
  public function __construct(array $values, ?string $entity_type, ?EntityInterface $decorated = NULL) {
    /** @var \Apigee\Edge\Api\Management\Entity\DeveloperAppInterface $decorated */
    $entity_type = $entity_type ?? $this->getDrupalEntityTypeId();
    parent::__construct($values, $entity_type, $decorated);
  }

  /**
   * {@inheritdoc}
   */
  protected static function decoratedClass(): string {
    return ApiPrepaidBalance::class;
  }

  /**
   * {@inheritdoc}
   */
  protected function drupalEntityId(): ?string {
    return $this->decorated->id();
  }

  /**
   * {@inheritdoc}
   */
  public static function idProperty(): string {
    return ApiPrepaidBalance::idProperty();
  }

  /**
   * {@inheritdoc}
   */
  public function getApproxTaxRate(): int {
    return $this->decorated->getApproxTaxRate();
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentBalance(): float {
    return $this->decorated->getCurrentBalance();
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentTotalBalance(): float {
    return $this->decorated->getCurrentTotalBalance();
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentUsage(): float {
    return $this->decorated->getCurrentUsage();
  }

  /**
   * {@inheritdoc}
   */
  public function getMonth(): string {
    return $this->decorated->getMonth();
  }

  /**
   * {@inheritdoc}
   */
  public function getPreviousBalance(): float {
    return $this->decorated->getPreviousBalance();
  }

  /**
   * {@inheritdoc}
   */
  public function getTax(): float {
    return $this->decorated->getTax();
  }

  /**
   * {@inheritdoc}
   */
  public function getTopUps(): float {
    return $this->decorated->getTopUps();
  }

  /**
   * {@inheritdoc}
   */
  public function getUsage(): float {
    return $this->decorated->getUsage();
  }

  /**
   * {@inheritdoc}
   */
  public function getYear(): int {
    return $this->decorated->getYear();
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrency(): SupportedCurrencyInterface {
    return $this->decorated->getCurrency();
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrency(SupportedCurrencyInterface $currency): void {
    $this->decorated->setCurrency($currency);
  }

  /**
   * {@inheritdoc}
   */
  public function getId(): ?string {
    return $this->decorated->getId();
  }

}
