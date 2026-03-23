const { createElement } = window.wp.element;

export function AddBlockButton({ label, onClick }) {
  return createElement(
    "button",
    {
      type: "button",
      className: "button-primary momsy-builder-button momsy-builder-button--add",
      onClick,
    },
    label
  );
}
