import { apiFetch, escapeHtml, getQueryParam } from "./api.js";
import { getSession, renderNavAuth } from "./session.js";

await renderNavAuth();

const errorEl = document.getElementById("ask-error");
const form = document.getElementById("ask-form");

(async () => {
  const session = await getSession();
  if (!session?.logged_in) {
    window.location.href = "login.html?error=must_login";
    return;
  }

  if (getQueryParam("error")) {
    errorEl.style.display = "block";
  }
})();

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  errorEl.style.display = "none";

  const fd = new FormData(form);
  const q_title = String(fd.get("q_title") ?? "").trim();
  const q_desc = String(fd.get("q_desc") ?? "").trim();
  const q_tags = String(fd.get("q_tags") ?? "").trim();

  if (!q_title || !q_desc) {
    errorEl.style.display = "block";
    return;
  }

  try {
    const res = await apiFetch("/actions/ask_logic.php", {
      method: "POST",
      body: { q_title, q_desc, q_tags },
    });

    const newId = res?.id ?? res?.question_id;
    if (newId) {
      window.location.href = `question.html?id=${encodeURIComponent(newId)}`;
      return;
    }

    window.location.href = "index.html";
  } catch (err) {
    errorEl.style.display = "block";
    errorEl.textContent = `⚠️ ${escapeHtml(err.message)}`;
  }
});
