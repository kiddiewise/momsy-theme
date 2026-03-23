const { createElement } = window.wp.element;

export function ContentEmptyState({ title, description }) {
  return createElement(
    "div",
    { className: "momsy-builder-empty-state" },
    createElement("span", { className: "section-kicker" }, "Builder"),
    createElement("h2", null, title),
    createElement("p", null, description)
  );
}
