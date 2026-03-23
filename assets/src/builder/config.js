const DEFAULT_I18N = {
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

export function getBuilderConfig() {
  const config = window.momsyBuilderConfig || {};

  return {
    pageTitle: config.pageTitle || "Yeni Yazı Oluştur",
    restUrl: config.restUrl || "",
    restNonce: config.restNonce || "",
    postType: config.postType || "post",
    canPublish: Boolean(config.canPublish),
    currentUser: config.currentUser || { id: 0, displayName: "" },
    i18n: {
      ...DEFAULT_I18N,
      ...(config.i18n || {}),
    },
  };
}
