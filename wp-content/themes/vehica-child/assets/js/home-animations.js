/**
 * WeCar Home Hero — Accordion Toggle (Figma Exact 2026-07)
 * ==========================================================
 * Figma hero component has 3 states:
 *   default: both cards equal (598px each)
 *   step-2:  left card expanded (976px), right collapsed (220px)
 *   step-3:  right card expanded (976px), left collapsed (220px)
 *
 * Clicking a card toggles: default → expanded → default
 * Clicking the other card switches the expanded side.
 * ==========================================================
 */

(function () {
  'use strict';

  if (!document.body.classList.contains('home')) return;

  var container = document.querySelector('#wecar-hero .elementor-container');
  if (!container) return;

  var leftCol = document.querySelector('#wecar-hero .elementor-column[data-id="h02c001"]');
  var rightCol = document.querySelector('#wecar-hero .elementor-column[data-id="h02c002"]');
  if (!leftCol || !rightCol) return;

  var STATE_CLASS = 'wecar-hero__column--active';
  var COLLAPSED_CLASS = 'wecar-hero__column--collapsed';

  function reset() {
    leftCol.classList.remove(STATE_CLASS, COLLAPSED_CLASS);
    rightCol.classList.remove(STATE_CLASS, COLLAPSED_CLASS);
  }

  function expandLeft() {
    reset();
    leftCol.classList.add(STATE_CLASS);
    rightCol.classList.add(COLLAPSED_CLASS);
  }

  function expandRight() {
    reset();
    rightCol.classList.add(STATE_CLASS);
    leftCol.classList.add(COLLAPSED_CLASS);
  }

  leftCol.addEventListener('click', function () {
    if (leftCol.classList.contains(STATE_CLASS)) {
      reset();
    } else {
      expandLeft();
    }
  });

  rightCol.addEventListener('click', function () {
    if (rightCol.classList.contains(STATE_CLASS)) {
      reset();
    } else {
      expandRight();
    }
  });
})();
