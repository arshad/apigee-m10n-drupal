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

namespace Drupal\apigee_m10n\Entity\Storage;

/**
 * Defines an interface for developer prepaid balance storage.
 */
interface DeveloperPrepaidBalanceStorageInterface extends PrepaidBalanceStorageInterface {

  /**
   * Loads prepaid balances by the developer id.
   *
   * @param string $developer_id
   *   Developer id (UUID) or email address of a developer.
   * @param \DateTimeImmutable $billingDate
   *   The date for the billing report.
   *
   * @return \Drupal\apigee_m10n\Entity\DeveloperPrepaidBalance[]
   *   An array of prepaid balances for the given developer.
   */
  public function loadByDeveloperId(string $developer_id, \DateTimeImmutable $billingDate = NULL): array;

}
