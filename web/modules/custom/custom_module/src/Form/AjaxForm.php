<?php

namespace Drupal\custom_module\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom_module form.
 */
class AjaxForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_module_ajax';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['custom_module.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('custom_module.settings');

    $form['vals'] = [
      '#type' => 'details',
      '#title' => $this->t('Rows'),
      '#required' => TRUE,
    ];

    $form['vals']['rows'] = [
      '#type' => 'item',
      '#tree' => TRUE,
      '#prefix' => '<div id="rows__replace">',
      '#suffix' => '</div>',
    ];

    $count = $form_state->getValue('count', 1);

    for ($i = 0; $i < $count; $i++) {
      if (!isset($form['vals']['rows'][$i])) {
        $form['vals']['rows'][$i] = [
          '#type' => 'url',
          '#title' => $this->t('URL %num', ['%num' => $i])
        ];
      }
    }

    $form['count'] = [
      '#type' => 'value',
      '#value' => $count,
    ];

    $form['add'] = [
      '#type' => 'submit',
      '#name' => 'add',
      '#value' => $this->t('Add row'),
      '#submit' => [ [$this, 'addRow'] ],
      '#ajax' => [
        'callback' => [ $this, 'ajaxCallback' ],
        'wrapper' => 'rows__replace',
        'effect' => 'fade'
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

  public function addRow(array &$form, FormStateInterface &$form_state) {
    $count = $form_state->getValue('count', 1);
    $count += 1;
    $form_state->setValue('count', $count);
    $form_state->setRebuild(TRUE);
  }

  public function ajaxCallback(array &$form, FormStateInterface &$form_state) {
    return $form['vals']['rows'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('custom_module.settings')
    ->set('name', $form_state->getValue('name'))
    ->save();

    parent::submitForm($form, $form_state);
  }

}
