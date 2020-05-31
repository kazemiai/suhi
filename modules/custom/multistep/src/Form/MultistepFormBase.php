<?php
/**
 * @file
 * Contains \Drupal\multistep\Form\MultistepFormBase.
 */

namespace Drupal\multistep\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;

abstract class MultistepFormBase extends FormBase {

  /**
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  protected $sessionManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * Constructs a \Drupal\demo\Form\Multistep\MultistepFormBase.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user) {
    $this->tempStoreFactory = $temp_store_factory; // used for temp storage for data private to current user
    $this->sessionManager = $session_manager; // used to start session for anon users
    $this->currentUser = $current_user; // allows us to check if the current user is anonymous

    $this->store = $this->tempStoreFactory->get('multistep_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Start a manual session for anonymous users.
    if ($this->currentUser->isAnonymous() && !isset($_SESSION['multistep_form_holds_session'])) {
      $_SESSION['multistep_form_holds_session'] = true; 
      $this->sessionManager->start(); // start session for anon user if one doesn't already exist
    }

    // Base submit action button present on all implementing forms
    $form = array();
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );

    $form['#attached']['library'][] = 'multistep/multistep_form';
    $this->store->set('currentUser', $this->currentUser);
    return $form;
  }

  // To implement
  /**
   * Saves the data from the multistep form.
   */
  protected function saveData() {
    // Logic for saving data goes here...

    $english = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('english'), 
      ]);

    $math = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('math'), 
      ]);
    
    $ss = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('ss'), 
      ]);

    
    $sci = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('sci'), 
      ]);


    $pe = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('pe'), 
      ]);

    
    $health = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('health'), 
      ]);

    $cpe = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('cpe'), 
      ]);

    $vapa = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('vapa'), 
      ]);

    $lang = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'course',
      'field_course_number' => $this->store->get('lang'), 
      ]);
      $elective = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
        'type' => 'course',
        'field_course_number' => $this->store->get('elective'), 
        ]);




    $node = Node::create([
      'type' => 'wishlist',
      'title' => $this->store->get('student_id'),
      'field_student_id' => $this->store->get('student_id'),
      'field_first_name' => $this->store->get('first_name'),
      'field_last_name' => $this->store->get('last_name'),
      'field_student_grade_level' => $this->store->get('grade_level') + 1,
      'field_comments' => $this->store->get('comments'),
      'field_english_course' => $english,
      'field_math_course' => $math,
      'field_pe_course' => $pe,
      'field_social_science_course' => $ss,
      'field_science_course' => $sci,
      'field_health_course' => $health,
      'field_vapa_course' => $vapa,
      'field_foreign_language_course' => $lang,
      'field_elective_course_s_' => $elective

    ]);

    $node->save();
   
    $this->deleteStore();
    drupal_set_message($this->t('The form has been saved.'));

  }

  /**
   * Helper method that removes all the keys from the store collection used for
   * the multistep form.
   */
  protected function deleteStore() {
      // better way to do this?
    $keys = ['first_name', 'last_name', 'student_id', 'grade_level', 'isAP', 'isFund', 'isELD', 'english', 'math', 'ss', 'sci', 'pe', 'health', 'cpe', 'elective', 'vapa', 'lang', 'comments'];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
}