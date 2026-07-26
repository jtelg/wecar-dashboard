(function () {
  "use strict";

  var cardsDuration = 2083.5380554;
  var finalDuration = 1250.1229048;

  function initQualification(section) {
    var blueKm = section.querySelector("[data-wecar-blue-km]");
    var started = false;
    var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var isTestHost = window.location.hostname === "test.wecar.com.ar";
    var hasScrolled = false;
    var ticking = false;
    var positionRafId = null;
    var animationRafId = null;
    var finalTimerId = null;
    var completeTimerId = null;

    function finishImmediately() {
      if (blueKm) blueKm.textContent = "100.000 km";
      section.classList.add("is-cards-in", "is-final", "is-complete");
    }

    function resetQualification() {
      if (animationRafId !== null) {
        window.cancelAnimationFrame(animationRafId);
        animationRafId = null;
      }
      if (finalTimerId !== null) {
        window.clearTimeout(finalTimerId);
        finalTimerId = null;
      }
      if (completeTimerId !== null) {
        window.clearTimeout(completeTimerId);
        completeTimerId = null;
      }

      section.classList.remove("is-animating", "is-cards-in", "is-final", "is-complete");
      if (blueKm) blueKm.textContent = "120.000 km";
      section.classList.add("is-animating");

      // Commit the reset state before the reveal transition begins.
      section.getBoundingClientRect();
    }

    function playQualification() {
      resetQualification();

      animationRafId = window.requestAnimationFrame(function () {
        animationRafId = null;
        section.classList.add("is-cards-in");
      });

      finalTimerId = window.setTimeout(function () {
        finalTimerId = null;
        if (blueKm) blueKm.textContent = "100.000 km";
        section.classList.add("is-final");
      }, cardsDuration + 1);

      completeTimerId = window.setTimeout(function () {
        completeTimerId = null;
        section.classList.remove("is-animating");
        section.classList.add("is-complete");
      }, cardsDuration + finalDuration + 2);
    }

    function start() {
      if (started) return;
      started = true;
      cleanup();
      playQualification();
    }

    function addTestReplayControl() {
      if (window.location.hostname !== "test.wecar.com.ar") return;
      if (document.querySelector(".wecar-cotizador__test-replay")) return;

      var button = document.createElement("button");
      button.type = "button";
      button.className = "wecar-cotizador__test-replay";
      button.textContent = "Replay Qualification Animation (TEST)";
      button.addEventListener("click", function () {
        playQualification();
      });
      document.body.appendChild(button);
    }

    // The qualification cards are static on narrow screens, so restored/touch sessions never wait for a scroll trigger.
    if (window.matchMedia("(max-width: 767px)").matches || (reduced && !isTestHost)) {
      started = true;
      finishImmediately();
      return;
    }

    if (reduced && isTestHost) {
      section.dataset.wecarReducedMotionQaOverride = "1";
      section.classList.add("wecar-cotizador__qualification--test-motion");
    }

    addTestReplayControl();
    section.addEventListener("pointerenter", start, { once: true });
    section.addEventListener("focusin", start, { once: true });

    function cleanup() {
      window.removeEventListener("scroll", handleScroll);
      window.removeEventListener("resize", handleResize);
      section.removeEventListener("pointerenter", start);
      section.removeEventListener("focusin", start);
      if (positionRafId !== null) {
        window.cancelAnimationFrame(positionRafId);
        positionRafId = null;
      }
      ticking = false;
    }

    function checkPosition() {
      positionRafId = null;
      ticking = false;
      var bounds = section.getBoundingClientRect();
      var triggerLine = window.innerHeight * 0.75;

      if (hasScrolled && bounds.top <= triggerLine && bounds.bottom > 0) start();
    }

    function requestCheck() {
      if (ticking) return;
      ticking = true;
      positionRafId = window.requestAnimationFrame(checkPosition);
    }

    function handleScroll() {
      hasScrolled = true;
      requestCheck();
    }

    function handleResize() {
      if (!hasScrolled) return;
      requestCheck();
    }

    // Match Home Steps: a restored position stays idle until a real scroll.
    window.addEventListener("scroll", handleScroll, { passive: true });
    window.addEventListener("resize", handleResize);
  }

  function init() {
    var section = document.querySelector("[data-wecar-qualification]");
    if (section) initQualification(section);
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();
})();
