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


namespace Drupal\apigee_m10n;

use Apigee\Edge\Api\Monetization\Entity\CompanyInterface;
use Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface;
use Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\RoleInterface;
use Drupal\user\UserInterface;
use Drupal\apigee_m10n\Entity\RatePlanInterface;

/**
 * Interface MonetizationInterface.
 */
interface MonetizationInterface {

  /**
   * A list of permissions that will be given to authenticated users on install.
   */
  const DEFAULT_AUTHENTICATED_PERMISSIONS = [
    'view package',
    'view rate_plan',
    'subscribe rate_plan',
    'view own purchased_plan',
    'update own purchased_plan',
    'view own prepaid balance',
    'refresh own prepaid balance',
    'download prepaid balance reports',
    'view own billing details',
  ];

  /**
   * Tests whether the current organization has monetization enabled.
   *
   * A monitization enabled org is a requirement for using this module.
   *
   * @return bool
   *   Whether or not monetization is enabled.
   */
  public function isMonetizationEnabled(): bool;

  /**
   * Checks access to a product for a given account.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The 'api_product'  entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   Whether or not the user has access to the entity.
   */
  public function apiProductAssignmentAccess(EntityInterface $entity, AccountInterface $account): AccessResultInterface;

  /**
   * Get's the prepaid balance for a developer.
   *
   * Takes in a developer UUID or email address, and a date specifying the
   * report month and year, and returns an array of prepaid balances.
   *
   * @param \Drupal\user\UserInterface $developer
   *   The developer user.
   * @param \DateTimeImmutable $billingDate
   *   The date for the billing report.
   *
   * @return \Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface[]|null
   *   The balance list or null if no balances are available.
   */
  public function getDeveloperPrepaidBalances(UserInterface $developer, \DateTimeImmutable $billingDate): ?array;

  /**
   * Get's the prepaid balance for a team.
   *
   * Takes in a company name, and a date specifying the report month and year,
   * and returns an array of prepaid balances.
   *
   * @param \Apigee\Edge\Api\Monetization\Entity\CompanyInterface $company
   *   The team.
   * @param \DateTimeImmutable $billingDate
   *   The date for the billing report.
   *
   * @return \Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface[]|null
   *   The balance list or null if no balances are available.
   */
  public function getCompanyPrepaidBalances(CompanyInterface $company, \DateTimeImmutable $billingDate): ?array;

  /**
   * Format an amount using the `CommerceGuys\Intl` library.
   *
   * Use the commerceguys internationalization library to format a currency
   * based on a currency id.
   *
   * @param string $amount
   *   The money amount.
   * @param string $currency_id
   *   Currency ID as defined by `commerceguys/intl`.
   *
   * @see \CommerceGuys\Intl\Currency\CurrencyRepository::getBaseDefinitions
   *
   * @return string
   *   The formatted amount as a string.
   */
  public function formatCurrency(string $amount, string $currency_id): string;

  /**
   * Get supported currencies for an organization.
   *
   * @return array
   *   An array of supported currency entities.
   */
  public function getSupportedCurrencies(): ?array;

  /**
   * Get the billing documents months for an organization.
   *
   * @return array|null
   *   An array of billing documents.
   */
  public function getBillingDocumentsMonths(): ?array;

  /**
   * Returns a CSV string for prepaid balances.
   *
   * @param string $developer_id
   *   The developer id.
   * @param \DateTimeImmutable $month
   *   The month for the prepaid balances.
   * @param string $currency
   *   The currency id. Example: usd.
   *
   * @return null|string
   *   A CSV string of prepaid balances.
   */
  public function getPrepaidBalanceReports(string $developer_id, \DateTimeImmutable $month, string $currency): ?string;

  /**
   * Check if developer accepted latest terms and conditions.
   *
   * @param string $developer_id
   *   Developer ID.
   *
   * @return bool|null
   *   User terms and conditions acceptance flag.
   */
  public function isLatestTermsAndConditionAccepted(string $developer_id): ?bool;

  /**
   * Get latest terms and condition.
   *
   * @return \Apigee\Edge\Api\Monetization\Entity\TermsAndConditionsInterface
   *   Latest term and condition.
   */
  public function getLatestTermsAndConditions(): ?TermsAndConditionsInterface;

  /**
   * Accepts a terms and conditions by its id.
   *
   * @param string $developer_id
   *   Developer ID.
   *
   * @return \Apigee\Edge\Api\Monetization\Structure\LegalEntityTermsAndConditionsHistoryItem|null
   *   Terms and conditions history item.
   */
  public function acceptLatestTermsAndConditions(string $developer_id): ?LegalEntityTermsAndConditionsHistoryItem;

  /**
   * Check if developer accepted latest terms and conditions.
   *
   * @param string $developer_id
   *   Developer ID.
   * @param \Drupal\apigee_m10n\Entity\RatePlanInterface $rate_plan
   *   Rate plan entity.
   *
   * @return bool|null
   *   Check if developer is subscribed to a plan.
   */
  public function isDeveloperAlreadySubscribed(string $developer_id, RatePlanInterface $rate_plan): bool;

  /**
   * Handles `hook_form_FORM_ID_alter` (user_admin_permissions) for this module.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param string $form_id
   *   The form ID (should always be `user_admin_permissions`).
   */
  public function formUserAdminPermissionsAlter(&$form, FormStateInterface $form_state, $form_id);

  /**
   * Handles `hook_ENTITY_TYPE_presave` (user_role) for this module.
   *
   * @param \Drupal\user\RoleInterface $user_role
   *   The user role.
   */
  public function userRolePresave(RoleInterface $user_role);

}
