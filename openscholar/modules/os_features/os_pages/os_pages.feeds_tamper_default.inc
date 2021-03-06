<?php
/**
 * @file
 * os_pages.feeds_tamper_default.inc
 */

/**
 * Implements hook_feeds_tamper_default().
 */
function os_pages_feeds_tamper_default() {
  $export = array();

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'os_pages-body-utf8_convert';
  $feeds_tamper->importer = 'os_pages';
  $feeds_tamper->source = 'Body';
  $feeds_tamper->plugin_id = 'utf8_convert';
  $feeds_tamper->settings = array();
  $feeds_tamper->weight = 0;
  $feeds_tamper->description = 'Convert to UTF-8';
  $export['os_pages-body-utf8_convert'] = $feeds_tamper;

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'os_pages-created_date-adjust_date_to_timezone';
  $feeds_tamper->importer = 'os_pages';
  $feeds_tamper->source = 'Created date';
  $feeds_tamper->plugin_id = 'adjust_date_to_timezone';
  $feeds_tamper->settings = array();
  $feeds_tamper->weight = 0;
  $feeds_tamper->description = 'Adjust date to timezone';
  $export['os_pages-created_date-adjust_date_to_timezone'] = $feeds_tamper;

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'os_pages-path-pathauto_cleanstring';
  $feeds_tamper->importer = 'os_pages';
  $feeds_tamper->source = 'Path';
  $feeds_tamper->plugin_id = 'pathauto_cleanstring';
  $feeds_tamper->settings = array();
  $feeds_tamper->weight = 0;
  $feeds_tamper->description = 'Clean string with pathauto';
  $export['os_pages-path-pathauto_cleanstring'] = $feeds_tamper;

  $feeds_tamper = new stdClass();
  $feeds_tamper->disabled = FALSE; /* Edit this to true to make a default feeds_tamper disabled initially */
  $feeds_tamper->api_version = 2;
  $feeds_tamper->id = 'os_pages-title-utf8_convert';
  $feeds_tamper->importer = 'os_pages';
  $feeds_tamper->source = 'Title';
  $feeds_tamper->plugin_id = 'utf8_convert';
  $feeds_tamper->settings = array();
  $feeds_tamper->weight = 0;
  $feeds_tamper->description = 'Convert to UTF-8';
  $export['os_pages-title-utf8_convert'] = $feeds_tamper;

  return $export;
}
