const body = document.body;

const revealItems = document.querySelectorAll(".reveal");
const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
const pageKey = (() => {
  if (body.classList.contains("page-brand-v1")) return "brand";
  if (body.classList.contains("page-contact-v1")) return "contact";
  if (body.classList.contains("page-article-list-v1")) return "archive";
  if (body.classList.contains("page-article-detail-v1")) return `article:${body.dataset.articleId || "sample"}`;
  return "home";
})();
const revealStorageKey = `eryitang:reveal-seen:${pageKey}`;
let revealHasPlayed = false;

try {
  revealHasPlayed = window.sessionStorage.getItem(revealStorageKey) === "1";
} catch (error) {
  revealHasPlayed = false;
}

if (revealItems.length && !revealHasPlayed && !prefersReducedMotion && "IntersectionObserver" in window) {
  body.classList.add("motion-ready");
  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-visible");
      observer.unobserve(entry.target);
    });
  }, {
    threshold: 0.12,
    rootMargin: "0px 0px -8% 0px"
  });

  revealItems.forEach((item) => revealObserver.observe(item));
  try {
    window.sessionStorage.setItem(revealStorageKey, "1");
  } catch (error) {
    // Storage may be unavailable in privacy modes; the page remains fully usable.
  }
} else {
  revealItems.forEach((item) => item.classList.add("is-visible"));
}

const articleCategory = document.documentElement.dataset.articleCategory;
if (articleCategory) {
  document.querySelectorAll(".category-bar [data-category]").forEach((link) => {
    if (link.dataset.category === articleCategory) {
      link.setAttribute("aria-current", "page");
    } else {
      link.removeAttribute("aria-current");
    }
  });
}
