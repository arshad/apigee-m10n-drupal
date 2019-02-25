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

/**
 * Defines the 'developer_prepaid_balance' entity.
 *
 * @\Drupal\apigee_edge\Annotation\EdgeEntityType(
 *   id             = "developer_prepaid_balance",
 *   label          = @Translation("Prepaid balance"),
 *   label_singular = @Translation("Prepaid balance"),
 *   label_plural   = @Translation("Prepaid balances"),
 *   label_count = @PluralTranslation(
 *     singular = "@count prepaid balance",
 *     plural   = "@count prepaid balances",
 *   ),
 *   handlers = {
 *     "storage" = "Drupal\apigee_m10n\Entity\Storage\DeveloperPrepaidBalanceStorage",
 *     "access" = "Drupal\apigee_edge\Entity\EdgeEntityAccessControlHandler",
 *     "permission_provider" = "Drupal\apigee_edge\Entity\EdgeEntityPermissionProviderBase",
 *     "list_builder" = "Drupal\apigee_m10n\Entity\ListBuilder\DeveloperPrepaidBalanceListBuilder",
 *   },
 * )
 */
class DeveloperPrepaidBalance extends PrepaidBalance {

  /**
   * {@inheritdoc}
   */
  public function getDrupalEntityTypeId(): string {
    return 'developer_prepaid_balance';
  }

}
