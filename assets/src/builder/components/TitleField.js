const { createElement } = window.wp.element;

export function TitleField({ label, placeholder, value, onChange }) {
  return createElement(
    "section",
    { className: "momsy-builder-card momsy-builder-card--field" },
    createElement(
      "label",
      { className: "momsy-builder-label", htmlFor: "momsy-builder-title" },
      label
    ),
    createElement("input", {
      id: "momsy-builder-title",
      className: "momsy-builder-input",
      type: "text",
      value,
      placeholder,
      onChange: (event) => onChange(event.target.value),
      autoComplete: "off",
    })
  );
}
