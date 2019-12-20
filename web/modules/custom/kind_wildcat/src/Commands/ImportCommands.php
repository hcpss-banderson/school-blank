<?php

namespace Drupal\kind_wildcat\Commands;

use Drush\Commands\DrushCommands;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\kind_wildcat\Entity\StaffMember;
use Drupal\kind_wildcat\Entity\StaffMemberInterface;
use Drupal\kind_wildcat\Entity\ContentChunk;
use Drupal\kind_wildcat\Entity\ContentChunkInterface;

class ImportCommands extends DrushCommands {
    
  /**
   * Scrape news.
   * 
   * @command kind-wildcat:scrape:news
   * @usage kind-wildcat:scrape:news
   *   Scrape news.
   */
  public function scrapeNews() {
    $this->scrape('news_message', function (Crawler $tr, $tr_i) {
      Node::create([
        'type' => 'news',
        'title' => trim($tr->filter('td')->getNode(0)->textContent),
        'created' => strtotime(trim($tr->filter('td')->getNode(1)->textContent)),
        'body' => [
          'value' => trim($tr->filter('td')->getNode(2)->textContent),
          'format' => 'basic_html',
          'summary' => trim($tr->filter('td')->getNode(3)->textContent),
        ],
      ])->save();
    });
  }
  
  /**
   * Delete staff.
   *
   * @command kind-wildcat:delete:staff
   * @usage kind-wildcat:delete:staff
   *   Delete staff.
   */
  public function deleteStaff() {    
    $cids = \Drupal::entityQuery('content_chunk')
      ->condition('type', 'staff_member')
      ->accessCheck(FALSE)
      ->execute();
    
    $members = ContentChunk::loadMultiple($cids);
    
    
    array_walk($members, function (ContentChunkInterface $member) {
      $member->delete();
    });
  }
  
  /**
   * Scrape staff.
   *
   * @command kind-wildcat:scrape:staff
   * @usage kind-wildcat:scrape:staff
   *   Scrape staff.
   */
  public function scrapeStaff() {
    $this->scrape('school_staff_member', function (Crawler $tr, $tr_i) {
      $name    = trim($tr->filter('td')->getNode(0)->textContent);
      $created = strtotime(trim($tr->filter('td')->getNode(1)->textContent));
      $email   = trim($tr->filter('td')->getNode(3)->textContent);
      $bio     = trim($tr->filter('td')->getNode(2)->textContent);
      $title   = trim($tr->filter('td')->getNode(4)->textContent);
      $phone   = trim($tr->filter('td')->getNode(5)->textContent);
      
      if (!$title) {
        // No scrubs.
        return;
      }
      
      $staff = ContentChunk::create([
        'type'            => 'staff_member',
        'name'            => $name,
        'created'         => $created,
        'field_email'     => $email,
        'field_phone'     => $phone,
        'field_job_title' => $title,
        'field_biography' => $bio,
      ]);
      
      $tr->filter('img')->each(function (Crawler $img, $i) use ($staff) {
        $phone = $this->createImage($img->attr('src'));
        $staff->field_photo = $phone;
      });
      
      $staff->save();
    });
  }
  
  /**
   * Scrape events.
   *
   * @command kind-wildcat:scrape:event
   * @usage kind-wildcat:scrape:event
   *   Scrape news.
   */
  public function scrapeEvents() {
    $this->scrape('event', function (Crawler $tr, $tr_i) {
      $rawDate = trim(str_replace('(All day)', '', $tr->filter('td')->getNode(1)->textContent));
      $dateParts = explode(' to ', $rawDate);
      
      $start = date('Y-m-d\TH:i:s', strtotime($dateParts[0]));
      $end = date('Y-m-d', strtotime($dateParts[0])) . 'T23:59:59';
      
      if (count($dateParts) == 2) {
        $end = date('Y-m-d\TH:i:s', strtotime($dateParts[1]));
      }
      
      Node::create([
        'type' => 'event',
        'title' => trim($tr->filter('td')->getNode(0)->textContent),
        'created' => strtotime(trim($tr->filter('td')->getNode(1)->textContent)),
        'field_date' => [
          'value' => $start,
          'end_value' => $end,
        ],
        'body' => [
          'value' => trim($tr->filter('td')->getNode(3)->textContent),
          'format' => 'basic_html',
        ],
      ])->save();
    });
  }
  
  /**
   * Create an file form the url.
   * 
   * @param string $url
   * @return FileInterface|NULL
   */
  public function createImage(string $url) : ?FileInterface {
    $data = file_get_contents($url);
    $file = file_save_data($data, 'public://' . basename($url));
    
    return $file;
  }
  
  /**
   * Scrape galleries.
   *
   * @command kind-wildcat:scrape:gallery
   * @usage kind-wildcat:scrape:gallery
   *   Scrape galleries.
   */
  public function scrapeGalleries() {
    $this->scrape('gallery', function (Crawler $tr, $tr_i) {
      $td = $tr->filter('td');
      
      $node = Node::create([
        'type' => 'gallery',
        'title' => trim($td->getNode(0)->textContent),
        'created' => strtotime(trim($td->getNode(1)->textContent)),
        'body' => [
          'value' => trim($td->getNode(2)->textContent),
          'format' => 'basic_html',
        ],
      ]);
      
      $node->field_images[] = $this->createImage(trim($td->getNode(3)->textContent));
      
      $tr->filter('.field-name-field-gallery-image .field-item')->each(function (Crawler $div, $div_i) use ($node) {
        $uri = $div->extract('_text')[0];        
        $node->field_images[] = $this->createImage(trim($uri));
      });
      
      $node->save();
    });
  }
  
  /**
   * Delete resources.
   *
   * @command kind-wildcat:delete:resources
   * @usage kind-wildcat:delete:resources
   *   Delete resources.
   */
  public function deleteResources() {
    $cids = \Drupal::entityQuery('content_chunk')
      ->condition('type', 'resource')
      ->accessCheck(FALSE)
      ->execute();
    
    $resources = ContentChunk::loadMultiple($cids);
    
    foreach ($resources as $resource) {
      $resource->delete();
    }
  }
  
  /**
   * Scrape resources.
   *
   * @command kind-wildcat:scrape:resources
   * @usage kind-wildcat:scrape:resources
   *   Scrape resources.
   */
  public function scrapeResources() {
    $client = new Client();
    
    $acronym = \Drupal::config('hcpss_schoolsite_config.settings')
      ->get('acronym');
    
    $crawler = $client
      ->request('GET', "https://{$acronym}.hcpss.org");
    
    $crawler->filter('.bullets .bullet-content > p')->each(function (Crawler $p, $i) {
      $title       = $p->filter('a')->extract('_text')[0];
      $href        = $p->filter('a')->attr('href');
      $description = str_replace($title, '', $p->extract('_text')[0]);
      
      ContentChunk::create([
        'type' => 'resource',
        'name' => trim($title),
        'field_url' => trim($href),
        'field_description' => trim($description),
      ])->save();
    });
  }
  
  /**
   * Scrape the node type and create the node using the closure.
   * 
   * @param string $type
   *   The node type.
   * @param \Closure $closure
   *   The closure that creates the node from the crawler.
   */
  private function scrape(string $type, \Closure $closure) {
    $client = new Client();
    
    $acronym = \Drupal::config('hcpss_schoolsite_config.settings')
      ->get('acronym');
    
    $crawler = $client
      ->request('GET', "https://{$acronym}.hcpss.org/content-export/{$type}");
    
    $crawler
      ->filter('.view-content > table.views-table > tbody > tr')
      ->each($closure);
  }
}
