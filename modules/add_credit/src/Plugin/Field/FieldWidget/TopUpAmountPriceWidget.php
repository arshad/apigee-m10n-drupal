<?php

/*
 * @file
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

namespace Drupal\apigee_m10n_add_credit\Plugin\Field\FieldWidget;

use Drupal\commerce_price\Plugin\Field\FieldWidget\PriceDefaultWidget;

/**
 * Plugin implementation of the 'apigee_top_up_amount_price' widget.
 *
 * @FieldWidget(
 *   id = "apigee_top_up_amount_price",
 *   label = @Translation("Top up amount"),
 *   field_types = {
 *     "apigee_top_up_amount"
 *   }
 * )
 */
class TopUpAmountPriceWidget extends PriceDefaultWidget {

}
