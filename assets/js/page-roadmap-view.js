import { apiFetch, escapeHtml, getQueryParam } from "./api.js";
import { renderNavAuth } from "./session.js";

await renderNavAuth();

const roadmapTitleEl = document.getElementById("roadmap-title");
const roadmapDescEl = document.getElementById("roadmap-description");
const roadmapAuthorEl = document.getElementById("roadmap-author");
const stepsListEl = document.getElementById("steps-list");
const progressBarEl = document.getElementById("main-progress-bar");
const percentEl = document.getElementById("percent-display");
const errorEl = document.getElementById("roadmap-error");

function setError(msg) {
  errorEl.style.display = "block";
  errorEl.textContent = msg;
}

function computePercent(checked, total) {
  if (!total) return 0;
  return Math.round((checked / total) * 100);
}

function renderStep(step) {
  const id = step?.id ?? step?.step_id;
  const title = step?.title ?? "";
  const description = step?.description ?? "";
  const completed = !!(step?.is_completed ?? step?.completed);

  return `
    <li class="step-item">
      <div style="background: white; padding: 10px; border-radius: 50%; border: 1px solid #ddd;">
        <input type="checkbox" class="step-check" data-step-id="${escapeHtml(id)}" ${completed ? "checked" : ""}>
      </div>
      <div class="step-content">
        <h4 class="step-title">${escapeHtml(title)}</h4>
        <p class="step-desc">${escapeHtml(description)}</p>
      </div>
    </li>
  `;
}

function updateProgressFromDom() {
  const allChecks = Array.from(document.querySelectorAll(".step-check"));
  const total = allChecks.length;
  const checkedCount = allChecks.filter((b) => b.checked).length;
  const percent = computePercent(checkedCount, total);

  progressBarEl.style.width = `${percent}%`;
  percentEl.textContent = String(percent);
}

async function toggleProgress(stepId, isCompleted) {
  // Teammate endpoint name preserved.
  // Expected to accept: step_id, status (1 or 0)
  await apiFetch("/actions/update_progress.php", {
    method: "POST",
    body: {
      step_id: stepId,
      status: isCompleted ? 1 : 0,
    },
  });
}

(async () => {
  const roadmapId = getQueryParam("id");
  if (!roadmapId) {
    setError("Missing roadmap id.");
    return;
  }

  try {
    const data = await apiFetch(`/actions/fetch_roadmap_details.php?id=${encodeURIComponent(roadmapId)}`);

    const roadmap = data?.roadmap ?? data;
    const steps = data?.steps ?? roadmap?.steps ?? [];

    const title = roadmap?.title ?? "Roadmap";
    const description = roadmap?.description ?? "";
    const creatorName = roadmap?.creator_name ?? roadmap?.author ?? "";

    document.title = `${title} - Coders' Journey`;
    roadmapTitleEl.textContent = title;
    roadmapDescEl.textContent = description;
    roadmapAuthorEl.textContent = creatorName || "—";

    stepsListEl.innerHTML = (Array.isArray(steps) ? steps : []).map(renderStep).join("");

    updateProgressFromDom();

    stepsListEl.addEventListener("change", async (e) => {
      const target = e.target;
      if (!(target instanceof HTMLInputElement)) return;
      if (!target.classList.contains("step-check")) return;

      const stepId = target.dataset.stepId;
      const checked = target.checked;

      updateProgressFromDom();

      try {
        await toggleProgress(stepId, checked);
      } catch (err) {
        // revert if backend rejects
        target.checked = !checked;
        updateProgressFromDom();
        setError(`Could not update progress: ${err.message}`);
      }
    });
  } catch (e) {
    setError(`Backend not connected yet: ${e.message}`);
  }
})();
