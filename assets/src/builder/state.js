import { createDefaultBlock } from "./schema.js";

export function createInitialBuilderState() {
  return {
    version: 1,
    title: "",
    featuredImage: null,
    blocks: [],
  };
}

export function updateBuilderTitle(state, nextTitle) {
  return {
    ...state,
    title: nextTitle,
  };
}

export function addBlockToState(state, blockType) {
  return {
    ...state,
    blocks: [...state.blocks, createDefaultBlock(blockType)],
  };
}

export function removeBlockFromState(state, blockId) {
  return {
    ...state,
    blocks: state.blocks.filter((block) => block.id !== blockId),
  };
}

export function moveBlockInState(state, blockId, direction) {
  const currentIndex = state.blocks.findIndex((block) => block.id === blockId);

  if (currentIndex < 0) {
    return state;
  }

  const targetIndex = direction === "up" ? currentIndex - 1 : currentIndex + 1;

  if (targetIndex < 0 || targetIndex >= state.blocks.length) {
    return state;
  }

  // Reorder immutably so the list can later be serialized straight into JSON.
  const nextBlocks = [...state.blocks];
  const [movedBlock] = nextBlocks.splice(currentIndex, 1);
  nextBlocks.splice(targetIndex, 0, movedBlock);

  return {
    ...state,
    blocks: nextBlocks,
  };
}
