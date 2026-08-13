(function () {
  const body = document.body;
  const categoryKeys = new Set(["therapies", "conditions", "tea", "cases", "news"]);
  const currentUrl = new URL(window.location.href);
  const requestedCategory = currentUrl.searchParams.get("category");
  const active = (body.classList.contains("page-article-list-v1") || body.classList.contains("page-article-detail-v1")) && categoryKeys.has(requestedCategory)
    ? requestedCategory
    : body.dataset.navActive || "";
  const footerAd = body.dataset.footerAd === "true";
  const navItems = [
    ["home", "index.html", "首页"], ["brand", "brand.html", "品牌介绍"],
    ["therapies", "article-list.html?category=therapies", "特色疗法"], ["conditions", "article-list.html?category=conditions", "调理方向"],
    ["tea", "article-list.html?category=tea", "养生茶品"], ["cases", "article-list.html?category=cases", "案例故事"],
    ["news", "article-list.html?category=news", "医馆资讯"], ["contact", "contact.html", "联系我们"]
  ];

  const links = navItems.map(([key, href, label]) => `<a${key === active ? ' class="active"' : ""} href="${href}">${label}</a>`).join("");
  const header = `
    <div class="utility-bar"><div class="container utility-inner"><div>董氏奇穴针灸技法非物质文化遗产传承医馆</div><div class="utility-meta"><span>营业时间 09:00—19:00</span><span>成都市锦江区梨花街8号名望大厦501</span></div></div></div>
    <header class="site-header" id="siteHeader"><div class="container nav-wrap">
      <a class="brand" href="index.html" aria-label="尔意堂首页"><img class="brand-logo" src="assets/images/home-v3/logo-header.png" alt="尔意堂中医馆"></a>
      <nav class="main-nav" id="mainNav" aria-label="主导航">${links}</nav>
      <span class="phone-link phone-desktop" aria-label="咨询电话 199 8209 7343">199 8209 7343</span>
      <a class="phone-link phone-mobile" href="tel:19982097343" aria-label="拨打咨询电话 199 8209 7343">199 8209 7343</a>
      <button class="menu-toggle" id="menuToggle" type="button" aria-label="打开导航" aria-expanded="false"><span></span><span></span><span></span></button>
    </div></header>`;

  const footer = `
    <footer class="site-footer" id="contact">
      ${footerAd ? '<div class="shared-footer-ad" role="img" aria-label="尔意堂中医馆环境图片广告位"></div>' : ""}
      <div class="container footer-main">
        <div class="footer-brand"><a class="brand" href="index.html" aria-label="尔意堂首页"><img class="footer-logo" src="assets/images/home-v3/logo-footer.png" alt="尔意堂中医馆"></a><p>尔意堂承董氏奇穴非遗文脉与岐黄医道，以传统脉、道家脉、太素全息脉三脉合参，循气化规律调理形神。于城市日常中营造一方安静、温暖的中式医馆空间。</p></div>
        <div class="footer-column"><h3>快速导航</h3><a href="index.html">首页</a><a href="brand.html">品牌介绍</a><a href="index.html#doctors">医师团队</a><a href="article-list.html?category=news">医馆资讯</a></div>
        <div class="footer-column"><h3>内容分类</h3><a href="article-list.html?category=therapies">特色疗法</a><a href="article-list.html?category=conditions">调理方向</a><a href="article-list.html?category=tea">养生茶品</a><a href="article-list.html?category=cases">案例故事</a></div>
        <div class="footer-column"><h3>联系我们</h3><span>成都市锦江区梨花街8号<br>名望大厦501</span><span>营业时间：09:00—19:00</span><span>预约咨询：19982097343</span><div class="qr-placeholder"><img src="assets/images/home-v4/wechat-qr.png" alt="尔意堂官方微信二维码" loading="lazy"></div></div>
      </div>
      <div class="container footer-bottom"><span>Copyright © 2026 尔意堂中医馆</span><span>ICP备案号待补充 · 公安备案号待补充</span></div>
    </footer>`;

  const headerSlot = document.getElementById("siteHeaderSlot");
  const footerSlot = document.getElementById("siteFooterSlot");
  if (headerSlot) headerSlot.outerHTML = header;
  if (footerSlot) footerSlot.outerHTML = footer;

  const siteHeader = document.getElementById("siteHeader");
  const toggle = document.getElementById("menuToggle");
  const updateHeader = () => siteHeader?.classList.toggle("scrolled", window.scrollY > 18);
  toggle?.addEventListener("click", () => {
    const open = body.classList.toggle("menu-open");
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute("aria-label", open ? "关闭导航" : "打开导航");
  });
  document.querySelectorAll(".main-nav a").forEach((link) => link.addEventListener("click", () => {
    body.classList.remove("menu-open");
    toggle?.setAttribute("aria-expanded", "false");
  }));
  window.addEventListener("scroll", updateHeader, { passive: true });
  updateHeader();
})();
