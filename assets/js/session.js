import { apiFetch } from "./api.js";

function buildAvatarUrl(avatarPath) {
  if (!avatarPath) return "";
  const p = String(avatarPath).replace(/^\/+/, "");
  // API_BASE already includes /webProjectBackend, so use it as the image origin too.
  try {
    // Lazy import to avoid circular deps: api.js imports no session.js.
    // eslint-disable-next-line no-undef
    return (window.__API_BASE__ || "") + "/" + p;
  } catch {
    return "/" + p;
  }
}

// This endpoint does not exist in your current backend yet.
// You said you'll adjust your backend to match his endpoints, so implement:
// GET /actions/session.php  -> { logged_in: boolean, user_id?: number, username?: string }
export async function getSession() {
  try {
    return await apiFetch("/actions/session.php");
  } catch {
    return { logged_in: false };
  }
}

export async function renderNavAuth() {
  const el = document.getElementById("nav-auth");
  if (!el) return;

  const session = await getSession();
  if (session && session.logged_in) {
    // Persist API base for building asset URLs without importing api.js (avoids circular deps).
    if (!window.__API_BASE__) {
      try {
        const mod = await import("./api.js");
        window.__API_BASE__ = mod.API_BASE;
      } catch {
        window.__API_BASE__ = "";
      }
    }

    const avatarPath = session?.avatar_path || "";
    const avatarUrl = buildAvatarUrl(avatarPath);
    const username = session?.username || "Profile";
    const initial = String(username).slice(0, 1).toUpperCase();

    el.innerHTML = `
      <a href="profile.html" class="nav-profile" title="Profile" style="display:flex; align-items:center; margin-right:12px;">
        ${
          avatarUrl
            ? `<img src="${avatarUrl}" alt="Profile" style="width:34px; height:34px; border-radius:999px; object-fit:cover; border:2px solid rgba(255,255,255,0.25);" />`
            : `<div style="width:34px; height:34px; border-radius:999px; background: rgba(255,255,255,0.18); display:flex; align-items:center; justify-content:center; font-weight:700; color:white;">${initial}</div>`
        }
        <span style="color:white; margin-left:10px;">Profile</span>
      </a>
      <a href="logout.html" class="btn-register" style="background:#e74c3c;">Logout</a>
    `;
  } else {
    el.innerHTML = `
      <a href="login.html" class="btn-login">Log In</a>
      <a href="register.html" class="btn-register">Sign Up</a>
    `;
  }
}
