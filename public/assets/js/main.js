// SanguTravel — shared front-end behaviours
document.addEventListener('DOMContentLoaded', () => {
  // Mobile nav toggle
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav-main');
  if (toggle && nav) {
    toggle.addEventListener('click', () => nav.classList.toggle('open'));
    nav.querySelectorAll('a').forEach(a => a.addEventListener('click', () => nav.classList.remove('open')));
  }

  // Finder tabs (home page quick search)
  const finderTabs = document.querySelectorAll('.finder-tab');
  finderTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      finderTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      document.querySelectorAll('.finder-body').forEach(b => b.classList.remove('active'));
      document.getElementById(tab.dataset.target)?.classList.add('active');
    });
  });

  // Detail page tabs
  const dTabs = document.querySelectorAll('.detail-tabs button');
  dTabs.forEach(btn => {
    btn.addEventListener('click', () => {
      dTabs.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('.detail-panel').forEach(p => p.classList.remove('active'));
      document.getElementById(btn.dataset.panel)?.classList.add('active');
    });
  });

  // Payment method selector
  const payMethods = document.querySelectorAll('.pay-method');
  payMethods.forEach(m => {
    m.addEventListener('click', () => {
      payMethods.forEach(x => x.classList.remove('selected'));
      m.classList.add('selected');
      document.querySelectorAll('.pay-panel').forEach(p => p.style.display = 'none');
      const panel = document.getElementById(m.dataset.panel);
      if (panel) panel.style.display = 'block';
    });
  });
});

function sanguToast(msg) {
  let t = document.querySelector('.toast');
  if (!t) {
    t = document.createElement('div');
    t.className = 'toast';
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(window._sanguToastTimer);
  window._sanguToastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}

function sanguQueryParam(name) {
  return new URLSearchParams(window.location.search).get(name);
}
