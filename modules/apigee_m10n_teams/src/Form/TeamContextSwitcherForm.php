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

namespace Drupal\apigee_m10n_teams\Form;

use Drupal\apigee_edge_teams\TeamMembershipManagerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form for switching team.
 */
class TeamContextSwitcherForm extends FormBase implements ContainerInjectionInterface {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The Apigee team membership manager.
   *
   * @var \Drupal\apigee_edge_teams\TeamMembershipManagerInterface
   */
  protected $teamMembershipManager;

  /**
   * The Apigee team storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * TeamContextSwitcherForm constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   * @param \Drupal\apigee_edge_teams\TeamMembershipManagerInterface $team_membership_manager
   *   The Apigee team membership manager.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The Apigee team storage.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(AccountInterface $account, TeamMembershipManagerInterface $team_membership_manager, EntityStorageInterface $storage, RouteMatchInterface $route_match) {
    $this->account = $account;
    $this->teamMembershipManager = $team_membership_manager;
    $this->storage = $storage;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('apigee_edge_teams.team_membership_manager'),
      $container->get('entity_type.manager')->getStorage('team'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'team_context_switcher_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if (!($current_team = $this->routeMatch->getParameter('team'))
      || !($teams = $this->teamMembershipManager->getTeams($this->currentUser()
        ->getEmail()))) {
      // Do not show a form if we are not on a team route or user does not belong to any team.
      return [];
    }

    // Get the current team from the route.
    /** @var \Drupal\apigee_edge_teams\Entity\TeamInterface $current_team */
    $current_team = $this->routeMatch->getParameter('team') ?? NULL;

    /** @var \Drupal\apigee_edge_teams\Entity\TeamInterface $team */
    $default_value = NULL;
    $options = ['' => $this->t('Select a team')];
    foreach ($this->storage->loadMultiple($teams) as $id => $team) {
      $value = $team->toUrl()->toString();
      $options[$value] = $team->label();

      // Set the default value.
      if ($current_team->id() === $id) {
        $default_value = $value;
      }
    }

    $form['context'] = [
      '#title' => $this->t('Select a team'),
      '#type' => 'select',
      '#required' => TRUE,
      '#options' => $options,
      '#default_value' => $default_value,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Switch team'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($context = $form_state->getValue('context')) {
      $form_state->setRedirectUrl(Url::fromUserInput($context));
    }
  }

}
