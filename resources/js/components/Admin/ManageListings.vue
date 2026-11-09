<template>
  <div>
    <!-- Toast notification -->
    <div v-if="toast" class="admin-toast" :class="toast.type">{{ toast.msg }}</div>

    <div class="finder" style="margin-top:0;box-shadow:none;border:1px solid var(--line);flex-wrap:wrap;">
      <div class="finder-tab" :class="{ active: activeTab === 'visa' }" @click="activeTab = 'visa'">Visas</div>
      <div class="finder-tab" :class="{ active: activeTab === 'umrah' }" @click="activeTab = 'umrah'">Umrah Packages</div>
      <div class="finder-tab" :class="{ active: activeTab === 'groups' }" @click="activeTab = 'groups'">Umrah Groups</div>
      <div class="finder-tab" :class="{ active: activeTab === 'hotel' }" @click="activeTab = 'hotel'">Hotels</div>
      <div class="finder-tab" :class="{ active: activeTab === 'promotions' }" @click="activeTab = 'promotions'">🎯 Promotions</div>
      <div class="finder-tab" :class="{ active: activeTab === 'gallery' }" @click="activeTab = 'gallery'">🖼 Gallery</div>
      <div class="finder-tab" :class="{ active: activeTab === 'blog' }" @click="activeTab = 'blog'">📝 Blog</div>
      <div class="finder-tab" :class="{ active: activeTab === 'seo' }" @click="activeTab = 'seo'">🔍 SEO Manager</div>
    </div>

    <!-- ===================== SEO TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'seo' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <SeoAudit />
    </div>

    <!-- ===================== VISA TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'visa' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <div class="admin-panel-card">
        <h2>{{ editingVisaId ? 'Edit Visa Destination' : 'Add New Visa Destination' }}</h2>
        <form @submit.prevent="addVisa">
          <div class="form-grid">
            <div class="form-group"><label>Country Name *</label><input type="text" v-model="visaForm.country" placeholder="e.g. Saudi Arabia" required></div>
            <div class="form-group"><label>Visa Title *</label><input type="text" v-model="visaForm.title" placeholder="e.g. Tourist E-Visa" required></div>
            <div class="form-group"><label>Visa Category</label><input type="text" v-model="visaForm.type" placeholder="e.g. Visit Visa"></div>
            <div class="form-group"><label>Visa Image</label><input type="file" @change="handleVisaImage" accept="image/*"></div>
            <div class="form-group"><label>Flag Emoji</label><input type="text" v-model="visaForm.flag" placeholder="e.g. 🇸🇦"></div>
            <div class="form-group"><label>Processing Time</label><input type="text" v-model="visaForm.processing" placeholder="e.g. 3-4 Working Days"></div>
            <div class="form-group"><label>Price (EUR €) *</label><input type="number" v-model="visaForm.price" placeholder="15000" required></div>
            <div class="form-group"><label>Discount Amount (€)</label><input type="number" v-model="visaForm.discount_amount" placeholder="10"></div>
            <div class="form-group"><label>Discount Percent (%)</label><input type="number" v-model="visaForm.discount_percent" placeholder="5" max="100"></div>
            <div class="form-group"><label>Discount Label</label><input type="text" v-model="visaForm.discount_label" placeholder="Summer Sale"></div>
            <div class="form-group"><label>Duration</label><input type="text" v-model="visaForm.duration" placeholder="e.g. 30 Days"></div>
            <div class="form-group full"><label>Requirements (One per line)</label><textarea rows="3" v-model="visaForm.requirements" placeholder="Passport valid for 6 months..."></textarea></div>
            <div class="form-group full"><label>Documents Required (One per line)</label><textarea rows="3" v-model="visaForm.documents_required" placeholder="Scan of Passport..."></textarea></div>
            <div class="form-group full"><label>Important Information</label><textarea rows="3" v-model="visaForm.important_info" placeholder="Any disclaimers..."></textarea></div>
            <div class="form-group full"><label>Description</label><textarea rows="2" v-model="visaForm.desc" placeholder="Brief description..."></textarea></div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : (editingVisaId ? 'Update Visa' : 'Add Visa') }}</button>
            <button v-if="editingVisaId" type="button" class="btn btn-ghost" @click="cancelEditVisa">Cancel</button>
          </div>
        </form>
      </div>
      <div class="admin-panel-card">
        <h2>All Visas <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ visas.length }} records</span></h2>
        <table class="admin-table">
          <thead><tr><th>Flag</th><th>Country</th><th>Title</th><th>Price (EUR €)</th><th>Actions</th></tr></thead>
          <tbody>
            <tr v-for="v in visas" :key="v.id">
              <td style="font-size:22px;">{{ v.flag || '🌍' }}</td>
              <td>{{ v.country }}</td>
              <td>{{ v.title || v.type }}</td>
              <td>{{ parseFloat(v.price).toLocaleString('de-DE') }}</td>
              <td>
                <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" @click="editVisa(v)">✏ Edit</button>
                <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;color:#c0392b;" @click="deleteVisa(v.id)">🗑 Delete</button>
              </td>
            </tr>
            <tr v-if="visas.length === 0"><td colspan="5" class="empty-state">No visas yet. Add one above.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ===================== UMRAH PACKAGES TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'umrah' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <div class="admin-panel-card">
        <h2>{{ editingUmrahId ? 'Edit Umrah Package' : 'Add New Umrah Package' }}</h2>
        <form @submit.prevent="addUmrah">
          <div class="form-grid">
            <div class="form-group"><label>Package Title *</label><input type="text" v-model="umrahForm.title" placeholder="e.g. Umrah Deluxe Package" required></div>
            <div class="form-group"><label>Duration</label>
              <select v-model="umrahForm.duration_type">
                <option value="14 Days">14 Days</option>
                <option value="21 Days">21 Days</option>
                <option value="28 Days">28 Days</option>
                <option value="Custom Package">Custom Package</option>
              </select>
            </div>
            <div class="form-group"><label>Package Image</label><input type="file" @change="handleUmrahImage" accept="image/*"></div>
            <div class="form-group"><label>Price (EUR €) *</label><input type="number" v-model="umrahForm.price" placeholder="200000" required></div>
            <div class="form-group"><label>Discount Amount (€)</label><input type="number" v-model="umrahForm.discount_amount" placeholder="10"></div>
            <div class="form-group"><label>Discount Percent (%)</label><input type="number" v-model="umrahForm.discount_percent" placeholder="5" max="100"></div>
            <div class="form-group"><label>Discount Label</label><input type="text" v-model="umrahForm.discount_label" placeholder="Summer Sale"></div>
            <div class="form-group"><label>Makkah Hotel</label><input type="text" v-model="umrahForm.makkah_hotel" placeholder="e.g. Swissôtel Makkah"></div>
            <div class="form-group"><label>Madinah Hotel</label><input type="text" v-model="umrahForm.madinah_hotel" placeholder="e.g. Anwar Al Madinah"></div>
            <div class="form-group"><label>Transport Details</label><input type="text" v-model="umrahForm.transport" placeholder="e.g. Private GMC"></div>
            <div class="form-group"><label>Rating (out of 5)</label><input type="number" v-model="umrahForm.rating" placeholder="4.5" step="0.1" min="1" max="5"></div>
            <div class="form-group full"><label>Description</label><textarea rows="3" v-model="umrahForm.desc" placeholder="Brief description..."></textarea></div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : (editingUmrahId ? 'Update Package' : 'Add Package') }}</button>
            <button v-if="editingUmrahId" type="button" class="btn btn-ghost" @click="cancelEditUmrah">Cancel</button>
          </div>
        </form>
      </div>
      <div class="admin-panel-card">
        <h2>All Umrah Packages <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ umrahs.length }} records</span></h2>
        <table class="admin-table">
          <thead><tr><th>Title</th><th>Duration</th><th>Price (EUR €)</th><th>Actions</th></tr></thead>
          <tbody>
            <tr v-for="u in umrahs" :key="u.id">
              <td>{{ u.title }}</td>
              <td>{{ u.duration_type || u.nights }}</td>
              <td>{{ parseFloat(u.price).toLocaleString('de-DE') }}</td>
              <td>
                <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" @click="editUmrah(u)">✏ Edit</button>
                <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;color:#c0392b;" @click="deleteUmrah(u.id)">🗑 Delete</button>
              </td>
            </tr>
            <tr v-if="umrahs.length === 0"><td colspan="4" class="empty-state">No packages yet. Add one above.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ===================== UMRAH GROUPS TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'groups' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <div class="admin-panel-card">
        <h2>{{ editingGroupId ? 'Edit Umrah Group' : 'Add New Umrah Group' }}</h2>
        <form @submit.prevent="addGroup">
          <div class="form-grid">
            <div class="form-group"><label>Group Name *</label><input type="text" v-model="groupForm.name" placeholder="e.g. Ramadan Special Group" required></div>
            <div class="form-group"><label>Departure Date</label><input type="date" v-model="groupForm.departure_date"></div>
            <div class="form-group"><label>Return Date</label><input type="date" v-model="groupForm.return_date"></div>
            <div class="form-group"><label>Package Image</label><input type="file" @change="handleGroupImage" accept="image/*"></div>
            <div class="form-group"><label>Available Seats</label><input type="number" v-model="groupForm.seats" placeholder="50"></div>
            <div class="form-group"><label>Price (EUR €) *</label><input type="number" v-model="groupForm.price" placeholder="250000" required></div>
            <div class="form-group"><label>Discount Amount (€)</label><input type="number" v-model="groupForm.discount_amount" placeholder="10"></div>
            <div class="form-group"><label>Discount Percent (%)</label><input type="number" v-model="groupForm.discount_percent" placeholder="5" max="100"></div>
            <div class="form-group"><label>Discount Label</label><input type="text" v-model="groupForm.discount_label" placeholder="Summer Sale"></div>
            <div class="form-group"><label>Hotel Tier/Name</label><input type="text" v-model="groupForm.hotel" placeholder="e.g. 5-Star Haram View"></div>
            <div class="form-group"><label>Transport</label><input type="text" v-model="groupForm.transport" placeholder="e.g. VIP Bus"></div>
            <div class="form-group full"><label>Group Details</label><textarea rows="3" v-model="groupForm.group_details" placeholder="Scholars joining, Ziyarat details..."></textarea></div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : (editingGroupId ? 'Update Group' : 'Add Group') }}</button>
            <button v-if="editingGroupId" type="button" class="btn btn-ghost" @click="cancelEditGroup">Cancel</button>
          </div>
        </form>
      </div>
      <div class="admin-panel-card">
        <h2>All Umrah Groups <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ groups.length }} records</span></h2>
        <table class="admin-table">
          <thead><tr><th>Group Name</th><th>Dates</th><th>Seats</th><th>Price (EUR €)</th><th>Actions</th></tr></thead>
          <tbody>
            <tr v-for="g in groups" :key="g.id">
              <td>{{ g.name }}</td>
              <td>{{ g.departure_date }} - {{ g.return_date }}</td>
              <td>{{ g.seats }}</td>
              <td>{{ parseFloat(g.price).toLocaleString('de-DE') }}</td>
              <td>
                <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" @click="editGroup(g)">✏ Edit</button>
                <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;color:#c0392b;" @click="deleteGroup(g.id)">🗑 Delete</button>
              </td>
            </tr>
            <tr v-if="groups.length === 0"><td colspan="5" class="empty-state">No groups yet. Add one above.</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ===================== PROMOTIONS TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'promotions' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <div class="admin-panel-card">
        <h2>{{ editingPromotionId ? 'Edit Promotion' : 'Add New Promotion' }}</h2>
        <form @submit.prevent="addPromotion">
          <div class="form-grid">
            <div class="form-group"><label>Title *</label><input type="text" v-model="promotionForm.title" placeholder="e.g. Ramadan Special Offer" required></div>
            <div class="form-group"><label>Promotion Image</label><input type="file" @change="handlePromotionImage" accept="image/*"></div>
            <div class="form-group"><label>Button Text</label><input type="text" v-model="promotionForm.button_text" placeholder="e.g. Book Now"></div>
            <div class="form-group"><label>Button / Redirect URL</label><input type="text" v-model="promotionForm.button_url" placeholder="https:// or /visa ..."></div>
            <div class="form-group"><label>Display Order</label><input type="number" v-model="promotionForm.display_order" placeholder="0"></div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:24px;"><label style="margin:0;">Active</label><input type="checkbox" v-model="promotionForm.is_active" style="width:auto;"></div>
            <div class="form-group full"><label>Short Description</label><textarea rows="3" v-model="promotionForm.description" placeholder="Brief promo description..."></textarea></div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : (editingPromotionId ? 'Update Promotion' : 'Add Promotion') }}</button>
            <button v-if="editingPromotionId" type="button" class="btn btn-ghost" @click="cancelEditPromotion">Cancel</button>
          </div>
        </form>
      </div>
      <div class="admin-panel-card">
        <h2>All Promotions <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ promotions.length }} records</span></h2>
        <div class="promo-grid">
          <div class="promo-admin-card" v-for="p in promotions" :key="p.id">
            <img v-if="p.image_url" :src="p.image_url" alt="Promo" style="width:100%;height:120px;object-fit:cover;border-radius:10px;margin-bottom:10px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
              <span class="badge" :style="p.is_active ? 'background:var(--teal-dark);color:#fff;' : 'background:#ccc;color:#555;'">{{ p.is_active ? 'Active' : 'Inactive' }}</span>
              <b style="font-size:14px;">{{ p.title }}</b>
            </div>
            <p style="font-size:13px;color:var(--ink-soft);margin:0 0 4px;">{{ p.description }}</p>
            <div style="font-size:11px;color:var(--ink-soft);margin-bottom:10px;">Order: {{ p.display_order }} · {{ formatDate(p.created_at) }}</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
              <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" @click="editPromotion(p)">✏ Edit</button>
              <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" @click="togglePromotion(p.id)">{{ p.is_active ? '⏸ Deactivate' : '▶ Activate' }}</button>
              <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;color:#c0392b;" @click="deletePromotion(p.id)">🗑 Delete</button>
            </div>
          </div>
          <div v-if="promotions.length === 0" style="color:var(--ink-soft);font-size:14px;">No promotions yet. Add one above.</div>
        </div>
      </div>
    </div>

    <!-- ===================== GALLERY TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'gallery' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <div class="admin-panel-card">
        <h2>{{ editingGalleryId ? 'Edit Gallery Image' : 'Add Gallery Image' }}</h2>
        <form @submit.prevent="addGalleryItem">
          <div class="form-grid">
            <div class="form-group"><label>Image {{ editingGalleryId ? '' : '*' }}</label><input type="file" @change="handleGalleryImage" accept="image/*" :required="!editingGalleryId"></div>
            <div class="form-group"><label>Title / Caption</label><input type="text" v-model="galleryForm.title" placeholder="e.g. Hajj Group 2026"></div>
            <div class="form-group"><label>Category</label>
              <select v-model="galleryForm.category">
                <option>General</option>
                <option>Umrah</option>
                <option>Tours</option>
                <option>Events</option>
                <option>Hajj</option>
                <option>Hotels</option>
              </select>
            </div>
            <div class="form-group"><label>Display Order</label><input type="number" v-model="galleryForm.display_order" placeholder="0"></div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:24px;"><label style="margin:0;">Active</label><input type="checkbox" v-model="galleryForm.is_active" style="width:auto;"></div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : (editingGalleryId ? 'Update Image' : 'Upload Image') }}</button>
            <button v-if="editingGalleryId" type="button" class="btn btn-ghost" @click="cancelEditGallery">Cancel</button>
          </div>
        </form>
      </div>
      <div class="admin-panel-card">
        <h2>Gallery Images <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ galleryItems.length }} images</span></h2>
        <div class="gallery-admin-grid">
          <div class="gallery-admin-item" v-for="g in galleryItems" :key="g.id">
            <img :src="g.image_url" :alt="g.title" style="width:100%;height:130px;object-fit:cover;border-radius:10px;">
            <div style="padding:8px 4px;">
              <div style="font-size:12px;font-weight:600;color:var(--ink-dark);margin-bottom:2px;">{{ g.title || 'Untitled' }}</div>
              <div style="font-size:11px;color:var(--ink-soft);">{{ g.category }} · Order: {{ g.display_order }}</div>
              <div style="display:flex;gap:6px;margin-top:8px;flex-wrap:wrap;">
                <button class="btn btn-ghost" style="padding:3px 8px;font-size:11px;" @click="editGalleryItem(g)">✏</button>
                <button class="btn btn-ghost" style="padding:3px 8px;font-size:11px;" @click="toggleGallery(g.id)">{{ g.is_active ? '⏸' : '▶' }}</button>
                <button class="btn btn-ghost" style="padding:3px 8px;font-size:11px;color:#c0392b;" @click="deleteGalleryItem(g.id)">🗑</button>
              </div>
            </div>
          </div>
          <div v-if="galleryItems.length === 0" style="color:var(--ink-soft);font-size:14px;">No images yet. Upload one above.</div>
        </div>
      </div>
    </div>

    <!-- ===================== BLOG TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'blog' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <div class="admin-panel-card">
        <h2>{{ editingBlogId ? 'Edit Blog Post' : 'Add New Blog Post' }}</h2>
        <form @submit.prevent="addBlog">
          <div class="form-grid">
            <div class="form-group"><label>Title *</label><input type="text" v-model="blogForm.title" placeholder="e.g. First-Timer's Guide to Umrah" required></div>
            <div class="form-group"><label>Tag / Category</label><input type="text" v-model="blogForm.tag" placeholder="e.g. Umrah Guide, Visa Tips"></div>
            <div class="form-group"><label>Author</label><input type="text" v-model="blogForm.author" placeholder="e.g. Sangu Travel Team"></div>
            <div class="form-group"><label>Featured Image</label><input type="file" @change="handleBlogImage" accept="image/*"></div>
            <div class="form-group"><label>Display Order</label><input type="number" v-model="blogForm.display_order" placeholder="0"></div>
            <div class="form-group"><label>Published Date</label><input type="date" v-model="blogForm.published_at"></div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:24px;"><label style="margin:0;">Active</label><input type="checkbox" v-model="blogForm.is_active" style="width:auto;"></div>
            <div class="form-group full"><label>Excerpt / Short Summary</label><textarea rows="2" v-model="blogForm.excerpt" placeholder="A brief summary that shows on the blog listing page..."></textarea></div>
            <div class="form-group full"><label>Full Content (HTML supported)</label><textarea rows="8" v-model="blogForm.content" placeholder="Write the full article content here. HTML tags are supported for formatting."></textarea></div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : (editingBlogId ? 'Update Post' : 'Add Post') }}</button>
            <button v-if="editingBlogId" type="button" class="btn btn-ghost" @click="cancelEditBlog">Cancel</button>
          </div>
        </form>
      </div>
      <div class="admin-panel-card">
        <h2>All Blog Posts <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ blogs.length }} posts</span></h2>
        <div class="promo-grid">
          <div class="promo-admin-card" v-for="b in blogs" :key="b.id">
            <img v-if="b.image_url" :src="b.image_url" alt="Blog" style="width:100%;height:120px;object-fit:cover;border-radius:10px;margin-bottom:10px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
              <span class="badge" :style="b.is_active ? 'background:var(--teal-dark);color:#fff;' : 'background:#ccc;color:#555;'">{{ b.is_active ? 'Active' : 'Draft' }}</span>
              <b style="font-size:14px;">{{ b.title }}</b>
            </div>
            <div v-if="b.tag" style="font-size:12px;color:var(--teal);margin-bottom:4px;">{{ b.tag }}</div>
            <p style="font-size:13px;color:var(--ink-soft);margin:0 0 4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ b.excerpt }}</p>
            <div style="font-size:11px;color:var(--ink-soft);margin-bottom:10px;">{{ formatDate(b.published_at) }} · {{ b.author || 'Unknown' }}</div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
              <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" @click="editBlog(b)">✏ Edit</button>
              <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;" @click="toggleBlog(b.id)">{{ b.is_active ? '⏸ Draft' : '▶ Publish' }}</button>
              <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;color:#c0392b;" @click="deleteBlog(b.id)">🗑 Delete</button>
            </div>
          </div>
          <div v-if="blogs.length === 0" style="color:var(--ink-soft);font-size:14px;">No blog posts yet. Add one above.</div>
        </div>
      </div>
    </div>

    <!-- ===================== HOTEL TAB ===================== -->
    <div class="finder-body" :class="{ active: activeTab === 'hotel' }" style="grid-template-columns:none;padding:24px 0 0;background:transparent;">
      <div class="admin-panel-card">
        <h2>Add New Hotel</h2>
        <form @submit.prevent="addHotel">
          <div class="form-grid">
            <div class="form-group"><label>Hotel Name *</label><input type="text" v-model="hotelForm.name" placeholder="e.g. Anjum Hotel Makkah" required></div>
            <div class="form-group"><label>City *</label>
              <select v-model="hotelForm.city" required>
                <option>Makkah</option>
                <option>Madinah</option>
              </select>
            </div>
            <div class="form-group"><label>Hotel Image</label><input type="file" @change="handleHotelImage" accept="image/*"></div>
            <div class="form-group"><label>Star Rating</label>
              <select v-model="hotelForm.stars">
                <option value="3">3-Star</option>
                <option value="4">4-Star</option>
                <option value="5">5-Star</option>
              </select>
            </div>
            <div class="form-group"><label>Distance from Haram</label><input type="text" v-model="hotelForm.distance" placeholder="e.g. 200m from Haram"></div>
            <div class="form-group"><label>Price / Night (PKR) *</label><input type="number" v-model="hotelForm.price" placeholder="18000" required></div>
            <div class="form-group"><label>Room Types (One per line)</label><textarea rows="3" v-model="hotelForm.room_types" placeholder="Quad Room&#10;Triple Room&#10;Double Room"></textarea></div>
            <div class="form-group full"><label>Description</label><textarea rows="2" v-model="hotelForm.desc" placeholder="Brief description of this hotel..."></textarea></div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;">
            <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Saving...' : 'Add Hotel' }}</button>
          </div>
        </form>
      </div>
      <div class="admin-panel-card">
        <h2>All Hotels <span class="mono" style="font-size:13px;color:var(--ink-soft);">{{ hotels.length }} records</span></h2>
        <table class="admin-table">
          <thead><tr><th>Name</th><th>City</th><th>Stars</th><th>Distance</th><th>Price/Night (EUR €)</th><th>Actions</th></tr></thead>
          <tbody>
            <tr v-for="h in hotels" :key="h.id">
              <td>{{ h.name }}</td>
              <td>{{ h.city }}</td>
              <td>{{ '★'.repeat(parseInt(h.stars) || 3) }}</td>
              <td>{{ h.distance }}</td>
              <td>{{ parseFloat(h.price).toLocaleString('de-DE') }}</td>
              <td>
                <button class="btn btn-ghost" style="padding:4px 10px;font-size:12px;color:#c0392b;" @click="deleteHotel(h.id)">🗑 Delete</button>
              </td>
            </tr>
            <tr v-if="hotels.length === 0"><td colspan="6" class="empty-state">No hotels yet. Add one above.</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import SeoAudit from './SeoAudit.vue';

