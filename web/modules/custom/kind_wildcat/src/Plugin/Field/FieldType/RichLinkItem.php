<?php

namespace Drupal\kind_wildcat\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'kind_wildcat_rich_link' field type.
 *
 * @FieldType(
 *   id = "kind_wildcat_rich_link",
 *   label = @Translation("Rich Link"),
 *   category = @Translation("General"),
 *   default_widget = "kind_wildcat_rich_link",
 *   default_formatter = "kind_wildcat_rich_link_default"
 * )
 */
class RichLinkItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    if ($this->label !== NULL) {
      return FALSE;
    }
    elseif ($this->uri !== NULL) {
      return FALSE;
    }
    elseif ($this->description !== NULL) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {

    $properties['label'] = DataDefinition::create('string')
      ->setLabel(t('Label'));
    $properties['uri'] = DataDefinition::create('uri')
      ->setLabel(t('URI'));
    $properties['description'] = DataDefinition::create('string')
      ->setLabel(t('Description'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    $options['label']['NotBlank'] = [];

    $options['uri']['NotBlank'] = [];

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    // @todo Add more constrains here.
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'label' => [
        'type' => 'varchar',
        'length' => 255,
      ],
      'uri' => [
        'type' => 'varchar',
        'length' => 2048,
      ],
      'description' => [
        'type' => 'text',
        'size' => 'big',
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @DCG Add indexes here if necessary.
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {

    $random = new Random();

    $values['label'] = $random->word(mt_rand(1, 255));

    $tlds = ['com', 'net', 'gov', 'org', 'edu', 'biz', 'info'];
    $domain_length = mt_rand(7, 15);
    $protocol = mt_rand(0, 1) ? 'https' : 'http';
    $www = mt_rand(0, 1) ? 'www' : '';
    $domain = $random->word($domain_length);
    $tld = $tlds[mt_rand(0, (count($tlds) - 1))];
    $values['uri'] = "$protocol://$www.$domain.$tld";

    $values['description'] = $random->paragraphs(5);

    return $values;
  }

}
