<?php

/*
 * @file
 * Copyright 2018 Google Inc.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

namespace Drupal\apigee_m10n_teams;

use Drupal\apigee_m10n_teams\Entity\Routing\MonetizationTeamsEntityRouteProvider;
use Drupal\apigee_m10n_teams\Entity\TeamRouteAwarePackage;

/**
 * The `apigee_m10n.teams` service.
 */
class MonetizationTeams implements MonetizationTeamsInterface {

  /**
   * {@inheritdoc}
   */
  public function entityTypeAlter(array &$entity_types) {
    /** @var \Drupal\Core\Entity\EntityTypeInterface[] $entity_types */
    if (isset($entity_types['package'])) {
      // Use our class to override the original entity class.
      $entity_types['package']->setClass(TeamRouteAwarePackage::class);
      // Create a link template for team packages.
      $entity_types['package']->setLinkTemplate('team', '/teams/{team}/monetization/package/{package}');
      // Get the entity route providers.
      $route_providers = $entity_types['package']->getRouteProviderClasses();
      // Override the `html` route provider.
      $route_providers['html'] = MonetizationTeamsEntityRouteProvider::class;
      $entity_types['package']->setHandlerClass('route_provider', $route_providers);
    }
  }

}