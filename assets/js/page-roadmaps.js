import { apiFetch, escapeHtml } from "./api.js";
import { getSession, renderNavAuth } from "./session.js";

await renderNavAuth();

const grid = document.getElementById("roadmaps-grid");
const emptyEl = document.getElementById("roadmaps-empty");

const genForm = document.getElementById("gen-form");
const genGoal = document.getElementById("gen-goal");
const genBtn = document.getElementById("gen-btn");
const genError = document.getElementById("gen-error");

function setGenError(msg) {
  genError.style.display = "block";
  genError.textContent = msg;
}

function clearGenError() {
  genError.style.display = "none";
  genError.textContent = "";
}

function badgeClass(diff) {
  if (diff === "Intermediate") return "badge-intermediate";
  if (diff === "Advanced") return "badge-advanced";
  return "badge-beginner";
}

function renderRoadmap(map) {
  const id = map?.id ?? map?.roadmap_id;
  const title = map?.title ?? "";
  const description = map?.description ?? "";
  const difficulty = map?.difficulty ?? "Beginner";
  const stepsCount = map?.steps_count ?? map?.total_steps ?? "—";
  const creatorName = map?.creator_name ?? "";
  const isVerified = map?.is_verified ?? false;

  return `
    <div class="roadmap-card">
      <div style="margin-bottom:10px;">
        <span class="badge ${badgeClass(difficulty)}">${escapeHtml(difficulty)}</span>
      </div>

      <h3 style="font-size: 1.2rem; margin-bottom:10px;">
        <a href="roadmap_view.html?id=${encodeURIComponent(id)}" style="color:#2c3e50; text-decoration:none;">${escapeHtml(title)}</a>
      </h3>

      <p style="font-size:0.9rem; color:#555; line-height:1.5; flex-grow:1;">${escapeHtml(description)}</p>

      <div style="margin-top:15px; border-top:1px solid #eee; padding-top:10px;">
        <div class="steps-count">📚 <strong>${escapeHtml(stepsCount)}</strong> Steps</div>
        <div style="font-size:0.85rem; color:#888; margin-top:5px;">
          ${creatorName ? `Created by <strong>${escapeHtml(creatorName)}</strong>` : ""}
          ${isVerified ? " <span title=\"Verified Author\">✅</span>" : ""}
        </div>
      </div>

      <a href="roadmap_view.html?id=${encodeURIComponent(id)}" class="btn btn-primary" style="margin-top:15px; text-align:center; text-decoration:none;">
        Start Journey
      </a>
    </div>
  `;
}

(async () => {
  try {
    const session = await getSession();

    // Generation requires login (backend route requiresLogin())
    if (!session?.logged_in) {
      genBtn.disabled = true;
      genGoal.disabled = true;
      genGoal.placeholder = "Log in to generate a roadmap";
    } else {
      genForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        clearGenError();

        const goal = String(genGoal.value ?? "").trim();
        if (!goal) return;

        genBtn.disabled = true;
        genBtn.textContent = "Generating…";

        try {
          // Use the existing app backend endpoint (not the /actions shims).
          const res = await apiFetch("/api/roadmaps/recommend", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ goal }),
          });

          const roadmapId = res?.roadmap_id ?? res?.roadmap?.roadmap_id;
          if (!roadmapId) {
            throw new Error("Roadmap generated but id missing.");
          }

          window.location.href = `roadmap_view.html?id=${encodeURIComponent(roadmapId)}`;
        } catch (err) {
          setGenError(err.message);
        } finally {
          genBtn.disabled = false;
          genBtn.textContent = "Generate";
        }
      });
    }

    const data = await apiFetch("/actions/fetch_roadmaps.php");
    const roadmaps = Array.isArray(data) ? data : (data?.roadmaps ?? []);

    if (!roadmaps.length) {
      emptyEl.style.display = "block";
      grid.innerHTML = "";
      return;
    }

    grid.innerHTML = roadmaps.map(renderRoadmap).join("");
  } catch (e) {
    emptyEl.style.display = "block";
    grid.innerHTML = `<div class="card" style="padding:16px;">Backend not connected yet: ${escapeHtml(e.message)}</div>`;
  }
})();
