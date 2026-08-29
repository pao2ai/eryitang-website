(() => {
  document.querySelectorAll("[data-carousel]").forEach((carousel) => {
    const viewport = carousel.querySelector(".doctor-viewport");
    const track = carousel.querySelector(".doctor-grid");
    const cards = Array.from(carousel.querySelectorAll(".doctor-card"));
    const controls = carousel.querySelector(".doctor-controls");
    const previous = carousel.querySelector(".doctor-prev");
    const next = carousel.querySelector(".doctor-next");
    if (!viewport || !track || !controls || !previous || !next || !cards.length) return;

    let current = 0;
    let touchStartX = null;
    const visibleCount = () => window.innerWidth <= 781 ? 1 : window.innerWidth <= 1050 ? 2 : 3;
    const update = () => {
      const visible = visibleCount();
      const maximum = Math.max(0, cards.length - visible);
      const gap = parseFloat(window.getComputedStyle(track).columnGap) || 0;
      current = Math.min(current, maximum);
      const requested = current * (cards[0].getBoundingClientRect().width + gap);
      const maximumOffset = Math.max(0, track.scrollWidth - viewport.clientWidth);
      track.style.transform = `translateX(-${Math.min(requested, maximumOffset)}px)`;
      controls.hidden = cards.length <= visible;
      previous.disabled = current === 0;
      next.disabled = current === maximum;
      cards.forEach((card, index) => {
        const visibleCard = index >= current && index < current + visible;
        card.setAttribute("aria-hidden", String(!visibleCard));
        if (card.hasAttribute("data-card-href")) card.tabIndex = visibleCard ? 0 : -1;
      });
    };
    const move = (direction) => { current = Math.max(0, Math.min(cards.length - visibleCount(), current + direction)); update(); };
    previous.addEventListener("click", () => move(-1));
    next.addEventListener("click", () => move(1));
    carousel.addEventListener("keydown", (event) => {
      if (event.key === "ArrowLeft") move(-1);
      if (event.key === "ArrowRight") move(1);
    });
    viewport.addEventListener("touchstart", (event) => { touchStartX = event.changedTouches[0]?.clientX ?? null; }, { passive: true });
    viewport.addEventListener("touchend", (event) => {
      if (touchStartX === null) return;
      const delta = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
      if (Math.abs(delta) > 48) move(delta > 0 ? -1 : 1);
      touchStartX = null;
    }, { passive: true });
    window.addEventListener("resize", update, { passive: true });
    update();
  });

  document.querySelectorAll("[data-card-href]").forEach((card) => {
    const activate = () => {
      const href = card.dataset.cardHref;
      if (!href) return;
      if (card.dataset.cardNewWindow === "1") {
        window.open(href, "_blank", "noopener,noreferrer");
      } else {
        window.location.assign(href);
      }
    };
    card.addEventListener("click", activate);
    card.addEventListener("keydown", (event) => {
      if (event.key !== "Enter" && event.key !== " ") return;
      event.preventDefault();
      activate();
    });
  });
})();
