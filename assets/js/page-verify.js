import { apiFetch, getQueryParam } from "./api.js";

const email = getQueryParam("email") || "";

const emailEl = document.getElementById("verify-email");
const emailInput = document.getElementById("verify-email-input");
const form = document.getElementById("verify-form");
const errorEl = document.getElementById("verify-error");

emailEl.textContent = email || "(missing email)";
emailInput.value = email;

const err = getQueryParam("error");
if (err) {
  errorEl.style.display = "block";
  if (err === "wrong_code") errorEl.textContent = "❌ Incorrect code. Try again.";
  else if (err === "empty") errorEl.textContent = "⚠️ Please enter the code.";
  else errorEl.textContent = "⚠️ Verification failed.";
}

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  errorEl.style.display = "none";

  const fd = new FormData(form);
  const otp_code = String(fd.get("otp_code") ?? "").trim();
  const emailVal = String(fd.get("email") ?? "").trim();

  try {
    await apiFetch("/actions/verify_logic.php", {
      method: "POST",
      body: { email: emailVal, otp_code },
    });

    window.location.href = "login.html?success=1";
  } catch (err2) {
    errorEl.style.display = "block";
    errorEl.textContent = err2.message;
  }
});
