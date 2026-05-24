(() => {
  const config = window.momsyMusicConfig || {};
  const labels = config.labels || {};
  const contentSelectors = Array.isArray(config.contentSelectors) && config.contentSelectors.length
    ? config.contentSelectors
    : ["main#content", "main", "#primary", ".site-main", ".content-area"];
  const player = document.querySelector("[data-momsy-music-player]");
  const playButton = document.querySelector("[data-momsy-music-toggle]");
  const muteButton = document.querySelector("[data-momsy-music-mute]");
  const volumeInput = document.querySelector("[data-momsy-music-volume]");
  const statusNode = document.querySelector("[data-momsy-music-status]");
  const storageKeys = {
    enabled: "momsyMusicEnabled",
    muted: "momsyMusicMuted",
    volume: "momsyMusicVolume",
  };
  const downloadPattern = /\.(?:7z|avi|csv|docx?|gif|jpe?g|m4a|mov|mp3|mp4|pdf|png|rar|svg|webp|xlsx?|zip)(?:[?#].*)?$/i;
  let audio = null;
  let isPlaying = false;
  let isLoadingPage = false;

  if (!player || !playButton || !muteButton || !volumeInput || !config.audioUrl) {
    return;
  }

  const storage = {
    get(key) {
      try {
        return window.localStorage.getItem(key);
      } catch (error) {
        return null;
      }
    },
    set(key, value) {
      try {
        window.localStorage.setItem(key, value);
      } catch (error) {
        return false;
      }

      return true;
    },
  };

  const clampVolume = (value) => {
    const volume = Number.parseFloat(value);

    if (Number.isNaN(volume)) {
      return 0.45;
    }

    return Math.min(1, Math.max(0, volume));
  };

  const announce = (message) => {
    if (statusNode) {
      statusNode.textContent = message;
    }
  };

  const getAudio = () => {
    if (audio) {
      return audio;
    }

    audio = new Audio(config.audioUrl);
    audio.loop = true;
    audio.preload = "none";
    audio.volume = clampVolume(storage.get(storageKeys.volume) || volumeInput.value);
    audio.muted = storage.get(storageKeys.muted) === "true";

    audio.addEventListener("pause", () => {
      if (!audio || audio.ended) {
        return;
      }

      isPlaying = false;
      syncPlayerUi();
    });

    audio.addEventListener("play", () => {
      isPlaying = true;
      syncPlayerUi();
    });

    return audio;
  };

  const syncPlayerUi = () => {
    const muted = audio ? audio.muted : storage.get(storageKeys.muted) === "true";
    const state = isPlaying ? "playing" : "paused";
    const playLabel = isPlaying
      ? labels.pause || "Rahatlatıcı müziği durdur"
      : labels.play || "Rahatlatıcı müziği başlat";
    const muteLabel = muted
      ? labels.unmute || "Sesi aç"
      : labels.mute || "Sesi kapat";

    player.dataset.state = state;
    player.dataset.muted = String(muted);
    player.classList.toggle("is-pjax-loading", isLoadingPage);
    playButton.setAttribute("aria-label", playLabel);
    playButton.setAttribute("aria-pressed", String(isPlaying));
    muteButton.setAttribute("aria-label", muteLabel);
    muteButton.setAttribute("aria-pressed", String(muted));

    announce(isPlaying ? labels.playing || "Müzik çalıyor" : labels.paused || "Müzik duraklatıldı");
  };

  const playMusic = async () => {
    const music = getAudio();
    music.volume = clampVolume(volumeInput.value);

    try {
      await music.play();
      isPlaying = true;
      storage.set(storageKeys.enabled, "true");
    } catch (error) {
      isPlaying = false;
      storage.set(storageKeys.enabled, "false");
    }

    syncPlayerUi();
  };

  const pauseMusic = () => {
    if (audio) {
      audio.pause();
    }

    isPlaying = false;
    storage.set(storageKeys.enabled, "false");
    syncPlayerUi();
  };

  const toggleMusic = async () => {
    if (isPlaying) {
      pauseMusic();
      return;
    }

    await playMusic();
  };

  const toggleMute = () => {
    const music = getAudio();
    music.muted = !music.muted;
    storage.set(storageKeys.muted, String(music.muted));
    announce(music.muted ? labels.muted || "Ses kapalı" : labels.unmuted || "Ses açık");
    syncPlayerUi();
  };

  const setInitialUi = () => {
    const storedVolume = clampVolume(storage.get(storageKeys.volume) || "0.45");
    const storedMuted = storage.get(storageKeys.muted) === "true";

    volumeInput.value = String(storedVolume);
    player.dataset.state = storage.get(storageKeys.enabled) === "true" ? "paused" : "idle";
    player.dataset.muted = String(storedMuted);
    syncPlayerUi();
  };

  const findContent = (root) => {
    for (const selector of contentSelectors) {
      const node = root.querySelector(selector);

      if (node instanceof HTMLElement) {
        return node;
      }
    }

    return null;
  };

  const updateOrRemoveHeadNode = (selector, nextDocument) => {
    const currentNode = document.head.querySelector(selector);
    const nextNode = nextDocument.head.querySelector(selector);

    if (currentNode && nextNode) {
      currentNode.replaceWith(nextNode.cloneNode(true));
      return;
    }

    if (!currentNode && nextNode) {
      document.head.appendChild(nextNode.cloneNode(true));
      return;
    }

    if (currentNode && !nextNode) {
      currentNode.remove();
    }
  };

  const syncDocumentMeta = (nextDocument) => {
    document.title = nextDocument.title || document.title;
    updateOrRemoveHeadNode('meta[name="description"]', nextDocument);
    updateOrRemoveHeadNode('link[rel="canonical"]', nextDocument);

    if (nextDocument.body) {
      document.body.className = nextDocument.body.className;
    }
  };

  const dispatchPageLoad = () => {
    document.dispatchEvent(new CustomEvent("momsy:page-load", {
      detail: {
        url: window.location.href,
      },
    }));
  };

  const shouldSkipLink = (link, event) => {
    if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return true;
    }

    if (link.target && link.target !== "_self") {
      return true;
    }

    if (link.hasAttribute("download") || link.closest("form, #wpadminbar, [data-no-pjax]")) {
      return true;
    }

    const href = link.getAttribute("href");

    if (!href || href.startsWith("#") || href.startsWith("mailto:") || href.startsWith("tel:") || href.startsWith("javascript:")) {
      return true;
    }

    let url;

    try {
      url = new URL(href, window.location.href);
    } catch (error) {
      return true;
    }

    if (url.origin !== window.location.origin || url.protocol !== window.location.protocol) {
      return true;
    }

    if (url.hash) {
      return true;
    }

    if (downloadPattern.test(url.pathname)) {
      return true;
    }

    const path = url.pathname.toLowerCase();

    if (path.includes("/wp-admin") || path.includes("wp-login.php") || path.includes("/wp-json/") || path.includes("/feed/")) {
      return true;
    }

    if (path.includes("/cart") || path.includes("/checkout") || path.includes("/my-account")) {
      return true;
    }

    return false;
  };

  const fallbackToLocation = (url) => {
    window.location.href = url.href;
  };

  const loadPage = async (url, pushState = true) => {
    const currentContent = findContent(document);

    if (!currentContent) {
      fallbackToLocation(url);
      return;
    }

    isLoadingPage = true;
    syncPlayerUi();
    announce(labels.loading || "Sayfa yükleniyor");

    try {
      const response = await window.fetch(url.href, {
        method: "GET",
        credentials: "same-origin",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) {
        throw new Error("request_failed");
      }

      const html = await response.text();
      const nextDocument = new DOMParser().parseFromString(html, "text/html");
      const nextContent = findContent(nextDocument);

      if (!nextContent) {
        throw new Error("content_not_found");
      }

      currentContent.replaceWith(nextContent);
      syncDocumentMeta(nextDocument);

      if (pushState) {
        window.history.pushState({ momsyPjax: true }, "", url.href);
      }

      window.scrollTo({ top: 0, left: 0, behavior: "auto" });
      dispatchPageLoad();
    } catch (error) {
      announce(labels.loadFailed || "Sayfa normal şekilde açılıyor");
      fallbackToLocation(url);
    } finally {
      isLoadingPage = false;
      syncPlayerUi();
    }
  };

  document.addEventListener("click", (event) => {
    const target = event.target;

    if (!(target instanceof Element)) {
      return;
    }

    const link = target.closest("a[href]");

    if (!(link instanceof HTMLAnchorElement) || shouldSkipLink(link, event)) {
      return;
    }

    const url = new URL(link.href);

    event.preventDefault();
    loadPage(url, true);
  });

  window.addEventListener("popstate", () => {
    loadPage(new URL(window.location.href), false);
  });

  playButton.addEventListener("click", () => {
    toggleMusic();
  });

  muteButton.addEventListener("click", () => {
    toggleMute();
  });

  volumeInput.addEventListener("input", () => {
    const volume = clampVolume(volumeInput.value);
    storage.set(storageKeys.volume, String(volume));

    if (audio) {
      audio.volume = volume;
      if (volume > 0 && audio.muted) {
        audio.muted = false;
        storage.set(storageKeys.muted, "false");
      }
    }

    syncPlayerUi();
  });

  window.history.replaceState({ momsyPjax: true }, "", window.location.href);
  setInitialUi();
})();
