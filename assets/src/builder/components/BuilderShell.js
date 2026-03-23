import { TitleField } from "./TitleField.js";
import { FeaturedImagePlaceholder } from "./FeaturedImagePlaceholder.js";
import { BuilderContentArea } from "./BuilderContentArea.js";
import { BuilderFooterActions } from "./BuilderFooterActions.js";
import { BlockPickerModal } from "./BlockPickerModal.js";

const { createElement } = window.wp.element;

export function BuilderShell({
  config,
  isBlockPickerOpen,
  state,
  onAddBlock,
  onCloseBlockPicker,
  onMoveBlockDown,
  onMoveBlockUp,
  onOpenBlockPicker,
  onRemoveBlock,
  onTitleChange,
}) {
  return createElement(
    "section",
    { className: "momsy-builder" },
    createElement(
      "header",
      { className: "momsy-builder-hero page-intro page-intro--compact" },
      createElement("span", { className: "section-kicker" }, "Momsy Studio"),
      createElement("h1", null, config.pageTitle),
      createElement(
        "div",
        { className: "momsy-builder-hero__meta" },
        createElement("span", { className: "status-pill" }, config.i18n.statusLabel),
        config.currentUser.displayName
          ? createElement(
              "span",
              { className: "meta-inline" },
              "Yazar: ",
              createElement("strong", null, config.currentUser.displayName)
            )
          : null
      ),
      createElement(
        "p",
        { className: "page-intro__description" },
        "Özel içerik oluşturma deneyiminin ilk shell sürümü hazır. Bu ekran bir sonraki adımda blok ekleme ve kayıt akışlarını barındıracak."
      )
    ),
    createElement(
      "div",
      { className: "momsy-builder-layout" },
      createElement(
        "div",
        { className: "momsy-builder-main" },
        createElement(TitleField, {
          label: config.i18n.titleLabel,
          placeholder: config.i18n.titlePlaceholder,
          value: state.title,
          onChange: onTitleChange,
        }),
        createElement(FeaturedImagePlaceholder, {
          label: config.i18n.featuredImageLabel,
          helpText: config.i18n.featuredImageHelp,
        }),
        createElement(BuilderContentArea, {
          addLabel: config.i18n.addContent,
          blocks: state.blocks,
          description: config.i18n.contentSectionDescription,
          emptyDescription: config.i18n.emptyStateDescription,
          emptyTitle: config.i18n.emptyStateTitle,
          moveDownLabel: config.i18n.moveDown,
          moveUpLabel: config.i18n.moveUp,
          onAddClick: onOpenBlockPicker,
          onMoveDown: onMoveBlockDown,
          onMoveUp: onMoveBlockUp,
          onRemove: onRemoveBlock,
          removeLabel: config.i18n.deleteBlock,
          title: config.i18n.contentSectionTitle,
        })
      )
    ),
    createElement(BuilderFooterActions, {
      saveLabel: config.i18n.saveDraft,
      publishLabel: config.i18n.publish,
      publishDisabled: !config.canPublish,
    }),
    createElement(BlockPickerModal, {
      closeLabel: config.i18n.closeModal,
      description: config.i18n.modalDescription,
      isOpen: isBlockPickerOpen,
      onClose: onCloseBlockPicker,
      onSelect: onAddBlock,
      selectLabel: config.i18n.selectBlock,
      title: config.i18n.modalTitle,
    })
  );
}
