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

use Apigee\Edge\Api\Monetization\Entity\PrepaidBalanceInterface as ApiPrepaidBalanceInterface;

interface PrepaidBalanceInterface extends ApiPrepaidBalanceInterface {

  /**
   * Returns the Drupal entity type id.
   *
   * @return string
   *   The entity type id.
   */
  public function getDrupalEntityTypeId(): string;

//  /**
//   * Sets the prepaid balance owner.
//   *
//   * @param \Drupal\Core\Entity\EntityInterface $entity
//   *   The owner of the prepaid balance.
//   */
//  public function setOwner(EntityInterface $entity);
//
//  /**
//   * Gets the prepaid balance owner.
//   *
//   * @return \Drupal\Core\Entity\EntityInterface
//   *   The prepaid balance owner.
//   */
//  public function getOwner(): EntityInterface;

}
