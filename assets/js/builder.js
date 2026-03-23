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

  function getStringValue(value) {
    if (typeof value === "string") {
      return value;
    }

    if (value === null || typeof value === "undefined") {
      return "";
    }

    return String(value);
  }

  function toPlainText(value) {
    return getStringValue(value)
      .replace(/<[^>]*>/g, " ")
      .replace(/\s+/g, " ")
      .trim();
  }

  function truncateText(value, maxLength) {
    var text = getStringValue(value).trim();

    if (!text || text.length <= maxLength) {
      return text;
    }

    return text.slice(0, maxLength - 3).trim() + "...";
  }

  function normalizeHeadingLevel(value) {
    if (value === 2 || value === "2" || value === "h2") {
      return "h2";
    }

    if (value === 3 || value === "3" || value === "h3") {
      return "h3";
    }

    if (value === 4 || value === "4" || value === "h4") {
      return "h4";
    }

    return "h2";
  }

  function normalizeTextAlign(value) {
    if (value === "center" || value === "right") {
      return value;
    }

    return "left";
  }

  function normalizeImageSize(value) {
    if (value === "thumbnail" || value === "medium" || value === "full") {
      return value;
    }

    return "large";
  }

  function normalizeCtaVariant(value) {
    if (value === "strong" || value === "outline") {
      return value;
    }

    return "soft";
  }

  function normalizeDividerStyle(value) {
    if (value === "space" || value === "dots") {
      return value;
    }

    return "line";
  }

  function normalizeDividerSpacing(value) {
    if (value === "sm" || value === "lg") {
      return value;
    }

    return "md";
  }

  function createDefaultSliderItem() {
    return {
      attachmentId: 0,
      caption: "",
    };
  }

  function normalizeSliderItems(items) {
    var result = [];
    var index;
    var item;

    if (!Array.isArray(items)) {
      return result;
    }

    for (index = 0; index < items.length; index += 1) {
      item = items[index] || {};
      result.push({
        attachmentId: typeof item.attachmentId === "number" ? item.attachmentId : 0,
        caption: getStringValue(item.caption),
      });
    }

    return result;
  }

  function clampNumber(value, min, max) {
    if (value < min) {
      return min;
    }

    if (value > max) {
      return max;
    }

    return value;
  }

  function hasHtmlMarkup(value) {
    return /<[^>]+>/.test(getStringValue(value));
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
          html: "",
        };
      },
      getPreview: function (props) {
        var preview = truncateText(toPlainText(props && props.html), 120);
        return preview || "Boş paragraf bloğu.";
      },
    },
    heading: {
      type: "heading",
      label: "Heading",
      description: "Başlık ve bölüm ayrımları için kullanılır.",
      createProps: function () {
        return {
          level: "h2",
          text: "",
          align: "left",
        };
      },
      getPreview: function (props) {
        var text = getStringValue(props && props.text).trim();

        if (!text) {
          return "Başlık metni bekleniyor.";
        }

        return normalizeHeadingLevel(props && props.level).toUpperCase() + " - " + text;
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
          caption: "",
          size: "large",
        };
      },
      getPreview: function (props) {
        var caption = getStringValue(props && props.caption).trim();
        var alt = getStringValue(props && props.alt).trim();
        var size = normalizeImageSize(props && props.size);

        if (caption || alt) {
          return truncateText(caption || alt, 120) + " (" + size + ")";
        }

        return "Henüz görsel seçilmedi (" + size + ").";
      },
    },
    quote: {
      type: "quote",
      label: "Quote",
      description: "Alıntı, uzman görüşü veya vurucu cümleler için.",
      createProps: function () {
        return {
          text: "",
          cite: "",
        };
      },
      getPreview: function (props) {
        var text = truncateText(getStringValue(props && props.text), 110);
        var cite = getStringValue(props && props.cite).trim();

        if (!text) {
          return "Alıntı metni bekleniyor.";
        }

        return cite ? '"' + text + '" - ' + cite : '"' + text + '"';
      },
    },
    cta: {
      type: "cta",
      label: "CTA",
      description: "Yönlendirme kutusu, buton ve kısa açıklama alanı.",
      createProps: function () {
        return {
          title: "",
          description: "",
          buttonLabel: "",
          buttonUrl: "",
          variant: "soft",
        };
      },
      getPreview: function (props) {
        var title = getStringValue(props && props.title).trim();
        var buttonLabel = getStringValue(props && props.buttonLabel).trim();

        if (!title && !buttonLabel) {
          return "CTA kutusu içeriği bekleniyor.";
        }

        return title || buttonLabel || "CTA";
      },
    },
    slider: {
      type: "slider",
      label: "Slider",
      description: "Birden fazla görseli sıralı galeri gibi göstermek için.",
      createProps: function () {
        return {
          items: [createDefaultSliderItem()],
        };
      },
      getPreview: function (props) {
        var count = normalizeSliderItems(props && props.items).length;

        if (count === 0) {
          return "Slider öğesi bekleniyor.";
        }

        return count === 1 ? "1 slider öğesi hazır." : count + " slider öğesi hazır.";
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
        var style = normalizeDividerStyle(props && props.style);
        var spacing = normalizeDividerSpacing(props && props.spacing);
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

  function updateBlockInState(state, blockId, updater) {
    return shallowMerge(state, {
      blocks: state.blocks.map(function (block) {
        if (block.id !== blockId) {
          return block;
        }

        return updater(block);
      }),
    });
  }

  function updateBlockPropsInState(state, blockId, newProps) {
    return updateBlockInState(state, blockId, function (block) {
      return shallowMerge(block, {
        props: shallowMerge(block.props || {}, newProps || {}),
      });
    });
  }

  function addSliderItemToState(state, blockId) {
    return updateBlockInState(state, blockId, function (block) {
      var blockProps = block.props || {};

      return shallowMerge(block, {
        props: shallowMerge(blockProps, {
          items: normalizeSliderItems(blockProps.items).concat([createDefaultSliderItem()]),
        }),
      });
    });
  }

  function removeSliderItemFromState(state, blockId, itemIndex) {
    return updateBlockInState(state, blockId, function (block) {
      var blockProps = block.props || {};

      return shallowMerge(block, {
        props: shallowMerge(blockProps, {
          items: normalizeSliderItems(blockProps.items).filter(function (_, index) {
            return index !== itemIndex;
          }),
        }),
      });
    });
  }

  function updateSliderItemInState(state, blockId, itemIndex, newItemProps) {
    return updateBlockInState(state, blockId, function (block) {
      var blockProps = block.props || {};

      return shallowMerge(block, {
        props: shallowMerge(blockProps, {
          items: normalizeSliderItems(blockProps.items).map(function (item, index) {
            if (index !== itemIndex) {
              return item;
            }

            return shallowMerge(item, shallowMerge({ attachmentId: 0 }, newItemProps || {}));
          }),
        }),
      });
    });
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

    function FieldLabel(props) {
      return createElement(
        "label",
        {
          className: "momsy-builder-label momsy-builder-editor-field__label",
          htmlFor: props.htmlFor,
        },
        props.children
      );
    }

    function EditorField(props) {
      return createElement(
        "div",
        {
          className: props.className
            ? "momsy-builder-editor-field " + props.className
            : "momsy-builder-editor-field",
        },
        createElement(FieldLabel, { htmlFor: props.htmlFor }, props.label),
        props.children,
        props.helpText
          ? createElement(
              "p",
              {
                className: "momsy-builder-editor-field__help momsy-builder-block-card__description",
              },
              props.helpText
            )
          : null
      );
    }

    function TextInputControl(props) {
      return createElement("input", {
        id: props.id,
        className: props.className
          ? "momsy-builder-input " + props.className
          : "momsy-builder-input",
        type: props.type || "text",
        value: props.value,
        placeholder: props.placeholder || "",
        autoComplete: props.autoComplete || "off",
        onChange: props.onChange,
      });
    }

    function TextareaControl(props) {
      return createElement("textarea", {
        id: props.id,
        className: props.className
          ? "momsy-builder-input momsy-builder-input--textarea " + props.className
          : "momsy-builder-input momsy-builder-input--textarea",
        value: props.value,
        placeholder: props.placeholder || "",
        rows: props.rows || 5,
        style: props.style || null,
        onChange: props.onChange,
      });
    }

    function SelectControl(props) {
      return createElement(
        "select",
        {
          id: props.id,
          className: props.className
            ? "momsy-builder-input " + props.className
            : "momsy-builder-input",
          value: props.value,
          onChange: props.onChange,
        },
        props.options.map(function (option) {
          return createElement(
            "option",
            { key: option.value, value: option.value },
            option.label
          );
        })
      );
    }

    function BlockEditorSection(props) {
      return createElement("div", { className: "momsy-builder-block-card__editor" }, props.children);
    }

    function FieldGrid(props) {
      return createElement(
        "div",
        {
          className: props.compact
            ? "momsy-builder-editor-grid momsy-builder-editor-grid--compact"
            : "momsy-builder-editor-grid",
        },
        props.children
      );
    }

    function PreviewEyebrow(props) {
      return createElement("span", { className: "momsy-builder-preview__eyebrow" }, props.children);
    }

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

    function renderTextPreview(block) {
      var blockProps = block.props || {};
      var html = getStringValue(blockProps.html).trim();
      var paragraphs = html ? html.split(/\n{2,}/).filter(Boolean) : [];

      return createElement(
        "div",
        { className: "momsy-builder-preview momsy-builder-preview--text" },
        createElement(PreviewEyebrow, null, "Live text preview"),
        html && hasHtmlMarkup(html)
          ? createElement("div", {
              className: "momsy-builder-richtext",
              dangerouslySetInnerHTML: { __html: html },
            })
          : createElement(
              "div",
              { className: "momsy-builder-richtext" },
              paragraphs.length
                ? paragraphs.map(function (paragraph, index) {
                    return createElement(
                      "p",
                      { key: block.id + "-text-preview-" + index },
                      paragraph
                    );
                  })
                : createElement(
                    "p",
                    { className: "momsy-builder-preview__placeholder" },
                    "Paragraf metni burada okunakli bir preview olarak gorunecek."
                  )
            )
      );
    }

    function renderHeadingPreview(block) {
      var blockProps = block.props || {};
      var level = normalizeHeadingLevel(blockProps.level);
      var align = normalizeTextAlign(blockProps.align);
      var headingText = getStringValue(blockProps.text).trim() || "Bolum basligi burada gorunecek";

      return createElement(
        "div",
        {
          className:
            "momsy-builder-preview momsy-builder-preview--heading momsy-builder-preview--heading-" +
            level +
            " momsy-builder-preview--align-" +
            align,
        },
        createElement(PreviewEyebrow, null, "Heading preview"),
        createElement(
          level,
          { className: "momsy-builder-preview-heading__text" },
          headingText
        ),
        createElement(
          "p",
          { className: "momsy-builder-preview-heading__meta" },
          level.toUpperCase() + " seviye",
          " ",
          "\u2022",
          " ",
          align
        )
      );
    }

    function renderImagePreview(block) {
      var blockProps = block.props || {};
      var size = normalizeImageSize(blockProps.size);
      var caption = getStringValue(blockProps.caption).trim();
      var alt = getStringValue(blockProps.alt).trim();

      return createElement(
        "figure",
        {
          className:
            "momsy-builder-preview momsy-builder-preview--image momsy-builder-preview--image-" +
            size,
        },
        createElement(
          "div",
          { className: "momsy-builder-image-preview__media" },
          createElement("span", { className: "momsy-builder-preview__eyebrow" }, "Image preview"),
          createElement("span", { className: "momsy-builder-image-preview__size" }, size),
          createElement("div", { className: "momsy-builder-image-preview__glow", "aria-hidden": "true" }),
          createElement(
            "div",
            { className: "momsy-builder-image-preview__surface" },
            createElement("div", { className: "momsy-builder-image-preview__art", "aria-hidden": "true" }),
            createElement(
              "span",
              { className: "momsy-builder-image-preview__label" },
              "Placeholder image"
            )
          )
        ),
        createElement(
          "figcaption",
          { className: "momsy-builder-image-preview__caption" },
          createElement(
            "strong",
            null,
            caption || "Gorsel caption alani burada editorial bir tonla gorunecek."
          ),
          createElement(
            "span",
            null,
            alt ? "Alt: " + alt : "Alt metin girdiginde burada gorunecek."
          )
        )
      );
    }

    function renderQuotePreview(block) {
      var blockProps = block.props || {};
      var quoteText = getStringValue(blockProps.text).trim();
      var cite = getStringValue(blockProps.cite).trim();

      return createElement(
        "blockquote",
        { className: "momsy-builder-preview momsy-builder-preview--quote" },
        createElement("span", { className: "momsy-builder-quote-preview__mark", "aria-hidden": "true" }, "\""),
        createElement(PreviewEyebrow, null, "Editorial quote"),
        createElement(
          "p",
          { className: "momsy-builder-quote-preview__text" },
          quoteText || "Alinti metni geldikce burada premium bir quote kutusu olarak gorunecek."
        ),
        createElement(
          "footer",
          { className: "momsy-builder-quote-preview__cite" },
          createElement("span", null, cite || "Kaynak veya konusmaci")
        )
      );
    }

    function renderCtaPreview(block) {
      var blockProps = block.props || {};
      var variant = normalizeCtaVariant(blockProps.variant);
      var title = getStringValue(blockProps.title).trim();
      var description = getStringValue(blockProps.description).trim();
      var buttonLabel = getStringValue(blockProps.buttonLabel).trim();
      var buttonUrl = getStringValue(blockProps.buttonUrl).trim();

      return createElement(
        "div",
        {
          className:
            "momsy-builder-preview momsy-builder-preview--cta momsy-builder-preview--cta-" +
            variant,
        },
        createElement(
          "div",
          { className: "momsy-builder-cta-preview__copy" },
          createElement(PreviewEyebrow, null, "CTA preview"),
          createElement(
            "h3",
            { className: "momsy-builder-cta-preview__title" },
            title || "Okuyucuyu bir sonraki adima tasiyan modern CTA burada gorunecek."
          ),
          createElement(
            "p",
            { className: "momsy-builder-cta-preview__description" },
            description || "Kisa bir aciklama, ikna edici ve editorial bir tonda burada yer alir."
          )
        ),
        createElement(
          "div",
          { className: "momsy-builder-cta-preview__footer" },
          createElement(
            "button",
            {
              type: "button",
              className:
                "momsy-builder-cta-preview__button momsy-builder-cta-preview__button-" +
                variant,
            },
            buttonLabel || "Buton etiketi"
          ),
          createElement(
            "span",
            { className: "momsy-builder-cta-preview__url" },
            buttonUrl || "https://ornek-link"
          )
        )
      );
    }

    function SliderPreview(props) {
      var items = normalizeSliderItems(props.items);
      var previewState = useState(0);
      var activeIndex = previewState[0];
      var setActiveIndex = previewState[1];
      var safeIndex = items.length ? clampNumber(activeIndex, 0, items.length - 1) : 0;
      var activeItem = items[safeIndex] || createDefaultSliderItem();

      useEffect(function () {
        if (!items.length && activeIndex !== 0) {
          setActiveIndex(0);
          return;
        }

        if (items.length && activeIndex !== safeIndex) {
          setActiveIndex(safeIndex);
        }
      }, [activeIndex, items.length, safeIndex]);

      function goTo(index) {
        if (!items.length) {
          return;
        }

        setActiveIndex(clampNumber(index, 0, items.length - 1));
      }

      return createElement(
        "div",
        { className: "momsy-builder-preview momsy-builder-preview--slider" },
        createElement(
          "div",
          { className: "momsy-builder-slider-preview__top" },
          createElement(PreviewEyebrow, null, "Carousel preview"),
          createElement(
            "span",
            { className: "momsy-builder-slider-preview__count" },
            items.length ? safeIndex + 1 + " / " + items.length : "0 / 0"
          )
        ),
        items.length
          ? createElement(
              "div",
              { className: "momsy-builder-slider-preview__frame" },
              createElement(
                "button",
                {
                  type: "button",
                  className: "momsy-builder-slider-preview__nav",
                  disabled: safeIndex === 0,
                  "aria-disabled": String(safeIndex === 0),
                  onClick: function () {
                    goTo(safeIndex - 1);
                  },
                },
                "<"
              ),
              createElement(
                "article",
                { className: "momsy-builder-slider-preview__stage" },
                createElement(
                  "div",
                  { className: "momsy-builder-slider-preview__media" },
                  createElement("div", { className: "momsy-builder-slider-preview__gradient", "aria-hidden": "true" }),
                  createElement(
                    "span",
                    { className: "momsy-builder-slider-preview__chip" },
                    "attachmentId: 0"
                  ),
                  createElement(
                    "div",
                    { className: "momsy-builder-slider-preview__caption" },
                    createElement(
                      "strong",
                      null,
                      activeItem.caption || "Slide caption burada gorunecek."
                    ),
                    createElement(
                      "span",
                      null,
                      "Instagram benzeri editor preview"
                    )
                  )
                )
              ),
              createElement(
                "button",
                {
                  type: "button",
                  className: "momsy-builder-slider-preview__nav",
                  disabled: safeIndex >= items.length - 1,
                  "aria-disabled": String(safeIndex >= items.length - 1),
                  onClick: function () {
                    goTo(safeIndex + 1);
                  },
                },
                ">"
              )
            )
          : createElement(
              "div",
              { className: "momsy-builder-slider-preview__empty" },
              createElement(
                "p",
                { className: "momsy-builder-preview__placeholder" },
                "Slider bos. Ilk slide eklendiginde carousel preview burada akmaya baslayacak."
              )
            ),
        createElement(
          "div",
          { className: "momsy-builder-slider-preview__track" },
          items.length
            ? items.map(function (item, index) {
                return createElement(
                  "button",
                  {
                    key: props.blockId + "-slider-preview-thumb-" + index,
                    type: "button",
                    className:
                      index === safeIndex
                        ? "momsy-builder-slider-preview__thumb is-active"
                        : "momsy-builder-slider-preview__thumb",
                    onClick: function () {
                      goTo(index);
                    },
                  },
                  createElement("span", { className: "momsy-builder-slider-preview__thumb-index" }, index + 1),
                  createElement(
                    "span",
                    { className: "momsy-builder-slider-preview__thumb-text" },
                    truncateText(item.caption || "Caption ekle", 28)
                  )
                );
              })
            : createElement(
                "span",
                { className: "momsy-builder-slider-preview__dots" },
                "Preview controls bu alanda gorunecek."
              )
        )
      );
    }

    function renderSliderPreview(block) {
      return createElement(SliderPreview, {
        blockId: block.id,
        items: (block.props || {}).items,
      });
    }

    function renderDividerPreview(block) {
      var blockProps = block.props || {};
      var style = normalizeDividerStyle(blockProps.style);
      var spacing = normalizeDividerSpacing(blockProps.spacing);

      return createElement(
        "div",
        {
          className:
            "momsy-builder-preview momsy-builder-preview--divider momsy-builder-preview--divider-" +
            style +
            " momsy-builder-preview--spacing-" +
            spacing,
        },
        createElement(PreviewEyebrow, null, "Divider preview"),
        createElement(
          "div",
          { className: "momsy-builder-divider-preview__surface" },
          style === "line"
            ? createElement("span", { className: "momsy-builder-divider-preview__line", "aria-hidden": "true" })
            : null,
          style === "space"
            ? createElement("span", { className: "momsy-builder-divider-preview__space", "aria-hidden": "true" })
            : null,
          style === "dots"
            ? createElement(
                "span",
                { className: "momsy-builder-divider-preview__dots", "aria-hidden": "true" },
                createElement("i", null),
                createElement("i", null),
                createElement("i", null)
              )
            : null
        )
      );
    }

    var BLOCK_PREVIEW_RENDERERS = {
      text: renderTextPreview,
      heading: renderHeadingPreview,
      image: renderImagePreview,
      quote: renderQuotePreview,
      cta: renderCtaPreview,
      slider: renderSliderPreview,
      divider: renderDividerPreview,
    };

    function renderTextBlockEditor(block, builderActions) {
      var htmlId = block.id + "-html";
      var blockProps = block.props || {};

      return createElement(
        BlockEditorSection,
        null,
        createElement(
          EditorField,
          { htmlFor: htmlId, label: "Metin" },
          createElement(TextareaControl, {
            id: htmlId,
            value: getStringValue(blockProps.html),
            placeholder: "<p>Paragraf HTML ya da metin içeriği...</p>",
            onChange: function (event) {
              builderActions.updateBlockProps(block.id, { html: event.target.value });
            },
          })
        )
      );
    }

    function renderHeadingBlockEditor(block, builderActions) {
      var blockProps = block.props || {};
      var textId = block.id + "-text";
      var levelId = block.id + "-level";
      var alignId = block.id + "-align";

      return createElement(
        BlockEditorSection,
        null,
        createElement(
          EditorField,
          { htmlFor: textId, label: "Başlık metni" },
          createElement(TextInputControl, {
            id: textId,
            value: getStringValue(blockProps.text),
            placeholder: "Bölüm başlığını yaz...",
            onChange: function (event) {
              builderActions.updateBlockProps(block.id, { text: event.target.value });
            },
          })
        ),
        createElement(
          FieldGrid,
          { compact: true },
          createElement(
            EditorField,
            { htmlFor: levelId, label: "Seviye" },
            createElement(SelectControl, {
              id: levelId,
              value: normalizeHeadingLevel(blockProps.level),
              options: [
                { value: "h2", label: "H2" },
                { value: "h3", label: "H3" },
                { value: "h4", label: "H4" },
              ],
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { level: event.target.value });
              },
            })
          ),
          createElement(
            EditorField,
            { htmlFor: alignId, label: "Hizalama" },
            createElement(SelectControl, {
              id: alignId,
              value: normalizeTextAlign(blockProps.align),
              options: [
                { value: "left", label: "Left" },
                { value: "center", label: "Center" },
                { value: "right", label: "Right" },
              ],
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { align: event.target.value });
              },
            })
          )
        )
      );
    }

    function renderImageBlockEditor(block, builderActions) {
      var blockProps = block.props || {};
      var altId = block.id + "-alt";
      var captionId = block.id + "-caption";
      var sizeId = block.id + "-size";

      return createElement(
        BlockEditorSection,
        null,
        createElement(
          FieldGrid,
          null,
          createElement(
            EditorField,
            { htmlFor: altId, label: "Alt metin" },
            createElement(TextInputControl, {
              id: altId,
              value: getStringValue(blockProps.alt),
              placeholder: "Görsel alt metni",
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { alt: event.target.value });
              },
            })
          ),
          createElement(
            EditorField,
            { htmlFor: captionId, label: "Caption" },
            createElement(TextInputControl, {
              id: captionId,
              value: getStringValue(blockProps.caption),
              placeholder: "Görsel açıklaması",
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { caption: event.target.value });
              },
            })
          ),
          createElement(
            EditorField,
            { htmlFor: sizeId, label: "Boyut" },
            createElement(SelectControl, {
              id: sizeId,
              value: normalizeImageSize(blockProps.size),
              options: [
                { value: "thumbnail", label: "Thumbnail" },
                { value: "medium", label: "Medium" },
                { value: "large", label: "Large" },
                { value: "full", label: "Full" },
              ],
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { size: event.target.value });
              },
            })
          )
        )
      );
    }

    function renderQuoteBlockEditor(block, builderActions) {
      var blockProps = block.props || {};
      var textId = block.id + "-quote-text";
      var citeId = block.id + "-quote-cite";

      return createElement(
        BlockEditorSection,
        null,
        createElement(
          EditorField,
          { htmlFor: textId, label: "Alıntı" },
          createElement(TextareaControl, {
            id: textId,
            value: getStringValue(blockProps.text),
            placeholder: "Vurgulanacak alıntıyı yaz...",
            onChange: function (event) {
              builderActions.updateBlockProps(block.id, { text: event.target.value });
            },
          })
        ),
        createElement(
          EditorField,
          { htmlFor: citeId, label: "Kaynak" },
          createElement(TextInputControl, {
            id: citeId,
            value: getStringValue(blockProps.cite),
            placeholder: "Kaynak veya konuşmacı",
            onChange: function (event) {
              builderActions.updateBlockProps(block.id, { cite: event.target.value });
            },
          })
        )
      );
    }

    function renderCtaBlockEditor(block, builderActions) {
      var blockProps = block.props || {};
      var titleId = block.id + "-cta-title";
      var descriptionId = block.id + "-cta-description";
      var buttonLabelId = block.id + "-cta-button-label";
      var buttonUrlId = block.id + "-cta-button-url";
      var variantId = block.id + "-cta-variant";

      return createElement(
        BlockEditorSection,
        null,
        createElement(
          EditorField,
          { htmlFor: titleId, label: "Başlık" },
          createElement(TextInputControl, {
            id: titleId,
            value: getStringValue(blockProps.title),
            placeholder: "CTA başlığı",
            onChange: function (event) {
              builderActions.updateBlockProps(block.id, { title: event.target.value });
            },
          })
        ),
        createElement(
          EditorField,
          { htmlFor: descriptionId, label: "Açıklama" },
          createElement(TextareaControl, {
            id: descriptionId,
            value: getStringValue(blockProps.description),
            placeholder: "Kısa açıklama",
            onChange: function (event) {
              builderActions.updateBlockProps(block.id, { description: event.target.value });
            },
          })
        ),
        createElement(
          FieldGrid,
          null,
          createElement(
            EditorField,
            { htmlFor: buttonLabelId, label: "Buton etiketi" },
            createElement(TextInputControl, {
              id: buttonLabelId,
              value: getStringValue(blockProps.buttonLabel),
              placeholder: "Örnek: Devam et",
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { buttonLabel: event.target.value });
              },
            })
          ),
          createElement(
            EditorField,
            { htmlFor: buttonUrlId, label: "Buton URL" },
            createElement(TextInputControl, {
              id: buttonUrlId,
              type: "url",
              value: getStringValue(blockProps.buttonUrl),
              placeholder: "https://example.com",
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { buttonUrl: event.target.value });
              },
            })
          ),
          createElement(
            EditorField,
            { htmlFor: variantId, label: "Varyant" },
            createElement(SelectControl, {
              id: variantId,
              value: normalizeCtaVariant(blockProps.variant),
              options: [
                { value: "soft", label: "Soft" },
                { value: "strong", label: "Strong" },
                { value: "outline", label: "Outline" },
              ],
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { variant: event.target.value });
              },
            })
          )
        )
      );
    }

    function renderSliderBlockEditor(block, builderActions) {
      var blockProps = block.props || {};
      var items = normalizeSliderItems(blockProps.items);

      return createElement(
        BlockEditorSection,
        null,
        createElement(
          "p",
          { className: "momsy-builder-block-card__description momsy-builder-editor-note" },
          "Görsel seçimi daha sonra bağlanacak. attachmentId değeri şu an 0 olarak kalır."
        ),
        items.length
          ? createElement(
              "div",
              { className: "momsy-builder-slider-editor-list" },
              items.map(function (item, index) {
                var captionId = block.id + "-slider-caption-" + index;

                return createElement(
                  "div",
                  {
                    key: block.id + "-slider-item-" + index,
                    className: "momsy-builder-slider-editor-item",
                  },
                  createElement(
                    "div",
                    { className: "momsy-builder-slider-editor-item__top" },
                    createElement(
                      "strong",
                      { className: "momsy-builder-slider-editor-item__title" },
                      "Slide #" + (index + 1)
                    ),
                    createElement(BlockActionButton, {
                      label: "Sil",
                      tone: "danger",
                      onClick: function () {
                        builderActions.removeSliderItem(block.id, index);
                      },
                    })
                  ),
                  createElement(
                    EditorField,
                    {
                      htmlFor: captionId,
                      label: "Caption",
                      helpText: "attachmentId: 0",
                    },
                    createElement(TextInputControl, {
                      id: captionId,
                      value: getStringValue(item.caption),
                      placeholder: "Slide açıklaması",
                      onChange: function (event) {
                        builderActions.updateSliderItem(block.id, index, {
                          caption: event.target.value,
                        });
                      },
                    })
                  )
                );
              })
            )
          : createElement(
              "p",
              { className: "momsy-builder-block-card__description momsy-builder-editor-note" },
              "Henüz slide yok."
            ),
        createElement(BlockActionButton, {
          label: "Slide ekle",
          onClick: function () {
            builderActions.addSliderItem(block.id);
          },
        })
      );
    }

    function renderDividerBlockEditor(block, builderActions) {
      var blockProps = block.props || {};
      var styleId = block.id + "-divider-style";
      var spacingId = block.id + "-divider-spacing";

      return createElement(
        BlockEditorSection,
        null,
        createElement(
          FieldGrid,
          { compact: true },
          createElement(
            EditorField,
            { htmlFor: styleId, label: "Stil" },
            createElement(SelectControl, {
              id: styleId,
              value: normalizeDividerStyle(blockProps.style),
              options: [
                { value: "line", label: "Line" },
                { value: "space", label: "Space" },
                { value: "dots", label: "Dots" },
              ],
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { style: event.target.value });
              },
            })
          ),
          createElement(
            EditorField,
            { htmlFor: spacingId, label: "Boşluk" },
            createElement(SelectControl, {
              id: spacingId,
              value: normalizeDividerSpacing(blockProps.spacing),
              options: [
                { value: "sm", label: "Small" },
                { value: "md", label: "Medium" },
                { value: "lg", label: "Large" },
              ],
              onChange: function (event) {
                builderActions.updateBlockProps(block.id, { spacing: event.target.value });
              },
            })
          )
        )
      );
    }

    var BLOCK_EDITOR_RENDERERS = {
      text: renderTextBlockEditor,
      heading: renderHeadingBlockEditor,
      image: renderImageBlockEditor,
      quote: renderQuoteBlockEditor,
      cta: renderCtaBlockEditor,
      slider: renderSliderBlockEditor,
      divider: renderDividerBlockEditor,
    };

    function renderBlockPreview(block) {
      var renderer = BLOCK_PREVIEW_RENDERERS[block.type];

      if (!renderer) {
        return createElement(
          "div",
          { className: "momsy-builder-preview" },
          createElement(PreviewEyebrow, null, "Preview"),
          createElement(
            "p",
            { className: "momsy-builder-preview__placeholder" },
            "Bu blok tipi için preview bulunamadı."
          )
        );
      }

      return renderer(block);
    }

    function renderBlockEditor(block, builderActions) {
      var renderer = BLOCK_EDITOR_RENDERERS[block.type];

      if (!renderer) {
        return createElement(
          BlockEditorSection,
          null,
          createElement(
            "p",
            { className: "momsy-builder-block-card__description momsy-builder-editor-note" },
            "Bu blok tipi için editör bulunamadı."
          )
        );
      }

      return renderer(block, builderActions);
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
            createElement("p", { className: "momsy-builder-block-card__preview" }, previewText),
            createElement(
              "div",
              { className: "momsy-builder-block-card__preview-shell" },
              renderBlockPreview(props.block)
            ),
            createElement(
              "div",
              { className: "momsy-builder-block-card__editor-shell" },
              createElement(
                "div",
                { className: "momsy-builder-block-card__editor-head" },
                createElement("strong", null, "Edit alanlari"),
                createElement("span", null, "JSON save state ile senkron")
              ),
              renderBlockEditor(props.block, props.builderActions)
            )
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
            builderActions: props.builderActions,
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
                builderActions: props.builderActions,
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
            "Kaydetme, yayınlama ve medya bağlama akışları bir sonraki adımda bağlanacak."
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
            "Özel içerik oluşturma deneyiminin ilk editör sürümü hazır. Kart içinden blok alanlarını düzenleyebilir, blok ekleyebilir ve sıralayabilirsin."
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
              builderActions: props.builderActions,
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

      function updateBlockProps(blockId, newProps) {
        setState(function (currentState) {
          return updateBlockPropsInState(currentState, blockId, newProps);
        });
      }

      function addSliderItem(blockId) {
        setState(function (currentState) {
          return addSliderItemToState(currentState, blockId);
        });
      }

      function removeSliderItem(blockId, itemIndex) {
        setState(function (currentState) {
          return removeSliderItemFromState(currentState, blockId, itemIndex);
        });
      }

      function updateSliderItem(blockId, itemIndex, newItemProps) {
        setState(function (currentState) {
          return updateSliderItemInState(currentState, blockId, itemIndex, newItemProps);
        });
      }

      return createElement(BuilderShell, {
        api: api,
        builderActions: {
          addSliderItem: addSliderItem,
          removeSliderItem: removeSliderItem,
          updateBlockProps: updateBlockProps,
          updateSliderItem: updateSliderItem,
        },
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
