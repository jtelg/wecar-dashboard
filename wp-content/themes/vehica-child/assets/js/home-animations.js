/**
 * WeCar Home Hero — Horizontal Accordion (Click)
 * ==============================================
 * Figma hero component (137:3003) has 3 states:
 *   step-1: both cards equal (50/50)
 *   step-2: left card expanded, right collapsed
 *   step-3: right card expanded, left collapsed
 *
 * Click a card to expand it (collapses the other).
 * Click the expanded card again to reset both to equal.
 * ==============================================
 */

(function () {
  'use strict';

  if (!document.body.classList.contains('home')) return;

  // ── Force eager loading on hero car images (lazy + hidden opacity = no load) ──
  var heroImages = document.querySelectorAll('#wecar-hero .wecar-hero__card__image img');
  for (var i = 0; i < heroImages.length; i++) {
    heroImages[i].setAttribute('loading', 'eager');
  }

  var leftCol = document.querySelector('#wecar-hero .elementor-column[data-id="h02c001"]');
  var rightCol = document.querySelector('#wecar-hero .elementor-column[data-id="h02c002"]');
  if (!leftCol || !rightCol) return;

  var ACTIVE = 'wecar-hero__column--active';
  var COLLAPSED = 'wecar-hero__column--collapsed';

  function reset() {
    leftCol.classList.remove(ACTIVE, COLLAPSED);
    rightCol.classList.remove(ACTIVE, COLLAPSED);
  }

  function expandLeft() {
    reset();
    leftCol.classList.add(ACTIVE);
    rightCol.classList.add(COLLAPSED);
  }

  function expandRight() {
    reset();
    rightCol.classList.add(ACTIVE);
    leftCol.classList.add(COLLAPSED);
  }

  leftCol.addEventListener('click', function () {
    if (window.innerWidth < 768) return;
    // If already active, reset to default. Otherwise expand left.
    if (leftCol.classList.contains(ACTIVE)) {
      reset();
    } else {
      expandLeft();
    }
  });

  rightCol.addEventListener('click', function () {
    if (window.innerWidth < 768) return;
    if (rightCol.classList.contains(ACTIVE)) {
      reset();
    } else {
      expandRight();
    }
  });
})();
