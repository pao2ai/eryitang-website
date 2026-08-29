// 全站进入动效由 assets/js/site.js 统一管理；本文件只保留联系页专属交互。
(function () {
  "use strict";

  function initializeGalleryMarquee() {
    var track = document.querySelector(".contact-gallery-track");
    if (!track || track.children.length < 2) return;

    var figures = Array.prototype.slice.call(track.children);
    var duplicateStart = Math.floor(figures.length / 2);
    if (!duplicateStart || !figures[duplicateStart]) return;

    figures.slice(duplicateStart).forEach(function (figure) {
      figure.setAttribute("aria-hidden", "true");
    });

    var images = Array.prototype.slice.call(track.querySelectorAll("img"));
    images.forEach(function (image) {
      image.loading = "eager";
      image.decoding = "async";
    });

    function setExactDistance() {
      var distance = figures[duplicateStart].offsetLeft - figures[0].offsetLeft;
      if (distance <= 0) return;
      track.classList.remove("is-ready");
      track.style.setProperty("--contact-gallery-translate", -distance + "px");
      void track.offsetWidth;
      track.classList.add("is-ready");
    }

    var pending = 0;
    function scheduleDistanceUpdate() {
      window.clearTimeout(pending);
      pending = window.setTimeout(setExactDistance, 120);
    }

    Promise.all(images.map(function (image) {
      if (image.complete) return Promise.resolve();
      return new Promise(function (resolve) {
        image.addEventListener("load", resolve, { once: true });
        image.addEventListener("error", resolve, { once: true });
      });
    })).then(setExactDistance);

    window.addEventListener("resize", scheduleDistanceUpdate, { passive: true });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeGalleryMarquee, { once: true });
  } else {
    initializeGalleryMarquee();
  }
}());
