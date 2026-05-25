/**
 * Pentasara — API Helper
 * Loaded globally via layouts/app.blade.php before any page script.
 */

const API_BASE = '/api';

// ──────────────────────────────
// HTTP helpers (auth-aware)
// ──────────────────────────────

async function apiGet(endpoint) {
    const res = await fetch(API_BASE + endpoint, {
        method: 'GET',
        headers: _authHeaders(),
    });
    return _handle(res);
}

async function apiPost(endpoint, body = {}) {
    const res = await fetch(API_BASE + endpoint, {
        method: 'POST',
        headers: { ..._authHeaders(), 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });
    return _handle(res);
}

async function apiPatch(endpoint, body = {}) {
    const res = await fetch(API_BASE + endpoint, {
        method: 'PATCH',
        headers: { ..._authHeaders(), 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });
    return _handle(res);
}

async function apiDelete(endpoint) {
    const res = await fetch(API_BASE + endpoint, {
        method: 'DELETE',
        headers: _authHeaders(),
    });
    return _handle(res);
}

async function apiUpload(endpoint, formData) {
    const headers = _authHeaders();
    // Do NOT set Content-Type — let the browser set it with boundary
    delete headers['Content-Type'];

    const res = await fetch(API_BASE + endpoint, {
        method: 'POST',
        headers,
        body: formData,
    });
    return _handle(res);
}

// ──────────────────────────────
// User / Token utilities
// ──────────────────────────────

function getUser() {
    return JSON.parse(localStorage.getItem('user') || 'null');
}

function getToken() {
    return localStorage.getItem('auth_token');
}

function isLoggedIn() {
    return !!getToken();
}

// ──────────────────────────────
// Guards
// ──────────────────────────────

function requireAuth(redirectTo = '/auth') {
    if (!isLoggedIn()) {
        window.location.href = redirectTo;
        return false;
    }
    return true;
}

function requireRole(role, redirectTo = '/') {
    const user = getUser();
    if (!user || user.role !== role) {
        window.location.href = redirectTo;
        return false;
    }
    return true;
}

// ──────────────────────────────
// Logout
// ──────────────────────────────

async function logout() {
    if (getToken()) {
        await apiPost('/logout', {}).catch(() => {});
    }
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = '/auth';
}

// ──────────────────────────────
// Internal helpers
// ──────────────────────────────

function _authHeaders() {
    const headers = { 'Accept': 'application/json' };
    const token = getToken();
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
    }
    return headers;
}

async function _handle(res) {
    const data = await res.json().catch(() => null);
    // Attach raw status so callers can inspect it
    if (data !== null) {
        data._status = res.status;
        data._ok = res.ok;
    }
    return data;
}

// Expose globally
window.apiGet = apiGet;
window.apiPost = apiPost;
window.apiPatch = apiPatch;
window.apiDelete = apiDelete;
window.apiUpload = apiUpload;
window.getUser = getUser;
window.getToken = getToken;
window.isLoggedIn = isLoggedIn;
window.requireAuth = requireAuth;
window.requireRole = requireRole;
window.logout = logout;

console.log('Pentasara API Helper loaded');
