const body = document.body;
const header = document.getElementById("siteHeader");
const toggle = document.getElementById("menuToggle");
const navLinks = document.querySelectorAll(".main-nav a");

function updateHeader() {
  header?.classList.toggle("scrolled", window.scrollY > 18);
}

toggle?.addEventListener("click", () => {
  const open = body.classList.toggle("menu-open");
  toggle.setAttribute("aria-expanded", String(open));
  toggle.setAttribute("aria-label", open ? "关闭导航" : "打开导航");
});

navLinks.forEach((link) => {
  link.addEventListener("click", () => {
    body.classList.remove("menu-open");
    toggle?.setAttribute("aria-expanded", "false");
  });
});

window.addEventListener("scroll", updateHeader, { passive: true });
updateHeader();
