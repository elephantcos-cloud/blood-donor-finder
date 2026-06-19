// ডার্ক মোড — পেজ লোড হওয়ার সাথে সাথেই প্রয়োগ হয় যাতে flash না হয়
(function () {
    var saved = localStorage.getItem('rb-theme');
    if (saved === 'dark') document.documentElement.classList.add('dark');
})();

function toggleTheme() {
    var isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('rb-theme', isDark ? 'dark' : 'light');
}

// মোডাল
function openModal(id) {
    var m = document.getElementById(id);
    if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    var m = document.getElementById(id);
    if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
}

// শেয়ার বাটন
function shareSite() {
    if (navigator.share) {
        navigator.share({ title: document.title, url: location.href }).catch(function () {});
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(location.href);
        alert('লিংক কপি হয়েছে / Link copied');
    }
}

// সার্চ পেজে ব্লাড গ্রুপ পিল ক্লিক করলে অটো-সাবমিট
function selectBloodPill(value) {
    var input = document.getElementById('bloodGroupInput');
    if (input) {
        input.value = (input.value === value) ? '' : value;
        document.getElementById('searchForm').submit();
    }
}
