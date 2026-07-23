(function () {
  "use strict";

  var duration = 496.1703419685364;

  function initTabs(root) {
    var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));

    function activate(tab, moveFocus) {
      tabs.forEach(function (item) {
        var active = item === tab;
        var panel = root.querySelector("#" + item.getAttribute("aria-controls"));
        item.setAttribute("aria-selected", active ? "true" : "false");
        item.setAttribute("tabindex", active ? "0" : "-1");
        if (panel) {
          panel.hidden = !active;
          panel.setAttribute("aria-hidden", active ? "false" : "true");
        }
      });
      if (moveFocus) tab.focus();
    }

    tabs.forEach(function (tab, index) {
      tab.addEventListener("click", function () { activate(tab, false); });
      tab.addEventListener("keydown", function (event) {
        var next = index;
        if (event.key === "ArrowRight") next = (index + 1) % tabs.length;
        else if (event.key === "ArrowLeft") next = (index - 1 + tabs.length) % tabs.length;
        else if (event.key === "Home") next = 0;
        else if (event.key === "End") next = tabs.length - 1;
        else return;
        event.preventDefault();
        activate(tabs[next], true);
      });
    });
  }

  function initAccordion(root) {
    var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var buttons = Array.prototype.slice.call(root.querySelectorAll(".wecar-faq__item button[aria-controls]"));

    function finish(panel, open) {
      clearTimeout(panel.wecarFaqTimer);
      panel.style.height = "";
      panel.style.opacity = "";
      panel.style.paddingTop = "";
      panel.style.paddingBottom = "";
      panel.hidden = !open;
      panel.setAttribute("aria-hidden", open ? "false" : "true");
    }

    function setState(button, panel, open) {
      clearTimeout(panel.wecarFaqTimer);
      button.setAttribute("aria-expanded", open ? "true" : "false");
      if (reduced) {
        finish(panel, open);
        return;
      }
      if (open) {
        panel.hidden = false;
        panel.setAttribute("aria-hidden", "false");
        panel.style.height = "0px";
        panel.style.opacity = "0";
        panel.style.paddingTop = "0px";
        panel.style.paddingBottom = "0px";
        panel.offsetHeight;
        panel.style.height = panel.scrollHeight + 28 + "px";
        panel.style.opacity = "1";
        panel.style.paddingTop = "8px";
        panel.style.paddingBottom = "20px";
      } else {
        panel.style.height = panel.getBoundingClientRect().height + "px";
        panel.style.opacity = "1";
        panel.offsetHeight;
        panel.setAttribute("aria-hidden", "true");
        panel.style.height = "0px";
        panel.style.opacity = "0";
        panel.style.paddingTop = "0px";
        panel.style.paddingBottom = "0px";
      }
      panel.wecarFaqTimer = window.setTimeout(function () { finish(panel, open); }, duration);
    }

    buttons.forEach(function (button) {
      button.addEventListener("click", function () {
        var panel = root.querySelector("#" + button.getAttribute("aria-controls"));
        if (panel) setState(button, panel, button.getAttribute("aria-expanded") !== "true");
      });
    });
  }

  function init() {
    Array.prototype.forEach.call(document.querySelectorAll("[data-wecar-faq]"), function (root) {
      if (root.dataset.wecarFaqReady === "true") return;
      root.dataset.wecarFaqReady = "true";
      initTabs(root);
      initAccordion(root);
    });
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
  else init();
})();
