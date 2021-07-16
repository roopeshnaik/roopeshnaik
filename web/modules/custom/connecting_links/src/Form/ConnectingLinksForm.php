<?php

namespace Drupal\connecting_links\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConnectingLinksForm.
 */
class ConnectingLinksForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $connecting_links = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $connecting_links->label(),
      '#description' => $this->t("Label for the Connecting links."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $connecting_links->id(),
      '#machine_name' => [
        'exists' => '\Drupal\connecting_links\Entity\ConnectingLinks::load',
      ],
      '#disabled' => !$connecting_links->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $connecting_links = $this->entity;
    $status = $connecting_links->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Connecting links.', [
          '%label' => $connecting_links->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Connecting links.', [
          '%label' => $connecting_links->label(),
        ]));
    }
    $form_state->setRedirectUrl($connecting_links->toUrl('collection'));
  }

}
