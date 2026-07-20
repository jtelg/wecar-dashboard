(function () {
  "use strict";

  function initTabs(root) {
    var tablist = root.querySelector('[role="tablist"]');

    if (!tablist) {
      return;
    }

    var tabs = Array.prototype.slice.call(tablist.querySelectorAll('[role="tab"]'));

    function activateTab(tab, moveFocus) {
      var targetPanel = root.querySelector("#" + tab.getAttribute("aria-controls"));

      if (!targetPanel) {
        return;
      }

      tabs.forEach(function (item) {
        var isActive = item === tab;
        var panel = root.querySelector("#" + item.getAttribute("aria-controls"));

        item.setAttribute("aria-selected", isActive ? "true" : "false");
        item.setAttribute("tabindex", isActive ? "0" : "-1");

        if (panel) {
          panel.hidden = !isActive;
          panel.setAttribute("aria-hidden", isActive ? "false" : "true");
        }
      });

      if (moveFocus) {
        tab.focus();
      }
    }

    tabs.forEach(function (tab, index) {
      tab.addEventListener("click", function () {
        activateTab(tab, false);
      });

      tab.addEventListener("keydown", function (event) {
        var nextIndex = index;

        if (event.key === "ArrowRight") {
          nextIndex = (index + 1) % tabs.length;
        } else if (event.key === "ArrowLeft") {
          nextIndex = (index - 1 + tabs.length) % tabs.length;
        } else if (event.key === "Home") {
          nextIndex = 0;
        } else if (event.key === "End") {
          nextIndex = tabs.length - 1;
        } else {
          return;
        }

        event.preventDefault();
        activateTab(tabs[nextIndex], true);
      });
    });
  }

  function initAccordion(root) {
    var buttons = Array.prototype.slice.call(
      root.querySelectorAll(".wecar-sell__faq-item button[aria-controls]")
    );
    var duration = 496.1703419685364;
    var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    function clearPanelStyles(panel) {
      panel.style.height = "";
      panel.style.opacity = "";
      panel.style.paddingTop = "";
      panel.style.paddingBottom = "";
    }

    function finishPanelTransition(panel, isOpen) {
      clearTimeout(panel.wecarFaqTimer);
      clearPanelStyles(panel);
      panel.hidden = !isOpen;
      panel.setAttribute("aria-hidden", isOpen ? "false" : "true");
    }

    function setPanelState(button, panel, shouldOpen) {
      clearTimeout(panel.wecarFaqTimer);

      if (reducedMotion) {
        button.setAttribute("aria-expanded", shouldOpen ? "true" : "false");
        finishPanelTransition(panel, shouldOpen);
        return;
      }

      if (shouldOpen) {
        panel.hidden = false;
        panel.setAttribute("aria-hidden", "false");
        panel.style.height = "0px";
        panel.style.opacity = "0";
        panel.style.paddingTop = "0px";
        panel.style.paddingBottom = "0px";
        panel.offsetHeight;
        button.setAttribute("aria-expanded", "true");
        panel.style.height = panel.scrollHeight + 28 + "px";
        panel.style.opacity = "1";
        panel.style.paddingTop = "8px";
        panel.style.paddingBottom = "20px";
      } else {
        panel.style.height = panel.getBoundingClientRect().height + "px";
        panel.style.opacity = "1";
        panel.offsetHeight;
        button.setAttribute("aria-expanded", "false");
        panel.setAttribute("aria-hidden", "true");
        panel.style.height = "0px";
        panel.style.opacity = "0";
        panel.style.paddingTop = "0px";
        panel.style.paddingBottom = "0px";
      }

      panel.wecarFaqTimer = window.setTimeout(function () {
        finishPanelTransition(panel, shouldOpen);
      }, duration);
    }

    buttons.forEach(function (button) {
      button.addEventListener("click", function () {
        var isOpen = button.getAttribute("aria-expanded") === "true";
        var panel = root.querySelector("#" + button.getAttribute("aria-controls"));

        if (panel) {
          setPanelState(button, panel, !isOpen);
        }
      });
    });
  }

  function init() {
    var root = document.getElementById("wecar-vende-tu-auto");

    if (!root) {
      return;
    }

    initTabs(root);
    initAccordion(root);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
