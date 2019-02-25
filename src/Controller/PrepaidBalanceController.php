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

namespace Drupal\apigee_m10n\Controller;

use Drupal\apigee_m10n\Entity\ListBuilder\PrepaidBalanceListBuilderInterface;
use Drupal\apigee_m10n\Entity\Storage\DeveloperPrepaidBalanceStorageInterface;
use Drupal\apigee_m10n\Form\PrepaidBalanceConfigForm;
use Drupal\apigee_m10n\Form\PrepaidBalanceRefreshForm;
use Drupal\apigee_m10n\Form\PrepaidBalanceReportsDownloadForm;
use Drupal\apigee_m10n\MonetizationInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for billing related routes.
 */
class PrepaidBalanceController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Cache prefix that is used for cache tags for this controller.
   */
  const CACHE_PREFIX = 'apigee.monetization.prepaid_balance';

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Apigee Monetization utility service.
   *
   * @var \Drupal\apigee_m10n\MonetizationInterface
   */
  protected $monetization;

  /**
   * The developer prepaid balance storage.
   *
   * @var \Drupal\apigee_m10n\Entity\Storage\DeveloperPrepaidBalanceStorageInterface
   */
  protected $prepaidBalanceStorage;

  /**
   * The prepaid balance list builder.
   *
   * @var \Drupal\apigee_m10n\Entity\ListBuilder\PrepaidBalanceListBuilderInterface
   */
  protected $listBuilder;

  /**
   * BillingController constructor.
   *
   * @param \Drupal\apigee_m10n\MonetizationInterface $monetization
   *   The `apigee_m10n.monetization` service.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\apigee_m10n\Entity\Storage\DeveloperPrepaidBalanceStorageInterface $prepaid_balance_storage
   *   The developer prepaid balance storage.
   * @param \Drupal\apigee_m10n\Entity\ListBuilder\PrepaidBalanceListBuilderInterface $list_builder
   *   The prepaid balance list builder.
   */
  public function __construct(MonetizationInterface $monetization, FormBuilderInterface $form_builder, AccountInterface $current_user, DeveloperPrepaidBalanceStorageInterface $prepaid_balance_storage, PrepaidBalanceListBuilderInterface $list_builder) {
    $this->monetization = $monetization;
    $this->currentUser = $current_user;
    $this->formBuilder = $form_builder;
    $this->prepaidBalanceStorage = $prepaid_balance_storage;
    $this->listBuilder = $list_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('apigee_m10n.monetization'),
      $container->get('form_builder'),
      $container->get('current_user'),
      $container->get('entity_type.manager')
        ->getStorage('developer_prepaid_balance'),
      $container->get('entity_type.manager')
        ->getListBuilder('developer_prepaid_balance')
    );
  }

  /**
   * Redirect to the user's prepaid balances page.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Gets a redirect to the users's balance page.
   */
  public function myPrepaidBalance(): RedirectResponse {
    return $this->redirect(
      'apigee_monetization.billing',
      ['user' => $this->currentUser->id()],
      ['absolute' => TRUE]
    );
  }

  /**
   * View prepaid balance and account statements, add money to prepaid balance.
   *
   * @param \Drupal\user\UserInterface $user
   *   The Drupal user.
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *   A render array or a redirect response.
   *
   * @throws \Exception
   */
  public function prepaidBalancePage(UserInterface $user) {
    $build = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['apigee-m10n-prepaid-balance-wrapper'],
      ],
      '#attached' => [
        'library' => [
          'apigee_m10n/prepaid_balance',
        ],
      ],
    ];

    if ($prepaid_balances = $this->prepaidBalanceStorage->loadByDeveloperId($user->getEmail())) {
      $build['prepaid_balances'] = $this->listBuilder->withEntities($prepaid_balances)
        ->render();
    }

    // Cache the render array if enabled.
    if ($max_age = $this->getCacheMaxAge()) {
      $build['prepaid_balances']['#cache'] = [
        'contexts' => ['url.path'],
        'tags' => $this->getCacheTags($user),
        'max-age' => $max_age,
        'keys' => [static::getCacheId($user, 'prepaid_balances')],
      ];
    }

    // Add a refresh cache form.
    if ($this->canRefreshBalance($user)) {
      $build['refresh_form'] = $this->formBuilder()
        ->getForm(PrepaidBalanceRefreshForm::class, $this->getCacheTags($user));
    }

    // Show the prepaid balance reports download form.
    if ($this->currentUser->hasPermission('download prepaid balance reports')) {
      $supported_currencies = $this->getDataFromCache($user, 'supported_currencies', function () {
        return $this->monetization->getSupportedCurrencies();
      });

      $billing_documents = $this->getDataFromCache($user, 'billing_documents', function () {
        return $this->monetization->getBillingDocumentsMonths();
      });

      // Build the form.
      $build['download_form'] = $this->formBuilder->getForm(PrepaidBalanceReportsDownloadForm::class, $user, $supported_currencies, $billing_documents);
      $build['download_form']['#cache']['keys'] = [static::getCacheId($user, 'download_form')];
    }

    return $build;
  }

  /**
   * Returns the cache max age.
   *
   * @return int
   *   The cache max age.
   */
  protected function getCacheMaxAge() {
    // Get the max-age from config.
    if ($config = $this->config(PrepaidBalanceConfigForm::CONFIG_NAME)) {
      return $config->get('cache.max_age');
    }

    return 0;
  }

  /**
   * Helper to check if user has access to refresh prepaid balance.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   *
   * @return bool
   *   TRUE is user can refresh balance.
   */
  protected function canRefreshBalance(UserInterface $user) {
    // @TODO Figure out why AccessResult is not working here.
    return $this->currentUser->hasPermission('refresh any prepaid balance') ||
      ($this->currentUser->hasPermission('refresh own prepaid balance') && $this->currentUser->id() === $user->id());
  }

  /**
   * Helper to retrieve data from cache.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $suffix
   *   The cache id suffix.
   * @param callable $callback
   *   The callback if not in cache.
   *
   * @return mixed
   *   The data.
   */
  protected function getDataFromCache(EntityInterface $entity, string $suffix, callable $callback) {
    $max_age = $this->getCacheMaxAge();

    // If caching is disable, run callback and return.
    if ($max_age == 0) {
      return $callback();
    }

    $cid = $this->getCacheId($entity, $suffix);

    // Check cache.
    if ($cache = $this->cache()->get($cid)) {
      return $cache->data;
    }

    $data = $callback();
    $this->cache()
      ->set($cid, $data, time() + $max_age, $this->getCacheTags($entity));

    return $data;
  }

  /**
   * Helper to get the billing cache tags.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return array
   *   The cache tags.
   */
  public static function getCacheTags(EntityInterface $entity) {
    return [
      static::CACHE_PREFIX,
      static::getCacheId($entity),
    ];
  }

  /**
   * Helper to get the cache id.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param null $suffix
   *   The suffix for the cache id.
   *
   * @return string
   *   The cache id.
   */
  public static function getCacheId(EntityInterface $entity, $suffix = NULL) {
    return static::CACHE_PREFIX . ":{$entity->getEntityTypeId()}:{$entity->id()}" . ($suffix ? ":{$suffix}" : '');
  }

}
