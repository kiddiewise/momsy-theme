(() => {
  const config = window.momsyConfig || {};
  const labels = config.labels || {};
  const doc = document.documentElement;
  const body = document.body;
  const nav = document.getElementById("site-nav");
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const themeToggles = document.querySelectorAll("[data-theme-toggle]");
  const header = document.querySelector("[data-site-header]");
  const savedPostsKey = "momsySavedPosts";
  const likeButtonsSelector = "[data-like-post]";

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

  const resolveTheme = (theme) => {
    if (theme === "system") {
      return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }

    return theme === "light" ? "light" : "dark";
  };

  const updateThemeToggleLabel = (theme) => {
    if (!themeToggles.length) {
      return;
    }

    const nextLabel = theme === "dark" ? labels.themeLight : labels.themeDark;

    themeToggles.forEach((button) => {
      button.setAttribute("aria-label", nextLabel || "Tema değiştir");
      button.dataset.theme = theme;
    });
  };

  const setTheme = (preference, persist = true) => {
    const preferredTheme = preference || config.defaultTheme || "system";
    const resolvedTheme = resolveTheme(preferredTheme);

    doc.setAttribute("data-theme", resolvedTheme);
    doc.setAttribute("data-theme-preference", preferredTheme);
    updateThemeToggleLabel(resolvedTheme);

    if (persist) {
      storage.set("momsyTheme", preferredTheme);
    }
  };

  const getSavedPosts = () => {
    const raw = storage.get(savedPostsKey);

    if (!raw) {
      return [];
    }

    try {
      const value = JSON.parse(raw);
      return Array.isArray(value) ? value : [];
    } catch (error) {
      return [];
    }
  };

  const setSavedPosts = (posts) => {
    storage.set(savedPostsKey, JSON.stringify(posts));
  };

  const updateSaveButtons = (postId, isSaved) => {
    document.querySelectorAll(`[data-save-post="${postId}"]`).forEach((button) => {
      const defaultLabel = button.getAttribute("data-label-default") || labels.save || "Kaydet";
      const activeLabel = button.getAttribute("data-label-active") || labels.saved || "Kaydedildi";
      const labelTarget = button.querySelector("[data-label-text]");

      button.setAttribute("aria-pressed", String(isSaved));
      button.setAttribute("aria-label", isSaved ? activeLabel : defaultLabel);

      if (labelTarget) {
        labelTarget.textContent = isSaved ? activeLabel : defaultLabel;
      }
    });
  };

  const updateLikeCount = (postId, count) => {
    document.querySelectorAll(`[data-stat="likes"][data-post-id="${postId}"]`).forEach((node) => {
      const valueNode = node.querySelector(".meta-pill__value");
      if (valueNode) {
        valueNode.textContent = String(count);
      }
    });
  };

  const updateLikeButtons = (postId, isLiked) => {
    document.querySelectorAll(`${likeButtonsSelector}[data-like-post="${postId}"]`).forEach((button) => {
      const defaultLabel = button.getAttribute("data-label-default") || labels.like || "Beğen";
      const activeLabel = button.getAttribute("data-label-active") || labels.liked || "Beğenildi";
      const labelTarget = button.querySelector("[data-label-text]");

      button.setAttribute("aria-pressed", String(isLiked));
      button.setAttribute("aria-label", isLiked ? activeLabel : defaultLabel);

      if (labelTarget) {
        labelTarget.textContent = isLiked ? activeLabel : defaultLabel;
      }
    });
  };

  const toggleSavedPost = (postId) => {
    const savedPosts = getSavedPosts();
    const exists = savedPosts.includes(postId);
    const nextSavedPosts = exists ? savedPosts.filter((item) => item !== postId) : [...savedPosts, postId];

    setSavedPosts(nextSavedPosts);
    updateSaveButtons(postId, !exists);
  };

  const toggleLikePost = async (postId) => {
    if (!config.ajaxUrl || !config.likeNonce) {
      return;
    }

    const payload = new URLSearchParams({
      action: "momsy_like_post",
      nonce: config.likeNonce,
      post_id: postId,
    });

    const response = await window.fetch(config.ajaxUrl, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
      },
      body: payload.toString(),
    });

    const result = await response.json();

    if (!response.ok || !result || !result.success) {
      throw new Error("like_failed");
    }

    updateLikeButtons(postId, Boolean(result.data.liked));
    updateLikeCount(postId, Number(result.data.count || 0));
  };

  const copyText = async (value) => {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(value);
      return;
    }

    const textarea = document.createElement("textarea");
    textarea.value = value;
    textarea.setAttribute("readonly", "readonly");
    textarea.style.position = "absolute";
    textarea.style.left = "-9999px";
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);
  };

  const handleShare = async (button) => {
    const url = button.getAttribute("data-share-post");
    const defaultLabel = button.getAttribute("data-label-default") || labels.copy || "Paylaş";
    const activeLabel = button.getAttribute("data-label-active") || labels.copied || "Kopyalandı";
    const labelTarget = button.querySelector("[data-label-text]");

    if (!url) {
      return;
    }

    if (navigator.share) {
      try {
        await navigator.share({ title: document.title, url });
        return;
      } catch (error) {
        // User dismissed the share sheet. Fall back to copy only if needed.
      }
    }

    try {
      await copyText(url);
      if (labelTarget) {
        labelTarget.textContent = activeLabel;
      }

      button.setAttribute("aria-label", activeLabel);

      window.setTimeout(() => {
        if (labelTarget) {
          labelTarget.textContent = defaultLabel;
        }

        button.setAttribute("aria-label", defaultLabel);
      }, 1800);
    } catch (error) {
      window.prompt("URL", url);
    }
  };

  const openMenu = () => {
    if (!nav || !menuToggle) {
      return;
    }

    nav.classList.add("is-open");
    menuToggle.setAttribute("aria-expanded", "true");
    body.classList.add("menu-open");
  };

  const closeMenu = () => {
    if (!nav || !menuToggle) {
      return;
    }

    nav.classList.remove("is-open");
    menuToggle.setAttribute("aria-expanded", "false");
    body.classList.remove("menu-open");
  };

  const syncSavedButtons = () => {
    getSavedPosts().forEach((postId) => updateSaveButtons(String(postId), true));
  };

  const currentStoredTheme = storage.get("momsyTheme");
  setTheme(currentStoredTheme || config.defaultTheme || "system", false);
  syncSavedButtons();

  themeToggles.forEach((button) => {
    button.addEventListener("click", () => {
      const currentTheme = doc.getAttribute("data-theme") === "dark" ? "dark" : "light";
      const nextTheme = currentTheme === "dark" ? "light" : "dark";
      setTheme(nextTheme);
    });
  });

  menuToggle?.addEventListener("click", () => {
    const isOpen = nav?.classList.contains("is-open");
    if (isOpen) {
      closeMenu();
      return;
    }

    openMenu();
  });

  document.addEventListener("click", (event) => {
    const target = event.target;

    if (!(target instanceof HTMLElement)) {
      return;
    }

    if (menuToggle && nav && !nav.contains(target) && !menuToggle.contains(target)) {
      closeMenu();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      closeMenu();
    }
  });

  document.querySelectorAll("[data-share-post]").forEach((button) => {
    button.addEventListener("click", async () => {
      await handleShare(button);
    });
  });

  document.querySelectorAll("[data-save-post]").forEach((button) => {
    const postId = button.getAttribute("data-save-post");
    if (!postId) {
      return;
    }

    button.addEventListener("click", () => {
      toggleSavedPost(postId);
    });
  });

  document.querySelectorAll(likeButtonsSelector).forEach((button) => {
    const postId = button.getAttribute("data-like-post");

    if (!postId) {
      return;
    }

    button.addEventListener("click", async () => {
      if (button.dataset.loading === "true") {
        return;
      }

      button.dataset.loading = "true";

      try {
        await toggleLikePost(postId);
      } catch (error) {
        // Keep the current UI state if the request fails.
      } finally {
        button.dataset.loading = "false";
      }
    });
  });

  const themeMedia = window.matchMedia("(prefers-color-scheme: dark)");
  const handleThemeMediaChange = () => {
    const currentPreference = storage.get("momsyTheme") || config.defaultTheme || "system";
    if (currentPreference === "system") {
      setTheme("system", false);
    }
  };

  if (typeof themeMedia.addEventListener === "function") {
    themeMedia.addEventListener("change", handleThemeMediaChange);
  } else if (typeof themeMedia.addListener === "function") {
    themeMedia.addListener(handleThemeMediaChange);
  }

  const syncHeader = () => {
    if (!header) {
      return;
    }

    header.classList.toggle("is-scrolled", window.scrollY > 12);
  };

  syncHeader();
  window.addEventListener("scroll", syncHeader, { passive: true });
})();
