// ===== Cafe Javas App =====

// ===== ThemeGoods Animated Text =====
function initThemeGoodsAnimatedText() {
  var animatedTexts = document.querySelectorAll('.themegoods-animated-text');
  if (!animatedTexts.length) return;

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        var el = entry.target;
        var delimiter = el.dataset.delimiter || 'word';
        var transitionDelay = parseInt(el.dataset.transitionDelay || '0', 10);
        var transitionDuration = parseInt(el.dataset.transitionDuration || '800', 10);
        var words = el.textContent.trim().split(delimiter === 'word' ? ' ' : '');
        var html = '';
        words.forEach(function (word, i) {
          var delay = transitionDelay + (i * 100);
          html += '<span style="display: inline-block; opacity: 0; transform: translateY(100%); transition: opacity ' + transitionDuration + 'ms ease ' + delay + 'ms, transform ' + transitionDuration + 'ms ease ' + delay + 'ms;">' + word + '</span>';
        });
        el.innerHTML = html;
        // Force reflow
        el.offsetHeight;
        // Trigger animation
        setTimeout(function () {
          el.querySelectorAll('span').forEach(function (span) {
            span.style.opacity = '1';
            span.style.transform = 'translateY(0)';
          });
        }, 50);
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.2, rootMargin: '0px 0px -50px 0px' });

  animatedTexts.forEach(function (el) {
    observer.observe(el);
  });
}

document.addEventListener('DOMContentLoaded', function () {
  // ===== Initialize Animated Text =====
  initThemeGoodsAnimatedText();

  // ===== Preloader =====
  var preloader = document.getElementById('preloader');
  function hidePreloader() {
    if (preloader && !preloader.classList.contains('hidden')) {
      preloader.classList.add('hidden');
      setTimeout(function () {
        preloader.style.display = 'none';
      }, 700);
    }
  }
  if (preloader) {
    window.addEventListener('load', hidePreloader);
    setTimeout(hidePreloader, 3000);
  }

  // ===== Mobile Nav Toggle =====
  var mobileIcon = document.getElementById('mobile_nav_icon');
  var topBar = document.querySelector('.top_bar');
  if (mobileIcon && topBar) {
    mobileIcon.addEventListener('click', function () {
      topBar.classList.toggle('open');
    });
  }

  // ===== Sticky Nav =====
  if (topBar) {
    var updateNav = function () {
      if (window.scrollY > 0) {
        topBar.classList.add('scrolled');
      } else {
        topBar.classList.remove('scrolled');
      }
    };
    window.addEventListener('scroll', updateNav, { passive: true });
    updateNav();
  }

  // ===== Smooth Scroll for Anchor Links =====
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      var href = anchor.getAttribute('href');
      if (href === '#' || href === '') return;
      var target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ===== Sauce Selector =====
  document.querySelectorAll('.sauce-selector').forEach(function (selector) {
    var options = selector.querySelectorAll('.sauce-option');
    options.forEach(function (option) {
      option.addEventListener('click', function () {
        options.forEach(function (o) { o.classList.remove('active'); });
        option.classList.add('active');
      });
    });
  });

  // ===== Star Rating =====
  var starContainer = document.getElementById('starContainer');
  var ratingInput = document.getElementById('feedbackRating');
  if (starContainer) {
    var stars = starContainer.querySelectorAll('.star-btn');
    var currentRating = 0;

    var updateStars = function (rating) {
      stars.forEach(function (s) {
        s.classList.toggle('active', parseInt(s.dataset.value) <= rating);
      });
    };

    stars.forEach(function (star) {
      star.addEventListener('click', function () {
        currentRating = parseInt(star.dataset.value);
        if (ratingInput) ratingInput.value = currentRating;
        updateStars(currentRating);
      });

      star.addEventListener('mouseenter', function () {
        updateStars(parseInt(star.dataset.value));
      });

      star.addEventListener('mouseleave', function () {
        updateStars(currentRating);
      });
    });
  }

  // ===== Feedback Type Buttons =====
  var typeBtns = document.querySelectorAll('.type-btn');
  var typeInput = document.getElementById('feedbackType');
  typeBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      typeBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      if (typeInput) typeInput.value = btn.dataset.type;
    });
  });

  // ===== Counter Animation =====
  var counters = document.querySelectorAll('.stat-number');
  if (counters.length > 0) {
    var counterObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var el = entry.target;
          var target = parseInt(el.dataset.count) || 0;
          var duration = 2000;
          var startTime = null;

          function animateCount(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var current = Math.floor(progress * target);
            el.textContent = current;
            if (progress < 1) {
              requestAnimationFrame(animateCount);
            } else {
              el.textContent = target;
            }
          }

          requestAnimationFrame(animateCount);
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.3 });

    counters.forEach(function (counter) {
      counterObserver.observe(counter);
    });
  }

  // ===== Back to Top =====
  var toTop = document.getElementById('toTop');
  if (toTop) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 500) {
        toTop.classList.add('visible');
      } else {
        toTop.classList.remove('visible');
      }
    }, { passive: true });

    toTop.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ===== Scroll Animations (IntersectionObserver) =====
  var animSections = document.querySelectorAll(
    '.hero-section, .beef-feature, .quote-banner, .burgers-section, .grand-menu-section, .split-section, .sauce-section, ' +
    '.fresh-section, .features-section, .improvements-section, ' +
    '.sunday-section, .subscribe-section, .about-story, .about-stats, ' +
    '.about-values, .menu-section, .form-section, .feedback-cta'
  );

  if (animSections.length > 0) {
    animSections.forEach(function (section) {
      section.classList.add('section-hidden');
    });

    var sectionObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('section-visible');
          sectionObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    animSections.forEach(function (section) {
      sectionObserver.observe(section);
    });
  }

  // ===== Track Form Handling =====
  var trackForm = document.querySelector('.track-form');
  if (trackForm) {
    trackForm.addEventListener('submit', function (e) {
      var ticket = trackForm.querySelector('input[name="ticket"]');
      var phone = trackForm.querySelector('input[name="phone"]');
      if (ticket && phone && (!ticket.value.trim() || !phone.value.trim())) {
        e.preventDefault();
        alert('Please enter both your ticket number and phone number.');
      }
    });
  }

  // ===== Feedback Form Validation =====
  var feedbackForm = document.getElementById('feedbackForm');
  var submitBtn = document.getElementById('submitBtn');
  if (feedbackForm) {
    var messageField = document.getElementById('message');

    var updateSubmitState = function () {
      var hasMessage = messageField && messageField.value.trim().length > 0;
      var hasRating = parseInt((ratingInput && ratingInput.value) || '0') > 0;
      if (submitBtn) submitBtn.disabled = !hasMessage || !hasRating;
    };

    if (messageField) {
      messageField.addEventListener('input', updateSubmitState);
    }
    if (ratingInput) {
      var observer = new MutationObserver(updateSubmitState);
      observer.observe(ratingInput, { attributes: true, attributeFilter: ['value'] });
    }

    feedbackForm.addEventListener('submit', function (e) {
      var hasMessage = messageField && messageField.value.trim().length > 0;
      var hasRating = parseInt((ratingInput && ratingInput.value) || '0') > 0;
      if (!hasMessage || !hasRating) {
        e.preventDefault();
        return;
      }
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';
      }
    });
  }
});

