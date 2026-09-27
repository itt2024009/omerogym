      </div>
    </main>

    <footer>
      <div class="footer-inner">© <?= date('Y') ?> OMERO GYM Admin · Discipline today, strength tomorrow.</div>
    </footer>
  </div>
  <script>
    (function () {
      var toggle = document.getElementById('navToggle'), menu = document.getElementById('mobileMenu');
      if (toggle && menu) toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
    })();
  </script>
</body>
</html>
