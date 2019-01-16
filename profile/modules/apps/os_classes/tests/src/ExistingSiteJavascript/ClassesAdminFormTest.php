<?php

namespace Drupal\Tests\os_classes\ExistingSiteJavascript;

use Behat\Mink\Exception\ExpectationException;

/**
 * Tests os_classes module.
 *
 * @group classes
 * @group functional-javascript
 * @coversDefaultClass \Drupal\os_classes\Form\SemesterFieldOptionsForm
 */
class ClassesAdminFormTest extends ClassesExistingSiteJavascriptTestBase {

  /**
   * Tests os_classes admin form access.
   */
  public function testAccessDeniedAdminForm() {

    // Create a non-admin user
    $user = $this->createUser();
    $this->drupalLogin($user);
    $this->visit('/admin/config/openscholar/classes/field-allowed-values');

    $web_assert = $this->assertSession();

    try {
      $web_assert->statusCodeEquals(403);
    }
    catch (ExpectationException $e) {
      $this->fail(sprintf("Test failed: %s\nBacktrace: %s", $e->getMessage(), $e->getTraceAsString()));
    }
  }
  /**
   * Tests os_classes admin form access.
   */
  public function testAccessAdminForm() {

    $session = $this->getSession();
    $user = $this->createUser(['administer site configuration']);
    $this->drupalLogin($user);
    $this->visit('/admin/config/openscholar/classes/field-allowed-values');

    $web_assert = $this->assertSession();

    $web_assert->statusCodeEquals(200);
    $web_assert->pageTextContains('Semester field options');
    $page = $this->getCurrentPage();
    $checkDefaultValue = $page->hasContent('fall|Fall');
    //$this->assertTrue($checkDefaultValue, 'Default value is not loaded on admin form.');
    $page->fillField('semester_field_options', 'test 1|Test 1');
    file_put_contents('public://screenshot-0.png', $session->getScreenshot());
    //file_put_contents('public://' . drupal_basename($session->getCurrentUrl()) . '.html', $this->getCurrentPageContent());
    $form = $page->find('css', '#semester-field-options-form');
    $form->submit();
    file_put_contents('public://screenshot-submit.png', $session->getScreenshot());

    $web_assert->statusCodeEquals(200);

    $this->visit('/admin/config/openscholar/classes/field-allowed-values');
    file_put_contents('public://screenshot-1.png', $session->getScreenshot());
  }

}