// ===== Legacy / Admin features =====
(function () {
  document.querySelectorAll('.toast').forEach(function (toast) {
    if (typeof bootstrap !== 'undefined') {
      new bootstrap.Toast(toast).show();
    }
  });

  document.querySelectorAll('.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!confirm(form.dataset.confirm || 'Continue?')) event.preventDefault();
    });
  });

  document.querySelector('[data-toggle-sidebar]')?.addEventListener('click', function () {
    document.querySelector('.sidebar')?.classList.toggle('open');
  });

  var monthly = document.getElementById('monthlyChart');
  if (monthly && typeof Chart !== 'undefined') {
    var rows = JSON.parse(monthly.dataset.chart || '[]');
    new Chart(monthly, {
      type: 'line',
      data: {
        labels: rows.map(function (r) { return r.month; }),
        datasets: [
          { label: 'Total', data: rows.map(function (r) { return r.total; }), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.12)', tension: 0.35, fill: true },
          { label: 'Resolved', data: rows.map(function (r) { return r.resolved; }), borderColor: '#0f766e', tension: 0.35 }
        ]
      },
      options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
  }

  var category = document.getElementById('categoryChart');
  if (category && typeof Chart !== 'undefined') {
    var rows = JSON.parse(category.dataset.chart || '[]');
    new Chart(category, {
      type: 'doughnut',
      data: {
        labels: rows.map(function (r) { return r.name; }),
        datasets: [{ data: rows.map(function (r) { return r.value; }), backgroundColor: ['#0f766e', '#2563eb', '#f59e0b', '#dc2626', '#7c3aed', '#0891b2', '#65a30d', '#64748b'] }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  }

  var loginRoleBtns = document.querySelectorAll('.auth-role-btn');
  var loginRoleInput = document.getElementById('loginRole');
  loginRoleBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      loginRoleBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      if (loginRoleInput) loginRoleInput.value = btn.dataset.role;
    });
  });
})();
