import { apiFetch } from "./api.js";

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
    el.innerHTML = `
      <span style="color:white; margin-right:15px;">Welcome!</span>
      <a href="logout.html" class="btn-register" style="background:#e74c3c;">Logout</a>
    `;
  } else {
    el.innerHTML = `
      <a href="login.html" class="btn-login">Log In</a>
      <a href="register.html" class="btn-register">Sign Up</a>
    `;
  }
}