const activeTab = ref('visa');
const saving = ref(false);
const toast = ref(null);

const visas  = ref([]);
const umrahs = ref([]);
const groups = ref([]);
const hotels = ref([]);
const promotions = ref([]);
const galleryItems = ref([]);
const blogs = ref([]);
const editingPromotionId = ref(null);
const editingGalleryId = ref(null);
const editingBlogId = ref(null);
const editingVisaId = ref(null);
const editingUmrahId = ref(null);
const editingGroupId = ref(null);

// ── Helpers ─────────────────────────────────────────
const parseLines = (str) => {
  if (!str) return [];
  return str.split('\n').map(s => s.trim()).filter(s => s.length > 0);
};

const formatDate = (d) => {
  if (!d) return '';
  return new Date(d).toLocaleDateString('en-PK', { day: 'numeric', month: 'short', year: 'numeric' });
};

const defaultPromotionForm = () => ({ title:'', description:'', button_text:'Learn More', button_url:'', image:null, is_active:true, display_order:0 });
const defaultGalleryForm = () => ({ title:'', category:'General', image:null, is_active:true, display_order:0 });
const defaultBlogForm = () => ({ title:'', tag:'', excerpt:'', content:'', author:'', image:null, is_active:true, display_order:0, published_at:'' });

// ── Form state ──────────────────────────────────────
const visaForm = ref({ country:'', title:'', type:'', image:null, flag:'', duration:'', processing:'', price:'', discount_amount:'', discount_percent:'', discount_label:'', requirements:'', documents_required:'', important_info:'', desc:'' });
const umrahForm = ref({ title:'', duration_type:'14 Days', image:null, price:'', discount_amount:'', discount_percent:'', discount_label:'', makkah_hotel:'', madinah_hotel:'', transport:'', rating:'', desc:'' });
const groupForm = ref({ name:'', departure_date:'', return_date:'', image:null, seats:'', price:'', discount_amount:'', discount_percent:'', discount_label:'', hotel:'', transport:'', group_details:'' });
const hotelForm = ref({ name:'', city:'Makkah', image:null, stars:'4', distance:'', price:'', discount_amount:'', discount_percent:'', discount_label:'', room_types:'', desc:'' });
const promotionForm = ref(defaultPromotionForm());
const galleryForm = ref(defaultGalleryForm());
const blogForm = ref(defaultBlogForm());

