const { createElement } = window.wp.element;

export function FeaturedImagePlaceholder({ label, helpText }) {
  return createElement(
    "section",
    { className: "momsy-builder-card momsy-builder-card--field" },
    createElement("span", { className: "momsy-builder-label" }, label),
    createElement(
      "div",
      { className: "momsy-builder-media-placeholder", aria-hidden: "true" },
      createElement("div", { className: "momsy-builder-media-placeholder__art" }),
      createElement(
        "div",
        { className: "momsy-builder-media-placeholder__copy" },
        createElement("strong", null, "Kapak görseli alanı"),
        createElement("span", null, helpText)
      )
    )
  );
}
