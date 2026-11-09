// SanguTravel Admin — shared helpers (demo data layer using localStorage)

const ADMIN_DEMO_USER = { email: "admin@sangutravel.pk", password: "admin123" };

function adminCheckAuth() {
  if (!sessionStorage.getItem('sangu_admin_auth')) {
    window.location.href = 'login.html';
  }
}

function adminLogout() {
  sessionStorage.removeItem('sangu_admin_auth');
  window.location.href = 'login.html';
}

function adminSeedBookings() {
  if (localStorage.getItem('sangu_bookings')) return;
  const seed = [
    {ref:'SGT-482913', type:'umrah', title:'Umrah Premium Package', name:'Ahmed Raza', email:'ahmed.raza@example.com', phone:'0301-1234567', travellers:'2', total:'PKR 531,500', status:'Confirmed', date:'2026-08-12'},
    {ref:'SGT-291847', type:'visa', title:'United Arab Emirates Visa', name:'Sana Malik', email:'sana.malik@example.com', phone:'0333-9876543', travellers:'1', total:'PKR 20,000', status:'Pending Review', date:'2026-08-15'},
    {ref:'SGT-773625', type:'hotel', title:'Swissôtel Al Maqam Makkah', name:'Bilal Ahmed', email:'bilal.a@example.com', phone:'0312-4455667', travellers:'4', total:'PKR 129,500', status:'Confirmed', date:'2026-08-16'},
    {ref:'SGT-118204', type:'umrah', title:'Ramadan Umrah Special', name:'Fatima Yousuf', email:'fatima.y@example.com', phone:'0345-2233445', travellers:'6', total:'PKR 1,861,500', status:'Pending Review', date:'2026-08-18'},
    {ref:'SGT-604512', type:'visa', title:'Schengen (EU) Visa', name:'Usman Tariq', email:'usman.t@example.com', phone:'0300-1122334', travellers:'1', total:'PKR 37,500', status:'Cancelled', date:'2026-08-10'},
  ];
  localStorage.setItem('sangu_bookings', JSON.stringify(seed));
}

function adminGetBookings() {
  adminSeedBookings();
  return JSON.parse(localStorage.getItem('sangu_bookings') || '[]');
}

function adminSaveBookings(list) {
  localStorage.setItem('sangu_bookings', JSON.stringify(list));
}

function adminStatusPillClass(status) {
  if (status === 'Confirmed') return 'confirmed';
  if (status === 'Cancelled') return 'cancelled';
  return 'pending';
}
