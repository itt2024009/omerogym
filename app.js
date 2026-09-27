(function () {
  "use strict";

  var CATEGORIES = [
    { id: "floor", label: "General Gym Floor Access", filter: "Gym Floor Access" },
    { id: "classes", label: "Group Fitness Classes", filter: "Group Fitness Classes" },
    { id: "personal", label: "Personal Training (1-on-1)", filter: "Personal Training" }
  ];
  var SESSIONS = [
    { id: "peak-floor", category: "floor", title: "Peak Hours Floor Pass",
      description: "Reservation for dynamic weight training and cardio equipment during premium high-energy intervals.",
      price: 1500, duration: 90 },
    { id: "offpeak-floor", category: "floor", title: "Off-Peak Floor Pass",
      description: "Perfect for crowd-free workouts with total accessibility to all lifting racks and fitness gear.",
      price: 1000, duration: 120 },
    { id: "hiit-blast", category: "classes", title: "High Intensity HIIT Blast",
      description: "Metabolic conditioning, functional intervals, and explosive plyometrics orchestrated by elite instructors.",
      price: 2500, duration: 60 },
    { id: "elite-power", category: "personal", title: "Elite Power & Strength Coaching",
      description: "Custom programming focusing heavily on biomechanics, heavy lifting form, and progressive overloading vectors.",
      price: 5000, duration: 60 }
  ];
  var TIME_BLOCKS = ["06:00 AM", "08:00 AM", "10:00 AM", "04:00 PM", "05:00 PM", "07:00 PM"];

  // ---------------------------------------------------------------------
  // Phase 3: real backend. Auth + bookings now live in MySQL via PHP
  // endpoints under auth/ and bookings/, called with fetch(). localStorage
  // is only kept for the "Hello, <name>" greeting so it can paint instantly
  // before the network call finishes — it is never trusted for security.
  // ---------------------------------------------------------------------
  var _mem = {};
  var store = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return (k in _mem) ? _mem[k] : null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) { _mem[k] = v; } },
    del: function (k) { try { localStorage.removeItem(k); } catch (e) { delete _mem[k]; } }
  };
  function getUser() { try { return JSON.parse(store.get("omero.user")); } catch (e) { return null; } }
  function setUser(u) { store.set("omero.user", JSON.stringify(u)); }
  function clearUser() { store.del("omero.user"); }

  function api(path, method, body) {
    return fetch(path, {
      method: method || "GET",
      headers: body ? { "Content-Type": "application/json" } : {},
      credentials: "same-origin",
      body: body ? JSON.stringify(body) : undefined
    }).then(function (res) {
      return res.json().then(function (data) { return { ok: res.ok, data: data }; });
    });
  }

  function LKR(n) { return "LKR " + n.toLocaleString("en-US"); }
  function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]; }); }
  function getSession(id) { for (var i = 0; i < SESSIONS.length; i++) { if (SESSIONS[i].id === id) return SESSIONS[i]; } return null; }
  function today() { return new Date().toISOString().split("T")[0]; }
  function param(name) { return new URLSearchParams(location.search).get(name); }
  function el(id) { return document.getElementById(id); }

  function icon(name, size) {
    size = size || 16;
    var P = {
      clock: '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
      arrow: '<path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>',
      calendar: '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/>',
      timer: '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2M9 2h6"/>',
      x: '<path d="M18 6 6 18"/><path d="M6 6l12 12"/>',
      rotate: '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>'
    };
    return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + (P[name] || "") + "</svg>";
  }

  function injectBackground() {
    var words = ["DISCIPLINE", "TODAY", "STRENGTH", "TOMORROW"].map(function (w) {
      return '<span class="' + (w === "TOMORROW" ? "red" : "") + '">' + w + "</span>";
    }).join("");
    var bg = document.createElement("div");
    bg.className = "bg";
    bg.innerHTML = '<div class="bg-glow"></div><div class="bg-grid"></div>' +
      '<div class="watermark left">' + words + "</div>" +
      '<div class="watermark right">' + words + "</div>";
    document.body.insertBefore(bg, document.body.firstChild);
  }

  // Shows a small inline error message under a form, creating the element on
  // first use so no HTML file needs to be touched to support it.
  function showFormError(form, message) {
    var box = form.querySelector(".js-form-error");
    if (!box) {
      box = document.createElement("p");
      box.className = "help js-form-error";
      box.style.color = "#ff5c5c";
      form.insertBefore(box, form.firstChild);
    }
    box.textContent = message;
  }

  function initChrome() {
    var user = getUser();
    var name = (user && user.name) ? user.name : "Dedicated Athlete";
    Array.prototype.forEach.call(document.querySelectorAll(".js-hello"), function (n) { n.textContent = name; });
    Array.prototype.forEach.call(document.querySelectorAll(".js-year"), function (n) { n.textContent = new Date().getFullYear(); });
    var toggle = el("navToggle"), menu = el("mobileMenu");
    if (toggle && menu) toggle.addEventListener("click", function () { menu.classList.toggle("open"); });
    Array.prototype.forEach.call(document.querySelectorAll('[data-action="logout"]'), function (b) {
      b.addEventListener("click", function () {
        api("auth/logout.php", "POST", {}).then(function () {
          clearUser();
          location.href = "login.html";
        });
      });
    });
  }

  // Called on every member-only page (classes/book/workouts). Confirms the
  // PHP session is really logged in before letting the page be used, and
  // bounces guests back to login.html — mirrors "session must be validated
  // server-side" from the Phase 3 guideline.
  function guardPage(onReady) {
    api("auth/check.php", "GET").then(function (r) {
      if (r.data && r.data.loggedIn) {
        setUser({ name: r.data.name, email: r.data.email });
        initChrome();
        if (onReady) onReady();
      } else {
        clearUser();
        location.href = "login.html";
      }
    }).catch(function () {
      // Backend unreachable (e.g. MySQL/XAMPP not running) — send to login
      // rather than showing a broken member page.
      location.href = "login.html";
    });
  }

  function initLogin() {
    var form = el("loginForm");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var email = el("li-email").value.trim();
      var password = el("li-pass").value;
      api("auth/login.php", "POST", { email: email, password: password }).then(function (r) {
        if (r.ok && r.data.success) {
          setUser({ name: r.data.name, email: r.data.email });
          location.href = "classes.html";
        } else {
          showFormError(form, (r.data && r.data.message) || "Login failed. Please try again.");
        }
      }).catch(function () {
        showFormError(form, "Could not reach the server. Check that XAMPP/MySQL is running.");
      });
    });
  }

  function initRegister() {
    var form = el("registerForm");
    if (!form) return;
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var name = el("rg-name").value.trim() || "Dedicated Athlete";
      var email = el("rg-email").value.trim();
      var phone = el("rg-phone") ? el("rg-phone").value.trim() : "";
      var password = el("rg-pass").value;
      api("auth/register.php", "POST", { name: name, email: email, phone: phone, password: password }).then(function (r) {
        if (r.ok && r.data.success) {
          setUser({ name: r.data.name, email: r.data.email });
          location.href = "classes.html";
        } else {
          showFormError(form, (r.data && r.data.message) || "Registration failed. Please try again.");
        }
      }).catch(function () {
        showFormError(form, "Could not reach the server. Check that XAMPP/MySQL is running.");
      });
    });
  }

  function initClasses() {
    var filter = "all";
    var filtersEl = el("filters");
    var sectionsEl = el("sessionSections");
    if (!filtersEl || !sectionsEl) return;

    function renderFilters() {
      var pills = ['<button class="pill ' + (filter === "all" ? "active" : "") + '" data-filter="all">All</button>'];
      CATEGORIES.forEach(function (c) {
        pills.push('<button class="pill ' + (filter === c.id ? "active" : "") + '" data-filter="' + c.id + '">' + c.filter + "</button>");
      });
      filtersEl.innerHTML = pills.join("");
    }

    function card(s) {
      return '<article class="card s-card">' +
        "<h3>" + esc(s.title) + "</h3><p>" + esc(s.description) + "</p>" +
        '<div class="s-foot"><div>' +
        '<div class="price">' + LKR(s.price) + "</div>" +
        '<div class="meta">' + icon("clock", 13) + " " + s.duration + " mins</div></div>" +
        '<button class="btn btn-primary" data-book="' + s.id + '">Book Slot ' + icon("arrow", 15) + "</button>" +
        "</div></article>";
    }

    function renderSections() {
      var cats = filter === "all" ? CATEGORIES : CATEGORIES.filter(function (c) { return c.id === filter; });
      sectionsEl.innerHTML = cats.map(function (cat) {
        var items = SESSIONS.filter(function (s) { return s.category === cat.id; });
        if (!items.length) return "";
        return '<section class="section"><div class="section-head"><span class="dot"></span>' +
          "<h2>" + cat.label + '</h2><span class="rule"></span></div>' +
          '<div class="grid2">' + items.map(card).join("") + "</div></section>";
      }).join("");
    }

    filtersEl.addEventListener("click", function (e) {
      var b = e.target.closest("[data-filter]");
      if (!b) return;
      filter = b.getAttribute("data-filter");
      renderFilters();
      renderSections();
    });
    sectionsEl.addEventListener("click", function (e) {
      var b = e.target.closest("[data-book]");
      if (!b) return;
      location.href = "book.html?session=" + encodeURIComponent(b.getAttribute("data-book"));
    });

    renderFilters();
    renderSections();
  }

  function initBook() {
    var sel = el("bk-session"), dateEl = el("bk-date"), blocksEl = el("blocks");
    var stepperEl = el("stepper"), summaryEl = el("summary"), confirmBtn = el("confirmBtn");
    if (!sel) return;

    var state = { sessionId: param("session") || "", date: "", time: "" };
    dateEl.min = today();

    sel.innerHTML = '<option value="">Select a session…</option>' + SESSIONS.map(function (x) {
      return '<option value="' + x.id + '"' + (x.id === state.sessionId ? " selected" : "") + ">" + esc(x.title) + " — " + LKR(x.price) + "</option>";
    }).join("");

    var STEPS = ["Pick a date", "Choose a time block", "Confirm"];
    function stepIndex() { return !state.date ? 0 : !state.time ? 1 : 2; }

    function renderStepper() {
      var idx = stepIndex();
      stepperEl.innerHTML = STEPS.map(function (label, i) {
        var cls = i === idx ? "current" : i < idx ? "done" : "";
        return '<span class="pill step ' + cls + '"><b>' + (i + 1) + "</b> " + label + "</span>";
      }).join("");
    }

    function renderBlocks() {
      if (!state.date) {
        blocksEl.innerHTML = '<div class="blocks-empty">' + icon("calendar", 26) +
          "<p>Select a date above to reveal live time-block availability.</p></div>";
        return;
      }
      blocksEl.innerHTML = '<div class="blocks">' + TIME_BLOCKS.map(function (t) {
        return '<button class="block ' + (state.time === t ? "active" : "") + '" data-time="' + t + '">' + t + "</button>";
      }).join("") + "</div>";
    }

    function renderSummary() {
      var s = getSession(state.sessionId);
      var sched = (state.date && state.time)
        ? '<div class="sched"><span>Scheduled:</span> ' + state.date + " · " + state.time + "</div>" : "";
      summaryEl.innerHTML =
        '<p class="sum-label">Selected Workout / Class</p>' +
        '<p class="sum-title">' + (s ? esc(s.title) : "Please pick a session card") + "</p>" +
        '<div class="sum-grid">' +
        '<div><p class="sum-label">' + icon("timer", 14) + " Duration</p><p class=\"sum-val\">" + (s ? s.duration + " mins" : "—") + "</p></div>" +
        '<div><p class="sum-label">' + icon("clock", 14) + ' Session Cost</p><p class="sum-val red">' + (s ? LKR(s.price) : "—") + "</p></div>" +
        "</div>" + sched;
    }

    function refreshConfirm() {
      var s = getSession(state.sessionId);
      confirmBtn.disabled = !(s && state.date && state.time);
    }
    function renderAll() { renderStepper(); renderBlocks(); renderSummary(); refreshConfirm(); }

    sel.addEventListener("change", function (e) { state.sessionId = e.target.value; renderAll(); });
    dateEl.addEventListener("change", function (e) { state.date = e.target.value; state.time = ""; renderAll(); });
    blocksEl.addEventListener("click", function (e) {
      var b = e.target.closest("[data-time]");
      if (!b) return;
      state.time = b.getAttribute("data-time");
      renderAll();
    });
    confirmBtn.addEventListener("click", function () {
      var s = getSession(state.sessionId);
      if (!s || !state.date || !state.time) return;
      confirmBtn.disabled = true;
      api("bookings/create.php", "POST", {
        sessionId: s.id, title: s.title, price: s.price, duration: s.duration,
        date: state.date, time: state.time
      }).then(function (r) {
        if (r.ok && r.data.success) {
          location.href = "workouts.html";
        } else {
          confirmBtn.disabled = false;
          alert((r.data && r.data.message) || "Could not save the booking.");
        }
      }).catch(function () {
        confirmBtn.disabled = false;
        alert("Could not reach the server. Check that XAMPP/MySQL is running.");
      });
    });

    renderAll();
  }

  function initWorkouts() {
    var upEl = el("upcoming"), pastEl = el("past");
    if (!upEl || !pastEl) return;

    function render(all) {
      var t = today();
      var upcoming = all.filter(function (b) { return b.date >= t; });
      var past = all.filter(function (b) { return b.date < t; });

      if (!upcoming.length) {
        upEl.innerHTML = '<div class="empty"><p>No upcoming slots yet.</p>' +
          '<a class="btn btn-primary" href="book.html">Book a Slot</a></div>';
      } else {
        upEl.innerHTML =
          '<div class="table-wrap"><table><thead><tr>' +
          "<th>Reservation ID</th><th>Class / Session</th><th>Scheduled Date &amp; Time</th><th>Cost</th><th>Status</th><th class=\"right\">Actions</th>" +
          "</tr></thead><tbody>" + upcoming.map(function (b) {
            return "<tr>" +
              '<td class="id">' + b.id + "</td>" +
              '<td class="cell-title">' + esc(b.title) + "</td>" +
              '<td class="cell-muted">' + b.date + " · " + b.time + "</td>" +
              '<td class="cell-muted">' + LKR(b.price) + "</td>" +
              '<td><span class="badge confirmed">Confirmed</span></td>' +
              '<td class="right"><button class="btn btn-ghost btn-sm" data-cancel="' + b.dbId + '">' + icon("x", 13) + " Cancel</button></td>" +
              "</tr>";
          }).join("") + "</tbody></table></div>";
      }

      pastEl.innerHTML = !past.length
        ? '<div class="empty"><p>No past sessions yet.</p></div>'
        : '<div class="table-wrap"><table><thead><tr>' +
          "<th>Reservation ID</th><th>Session</th><th>Date &amp; Time</th><th>Cost</th><th>Status</th><th class=\"right\">Shortcut</th>" +
          "</tr></thead><tbody>" + past.map(function (b) {
            return "<tr>" +
              '<td class="id">' + b.id + "</td>" +
              '<td class="cell-title">' + esc(b.title) + "</td>" +
              '<td class="cell-muted">' + b.date + " · " + b.time + "</td>" +
              '<td class="cell-muted">' + LKR(b.price) + "</td>" +
              '<td><span class="badge attended">Attended</span></td>' +
              '<td class="right"><a class="btn btn-ghost btn-sm" href="book.html">' + icon("rotate", 13) + " Book Again</a></td>" +
              "</tr>";
          }).join("") + "</tbody></table></div>";
    }

    function load() {
      api("bookings/list.php", "GET").then(function (r) {
        render((r.ok && r.data.success) ? r.data.bookings : []);
      }).catch(function () {
        upEl.innerHTML = '<div class="empty"><p>Could not load your bookings. Check that XAMPP/MySQL is running.</p></div>';
        pastEl.innerHTML = "";
      });
    }

    upEl.addEventListener("click", function (e) {
      var b = e.target.closest("[data-cancel]");
      if (!b) return;
      api("bookings/cancel.php", "POST", { dbId: b.getAttribute("data-cancel") }).then(function () { load(); });
    });

    load();
  }

  document.addEventListener("DOMContentLoaded", function () {
    injectBackground();
    var page = document.body.getAttribute("data-page");
    var memberPages = { classes: initClasses, book: initBook, workouts: initWorkouts };

    if (page === "login") { initChrome(); initLogin(); }
    else if (page === "register") { initChrome(); initRegister(); }
    else if (memberPages[page]) { guardPage(memberPages[page]); }
    else { initChrome(); }
  });
})();
