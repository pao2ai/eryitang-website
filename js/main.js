/**
 * 尔意堂中医馆 - 共享交互脚本
 * Navigation highlight, responsive scaling, smooth scroll
 */

(function () {
  'use strict';

  // ---- Page URL Mapping ----
  var PAGE_MAP = {
    '/': 'index.html',
    '/index.html': 'index.html',
    '/therapy.html': 'therapy.html',
    '/conditions.html': 'conditions.html',
    '/tea-list.html': 'tea-list.html',
    '/tea-detail.html': 'tea-detail.html',
    '/news-list.html': 'news-list.html',
    '/news-detail.html': 'news-detail.html',
    '/cases-list.html': 'cases-list.html',
    '/cases-detail.html': 'cases-detail.html',
    '/about.html': 'about.html',
    '/contact.html': 'contact.html'
  };

  var PAGE_LABELS = {
    'index.html': '首页',
    'therapy.html': '特色疗法',
    'conditions.html': '专长调理',
    'tea-list.html': '养生茶品',
    'tea-detail.html': '养生茶品',
    'news-list.html': '医馆资讯',
    'news-detail.html': '医馆资讯',
    'cases-list.html': '案例故事',
    'cases-detail.html': '案例故事',
    'about.html': '关于我们',
    'contact.html': '联系我们'
  };

  // ---- Get Current Page Name ----
  function getCurrentPage() {
    var path = window.location.pathname;
    var parts = path.split('/');
    var filename = parts[parts.length - 1] || 'index.html';
    return filename;
  }

  // ---- Navigation Active State ----
  function highlightNav() {
    var current = getCurrentPage();
    var label = PAGE_LABELS[current] || '';
    var navLinks = document.querySelectorAll('.nav-menu a');

    navLinks.forEach(function (link) {
      link.classList.remove('active');
      if (link.textContent.trim() === label) {
        link.classList.add('active');
      }
    });

    // Special case: index
    if (current === 'index.html') {
      var homeLink = document.querySelector('.nav-menu a[href="index.html"]');
      if (homeLink) homeLink.classList.add('active');
    }
  }

  // ---- Responsive Scaling ----
  var DESIGN_WIDTH = 1440;

  function applyScale() {
    var wrapper = document.querySelector('.page-wrapper');
    if (!wrapper) return;

    var viewportW = window.innerWidth;
    var scale = Math.min(viewportW / DESIGN_WIDTH, 1);

    wrapper.style.transform = 'scale(' + scale + ')';
    wrapper.style.transformOrigin = 'top left';
    wrapper.style.marginBottom = (wrapper.offsetHeight * scale - wrapper.offsetHeight) + 'px';

    // Also scale body height compensation
    document.body.style.minHeight = (wrapper.offsetHeight * scale) + 'px';
  }

  function debounce(fn, delay) {
    var timer;
    return function () {
      clearTimeout(timer);
      timer = setTimeout(fn, delay);
    };
  }

  // ---- Smooth Scroll for Anchor Links ----
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
      anchor.addEventListener('click', function (e) {
        var targetId = this.getAttribute('href').substring(1);
        var target = document.getElementById(targetId);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

  // ---- Card Hover Effects ----
  function initCardEffects() {
    document.querySelectorAll('.dark-card, .card-item, .product-card, .activity-card, .h-card, .info-card, .story-col, .team-card').forEach(function (card) {
      card.addEventListener('mouseenter', function () {
        this.style.transform = 'translateY(-2px)';
      });
      card.addEventListener('mouseleave', function () {
        this.style.transform = '';
      });
    });
  }

  // ---- Filter Tab Interaction ----
  function initFilterTabs() {
    document.querySelectorAll('.filter-tab').forEach(function (tab) {
      tab.addEventListener('click', function () {
        document.querySelectorAll('.filter-tab').forEach(function (t) { t.classList.remove('active'); });
        this.classList.add('active');
      });
    });
  }

  // ---- Page Load ----
  function init() {
    highlightNav();
    applyScale();
    initSmoothScroll();
    initCardEffects();
    initFilterTabs();
  }

  // ---- Event Listeners ----
  window.addEventListener('DOMContentLoaded', init);
  window.addEventListener('resize', debounce(applyScale, 150));

})();
