(() => {
  const body = document.body;
  const siteHeader = document.getElementById("siteHeader");
  const toggle = document.getElementById("menuToggle");
  const nav = document.getElementById("mainNav");
  const mobileMenu = window.matchMedia("(max-width: 920px)");
  const navAnchor = nav ? document.createComment("eryitang-main-nav") : null;
  let previousRootOverflow = "";
  let previousBodyOverflow = "";

  if (nav && navAnchor) nav.parentNode?.insertBefore(navAnchor, nav);

  const syncNavPosition = () => {
    if (!nav || !navAnchor) return;
    if (mobileMenu.matches) {
      if (nav.parentNode !== body) body.appendChild(nav);
      return;
    }
    if (navAnchor.parentNode && nav.parentNode !== navAnchor.parentNode) {
      navAnchor.parentNode.insertBefore(nav, navAnchor.nextSibling);
    }
  };

  const syncMobileNavTop = () => {
    if (!nav || !mobileMenu.matches) return;
    const headerBottom = siteHeader?.getBoundingClientRect().bottom || 0;
    nav.style.setProperty("--mobile-nav-top", `${Math.max(0, Math.round(headerBottom))}px`);
  };

  const closeMenu = () => {
    if (!body.classList.contains("menu-open")) return;
    body.classList.remove("menu-open");
    document.documentElement.style.overflow = previousRootOverflow;
    body.style.overflow = previousBodyOverflow;
    toggle?.setAttribute("aria-expanded", "false");
    toggle?.setAttribute("aria-label", "打开导航");
  };

  const openMenu = () => {
    syncNavPosition();
    syncMobileNavTop();
    previousRootOverflow = document.documentElement.style.overflow;
    previousBodyOverflow = body.style.overflow;
    document.documentElement.style.overflow = "hidden";
    body.style.overflow = "hidden";
    body.classList.add("menu-open");
    toggle?.setAttribute("aria-expanded", "true");
    toggle?.setAttribute("aria-label", "关闭导航");
  };

  const updateHeader = () => siteHeader?.classList.toggle("scrolled", window.scrollY > 18);
  toggle?.addEventListener("click", () => {
    if (body.classList.contains("menu-open")) closeMenu();
    else openMenu();
  });
  document.querySelectorAll(".main-nav a").forEach((link) => link.addEventListener("click", () => {
    closeMenu();
  }));
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeMenu();
  });
  const handleBreakpointChange = () => {
    closeMenu();
    syncNavPosition();
    syncMobileNavTop();
  };
  if (typeof mobileMenu.addEventListener === "function") mobileMenu.addEventListener("change", handleBreakpointChange);
  else mobileMenu.addListener(handleBreakpointChange);
  window.addEventListener("scroll", () => {
    updateHeader();
    if (body.classList.contains("menu-open")) syncMobileNavTop();
  }, { passive: true });
  window.addEventListener("resize", syncMobileNavTop, { passive: true });
  syncNavPosition();
  syncMobileNavTop();
  updateHeader();

  const revealItems = document.querySelectorAll(".reveal");
  if (!revealItems.length) return;

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const desktopMotion = window.matchMedia("(min-width: 921px)").matches;
  const pageClass = body.classList.contains("page-brand-v1") ? "brand"
    : body.classList.contains("page-contact-v1") ? "contact"
      : body.classList.contains("page-article-list-v1") ? "archive"
        : body.classList.contains("page-article-detail-v1") ? `article:${body.className.match(/postid-(\d+)/)?.[1] || "current"}`
          : "home";
  const storageKey = `eryitang:reveal-seen:${pageClass}`;
  let hasPlayed = false;
  try { hasPlayed = window.sessionStorage.getItem(storageKey) === "1"; } catch (error) { hasPlayed = false; }

  if (hasPlayed || reducedMotion || !desktopMotion || !("IntersectionObserver" in window)) {
    revealItems.forEach((item) => item.classList.add("is-visible"));
    return;
  }

  body.classList.add("motion-ready");
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-visible");
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
  revealItems.forEach((item) => observer.observe(item));
  try { window.sessionStorage.setItem(storageKey, "1"); } catch (error) { /* storage is optional */ }
})();
