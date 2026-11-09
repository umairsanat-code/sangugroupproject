<template>
  <div>
    <section class="page-hero" style="background: linear-gradient(135deg, rgba(15,98,86,0.4), rgba(9,64,56,0.6)), url('https://images.unsplash.com/photo-1520333789090-1afc82db536a?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;">
      <div class="container text-center" style="max-width: 800px; margin: 0 auto; padding: 40px 0;">
        <div class="eyebrow" style="color: #4cd8c2; text-shadow: 0 4px 12px rgba(0,0,0,0.6);">Stay Connected Globally</div>
        <h1 style="font-size: clamp(32px, 5vw, 56px); margin-bottom: 24px; color: #fff; text-shadow: 0 4px 12px rgba(0,0,0,0.6);">Travel Smart. Stay Connected.</h1>
        <p style="font-size: 18px; line-height: 1.6; opacity: 0.9; margin-bottom: 32px; color: rgba(255,255,255,0.9); text-shadow: 0 4px 12px rgba(0,0,0,0.6);">
          Avoid expensive roaming charges. Get your Breeze eSIM in seconds and enjoy seamless data across 190+ countries. Keep your own number for calls and WhatsApp.
        </p>
        <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
          <a href="https://breezesim.com/?sca_ref=12066146.qgxLNtl7KOa&utm_source=uppromote&utm_medium=affiliate&utm_campaign=farooq-muhammad" target="_blank" class="btn btn-primary" style="padding: 16px 32px; font-size: 16px;">Buy eSIM Online</a>
          <a href="#scan-qr" class="btn btn-ghost" style="padding: 16px 32px; font-size: 16px; border-color: rgba(255,255,255,0.2);">Scan QR to Setup</a>
        </div>
      </div>
    </section>

    <section id="scan-qr" style="padding: 80px 0; background: #f8fafc;">
      <div class="container">
        <div class="grid grid-2" style="align-items: center; gap: 64px;">
          <div>
            <h2 style="font-size: 32px; margin-bottom: 24px; color: #1e293b;">Instant Setup, Zero Hassle.</h2>
            <ul class="feature-list">
              <li>
                <div class="icon">✓</div>
                <div>
                  <strong>Coverage in 190+ Countries</strong>
                  <p>Reliable, high-speed 4G/5G data wherever you land.</p>
                </div>
              </li>
              <li>
                <div class="icon">✓</div>
                <div>
                  <strong>Keep Your Existing Number</strong>
                  <p>Use your current WhatsApp, iMessage, and calls while saving on data.</p>
                </div>
              </li>
              <li>
                <div class="icon">✓</div>
                <div>
                  <strong>60-Second Setup</strong>
                  <p>No physical SIM needed. Just scan the QR code and connect instantly.</p>
                </div>
              </li>
              <li>
                <div class="icon">✓</div>
                <div>
                  <strong>No Roaming Bill Shock</strong>
                  <p>Prepaid plans starting from €4.95. Never pay unexpected fees again.</p>
                </div>
              </li>
            </ul>
          </div>

          <div style="text-align: center;">
            <div class="qr-card">
              <img src="/images/esim-qr.jpg" alt="Scan to get your Breeze eSIM" style="width: 100%; max-width: 400px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
              <p style="margin-top: 24px; font-size: 18px; font-weight: 600; color: #334155;">Scan to Get Your eSIM Now</p>
              <a href="https://breezesim.com/?sca_ref=12066146.qgxLNtl7KOa&utm_source=uppromote&utm_medium=affiliate&utm_campaign=farooq-muhammad" target="_blank" class="btn btn-primary" style="margin-top: 16px; width: 100%; max-width: 400px;">Or Click Here to Buy Online</a>
              <p style="margin-top: 12px; font-size: 13px; color: #94a3b8;">Affiliate link • Breeze eSIM</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
.text-center { text-align: center; }
.feature-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 24px; }
.feature-list li { display: flex; gap: 16px; align-items: flex-start; }
.feature-list .icon { background: #e0f2fe; color: #0284c7; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0; }
.feature-list strong { display: block; font-size: 18px; color: #0f172a; margin-bottom: 4px; }
.feature-list p { margin: 0; font-size: 15px; color: #64748b; line-height: 1.5; }
.qr-card { background: #ffffff; padding: 40px; border-radius: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #e2e8f0; }
</style>
