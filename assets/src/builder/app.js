import { getBuilderConfig } from "./config.js";
import {
  createInitialBuilderState,
  updateBuilderTitle,
  addBlockToState,
  removeBlockFromState,
  moveBlockInState,
} from "./state.js";
import { createBuilderApi } from "./api.js";
import { BuilderShell } from "./components/BuilderShell.js";

const { createElement, useState } = window.wp.element;

export function App() {
  const config = getBuilderConfig();
  const api = createBuilderApi(config);
  const [state, setState] = useState(() => createInitialBuilderState());
  const [isBlockPickerOpen, setIsBlockPickerOpen] = useState(false);

  const handleTitleChange = (nextTitle) => {
    setState((currentState) => updateBuilderTitle(currentState, nextTitle));
  };

  const handleOpenBlockPicker = () => {
    setIsBlockPickerOpen(true);
  };

  const handleCloseBlockPicker = () => {
    setIsBlockPickerOpen(false);
  };

  const handleAddBlock = (blockType) => {
    // Blocks are created from the shared schema so the future JSON payload stays consistent.
    setState((currentState) => addBlockToState(currentState, blockType));
    setIsBlockPickerOpen(false);
  };

  const handleRemoveBlock = (blockId) => {
    setState((currentState) => removeBlockFromState(currentState, blockId));
  };

  const handleMoveBlockUp = (blockId) => {
    setState((currentState) => moveBlockInState(currentState, blockId, "up"));
  };

  const handleMoveBlockDown = (blockId) => {
    setState((currentState) => moveBlockInState(currentState, blockId, "down"));
  };

  return createElement(BuilderShell, {
    api,
    config,
    isBlockPickerOpen,
    state,
    onAddBlock: handleAddBlock,
    onCloseBlockPicker: handleCloseBlockPicker,
    onMoveBlockDown: handleMoveBlockDown,
    onMoveBlockUp: handleMoveBlockUp,
    onOpenBlockPicker: handleOpenBlockPicker,
    onRemoveBlock: handleRemoveBlock,
    onTitleChange: handleTitleChange,
  });
}
