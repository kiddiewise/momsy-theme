const BLOCK_REGISTRY = {
  text: {
    type: "text",
    label: "Text",
    description: "Paragraf ve uzun yazı alanları için temel blok.",
    createProps: () => ({
      html: "<p>Yeni metin bloğu içeriği buraya gelecek.</p>",
    }),
    getPreview: (props) =>
      props.html
        ? "Paragraf içeriği placeholder ön izlemesi hazır."
        : "Boş paragraf bloğu.",
  },
  heading: {
    type: "heading",
    label: "Heading",
    description: "Başlık ve bölüm ayrımları için kullanılır.",
    createProps: () => ({
      level: 2,
      text: "Yeni bölüm başlığı",
      align: "left",
    }),
    getPreview: (props) => props.text || "Başlık metni bekleniyor.",
  },
  image: {
    type: "image",
    label: "Image",
    description: "Tek görsel, alt metin ve caption alanı için hazır.",
    createProps: () => ({
      attachmentId: 0,
      alt: "",
      caption: "Görsel açıklaması daha sonra eklenecek.",
      size: "large",
    }),
    getPreview: () => "Henüz görsel seçilmedi. Kapak benzeri bir medya alanı hazır.",
  },
  quote: {
    type: "quote",
    label: "Quote",
    description: "Alıntı, uzman görüşü veya vurucu cümleler için.",
    createProps: () => ({
      text: "Vurgulanacak alıntı metni buraya gelecek.",
      cite: "Kaynak veya konuşmacı",
    }),
    getPreview: (props) => props.text || "Alıntı metni bekleniyor.",
  },
  cta: {
    type: "cta",
    label: "CTA",
    description: "Yönlendirme kutusu, buton ve kısa açıklama alanı.",
    createProps: () => ({
      title: "Harekete geçirici kutu",
      description: "Okuyucuyu bir sonraki adıma taşıyacak kısa açıklama.",
      buttonLabel: "Detaylar",
      buttonUrl: "#",
      variant: "soft",
    }),
    getPreview: (props) =>
      props.title
        ? `${props.title} - ${props.buttonLabel || "Buton"}`
        : "CTA kutusu içeriği bekleniyor.",
  },
  slider: {
    type: "slider",
    label: "Slider",
    description: "Birden fazla görseli sıralı galeri gibi göstermek için.",
    createProps: () => ({
      items: [
        { attachmentId: 0, caption: "İlk slider görseli" },
        { attachmentId: 0, caption: "İkinci slider görseli" },
      ],
    }),
    getPreview: (props) =>
      `${Array.isArray(props.items) ? props.items.length : 0} görsel için yer ayrıldı.`,
  },
  divider: {
    type: "divider",
    label: "Divider",
    description: "Bölümler arası görsel ayırıcı ve nefes alanı ekler.",
    createProps: () => ({
      style: "line",
      spacing: "md",
    }),
    getPreview: (props) =>
      `Ayırıcı stili: ${props.style || "line"}, boşluk: ${props.spacing || "md"}.`,
  },
};

let blockIdCounter = 0;

function createBlockId(type) {
  blockIdCounter += 1;

  return `blk_${type}_${Date.now().toString(36)}_${blockIdCounter.toString(36)}`;
}

export function getBlockRegistry() {
  return Object.values(BLOCK_REGISTRY);
}

export function getBlockDefinition(type) {
  return BLOCK_REGISTRY[type] || null;
}

export function createDefaultBlock(type) {
  const definition = getBlockDefinition(type);

  if (!definition) {
    throw new Error(`Unknown block type: ${type}`);
  }

  return {
    id: createBlockId(type),
    type: definition.type,
    props: definition.createProps(),
  };
}

export function getBlockPreviewText(block) {
  const definition = getBlockDefinition(block.type);

  if (!definition) {
    return "Tanımsız blok tipi.";
  }

  return definition.getPreview(block.props || {});
}
