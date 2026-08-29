(function () {
  "use strict";

  document.addEventListener("click", function (event) {
    const selectButton = event.target.closest(".eryitang-media-select");
    const clearButton = event.target.closest(".eryitang-media-clear");

    if (clearButton) {
      const field = clearButton.closest(".eryitang-media-field");
      const input = field && field.querySelector(".eryitang-media-url");
      if (input) input.value = "";
      return;
    }

    if (!selectButton || !window.wp || !window.wp.media) return;

    const field = selectButton.closest(".eryitang-media-field");
    const input = field && field.querySelector(".eryitang-media-url");
    if (!input) return;

    const recommendation = field.dataset.recommendation || "";
    const frame = window.wp.media({
      title: recommendation ? `选择图片 · ${recommendation}` : "选择图片",
      button: { text: "使用这张图片" },
      library: { type: "image" },
      multiple: false,
    });

    frame.on("select", function () {
      const attachment = frame.state().get("selection").first().toJSON();
      input.value = attachment.url || "";
      input.dispatchEvent(new Event("change", { bubbles: true }));

      const spec = field.nextElementSibling;
      if (spec && spec.classList.contains("eryitang-image-spec") && attachment.width && attachment.height) {
        const size = attachment.filesizeInBytes
          ? `，${(attachment.filesizeInBytes / 1024).toFixed(0)}KB`
          : "";
        spec.textContent = `${recommendation} 当前选择：${attachment.width}×${attachment.height}px${size}。`;
      }
    });

    frame.open();
  });
})();
