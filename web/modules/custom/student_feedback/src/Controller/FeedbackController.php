<?php

namespace Drupal\student_feedback\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * FormController that extends the controller base class.
 */
class FeedbackController extends ControllerBase {

  protected $feedbackManager;

  public function __construct($feedbackManager) {
    $this->feedbackManager = $feedbackManager;
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
  public function listFeedback() {
    $feedbackData = $this->feedbackManager->getAllFeedback();

    return [
      '#theme' => 'feedback_list',
      '#items' => $feedbackData,
      '#attached' => [
        'library' => ['student_feedback/styles'],
      ],
    ];
  }

  /**
   * Implelemts the dependency injected method named deletefeedbackbyId form the servie.
   */
  public function deleteFeedback($id) {
    $this->feedbackManager->deleteFeedback($id);
    $this->messenger()->addMessage('Deleted');
    return $this->redirect('student_feedback.list');
  }

}