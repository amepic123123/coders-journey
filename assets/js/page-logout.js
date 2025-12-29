import { apiFetch } from "./api.js";

const statusEl = document.getElementById("logout-status");

(async () => {
  try {
    // Preserve teammate endpoint naming expectation.
    await apiFetch("/actions/logout.php");
    statusEl.textContent = "Logged out.";
  } catch {
    // Even if backend endpoint isn't ready yet, take user back home.
    statusEl.textContent = "Done.";
  }

  setTimeout(() => {
    window.location.href = "index.html";
  }, 400);
})();
