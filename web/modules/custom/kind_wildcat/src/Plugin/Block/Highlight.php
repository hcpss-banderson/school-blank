<?php

namespace Drupal\kind_wildcat\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Provides a 'Highlight' block.
 *
 * @Block(
 *  id = "highlight",
 *  admin_label = @Translation("Highlight"),
 * )
 */
class Highlight extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new Highlight object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The EntityManagerInterface definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityManagerInterface $entity_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['first'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('First'),
      '#default_value' => $this->configuration['first'],
      '#target_type' => 'node',
      '#weight' => '0',
    ];
    $form['second'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Second'),
      '#default_value' => $this->configuration['second'],
      '#target_type' => 'node',
      '#weight' => '1',
    ];
    $form['third'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Third'),
      '#default_value' => $this->configuration['third'],
      '#target_type' => 'node',
      '#weight' => '2',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['first'] = $form_state->getValue('first');
    $this->configuration['second'] = $form_state->getValue('second');
    $this->configuration['third'] = $form_state->getValue('third');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $one = $this->entityManager
      ->getStorage('node')
      ->load($this->configuration['first']);
    
    $two = $this->entityManager
      ->getStorage('node')
      ->load($this->configuration['second']);
      
    $three = $this->entityManager
      ->getStorage('node')
      ->load($this->configuration['third']);
    return [
      '#markup' => vsprintf('<p>%s<br>%s<br>%s</p>', [
        $one->label(),
        $two->label(),
        $three->label(),
      ]),
    ];
    
    
//     $build = [];
//     $build['#theme'] = 'highlight';
//     $build['#content'][] = $this->configuration['first'];
//     $build['#content'][] = $this->configuration['second'];
//     $build['#content'][] = $this->configuration['third'];

//     return $build;
  }

}
