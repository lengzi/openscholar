<?php

namespace Drupal\vsite_privacy;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\vsite\VsiteEvents;
use Drupal\vsite_privacy\Plugin\VsitePrivacyLevelManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class VsitePrivacyAccessCheck.
 */
class VsitePrivacyAccessCheck implements EventSubscriberInterface {

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Vsite privacy level manager.
   *
   * @var \Drupal\vsite_privacy\Plugin\VsitePrivacyLevelManagerInterface
   */
  protected $vsitePrivacyLevelManager;

  /**
   * Checked.
   *
   * @var bool
   */
  protected $checked = FALSE;

  /**
   * Creates new object for VsitePrivacyAccessCheck.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param \Drupal\vsite_privacy\Plugin\VsitePrivacyLevelManagerInterface $vsitePrivacyLevelManager
   *   Vsite privacy level manager.
   */
  public function __construct(ConfigFactoryInterface $configFactory, VsitePrivacyLevelManagerInterface $vsitePrivacyLevelManager) {
    $this->configFactory = $configFactory;
    $this->vsitePrivacyLevelManager = $vsitePrivacyLevelManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[VsiteEvents::VSITE_ACTIVATED][] = ['onVsiteActivated', 0];
    return $events;
  }

  /**
   * React on site activated event.
   */
  public function onVsiteActivated() {
    if ($this->checked) {
      return;
    }

    $this->checked = TRUE;
    $privacy = $this->configFactory->get('vsite.privacy');
    $level = $privacy->get('level');
    if (!isset($level)) {
      $level = 'public';
    }
    if (!$this->vsitePrivacyLevelManager->checkAccessForPlugin(\Drupal::currentUser(), $level)) {
      throw new AccessDeniedHttpException();
    }
  }

}