const { createElement } = window.wp.element;

export function BuilderFooterActions({ saveLabel, publishLabel, publishDisabled }) {
  return createElement(
    "footer",
    { className: "momsy-builder-footer momsy-builder-card" },
    createElement(
      "div",
      { className: "momsy-builder-footer__meta" },
      createElement("span", { className: "status-pill" }, "MVP Shell"),
      createElement(
        "p",
        null,
        "Kaydetme, yayınlama ve blok ekleme akışları bir sonraki adımda bağlanacak."
      )
    ),
    createElement(
      "div",
      { className: "momsy-builder-footer__actions" },
      createElement(
        "button",
        {
          type: "button",
          className: "button-secondary momsy-builder-button",
          disabled: true,
          "aria-disabled": "true",
        },
        saveLabel
      ),
      createElement(
        "button",
        {
          type: "button",
          className: "button-primary momsy-builder-button",
          disabled: publishDisabled,
          "aria-disabled": String(publishDisabled),
        },
        publishLabel
      )
    )
  );
}
