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

namespace Drupal\apigee_m10n\Entity\ListBuilder;

use Drupal\apigee_m10n\MonetizationInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class PrepaidBalanceListBuilderBase extends EntityListBuilder implements PrepaidBalanceListBuilderInterface, ContainerInjectionInterface {

  /**
   * The monetization service.
   *
   * @var \Drupal\apigee_m10n\MonetizationInterface
   */
  protected $monetization;

  /**
   * An array of prepaid balance entities.
   *
   * @var \Drupal\apigee_m10n\Entity\PrepaidBalanceInterface[]
   */
  protected $entities;

  /**
   * DeveloperPrepaidBalanceListBuilder constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   * @param \Drupal\apigee_m10n\MonetizationInterface $monetization
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, MonetizationInterface $monetization) {
    parent::__construct($entity_type, $storage);
    $this->monetization = $monetization;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_type = $container->get('entity_type.manager')
      ->getDefinition('developer_prepaid_balance');
    return static::createInstance($container, $entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage('developer_prepaid_balance'),
      $container->get('apigee_m10n.monetization')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    return [
      'currency' => $this->t('Account Currency'),
      'previous_balance' => $this->t('Previous Balance'),
      'credit' => $this->t('Credit'),
      'usage' => $this->t('Usage'),
      'tax' => $this->t('Tax'),
      'current_balance' => $this->t('Current Balance'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\apigee_m10n\Entity\PrepaidBalanceInterface $entity */
    $currency_code = $entity->getCurrency()->getName();
    return [
      'currency' => $currency_code,
      'previous_balance' => $this->formatCurrency($entity->getPreviousBalance(), $currency_code),
      'credit' => $this->formatCurrency($entity->getTopUps(), $currency_code),
      'usage' => $this->formatCurrency($entity->getUsage(), $currency_code),
      'tax' => $this->formatCurrency($entity->getTax(), $currency_code),
      'current_balance' => $this->formatCurrency($entity->getCurrentBalance(), $currency_code),
    ];
  }

  /**
   * Format an amount using the `monetization` service.
   *
   * See: \Drupal\apigee_m10n\MonetizationInterface::formatCurrency().
   *
   * @param string $amount
   *   The money amount.
   * @param string $currency_code
   *   Currency code.
   *
   * @return string
   *   The formatted amount as a string.
   */
  protected function formatCurrency($amount, $currency_code) {
    return $this->monetization->formatCurrency($amount, $currency_code);
  }

  /**
   * {@inheritdoc}
   */
  public function withEntities(array $entities): PrepaidBalanceListBuilderInterface {
    $this->entities = $entities;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    if (! count($this->entities)) {
      throw new EntityStorageException('Prepaid balances cannot be loaded directly. Use ::withEntities to set the list builder entities.');
    }

    return $this->entities;
  }

}
