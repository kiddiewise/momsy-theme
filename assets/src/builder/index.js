const root = document.getElementById("momsy-content-builder-root");

function renderFallback(message) {
  if (!root) {
    return;
  }

  root.innerHTML = `
    <article class="momsy-builder-runtime-fallback">
      <span class="section-kicker">Builder</span>
      <h1>Yeni Yazı Oluştur</h1>
      <p>${message}</p>
    </article>
  `;
}

async function bootstrapBuilder() {
  if (!root) {
    return;
  }

  const wpElement = window.wp && window.wp.element ? window.wp.element : null;

  if (!wpElement || typeof wpElement.createElement !== "function") {
    renderFallback(
      "Builder uygulaması başlatılamadı. WordPress script bağımlılıkları yüklenemedi."
    );
    return;
  }

  try {
    const { App } = await import("./app.js");
    const appElement = wpElement.createElement(App);

    // Support both older render API and newer createRoot API.
    if (typeof wpElement.createRoot === "function") {
      root.innerHTML = "";
      wpElement.createRoot(root).render(appElement);
      return;
    }

    if (typeof wpElement.render === "function") {
      root.innerHTML = "";
      wpElement.render(appElement, root);
      return;
    }

    renderFallback(
      "Builder uygulaması başlatılamadı. Bu WordPress sürümünde uygun render API bulunamadı."
    );
  } catch (error) {
    console.error("Momsy builder bootstrap failed:", error);
    renderFallback(
      "Builder yüklenirken bir JavaScript hatası oluştu. Tarayıcı konsolunu kontrol ederek ayrıntıyı görebilirsin."
    );
  }
}

if (root) {
  // Boot the builder only on the dedicated front-end content creation page.
  bootstrapBuilder();
}
