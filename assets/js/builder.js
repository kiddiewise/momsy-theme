(function (window, document) {
  "use strict";

  var root = document.getElementById("momsy-content-builder-root");

  if (!root) {
    return;
  }

  function renderFallback(message) {
    root.innerHTML =
      '<article class="momsy-builder-runtime-fallback">' +
      '<span class="section-kicker">Builder</span>' +
      "<h1>Yeni Yazı Oluştur</h1>" +
      "<p>" +
      message +
      "</p>" +
      "</article>";
  }

  function shallowMerge(base, extension) {
    var result = {};
    var source = base || {};
    var extra = extension || {};
    var key;

    for (key in source) {
      if (Object.prototype.hasOwnProperty.call(source, key)) {
        result[key] = source[key];
      }
    }

    for (key in extra) {
      if (Object.prototype.hasOwnProperty.call(extra, key)) {
        result[key] = extra[key];
      }
    }

    return result;
  }

  function getBuilderConfig() {
    var config = window.momsyBuilderConfig || {};
    var defaultI18n = {
      titleLabel: "Yazı başlığı",
      titlePlaceholder: "Başlığını yazmaya başla...",
      featuredImageLabel: "Öne çıkan görsel",
      featuredImageHelp: "Kapak görseli seçme alanı bir sonraki adımda aktif olacak.",
      contentSectionTitle: "İçerik akışı",
      contentSectionDescription:
        "Bloklar sıralı şekilde burada birikecek. Bu sürümde ekleme, silme ve yukarı aşağı sıralama aktif.",
      emptyStateTitle: "İçerik blokları burada görünecek",
      emptyStateDescription:
        "İlk sürümde bu alan blokları listeleyecek. Şimdilik boş shell görünümü hazırlanıyor.",
      addContent: "İçerik ekle +",
      modalTitle: "Bir blok seç",
      modalDescription:
        "Aşağıdaki bileşenlerden birini seçerek yazı akışına yeni bir parça ekleyebilirsin.",
      closeModal: "Kapat",
      selectBlock: "Bloğu ekle",
      moveUp: "Yukarı taşı",
      moveDown: "Aşağı taşı",
      deleteBlock: "Bloğu sil",
      saveDraft: "Taslak Kaydet",
      publish: "Yayınla",
      statusLabel: "Hazırlık aşaması",
    };

    return {
      pageTitle: config.pageTitle || "Yeni Yazı Oluştur",
      restUrl: config.restUrl || "",
      restNonce: config.restNonce || "",
      postType: config.postType || "post",
      canPublish: Boolean(config.canPublish),
      currentUser: config.currentUser || { id: 0, displayName: "" },
      i18n: shallowMerge(defaultI18n, config.i18n || {}),
    };
  }

  var blockIdCounter = 0;
  var BLOCK_REGISTRY = {
    text: {
      type: "text",
      label: "Text",
      description: "Paragraf ve uzun yazı alanları için temel blok.",
      createProps: function () {
        return {
          html: "<p>Yeni metin bloğu içeriği buraya gelecek.</p>",
        };
      },
      getPreview: function (props) {
        return props && props.html
          ? "Paragraf içeriği placeholder ön izlemesi hazır."
          : "Boş paragraf bloğu.";
      },
    },
    heading: {
      type: "heading",
      label: "Heading",
      description: "Başlık ve bölüm ayrımları için kullanılır.",
      createProps: function () {
        return {
          level: 2,
          text: "Yeni bölüm başlığı",
          align: "left",
        };
      },
      getPreview: function (props) {
        return props && props.text ? props.text : "Başlık metni bekleniyor.";
      },
    },
    image: {
      type: "image",
      label: "Image",
      description: "Tek görsel, alt metin ve caption alanı için hazır.",
      createProps: function () {
        return {
          attachmentId: 0,
          alt: "",
          caption: "Görsel açıklaması daha sonra eklenecek.",
          size: "large",
        };
      },
      getPreview: function () {
        return "Henüz görsel seçilmedi. Kapak benzeri bir medya alanı hazır.";
      },
    },
    quote: {
      type: "quote",
      label: "Quote",
      description: "Alıntı, uzman görüşü veya vurucu cümleler için.",
      createProps: function () {
        return {
          text: "Vurgulanacak alıntı metni buraya gelecek.",
          cite: "Kaynak veya konuşmacı",
        };
      },
      getPreview: function (props) {
        return props && props.text ? props.text : "Alıntı metni bekleniyor.";
      },
    },
    cta: {
      type: "cta",
      label: "CTA",
      description: "Yönlendirme kutusu, buton ve kısa açıklama alanı.",
      createProps: function () {
        return {
          title: "Harekete geçirici kutu",
          description: "Okuyucuyu bir sonraki adıma taşıyacak kısa açıklama.",
          buttonLabel: "Detaylar",
          buttonUrl: "#",
          variant: "soft",
        };
      },
      getPreview: function (props) {
        if (!props || !props.title) {
          return "CTA kutusu içeriği bekleniyor.";
        }

        return props.title + " - " + (props.buttonLabel || "Buton");
      },
    },
    slider: {
      type: "slider",
      label: "Slider",
      description: "Birden fazla görseli sıralı galeri gibi göstermek için.",
      createProps: function () {
        return {
          items: [
            { attachmentId: 0, caption: "İlk slider görseli" },
            { attachmentId: 0, caption: "İkinci slider görseli" },
          ],
        };
      },
      getPreview: function (props) {
        var count = props && Array.isArray(props.items) ? props.items.length : 0;
        return count + " görsel için yer ayrıldı.";
      },
    },
    divider: {
      type: "divider",
      label: "Divider",
      description: "Bölümler arası görsel ayırıcı ve nefes alanı ekler.",
      createProps: function () {
        return {
          style: "line",
          spacing: "md",
        };
      },
      getPreview: function (props) {
        var style = props && props.style ? props.style : "line";
        var spacing = props && props.spacing ? props.spacing : "md";
        return "Ayırıcı stili: " + style + ", boşluk: " + spacing + ".";
      },
    },
  };

  function getBlockRegistry() {
    var registry = [];
    var key;

    for (key in BLOCK_REGISTRY) {
      if (Object.prototype.hasOwnProperty.call(BLOCK_REGISTRY, key)) {
        registry.push(BLOCK_REGISTRY[key]);
      }
    }

    return registry;
  }

  function getBlockDefinition(type) {
    return BLOCK_REGISTRY[type] || null;
  }

  function createBlockId(type) {
    blockIdCounter += 1;
    return "blk_" + type + "_" + new Date().getTime().toString(36) + "_" + blockIdCounter.toString(36);
  }

  function createDefaultBlock(type) {
    var definition = getBlockDefinition(type);

    if (!definition) {
      throw new Error("Unknown block type: " + type);
    }

    return {
      id: createBlockId(type),
      type: definition.type,
      props: definition.createProps(),
    };
  }

  function getBlockPreviewText(block) {
    var definition = getBlockDefinition(block.type);

    if (!definition) {
      return "Tanımsız blok tipi.";
    }

    return definition.getPreview(block.props || {});
  }

  function createInitialBuilderState() {
    return {
      version: 1,
      title: "",
      featuredImage: null,
      blocks: [],
    };
  }

  function updateBuilderTitle(state, nextTitle) {
    return shallowMerge(state, { title: nextTitle });
  }

  function addBlockToState(state, blockType) {
    return shallowMerge(state, {
      blocks: state.blocks.concat([createDefaultBlock(blockType)]),
    });
  }

  function removeBlockFromState(state, blockId) {
    return shallowMerge(state, {
      blocks: state.blocks.filter(function (block) {
        return block.id !== blockId;
      }),
    });
  }

  function moveBlockInState(state, blockId, direction) {
    var currentIndex = -1;
    var index;
    var targetIndex;
    var nextBlocks;
    var movedBlock;

    for (index = 0; index < state.blocks.length; index += 1) {
      if (state.blocks[index].id === blockId) {
        currentIndex = index;
        break;
      }
    }

    if (currentIndex < 0) {
      return state;
    }

    targetIndex = direction === "up" ? currentIndex - 1 : currentIndex + 1;

    if (targetIndex < 0 || targetIndex >= state.blocks.length) {
      return state;
    }

    nextBlocks = state.blocks.slice();
    movedBlock = nextBlocks.splice(currentIndex, 1)[0];
    nextBlocks.splice(targetIndex, 0, movedBlock);

    return shallowMerge(state, {
      blocks: nextBlocks,
    });
  }

  function createBuilderApi(config) {
    return {
      config: config,
      saveDraft: function () {
        return Promise.resolve(null);
      },
      publish: function () {
        return Promise.resolve(null);
      },
    };
  }

  function bootstrapBuilder() {
    var wpElement = window.wp && window.wp.element ? window.wp.element : null;
    var createElement;
    var useState;
    var useEffect;
    var useRef;

    if (!wpElement || typeof wpElement.createElement !== "function") {
      renderFallback("Builder uygulaması başlatılamadı. WordPress script bağımlılıkları yüklenemedi.");
      return;
    }

    createElement = wpElement.createElement;
    useState = wpElement.useState;
    useEffect = wpElement.useEffect;
    useRef = wpElement.useRef;

    function TitleField(props) {
      return createElement(
        "section",
        { className: "momsy-builder-card momsy-builder-card--field" },
        createElement(
          "label",
          { className: "momsy-builder-label", htmlFor: "momsy-builder-title" },
          props.label
        ),
        createElement("input", {
          id: "momsy-builder-title",
          className: "momsy-builder-input",
          type: "text",
          value: props.value,
          placeholder: props.placeholder,
          autoComplete: "off",
          onChange: function (event) {
            props.onChange(event.target.value);
          },
        })
      );
    }

    function FeaturedImagePlaceholder(props) {
      return createElement(
        "section",
        { className: "momsy-builder-card momsy-builder-card--field" },
        createElement("span", { className: "momsy-builder-label" }, props.label),
        createElement(
          "div",
          { className: "momsy-builder-media-placeholder", "aria-hidden": "true" },
          createElement("div", { className: "momsy-builder-media-placeholder__art" }),
          createElement(
            "div",
            { className: "momsy-builder-media-placeholder__copy" },
            createElement("strong", null, "Kapak görseli alanı"),
            createElement("span", null, props.helpText)
          )
        )
      );
    }

    function ContentEmptyState(props) {
      return createElement(
        "div",
        { className: "momsy-builder-empty-state" },
        createElement("span", { className: "section-kicker" }, "Builder"),
        createElement("h2", null, props.title),
        createElement("p", null, props.description)
      );
    }

    function AddBlockButton(props) {
      return createElement(
        "button",
        {
          type: "button",
          className: "button-primary momsy-builder-button momsy-builder-button--add",
          onClick: props.onClick,
        },
        props.label
      );
    }

    function BlockActionButton(props) {
      var className = props.tone === "danger"
        ? "momsy-builder-control momsy-builder-control--danger"
        : "momsy-builder-control";

      return createElement(
        "button",
        {
          type: "button",
          className: className,
          disabled: props.disabled,
          "aria-disabled": String(Boolean(props.disabled)),
          onClick: props.onClick,
        },
        props.label
      );
    }

    function BlockItem(props) {
      var definition = getBlockDefinition(props.block.type);
      var label = definition ? definition.label : props.block.type;
      var description = definition
        ? definition.description
        : "Bu blok tipi için tanım bulunamadı.";
      var previewText = getBlockPreviewText(props.block);

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
                "#" + (props.index + 1)
              ),
              createElement("span", { className: "momsy-builder-block-card__type" }, label)
            ),
            createElement("code", { className: "momsy-builder-block-card__slug" }, props.block.type)
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
            createElement(BlockActionButton, {
              disabled: props.isFirst,
              label: props.moveUpLabel,
              onClick: function () {
                props.onMoveUp(props.block.id);
              },
            }),
            createElement(BlockActionButton, {
              disabled: props.isLast,
              label: props.moveDownLabel,
              onClick: function () {
                props.onMoveDown(props.block.id);
              },
            }),
            createElement(BlockActionButton, {
              label: props.removeLabel,
              onClick: function () {
                props.onRemove(props.block.id);
              },
              tone: "danger",
            })
          )
        )
      );
    }

    function BlockList(props) {
      return createElement(
        "ol",
        { className: "momsy-builder-block-list" },
        props.blocks.map(function (block, index) {
          return createElement(BlockItem, {
            key: block.id,
            block: block,
            index: index,
            isFirst: index === 0,
            isLast: index === props.blocks.length - 1,
            moveDownLabel: props.moveDownLabel,
            moveUpLabel: props.moveUpLabel,
            onMoveDown: props.onMoveDown,
            onMoveUp: props.onMoveUp,
            onRemove: props.onRemove,
            removeLabel: props.removeLabel,
          });
        })
      );
    }

    function getBlockCountLabel(count) {
      return count === 1 ? "1 blok" : count + " blok";
    }

    function BuilderContentArea(props) {
      var hasBlocks = props.blocks.length > 0;

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
            createElement("h2", null, props.title),
            createElement("p", null, props.description)
          ),
          createElement(
            "span",
            { className: "status-pill momsy-builder-content-header__count" },
            getBlockCountLabel(props.blocks.length)
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
                blocks: props.blocks,
                moveDownLabel: props.moveDownLabel,
                moveUpLabel: props.moveUpLabel,
                onMoveDown: props.onMoveDown,
                onMoveUp: props.onMoveUp,
                onRemove: props.onRemove,
                removeLabel: props.removeLabel,
              })
            : createElement(ContentEmptyState, {
                title: props.emptyTitle,
                description: props.emptyDescription,
              })
        ),
        createElement(
          "div",
          { className: "momsy-builder-content-actions" },
          createElement(AddBlockButton, {
            label: props.addLabel,
            onClick: props.onAddClick,
          })
        )
      );
    }

    function BuilderFooterActions(props) {
      return createElement(
        "footer",
        { className: "momsy-builder-footer momsy-builder-card" },
        createElement(
          "div",
          { className: "momsy-builder-footer__meta" },
          createElement("span", { className: "status-pill" }, "MVP Shell"),
          createElement(
            "p",
            null,
            "Kaydetme, yayınlama ve blok ekleme akışları bir sonraki adımda bağlanacak."
          )
        ),
        createElement(
          "div",
          { className: "momsy-builder-footer__actions" },
          createElement(
            "button",
            {
              type: "button",
              className: "button-secondary momsy-builder-button",
              disabled: true,
              "aria-disabled": "true",
            },
            props.saveLabel
          ),
          createElement(
            "button",
            {
              type: "button",
              className: "button-primary momsy-builder-button",
              disabled: props.publishDisabled,
              "aria-disabled": String(Boolean(props.publishDisabled)),
            },
            props.publishLabel
          )
        )
      );
    }

    function BlockPickerModal(props) {
      var dialogRef = useRef(null);
      var options = getBlockRegistry();

      useEffect(function () {
        function handleKeyDown(event) {
          if (event.key === "Escape") {
            props.onClose();
          }
        }

        if (!props.isOpen) {
          return undefined;
        }

        document.addEventListener("keydown", handleKeyDown);

        if (dialogRef.current) {
          dialogRef.current.focus();
        }

        return function () {
          document.removeEventListener("keydown", handleKeyDown);
        };
      }, [props.isOpen, props.onClose]);

      if (!props.isOpen) {
        return null;
      }

      return createElement(
        "div",
        {
          className: "momsy-builder-modal",
          onClick: function (event) {
            if (event.target === event.currentTarget) {
              props.onClose();
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
              createElement("h2", { id: "momsy-builder-modal-title" }, props.title),
              createElement("p", null, props.description)
            ),
            createElement(
              "button",
              {
                type: "button",
                className: "button-secondary momsy-builder-button momsy-builder-button--close",
                onClick: props.onClose,
              },
              props.closeLabel
            )
          ),
          createElement(
            "div",
            { className: "momsy-builder-modal__grid" },
            options.map(function (option) {
              return createElement(
                "button",
                {
                  key: option.type,
                  type: "button",
                  className: "momsy-builder-picker-card",
                  onClick: function () {
                    props.onSelect(option.type);
                  },
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
                  props.selectLabel
                )
              );
            })
          )
        )
      );
    }

    function BuilderShell(props) {
      return createElement(
        "section",
        { className: "momsy-builder" },
        createElement(
          "header",
          { className: "momsy-builder-hero page-intro page-intro--compact" },
          createElement("span", { className: "section-kicker" }, "Momsy Studio"),
          createElement("h1", null, props.config.pageTitle),
          createElement(
            "div",
            { className: "momsy-builder-hero__meta" },
            createElement("span", { className: "status-pill" }, props.config.i18n.statusLabel),
            props.config.currentUser.displayName
              ? createElement(
                  "span",
                  { className: "meta-inline" },
                  "Yazar: ",
                  createElement("strong", null, props.config.currentUser.displayName)
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
              label: props.config.i18n.titleLabel,
              placeholder: props.config.i18n.titlePlaceholder,
              value: props.state.title,
              onChange: props.onTitleChange,
            }),
            createElement(FeaturedImagePlaceholder, {
              label: props.config.i18n.featuredImageLabel,
              helpText: props.config.i18n.featuredImageHelp,
            }),
            createElement(BuilderContentArea, {
              addLabel: props.config.i18n.addContent,
              blocks: props.state.blocks,
              description: props.config.i18n.contentSectionDescription,
              emptyDescription: props.config.i18n.emptyStateDescription,
              emptyTitle: props.config.i18n.emptyStateTitle,
              moveDownLabel: props.config.i18n.moveDown,
              moveUpLabel: props.config.i18n.moveUp,
              onAddClick: props.onOpenBlockPicker,
              onMoveDown: props.onMoveBlockDown,
              onMoveUp: props.onMoveBlockUp,
              onRemove: props.onRemoveBlock,
              removeLabel: props.config.i18n.deleteBlock,
              title: props.config.i18n.contentSectionTitle,
            })
          )
        ),
        createElement(BuilderFooterActions, {
          saveLabel: props.config.i18n.saveDraft,
          publishLabel: props.config.i18n.publish,
          publishDisabled: !props.config.canPublish,
        }),
        createElement(BlockPickerModal, {
          closeLabel: props.config.i18n.closeModal,
          description: props.config.i18n.modalDescription,
          isOpen: props.isBlockPickerOpen,
          onClose: props.onCloseBlockPicker,
          onSelect: props.onAddBlock,
          selectLabel: props.config.i18n.selectBlock,
          title: props.config.i18n.modalTitle,
        })
      );
    }

    function App() {
      var config = getBuilderConfig();
      var api = createBuilderApi(config);
      var statePair = useState(createInitialBuilderState);
      var state = statePair[0];
      var setState = statePair[1];
      var pickerPair = useState(false);
      var isBlockPickerOpen = pickerPair[0];
      var setIsBlockPickerOpen = pickerPair[1];

      function handleTitleChange(nextTitle) {
        setState(function (currentState) {
          return updateBuilderTitle(currentState, nextTitle);
        });
      }

      function handleOpenBlockPicker() {
        setIsBlockPickerOpen(true);
      }

      function handleCloseBlockPicker() {
        setIsBlockPickerOpen(false);
      }

      function handleAddBlock(blockType) {
        setState(function (currentState) {
          return addBlockToState(currentState, blockType);
        });
        setIsBlockPickerOpen(false);
      }

      function handleRemoveBlock(blockId) {
        setState(function (currentState) {
          return removeBlockFromState(currentState, blockId);
        });
      }

      function handleMoveBlockUp(blockId) {
        setState(function (currentState) {
          return moveBlockInState(currentState, blockId, "up");
        });
      }

      function handleMoveBlockDown(blockId) {
        setState(function (currentState) {
          return moveBlockInState(currentState, blockId, "down");
        });
      }

      return createElement(BuilderShell, {
        api: api,
        config: config,
        isBlockPickerOpen: isBlockPickerOpen,
        state: state,
        onAddBlock: handleAddBlock,
        onCloseBlockPicker: handleCloseBlockPicker,
        onMoveBlockDown: handleMoveBlockDown,
        onMoveBlockUp: handleMoveBlockUp,
        onOpenBlockPicker: handleOpenBlockPicker,
        onRemoveBlock: handleRemoveBlock,
        onTitleChange: handleTitleChange,
      });
    }

    try {
      if (typeof wpElement.createRoot === "function") {
        root.innerHTML = "";
        wpElement.createRoot(root).render(createElement(App));
        return;
      }

      if (typeof wpElement.render === "function") {
        root.innerHTML = "";
        wpElement.render(createElement(App), root);
        return;
      }

      renderFallback("Builder uygulaması başlatılamadı. Bu WordPress sürümünde uygun render API bulunamadı.");
    } catch (error) {
      console.error("Momsy builder bootstrap failed:", error);
      renderFallback("Builder yüklenirken bir JavaScript hatası oluştu. Tarayıcı konsolunu kontrol ederek ayrıntıyı görebilirsin.");
    }
  }

  bootstrapBuilder();
}(window, document));
