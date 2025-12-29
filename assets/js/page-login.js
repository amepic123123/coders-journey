import { apiFetch, getQueryParam } from "./api.js";
import { getSession, renderNavAuth } from "./session.js";

await renderNavAuth();

const form = document.getElementById("login-form");
const errorEl = document.getElementById("login-error");
const successEl = document.getElementById("login-success");

(async () => {
  const session = await getSession();
  if (session?.logged_in) {
    window.location.href = "index.html";
    return;
  }

  const err = getQueryParam("error");
  if (err) {
    errorEl.style.display = "block";
    if (err === "wrong_login") errorEl.textContent = "❌ Incorrect email or password.";
    else if (err === "unverified") errorEl.textContent = "⚠️ Please verify your email first.";
    else if (err === "must_login") errorEl.textContent = "⚠️ Please log in to continue.";
    else errorEl.textContent = "⚠️ Login failed.";
  }

  const success = getQueryParam("success");
  if (success) {
    successEl.style.display = "block";
    successEl.textContent = "✅ Registration successful! Please log in.";
  }
})();

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  errorEl.style.display = "none";
  successEl.style.display = "none";

  const fd = new FormData(form);
  const email = String(fd.get("email") ?? "").trim();
  const password = String(fd.get("password") ?? "");

  try {
    await apiFetch("/actions/login_logic.php", {
      method: "POST",
      body: { email, password },
    });

    window.location.href = "index.html";
  } catch (err) {
    errorEl.style.display = "block";
    errorEl.textContent = err.message;
  }
});
