<?php

/*
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Drupal\apigee_m10n\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class for managing `apigee_m10n.subscription.config` settings.
 *
 * @package Drupal\apigee_m10n\Form
 */
class SubscriptionConfigForm extends ConfigFormBase {

  /**
   * The config named used by this form.
   */
  const CONFIG_NAME = 'apigee_m10n.subscription.config';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [static::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return preg_replace('/[^a-zA-Z0-9_]+/', '_', static::CONFIG_NAME);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the working configuration.
    $config = $this->config(static::CONFIG_NAME);

    $form['subscribe_form_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subscribe form page title.'),
      '#description' => $this->t('The page title to use for the subscribe form page. i.e. "Purchase %rate_plan"'),
      '#default_value' => $config->get('subscribe_form_title'),
    ];
    $form['subscribe_button_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subscribe button label.'),
      '#description' => $this->t('This Label will be used when creating a subscription.'),
      '#default_value' => $config->get('subscribe_button_label'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config(static::CONFIG_NAME)
      ->set('subscribe_form_title', $form_state->getValue('subscribe_form_title'))
      ->set('subscribe_button_label', $form_state->getValue('subscribe_button_label'))
      ->save();
  }

}
