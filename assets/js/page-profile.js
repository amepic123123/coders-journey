import { apiFetch, escapeHtml, getQueryParam } from "./api.js";
import { getSession, renderNavAuth } from "./session.js";

await renderNavAuth();

const avatarEl = document.getElementById("profile-avatar");
const usernameEl = document.getElementById("profile-username");
const verifiedEl = document.getElementById("profile-verified");
const joinedEl = document.getElementById("profile-joined");
const reputationEl = document.getElementById("profile-reputation");
const questionsCountEl = document.getElementById("profile-questions-count");
const roadmapsCountEl = document.getElementById("profile-roadmaps-count");

const roadmapsEl = document.getElementById("profile-roadmaps");
const questionsEl = document.getElementById("profile-questions");
const errorEl = document.getElementById("profile-error");

function setError(msg) {
  errorEl.style.display = "block";
  errorEl.textContent = msg;
}

function renderRoadmaps(list) {
  const maps = Array.isArray(list) ? list : [];
  if (!maps.length) return `<p class="text-muted">You are not following any roadmaps yet.</p>`;

  return `
    <ul style="list-style: none; padding: 0; margin-top: 1rem;">
      ${maps
        .map((m) => {
          const id = m?.id ?? m?.roadmap_id;
          const title = m?.title ?? "";
          const percent = Number(m?.percent ?? m?.progress_percent ?? 0);

          return `
            <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 10px 0;">
              <div>
                <a href="roadmap_view.html?id=${encodeURIComponent(id)}" style="font-weight: bold; color: #2c3e50; text-decoration: none;">${escapeHtml(title)}</a>
              </div>
              <div>
                <div class="mini-progress"><div class="mini-fill" style="width: ${escapeHtml(percent)}%;"></div></div>
                <div style="font-size: 0.75rem; text-align: right; color: #666;">${escapeHtml(percent)}%</div>
              </div>
            </li>
          `;
        })
        .join("")}
    </ul>
  `;
}

function renderQuestions(list) {
  const qs = Array.isArray(list) ? list : [];
  if (!qs.length) return `<p class="text-muted">No questions asked yet.</p>`;

  return `
    <ul style="list-style: none; padding: 0; margin-top: 1rem;">
      ${qs
        .map((q) => {
          const id = q?.id ?? q?.question_id;
          const title = q?.title ?? "";
          const votes = q?.votes ?? q?.vote_count ?? 0;

          return `
            <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
              <div style="display: flex; justify-content: space-between;">
                <a href="question.html?id=${encodeURIComponent(id)}" style="color: #4a90e2; text-decoration: none;">${escapeHtml(title)}</a>
                <span style="font-size: 0.9rem; color: #666;">${escapeHtml(votes)} votes</span>
              </div>
            </li>
          `;
        })
        .join("")}
    </ul>
  `;
}

(async () => {
  const session = await getSession();
  if (!session?.logged_in) {
    window.location.href = "login.html?error=must_login";
    return;
  }

  const id = getQueryParam("id");

  try {
    const data = await apiFetch(id ? `/actions/fetch_profile.php?id=${encodeURIComponent(id)}` : "/actions/fetch_profile.php");

    const user = data?.user ?? data;
    const myRoadmaps = data?.my_roadmaps ?? data?.roadmaps ?? [];
    const myQuestions = data?.my_questions ?? data?.questions ?? [];

    const username = user?.username ?? "User";
    const reputation = user?.reputation ?? 0;
    const joined = user?.joined_date ?? user?.created_at ?? "";
    const role = user?.role ?? "user";

    const avatarColor = user?.avatar_color ?? "#f1c40f";

    document.title = `${username}'s Profile`;

    avatarEl.style.backgroundColor = avatarColor;
    avatarEl.textContent = String(username).slice(0, 1).toUpperCase();

    usernameEl.textContent = username;
    verifiedEl.style.display = role === "verified" ? "inline" : "none";

    joinedEl.textContent = joined ? joined : "—";
    reputationEl.textContent = String(reputation);

    questionsCountEl.textContent = String(Array.isArray(myQuestions) ? myQuestions.length : 0);
    roadmapsCountEl.textContent = String(Array.isArray(myRoadmaps) ? myRoadmaps.length : 0);

    roadmapsEl.innerHTML = renderRoadmaps(myRoadmaps);
    questionsEl.innerHTML = renderQuestions(myQuestions);
  } catch (e) {
    setError(`Backend not connected yet: ${e.message}`);
  }
})();
