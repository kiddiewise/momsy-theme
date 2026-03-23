import { ContentEmptyState } from "./ContentEmptyState.js";
import { BlockList } from "./BlockList.js";
import { AddBlockButton } from "./AddBlockButton.js";

const { createElement } = window.wp.element;

function getBlockCountLabel(count) {
  if (count === 1) {
    return "1 blok";
  }

  return `${count} blok`;
}

export function BuilderContentArea({
  addLabel,
  blocks,
  description,
  emptyDescription,
  emptyTitle,
  moveDownLabel,
  moveUpLabel,
  onAddClick,
  onMoveDown,
  onMoveUp,
  onRemove,
  removeLabel,
  title,
}) {
  const hasBlocks = blocks.length > 0;

  return createElement(
    "section",
    { className: "momsy-builder-card momsy-builder-card--content" },
    createElement(
      "div",
      { className: "momsy-builder-content-header" },
      createElement(
        "div",
        { className: "momsy-builder-content-header__copy" },
        createElement("span", { className: "section-kicker" }, "Flow"),
        createElement("h2", null, title),
        createElement("p", null, description)
      ),
      createElement(
        "span",
        { className: "status-pill momsy-builder-content-header__count" },
        getBlockCountLabel(blocks.length)
      )
    ),
    createElement(
      "div",
      {
        className: hasBlocks
          ? "momsy-builder-blocks momsy-builder-blocks--list"
          : "momsy-builder-blocks",
      },
      hasBlocks
        ? createElement(BlockList, {
            blocks,
            moveDownLabel,
            moveUpLabel,
            onMoveDown,
            onMoveUp,
            onRemove,
            removeLabel,
          })
        : createElement(ContentEmptyState, {
            title: emptyTitle,
            description: emptyDescription,
          })
    ),
    createElement(
      "div",
      { className: "momsy-builder-content-actions" },
      createElement(AddBlockButton, {
        label: addLabel,
        onClick: onAddClick,
      })
    )
  );
}
