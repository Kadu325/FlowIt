/* FlowIT 0.1.2: recurso local, sem alterar arquivos do núcleo. */
(() => {
  'use strict';
  const scriptUrl = document.currentScript && document.currentScript.src;
  function applyFavicon() {
    const css = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
      .find(link => /\/flowitwhite\//.test(link.href));
    const base = scriptUrl && /\/flowitwhite\//.test(scriptUrl)
      ? scriptUrl : css && css.href;
    if (!base) return;
    const url = new URL('favicon-flowit.png?v=0.1.2', base).href;
    // Só substitui o ícone nativo depois de confirmar que a imagem carregou.
    const probe = new Image();
    probe.onload = () => {
      document.querySelectorAll('link[rel~="icon"]').forEach(link => link.remove());
      const link = document.createElement('link');
      link.id = 'flowit-favicon';
      link.rel = 'icon';
      link.type = 'image/png';
      link.href = url;
      document.head.appendChild(link);
    };
    probe.src = url;
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', applyFavicon, { once: true });
  } else {
    applyFavicon();
  }
})();
