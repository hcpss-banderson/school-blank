<?php

namespace Drupal\kind_wildcat\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\kind_wildcat\Entity\ContentChunkInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ContentChunkController.
 *
 *  Returns responses for Content chunk routes.
 */
class ContentChunkController extends ControllerBase implements ContainerInjectionInterface {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new ContentChunkController.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a Content chunk revision.
   *
   * @param int $content_chunk_revision
   *   The Content chunk revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($content_chunk_revision) {
    $content_chunk = $this->entityTypeManager()->getStorage('content_chunk')
      ->loadRevision($content_chunk_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('content_chunk');

    return $view_builder->view($content_chunk);
  }

  /**
   * Page title callback for a Content chunk revision.
   *
   * @param int $content_chunk_revision
   *   The Content chunk revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($content_chunk_revision) {
    $content_chunk = $this->entityTypeManager()->getStorage('content_chunk')
      ->loadRevision($content_chunk_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $content_chunk->label(),
      '%date' => $this->dateFormatter->format($content_chunk->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Content chunk.
   *
   * @param \Drupal\kind_wildcat\Entity\ContentChunkInterface $content_chunk
   *   A Content chunk object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(ContentChunkInterface $content_chunk) {
    $account = $this->currentUser();
    $content_chunk_storage = $this->entityTypeManager()->getStorage('content_chunk');

    $langcode = $content_chunk->language()->getId();
    $langname = $content_chunk->language()->getName();
    $languages = $content_chunk->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $content_chunk->label()]) : $this->t('Revisions for %title', ['%title' => $content_chunk->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all content chunk revisions") || $account->hasPermission('administer content chunk entities')));
    $delete_permission = (($account->hasPermission("delete all content chunk revisions") || $account->hasPermission('administer content chunk entities')));

    $rows = [];

    $vids = $content_chunk_storage->revisionIds($content_chunk);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\kind_wildcat\ContentChunkInterface $revision */
      $revision = $content_chunk_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $content_chunk->getRevisionId()) {
          $link = $this->l($date, new Url('entity.content_chunk.revision', [
            'content_chunk' => $content_chunk->id(),
            'content_chunk_revision' => $vid,
          ]));
        }
        else {
          $link = $content_chunk->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.content_chunk.translation_revert', [
                'content_chunk' => $content_chunk->id(),
                'content_chunk_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.content_chunk.revision_revert', [
                'content_chunk' => $content_chunk->id(),
                'content_chunk_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.content_chunk.revision_delete', [
                'content_chunk' => $content_chunk->id(),
                'content_chunk_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['content_chunk_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
