document.addEventListener('DOMContentLoaded', () => {
  const slot = document.getElementById('footer-slot');
  if (!slot) return;
  slot.innerHTML = `
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <a href="index.html" class="logo" style="color:#fff;margin-bottom:14px;"><span class="mark"></span> SanguTravel</a>
          <p style="max-width:280px;">Pakistan's online travel partner for visas, Umrah packages and Haramain hotel bookings — trusted since day one, backed by real people.</p>
          <div class="social-row">
            <a href="#" aria-label="Facebook">f</a>
            <a href="#" aria-label="Instagram">ig</a>
            <a href="#" aria-label="WhatsApp">wa</a>
            <a href="#" aria-label="YouTube">yt</a>
          </div>
        </div>
        <div>
          <h4>Services</h4>
          <ul>
            <li><a href="visa.html">Visit Visas</a></li>
            <li><a href="umrah.html">Umrah Packages</a></li>
            <li><a href="umrah.html?group=1">Umrah Groups</a></li>
            <li><a href="hotels.html">Hotel Bookings</a></li>
          </ul>
        </div>
        <div>
          <h4>Company</h4>
          <ul>
            <li><a href="about.html">About Us</a></li>
            <li><a href="blog.html">Blog</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="terms.html">Terms &amp; Conditions</a></li>
          </ul>
        </div>
        <div>
          <h4>Get in Touch</h4>
          <ul>
            <li>Gulberg III, Lahore, Pakistan</li>
            <li>+92 42 1234 5678</li>
            <li>hello@sangutravel.pk</li>
            <li>Mon–Sat, 9am–9pm PKT</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 SanguTravel. All rights reserved.</span>
        <span>IATA Accredited · SECP Registered Travel Agency</span>
      </div>
    </div>
  </footer>`;
});
