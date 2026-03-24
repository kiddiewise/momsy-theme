(() => {
  const config = window.momsyConfig || {};
  const labels = config.labels || {};
  const doc = document.documentElement;
  const body = document.body;
  const nav = document.getElementById("site-nav");
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const themeToggles = document.querySelectorAll("[data-theme-toggle]");
  const header = document.querySelector("[data-site-header]");
  const mobileActionBar = document.querySelector("[data-mobile-actions]");
  const savedPostsKey = "momsySavedPosts";
  const likeButtonsSelector = "[data-like-post]";
  const homeFeed = document.querySelector("[data-home-feed]");
  const homeFeedList = homeFeed?.querySelector("[data-home-feed-list]") || null;
  const homeFeedStatus = homeFeed?.querySelector("[data-home-feed-status]") || null;
  const homeFeedSentinel = homeFeed?.querySelector("[data-home-feed-sentinel]") || null;
  const homeFeedMoreButton = homeFeed?.querySelector("[data-home-feed-more]") || null;
  const homeSearchPanel = document.querySelector("[data-home-search-panel]");
  const homeSearchButtons = document.querySelectorAll("[data-home-search-toggle]");
  const homeSearchCloseButtons = document.querySelectorAll("[data-home-search-close]");
  const homeSearchForm = homeSearchPanel?.querySelector("[data-home-search-form]") || null;
  const homeSearchInput = homeSearchPanel?.querySelector("[data-home-search-input]") || null;
  const homeSearchResults = homeSearchPanel?.querySelector("[data-home-search-results]") || null;
  const homeCategoryJumpButtons = document.querySelectorAll("[data-home-open-categories]");
  const homeNavItems = document.querySelectorAll("[data-home-nav-item]");
  const homeCategoriesSection = document.getElementById("momsy-home-categories");
  let homeFeedController = null;
  let homeFeedObserver = null;
  let homeSearchController = null;
  let homeSearchTimer = null;

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
      button.setAttribute("aria-label", nextLabel || "Tema degistir");
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
      const defaultLabel = button.getAttribute("data-label-default") || labels.like || "Begen";
      const activeLabel = button.getAttribute("data-label-active") || labels.liked || "Begenildi";
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
    const defaultLabel = button.getAttribute("data-label-default") || labels.copy || "Paylas";
    const activeLabel = button.getAttribute("data-label-active") || labels.copied || "Kopyalandi";
    const labelTarget = button.querySelector("[data-label-text]");

    if (!url) {
      return;
    }

    if (navigator.share) {
      try {
        await navigator.share({ title: document.title, url });
        return;
      } catch (error) {
        // User dismissed the share sheet.
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

  const markUiReady = () => {
    doc.classList.remove("momsy-ui-loading");
    doc.classList.add("momsy-ui-ready");
  };

  const syncReadingProgress = () => {
    if (!mobileActionBar) {
      return;
    }

    const maxScroll = Math.max(1, doc.scrollHeight - window.innerHeight);
    const progress = Math.min(1, Math.max(0, window.scrollY / maxScroll));
    mobileActionBar.style.setProperty("--scroll-progress", String(progress));
  };

  const getHomeFeedState = () => {
    if (!homeFeed) {
      return {
        currentCategory: 0,
        currentPage: 1,
        heroId: 0,
        maxPages: 1,
      };
    }

    return {
      currentCategory: Number(homeFeed.dataset.category || "0"),
      currentPage: Number(homeFeed.dataset.page || "1"),
      heroId: Number(homeFeed.dataset.heroId || "0"),
      maxPages: Math.max(1, Number(homeFeed.dataset.maxPages || "1")),
    };
  };

  const setHomeFeedStatus = (message = "", state = "default") => {
    if (!homeFeedStatus) {
      return;
    }

    homeFeedStatus.hidden = message === "";
    homeFeedStatus.textContent = message;
    homeFeedStatus.dataset.state = state;
  };

  const syncHomeFeedMoreButton = () => {
    if (!homeFeedMoreButton || !homeFeed) {
      return;
    }

    const { currentPage, maxPages } = getHomeFeedState();
    const shouldShowButton = !("IntersectionObserver" in window) && currentPage < maxPages;
    homeFeedMoreButton.hidden = !shouldShowButton;
  };

  const syncHomeCategoryButtons = (activeCategory) => {
    if (!homeFeed) {
      return;
    }

    homeFeed.dataset.category = String(activeCategory);

    homeFeed.querySelectorAll("[data-home-category]").forEach((button) => {
      const isActive = button.getAttribute("data-home-category") === String(activeCategory);
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-selected", String(isActive));
    });
  };

  const syncHomeNavState = (activeItem = "home") => {
    homeNavItems.forEach((item) => {
      const isActive = item.getAttribute("data-home-nav-item") === activeItem;
      item.classList.toggle("is-active", isActive);

      if (isActive) {
        item.setAttribute("aria-current", "page");
      } else {
        item.removeAttribute("aria-current");
      }
    });
  };

  const toggleHomeSearch = (forceState) => {
    if (!homeSearchPanel) {
      return;
    }

    const isOpen = !homeSearchPanel.hidden;
    const nextState = typeof forceState === "boolean" ? forceState : !isOpen;

    homeSearchPanel.hidden = !nextState;
    homeSearchPanel.setAttribute("aria-hidden", String(!nextState));

    homeSearchButtons.forEach((button) => {
      button.setAttribute("aria-expanded", String(nextState));
      button.setAttribute("aria-label", nextState ? (labels.searchClose || "Aramayi kapat") : (labels.searchOpen || "Aramayi ac"));
    });

    syncHomeNavState(nextState ? "search" : "home");

    if (nextState) {
      window.requestAnimationFrame(() => {
        homeSearchInput?.focus();
      });
    }
  };

  const renderHomeSearchResults = async (searchTerm) => {
    if (!homeSearchResults || !config.ajaxUrl || !config.homeSearchNonce) {
      return;
    }

    const normalizedTerm = String(searchTerm || "").trim();

    if (homeSearchController) {
      homeSearchController.abort();
      homeSearchController = null;
    }

    if (normalizedTerm.length < 2) {
      homeSearchResults.innerHTML = '<div class="home-search-empty"><p>En az 2 karakter ile arama yapabilirsiniz.</p></div>';
      return;
    }

    const requestController = new AbortController();
    homeSearchController = requestController;
    homeSearchResults.dataset.loading = "true";
    homeSearchResults.innerHTML = `<div class="home-search-empty"><p>${labels.searching || "Arama sonuclari getiriliyor..."}</p></div>`;

    try {
      const payload = new URLSearchParams({
        action: "momsy_home_search",
        nonce: config.homeSearchNonce,
        search: normalizedTerm,
      });

      const response = await window.fetch(config.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: payload.toString(),
        signal: requestController.signal,
      });

      const result = await response.json();

      if (!response.ok || !result || !result.success || !result.data) {
        throw new Error("home_search_failed");
      }

      homeSearchResults.innerHTML = result.data.html || "";
    } catch (error) {
      if (error?.name === "AbortError") {
        return;
      }

      homeSearchResults.innerHTML = `<div class="home-search-empty"><p>${labels.loadError || "Icerikler yuklenirken bir sorun olustu."}</p></div>`;
    } finally {
      if (homeSearchController === requestController) {
        homeSearchController = null;
        delete homeSearchResults.dataset.loading;
      }
    }
  };

  const loadHomeFeed = async ({ append = false, force = false } = {}) => {
    if (!homeFeed || !homeFeedList || (!force && homeFeed.dataset.loading === "true") || !config.ajaxUrl || !config.homePostsNonce) {
      return;
    }

    const { currentCategory, currentPage, heroId, maxPages } = getHomeFeedState();

    if (append && currentPage >= maxPages) {
      syncHomeFeedMoreButton();
      return;
    }

    const nextPage = append ? currentPage + 1 : 1;

    if (!append && homeFeedController) {
      homeFeedController.abort();
    }

    const requestController = new AbortController();
    homeFeedController = requestController;
    homeFeed.dataset.loading = "true";
    homeFeed.setAttribute("aria-busy", "true");
    homeFeed.classList.add("is-loading");

    if (homeFeedMoreButton) {
      homeFeedMoreButton.disabled = true;
    }

    if (!append) {
      setHomeFeedStatus(labels.loading || "Icerikler yukleniyor...");
    }

    try {
      const payload = new URLSearchParams({
        action: "momsy_load_home_posts",
        nonce: config.homePostsNonce,
        page: String(nextPage),
        category_id: String(currentCategory),
        hero_id: String(heroId),
      });

      const response = await window.fetch(config.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: payload.toString(),
        signal: requestController.signal,
      });

      const result = await response.json();

      if (!response.ok || !result || !result.success || !result.data) {
        throw new Error("home_feed_failed");
      }

      if (append) {
        homeFeedList.insertAdjacentHTML("beforeend", result.data.html || "");
      } else {
        homeFeedList.innerHTML = result.data.html || "";
      }

      homeFeed.dataset.page = String(Number(result.data.page || nextPage));
      homeFeed.dataset.maxPages = String(Math.max(1, Number(result.data.maxPages || "1")));
      setHomeFeedStatus("");
      syncHomeFeedMoreButton();
    } catch (error) {
      if (error?.name === "AbortError") {
        return;
      }

      setHomeFeedStatus(labels.loadError || "Icerikler yuklenirken bir sorun olustu.", "error");
    } finally {
      if (homeFeedController === requestController) {
        homeFeed.dataset.loading = "false";
        homeFeed.removeAttribute("aria-busy");
        homeFeed.classList.remove("is-loading");
        homeFeedController = null;

        if (homeFeedMoreButton) {
          homeFeedMoreButton.disabled = false;
        }
      }
    }
  };

  const initHomeFeed = () => {
    if (!homeFeed) {
      return;
    }

    syncHomeNavState("home");
    syncHomeCategoryButtons(homeFeed.dataset.category || "0");
    syncHomeFeedMoreButton();

    homeFeed.querySelectorAll("[data-home-category]").forEach((button) => {
      button.addEventListener("click", () => {
        const categoryId = button.getAttribute("data-home-category");

        if (!categoryId || categoryId === homeFeed.dataset.category) {
          return;
        }

        if (homeFeedController) {
          homeFeedController.abort();
        }

        syncHomeNavState("categories");
        syncHomeCategoryButtons(categoryId);
        loadHomeFeed({ force: true });
      });
    });

    homeFeedMoreButton?.addEventListener("click", () => {
      loadHomeFeed({ append: true });
    });

    homeCategoryJumpButtons.forEach((button) => {
      button.addEventListener("click", () => {
        syncHomeNavState("categories");
        toggleHomeSearch(false);
        homeCategoriesSection?.scrollIntoView({ behavior: "smooth", block: "center" });
      });
    });

    homeSearchButtons.forEach((button) => {
      button.addEventListener("click", () => {
        toggleHomeSearch();
      });
    });

    homeSearchCloseButtons.forEach((button) => {
      button.addEventListener("click", () => {
        toggleHomeSearch(false);
      });
    });

    homeSearchForm?.addEventListener("submit", (event) => {
      event.preventDefault();
      renderHomeSearchResults(homeSearchInput?.value || "");
    });

    homeSearchInput?.addEventListener("input", () => {
      window.clearTimeout(homeSearchTimer);
      const nextTerm = homeSearchInput.value || "";

      homeSearchTimer = window.setTimeout(() => {
        renderHomeSearchResults(nextTerm);
      }, 220);
    });

    if (homeSearchResults) {
      homeSearchResults.innerHTML = '<div class="home-search-empty"><p>Aramak istediginiz konuyu yazin.</p></div>';
    }

    if (!homeFeedSentinel) {
      return;
    }

    if (!("IntersectionObserver" in window)) {
      syncHomeFeedMoreButton();
      return;
    }

    homeFeedObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          loadHomeFeed({ append: true });
        }
      });
    }, {
      rootMargin: "180px 0px",
    });

    homeFeedObserver.observe(homeFeedSentinel);
  };

  const currentStoredTheme = storage.get("momsyTheme");
  setTheme(currentStoredTheme || config.defaultTheme || "system", false);
  syncSavedButtons();
  syncReadingProgress();

  if (document.readyState === "complete") {
    window.setTimeout(markUiReady, 120);
  } else {
    window.addEventListener("load", () => {
      window.setTimeout(markUiReady, 120);
    }, { once: true });
  }

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
      toggleHomeSearch(false);
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

  initHomeFeed();

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

  const syncChrome = () => {
    syncHeader();
    syncReadingProgress();
  };

  syncChrome();
  window.addEventListener("scroll", syncChrome, { passive: true });
  window.addEventListener("resize", syncReadingProgress, { passive: true });
})();
