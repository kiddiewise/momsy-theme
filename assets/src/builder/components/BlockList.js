import { BlockItem } from "./BlockItem.js";

const { createElement } = window.wp.element;

export function BlockList({
  blocks,
  moveDownLabel,
  moveUpLabel,
  onMoveDown,
  onMoveUp,
  onRemove,
  removeLabel,
}) {
  return createElement(
    "ol",
    { className: "momsy-builder-block-list" },
    blocks.map((block, index) =>
      createElement(BlockItem, {
        block,
        index,
        isFirst: index === 0,
        isLast: index === blocks.length - 1,
        key: block.id,
        moveDownLabel,
        moveUpLabel,
        onMoveDown,
        onMoveUp,
        onRemove,
        removeLabel,
      })
    )
  );
}
