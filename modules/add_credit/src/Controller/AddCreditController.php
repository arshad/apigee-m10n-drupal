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

namespace Drupal\apigee_m10n_add_credit\Controller;

use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AddCreditController.
 */
class AddCreditController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The commerce_product entity view builder.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $viewBuilder;

  /**
   * AddCreditController constructor.
   *
   * @param \Drupal\Core\Entity\EntityViewBuilderInterface $view_builder
   *   The commerce_product entity view builder.
   */
  public function __construct(EntityViewBuilderInterface $view_builder) {
    $this->viewBuilder = $view_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getViewBuilder('commerce_product')
    );
  }

  /**
   * Returns the view for the selected product.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user entity.
   * @param \Drupal\commerce_product\Entity\ProductInterface $commerce_product
   *   The commerce product entity.
   *
   * @return array
   *   A renderable array for the product view.
   */
  public function view(UserInterface $user, ProductInterface $commerce_product) {
    return $this->viewBuilder->view($commerce_product);
  }

}
