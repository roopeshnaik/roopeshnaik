<?php

namespace Drupal\connecting_links\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Connecting links entity.
 *
 * @ConfigEntityType(
 *   id = "connecting_links",
 *   label = @Translation("Connecting links"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\connecting_links\ConnectingLinksListBuilder",
 *     "form" = {
 *       "add" = "Drupal\connecting_links\Form\ConnectingLinksForm",
 *       "edit" = "Drupal\connecting_links\Form\ConnectingLinksForm",
 *       "delete" = "Drupal\connecting_links\Form\ConnectingLinksDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\connecting_links\ConnectingLinksHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "connecting_links",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/connecting_links/{connecting_links}",
 *     "add-form" = "/admin/structure/connecting_links/add",
 *     "edit-form" = "/admin/structure/connecting_links/{connecting_links}/edit",
 *     "delete-form" = "/admin/structure/connecting_links/{connecting_links}/delete",
 *     "collection" = "/admin/structure/connecting_links"
 *   }
 * )
 */
class ConnectingLinks extends ConfigEntityBase implements ConnectingLinksInterface {

  /**
   * The Connecting links ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Connecting links label.
   *
   * @var string
   */
  protected $label;

}
