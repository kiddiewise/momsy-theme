import { getBlockDefinition, getBlockPreviewText } from "../schema.js";

const { createElement } = window.wp.element;

function ActionButton({ label, onClick, disabled, tone = "default" }) {
  const className =
    tone === "danger"
      ? "momsy-builder-control momsy-builder-control--danger"
      : "momsy-builder-control";

  return createElement(
    "button",
    {
      type: "button",
      className,
      disabled,
      "aria-disabled": String(Boolean(disabled)),
      onClick,
    },
    label
  );
}

export function BlockItem({
  block,
  index,
  isFirst,
  isLast,
  moveDownLabel,
  moveUpLabel,
  onMoveDown,
  onMoveUp,
  onRemove,
  removeLabel,
}) {
  const definition = getBlockDefinition(block.type);
  const label = definition ? definition.label : block.type;
  const description = definition
    ? definition.description
    : "Bu blok tipi için tanım bulunamadı.";
  const previewText = getBlockPreviewText(block);

  return createElement(
    "li",
    { className: "momsy-builder-block-list__item" },
    createElement(
      "article",
      { className: "momsy-builder-block-card" },
      createElement(
        "div",
        { className: "momsy-builder-block-card__top" },
        createElement(
          "div",
          { className: "momsy-builder-block-card__meta" },
          createElement(
            "span",
            { className: "status-pill momsy-builder-block-card__index" },
            `#${index + 1}`
          ),
          createElement("span", { className: "momsy-builder-block-card__type" }, label)
        ),
        createElement(
          "code",
          { className: "momsy-builder-block-card__slug" },
          block.type
        )
      ),
      createElement(
        "div",
        { className: "momsy-builder-block-card__body" },
        createElement("p", { className: "momsy-builder-block-card__description" }, description),
        createElement("p", { className: "momsy-builder-block-card__preview" }, previewText)
      ),
      createElement(
        "div",
        { className: "momsy-builder-block-card__actions" },
        createElement(ActionButton, {
          disabled: isFirst,
          label: moveUpLabel,
          onClick: () => onMoveUp(block.id),
        }),
        createElement(ActionButton, {
          disabled: isLast,
          label: moveDownLabel,
          onClick: () => onMoveDown(block.id),
        }),
        createElement(ActionButton, {
          label: removeLabel,
          onClick: () => onRemove(block.id),
          tone: "danger",
        })
      )
    )
  );
}