// ── Toast helper ─────────────────────────────────────
const showToast = (msg, type = 'success') => {
  toast.value = { msg, type };
  setTimeout(() => { toast.value = null; }, 3500);
};

// ── Load data ────────────────────────────────────────
const loadAll = async () => {
  try {
    const [vr, ur, gr, hr, pr, glr, br] = await Promise.all([
      axios.get('/api/visas'),
      axios.get('/api/umrah-packages'),
      axios.get('/api/umrah-groups'),
      axios.get('/api/hotels'),
      axios.get('/api/promotions?all=1'),
      axios.get('/api/gallery?all=1'),
      axios.get('/api/blogs?all=1'),
    ]);
    visas.value  = vr.data;
    umrahs.value = ur.data;
    groups.value = gr.data;
    hotels.value = hr.data;
    promotions.value = pr.data;
    galleryItems.value = glr.data;
    blogs.value = br.data;
  } catch(err) {
    console.error(err);
    showToast('Failed to load data', 'error');
  }
};

onMounted(loadAll);

// ── ADD VISA ─────────────────────────────────────────
const handleVisaImage = (e) => {
  if (e.target.files.length) {
    visaForm.value.image = e.target.files[0];
  }
};

const addVisa = async () => {
  saving.value = true;
  try {
    const formData = new FormData();
    Object.keys(visaForm.value).forEach(k => {
      if (k === 'image' && !visaForm.value.image) return;
      if (k === 'requirements' || k === 'documents_required') {
         parseLines(visaForm.value[k]).forEach((val, idx) => {
            formData.append(`${k}[${idx}]`, val);
         });
      } else {
         formData.append(k, visaForm.value[k]);
      }
    });

    if (editingVisaId.value) {
      await axios.post('/api/visas/' + editingVisaId.value, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      showToast(`✓ Visa for ${visaForm.value.country} updated successfully!`);
    } else {
      await axios.post('/api/visas', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      showToast(`✓ Visa for ${visaForm.value.country} added successfully!`);
    }

    cancelEditVisa();
    await loadAll();
  } catch(err) {
    showToast(err.response?.data?.message || 'Failed to save visa', 'error');
  } finally {
    saving.value = false;
  }
};

const editVisa = (v) => {
  editingVisaId.value = v.id;
  visaForm.value = {
    country: v.country || '',
    title: v.title || '',
    type: v.type || '',
    image: null,
    flag: v.flag || '',
    duration: v.duration || '',
    processing: v.processing || '',
    price: v.price || '',
    discount_amount: v.discount_amount || '',
    discount_percent: v.discount_percent || '',
    discount_label: v.discount_label || '',
    requirements: Array.isArray(v.requirements) ? v.requirements.join('\n') : (v.requirements || ''),
    documents_required: Array.isArray(v.documents_required) ? v.documents_required.join('\n') : (v.documents_required || ''),
    important_info: v.important_info || '',
    desc: v.desc || ''
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEditVisa = () => {
  editingVisaId.value = null;
  visaForm.value = { country:'', title:'', type:'', image:null, flag:'', duration:'', processing:'', price:'', discount_amount:'', discount_percent:'', discount_label:'', requirements:'', documents_required:'', important_info:'', desc:'' };
};

// ── DELETE VISA ──────────────────────────────────────
const deleteVisa = async (id) => {
  if (!confirm('Delete this visa?')) return;
  try {
    await axios.delete('/api/visas/' + id);
    showToast('Visa deleted.');
    if (editingVisaId.value === id) cancelEditVisa();
    await loadAll();
  } catch(err) {
    showToast('Failed to delete', 'error');
  }
};

// ── ADD UMRAH ─────────────────────────────────────────
const handleUmrahImage = (e) => { if (e.target.files.length) umrahForm.value.image = e.target.files[0]; };

const addUmrah = async () => {
  saving.value = true;
  try {
    const fd = new FormData();
    Object.keys(umrahForm.value).forEach(k => {
      if (k === 'image' && !umrahForm.value.image) return;
      fd.append(k, umrahForm.value[k]);
    });
    if (editingUmrahId.value) {
      await axios.post('/api/umrah-packages/' + editingUmrahId.value, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Package "${umrahForm.value.title}" updated successfully!`);
    } else {
      await axios.post('/api/umrah-packages', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Package "${umrahForm.value.title}" added successfully!`);
    }
    cancelEditUmrah();
    await loadAll();
  } catch(err) {
    showToast(err.response?.data?.message || 'Failed to save package', 'error');
  } finally {
    saving.value = false;
  }
};

const editUmrah = (u) => {
  editingUmrahId.value = u.id;
  umrahForm.value = {
    title: u.title || '',
    duration_type: u.duration_type || '14 Days',
    image: null,
    price: u.price || '',
    discount_amount: u.discount_amount || '',
    discount_percent: u.discount_percent || '',
    discount_label: u.discount_label || '',
    makkah_hotel: u.makkah_hotel || '',
    madinah_hotel: u.madinah_hotel || '',
    transport: u.transport || '',
    rating: u.rating || '',
    desc: u.desc || ''
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEditUmrah = () => {
  editingUmrahId.value = null;
  umrahForm.value = { title:'', duration_type:'14 Days', image:null, price:'', discount_amount:'', discount_percent:'', discount_label:'', makkah_hotel:'', madinah_hotel:'', transport:'', rating:'', desc:'' };
};

// ── DELETE UMRAH ─────────────────────────────────────
const deleteUmrah = async (id) => {
  if (!confirm('Delete this package?')) return;
  try {
    await axios.delete('/api/umrah-packages/' + id);
    showToast('Package deleted.');
    if (editingUmrahId.value === id) cancelEditUmrah();
    await loadAll();
  } catch(err) {
    showToast('Failed to delete', 'error');
  }
};

// ── ADD GROUP ─────────────────────────────────────────
const handleGroupImage = (e) => { if (e.target.files.length) groupForm.value.image = e.target.files[0]; };

const addGroup = async () => {
  saving.value = true;
  try {
    const fd = new FormData();
    Object.keys(groupForm.value).forEach(k => {
      if (k === 'image' && !groupForm.value.image) return;
      fd.append(k, groupForm.value[k]);
    });
    if (editingGroupId.value) {
      await axios.post('/api/umrah-groups/' + editingGroupId.value, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Group "${groupForm.value.name}" updated successfully!`);
    } else {
      await axios.post('/api/umrah-groups', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Group "${groupForm.value.name}" added successfully!`);
    }
    cancelEditGroup();
    await loadAll();
  } catch(err) {
    showToast(err.response?.data?.message || 'Failed to save group', 'error');
  } finally {
    saving.value = false;
  }
};

const editGroup = (g) => {
  editingGroupId.value = g.id;
  groupForm.value = {
    name: g.name || '',
    departure_date: g.departure_date ? g.departure_date.split('T')[0] : '',
    return_date: g.return_date ? g.return_date.split('T')[0] : '',
    image: null,
    seats: g.seats || '',
    price: g.price || '',
    discount_amount: g.discount_amount || '',
    discount_percent: g.discount_percent || '',
    discount_label: g.discount_label || '',
    hotel: g.hotel || '',
    transport: g.transport || '',
    group_details: g.group_details || ''
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEditGroup = () => {
  editingGroupId.value = null;
  groupForm.value = { name:'', departure_date:'', return_date:'', image:null, seats:'', price:'', discount_amount:'', discount_percent:'', discount_label:'', hotel:'', transport:'', group_details:'' };
};

// ── DELETE GROUP ─────────────────────────────────────
const deleteGroup = async (id) => {
  if (!confirm('Delete this group?')) return;
  try {
    await axios.delete('/api/umrah-groups/' + id);
    showToast('Group deleted.');
    if (editingGroupId.value === id) cancelEditGroup();
    await loadAll();
  } catch(err) {
    showToast('Failed to delete', 'error');
  }
};


// ── ADD HOTEL ─────────────────────────────────────────
const handleHotelImage = (e) => { if (e.target.files.length) hotelForm.value.image = e.target.files[0]; };

const addHotel = async () => {
  saving.value = true;
  try {
    const fd = new FormData();
    parseLines(hotelForm.value.room_types).forEach((v, i) => fd.append(`room_types[${i}]`, v));
    Object.keys(hotelForm.value).forEach(k => {
      if (k === 'room_types') return;
      if (k === 'image' && !hotelForm.value.image) return;
      fd.append(k, hotelForm.value[k]);
    });
    await axios.post('/api/hotels', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
    showToast(`✓ Hotel "${hotelForm.value.name}" added successfully!`);
    hotelForm.value = { name:'', city:'Makkah', image:null, stars:'4', distance:'', price:'', discount_amount:'', discount_percent:'', discount_label:'', room_types:'', desc:'' };
    await loadAll();
  } catch(err) {
    showToast(err.response?.data?.message || 'Failed to save hotel', 'error');
  } finally {
    saving.value = false;
  }
};

// ── DELETE HOTEL ─────────────────────────────────────
const deleteHotel = async (id) => {
  if (!confirm('Delete this hotel?')) return;
  try {
    await axios.delete('/api/hotels/' + id);
    showToast('Hotel deleted.');
    await loadAll();
  } catch(err) {
    showToast('Failed to delete', 'error');
  }
};

// ── PROMOTIONS ───────────────────────────────────────
const handlePromotionImage = (e) => { if (e.target.files.length) promotionForm.value.image = e.target.files[0]; };

const editPromotion = (p) => {
  editingPromotionId.value = p.id;
  promotionForm.value = {
    title: p.title,
    description: p.description || '',
    button_text: p.button_text || 'Learn More',
    button_url: p.button_url || '',
    image: null,
    is_active: !!p.is_active,
    display_order: p.display_order ?? 0,
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEditPromotion = () => {
  editingPromotionId.value = null;
  promotionForm.value = defaultPromotionForm();
};

const addPromotion = async () => {
  saving.value = true;
  try {
    const fd = new FormData();
    Object.keys(promotionForm.value).forEach(k => {
      if (k === 'image' && !promotionForm.value.image) return;
      if (k === 'is_active') {
        fd.append(k, promotionForm.value.is_active ? '1' : '0');
      } else {
        fd.append(k, promotionForm.value[k] ?? '');
      }
    });

    if (editingPromotionId.value) {
      await axios.post('/api/promotions/' + editingPromotionId.value, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Promotion "${promotionForm.value.title}" updated!`);
    } else {
      await axios.post('/api/promotions', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Promotion "${promotionForm.value.title}" added!`);
    }

    cancelEditPromotion();
    await loadAll();
  } catch(err) {
    showToast(err.response?.data?.message || 'Failed to save promotion', 'error');
  } finally {
    saving.value = false;
  }
};

const togglePromotion = async (id) => {
  try {
    await axios.patch('/api/promotions/' + id + '/toggle');
    showToast('Promotion status updated.');
    await loadAll();
  } catch(err) {
    showToast('Failed to update status', 'error');
  }
};

const deletePromotion = async (id) => {
  if (!confirm('Delete this promotion?')) return;
  try {
    await axios.delete('/api/promotions/' + id);
    showToast('Promotion deleted.');
    if (editingPromotionId.value === id) cancelEditPromotion();
    await loadAll();
  } catch(err) {
    showToast('Failed to delete', 'error');
  }
};

// ── GALLERY ──────────────────────────────────────────
const handleGalleryImage = (e) => { if (e.target.files.length) galleryForm.value.image = e.target.files[0]; };

const editGalleryItem = (g) => {
  editingGalleryId.value = g.id;
  galleryForm.value = {
    title: g.title || '',
    category: g.category || 'General',
    image: null,
    is_active: !!g.is_active,
    display_order: g.display_order ?? 0,
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEditGallery = () => {
  editingGalleryId.value = null;
  galleryForm.value = defaultGalleryForm();
};

const addGalleryItem = async () => {
  saving.value = true;
  try {
    const fd = new FormData();
    Object.keys(galleryForm.value).forEach(k => {
      if (k === 'image' && !galleryForm.value.image) return;
      if (k === 'is_active') {
        fd.append(k, galleryForm.value.is_active ? '1' : '0');
      } else {
        fd.append(k, galleryForm.value[k] ?? '');
      }
    });

    if (editingGalleryId.value) {
      await axios.post('/api/gallery/' + editingGalleryId.value, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('✓ Gallery image updated!');
    } else {
      if (!galleryForm.value.image) {
        showToast('Please select an image', 'error');
        return;
      }
      await axios.post('/api/gallery', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('✓ Gallery image uploaded!');
    }

    cancelEditGallery();
    await loadAll();
  } catch(err) {
    showToast(err.response?.data?.message || 'Failed to save gallery item', 'error');
  } finally {
    saving.value = false;
  }
};

const toggleGallery = async (id) => {
  try {
    await axios.patch('/api/gallery/' + id + '/toggle');
    showToast('Gallery status updated.');
    await loadAll();
  } catch(err) {
    showToast('Failed to update status', 'error');
  }
};

const deleteGalleryItem = async (id) => {
  if (!confirm('Delete this gallery image?')) return;
  try {
    await axios.delete('/api/gallery/' + id);
    showToast('Gallery image deleted.');
    if (editingGalleryId.value === id) cancelEditGallery();
    await loadAll();
  } catch(err) {
    showToast('Failed to delete', 'error');
  }
};

// ── BLOG ──────────────────────────────────────────
const handleBlogImage = (e) => { if (e.target.files.length) blogForm.value.image = e.target.files[0]; };

const editBlog = (b) => {
  editingBlogId.value = b.id;
  blogForm.value = {
    title: b.title || '',
    tag: b.tag || '',
    excerpt: b.excerpt || '',
    content: b.content || '',
    author: b.author || '',
    image: null,
    is_active: !!b.is_active,
    display_order: b.display_order ?? 0,
    published_at: b.published_at ? b.published_at.split('T')[0] : '',
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEditBlog = () => {
  editingBlogId.value = null;
  blogForm.value = defaultBlogForm();
};

const addBlog = async () => {
  saving.value = true;
  try {
    const fd = new FormData();
    Object.keys(blogForm.value).forEach(k => {
      if (k === 'image' && !blogForm.value.image) return;
      if (k === 'is_active') {
        fd.append(k, blogForm.value.is_active ? '1' : '0');
      } else {
        fd.append(k, blogForm.value[k] ?? '');
      }
    });

    if (editingBlogId.value) {
      await axios.post('/api/blogs/' + editingBlogId.value, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Blog post "${blogForm.value.title}" updated!`);
    } else {
      await axios.post('/api/blogs', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast(`✓ Blog post "${blogForm.value.title}" added!`);
    }

    cancelEditBlog();
    await loadAll();
  } catch(err) {
    showToast(err.response?.data?.message || 'Failed to save blog post', 'error');
  } finally {
    saving.value = false;
  }
};

const toggleBlog = async (id) => {
  try {
    await axios.patch('/api/blogs/' + id + '/toggle');
    showToast('Blog post status updated.');
    await loadAll();
  } catch(err) {
    showToast('Failed to update status', 'error');
  }
};

const deleteBlog = async (id) => {
  if (!confirm('Delete this blog post?')) return;
  try {
    await axios.delete('/api/blogs/' + id);
    showToast('Blog post deleted.');
    if (editingBlogId.value === id) cancelEditBlog();
    await loadAll();
  } catch(err) {
    showToast('Failed to delete', 'error');
  }
};
</script>

<style scoped>
.admin-toast {
  position: fixed;
  bottom: 28px;
  right: 28px;
  background: var(--teal-dark);
  color: #fff;
  padding: 14px 22px;
  border-radius: 12px;
  font-size: 14px;
  z-index: 9999;
  box-shadow: 0 8px 24px rgba(0,0,0,.18);
  animation: slideUp .25s ease;
}
.admin-toast.error { background: #c0392b; }
@keyframes slideUp {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}
.promo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
}
.promo-admin-card {
  background: var(--bg-raised);
  border: 1px solid var(--line);
  border-radius: 14px;
  padding: 14px;
}
.gallery-admin-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 14px;
}
.gallery-admin-item {
  background: var(--bg-raised);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 10px;
}
</style>
