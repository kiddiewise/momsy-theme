import { getBlockRegistry } from "../schema.js";

const { createElement, useEffect, useRef } = window.wp.element;

export function BlockPickerModal({
  closeLabel,
  description,
  isOpen,
  onClose,
  onSelect,
  selectLabel,
  title,
}) {
  const dialogRef = useRef(null);
  const options = getBlockRegistry();

  useEffect(() => {
    if (!isOpen) {
      return undefined;
    }

    // Keep the modal keyboard-friendly even before we add richer interactions.
    const handleKeyDown = (event) => {
      if (event.key === "Escape") {
        onClose();
      }
    };

    document.addEventListener("keydown", handleKeyDown);

    if (dialogRef.current) {
      dialogRef.current.focus();
    }

    return () => {
      document.removeEventListener("keydown", handleKeyDown);
    };
  }, [isOpen, onClose]);

  if (!isOpen) {
    return null;
  }

  return createElement(
    "div",
    {
      className: "momsy-builder-modal",
      onClick: (event) => {
        if (event.target === event.currentTarget) {
          onClose();
        }
      },
    },
    createElement(
      "div",
      {
        className: "momsy-builder-modal__dialog",
        role: "dialog",
        "aria-labelledby": "momsy-builder-modal-title",
        "aria-modal": "true",
        ref: dialogRef,
        tabIndex: "-1",
      },
      createElement(
        "div",
        { className: "momsy-builder-modal__header" },
        createElement(
          "div",
          { className: "momsy-builder-modal__copy" },
          createElement("span", { className: "section-kicker" }, "Blocks"),
          createElement("h2", { id: "momsy-builder-modal-title" }, title),
          createElement("p", null, description)
        ),
        createElement(
          "button",
          {
            type: "button",
            className: "button-secondary momsy-builder-button momsy-builder-button--close",
            onClick: onClose,
          },
          closeLabel
        )
      ),
      createElement(
        "div",
        { className: "momsy-builder-modal__grid" },
        options.map((option) =>
          createElement(
            "button",
            {
              key: option.type,
              type: "button",
              className: "momsy-builder-picker-card",
              onClick: () => onSelect(option.type),
            },
            createElement(
              "div",
              { className: "momsy-builder-picker-card__body" },
              createElement("strong", null, option.label),
              createElement("code", null, option.type),
              createElement("p", null, option.description)
            ),
            createElement(
              "span",
              { className: "momsy-builder-picker-card__action" },
              selectLabel
            )
          )
        )
      )
    )
  );
}
