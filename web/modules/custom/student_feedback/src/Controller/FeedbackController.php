<?php

namespace Drupal\student_feedback\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * FormController that extends the controller base class.
 */
class FeedbackController extends ControllerBase {

  protected $manager;

  public function __construct($manager) {
    $this->manager = $manager;
  }

  /**
   * It is used for dependency injection that allows manager to acces the whole controll over the functionlity provided by the service file.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('student_feedback.manager')
    );
  }

  /**
   * It is used for rendering the list of feedback of feedBack list page.
   */
  public function list() {
    $data = $this->manager->getAllFeedback();

    $rows = [];
    foreach ($data as $item) {
      $rows[] = [
        $item->name,
        $item->email,
        $item->message,
        $item->rating,
        [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'edit' => [
                'title' => 'Edit',
                'url' => Url::fromRoute('student_feedback.edit', ['id' => $item->id]),
              ],
              'delete' => [
                'title' => 'Delete',
                'url' => Url::fromRoute('student_feedback.delete', ['id' => $item->id]),
              ],
            ],
          ],
        ],
      ];
    }

    return [
      '#type' => 'table',
      '#header' => ['Name', 'Email', 'Message', 'Rating', 'Operations'],
      '#rows' => $rows,
    ];
  }

  /**
   * Implelemts the dependency injected method named deletefeedbackbyId form the servie.
   */
  public function delete($id) {
    $this->manager->deleteFeedback($id);
    $this->messenger()->addMessage('Deleted');
    return $this->redirect('student_feedback.list');
  }

}
