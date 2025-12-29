import { apiFetch } from "./api.js";
import { getSession, renderNavAuth } from "./session.js";

await renderNavAuth();

const form = document.getElementById("register-form");
const errorEl = document.getElementById("register-error");

(async () => {
  const session = await getSession();
  if (session?.logged_in) {
    window.location.href = "index.html";
  }
})();

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  errorEl.style.display = "none";

  const fd = new FormData(form);
  const username = String(fd.get("username") ?? "").trim();
  const email = String(fd.get("email") ?? "").trim();
  const password = String(fd.get("password") ?? "");

  try {
    const res = await apiFetch("/actions/register_logic.php", {
      method: "POST",
      body: { username, email, password },
    });

    // If backend returns a verification step, send user there.
    const needsVerify = res?.needs_verify ?? res?.verify_required ?? false;
    if (needsVerify) {
      window.location.href = `verify.html?email=${encodeURIComponent(email)}`;
      return;
    }

    window.location.href = "login.html?success=1";
  } catch (err) {
    errorEl.style.display = "block";
    errorEl.textContent = err.message;
  }
});
