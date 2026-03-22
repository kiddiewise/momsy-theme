(() => {
  const html = document.documentElement;
  const toggle = document.querySelector('[data-theme-toggle]');
  const menuToggle = document.querySelector('[data-menu-toggle]');
  const nav = document.getElementById('site-nav');

  const stored = localStorage.getItem('momsyTheme');
  if (stored) html.setAttribute('data-theme', stored);

  toggle?.addEventListener('click', () => {
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('momsyTheme', next);
  });

  menuToggle?.addEventListener('click', () => {
    const isOpen = nav.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
  });

  document.querySelectorAll('[data-share-post]').forEach((button) => {
    button.addEventListener('click', async () => {
      const url = button.getAttribute('data-share-post');
      if (navigator.share) {
        try {
          await navigator.share({ url, title: document.title });
          return;
        } catch (e) {}
      }
      await navigator.clipboard.writeText(url);
      button.textContent = 'Kopyalandı';
      setTimeout(() => (button.textContent = 'Paylaş'), 1600);
    });
  });
})();
