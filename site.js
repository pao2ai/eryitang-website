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

const categoryLinks = document.querySelectorAll(".category-bar [data-category]");
const subcategoryLinks = document.getElementById("subcategoryLinks");

const categoryChildren = {
  all: ["全部文章"],
  therapies: ["针灸", "艾灸", "推拿", "正骨", "药蒸药浴", "康复治疗"],
  conditions: ["疼痛调理", "脊柱侧弯", "中风偏瘫", "慢病调理", "男科妇科", "睡眠情绪", "术后康复"],
  tea: ["三降清脂茶", "清心安眠茶", "葛芝醒酒茶", "祛湿茶", "药食同源"],
  cases: ["疼痛与脊柱", "中风康复", "慢病调理", "皮肤问题", "术后康复"],
  news: ["医馆活动", "非遗讲座", "公益交流", "中医文化"]
};

function updateCategoryNavigation() {
  if (!categoryLinks.length || !subcategoryLinks) return;
  const hashCategory = window.location.hash.replace("#", "");
  const activeCategory = categoryChildren[hashCategory] ? hashCategory : "all";

  categoryLinks.forEach((link) => {
    link.classList.toggle("active", link.dataset.category === activeCategory);
  });

  const childLinks = activeCategory === "all"
    ? ""
    : categoryChildren[activeCategory]
      .map((label) => `<a href="#${activeCategory}">${label}</a>`)
      .join("");

  subcategoryLinks.innerHTML = `<a class="active" href="${activeCategory === "all" ? "article-list.html" : `#${activeCategory}`}">全部</a>${childLinks}`;
}

window.addEventListener("hashchange", updateCategoryNavigation);
updateCategoryNavigation();
