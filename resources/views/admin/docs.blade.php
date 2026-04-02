@extends('layouts.admin')
@section('title', 'Documentation')
@push('styles')
<style>
.docs-wrap{display:grid;grid-template-columns:220px 1fr;gap:32px;align-items:start}
.docs-toc{position:sticky;top:88px;background:#fff;border-radius:8px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.08);font-size:13px}
.docs-toc h3{font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#888;margin-bottom:10px}
.docs-toc a{display:block;padding:5px 8px;color:#555;text-decoration:none;border-radius:4px;margin-bottom:1px;border-left:2px solid transparent;transition:all .12s}
.docs-toc a:hover{background:#f5f0e8;color:var(--maroon-d)}
.docs-toc a.active{background:#f5f0e8;color:var(--maroon-d);border-left-color:var(--gold);font-weight:700}
.docs-toc .toc-section{margin-top:12px}
.docs-body section{margin-bottom:48px;scroll-margin-top:80px}
.docs-body h2{font-family:'Playfair Display',serif;font-size:22px;color:var(--maroon-d);border-bottom:2px solid var(--cream);padding-bottom:10px;margin-bottom:20px}
.docs-body h3{font-size:15px;font-weight:700;color:#333;margin:24px 0 10px}
.docs-body h4{font-size:13px;font-weight:700;color:#555;margin:16px 0 6px;text-transform:uppercase;letter-spacing:.04em}
.docs-body p{font-size:14px;color:#444;line-height:1.75;margin-bottom:12px}
.docs-body ul,
.docs-body ol{font-size:14px;color:#444;line-height:1.75;margin-bottom:12px;padding-left:22px}
.docs-body li{margin-bottom:5px}
.docs-body code{background:#f0f0f0;padding:2px 7px;border-radius:3px;font-family:monospace;font-size:12px;color:#c7254e}
.docs-body pre{background:#f8f8f8;border:1px solid #e8e8e8;border-radius:6px;padding:14px 16px;font-family:monospace;font-size:12px;line-height:1.6;overflow-x:auto;margin-bottom:14px}
.callout{border-radius:6px;padding:14px 16px;margin:14px 0;font-size:13px;line-height:1.6}
.callout-info{background:#e8f4fd;border-left:4px solid #3498db;color:#1a5276}
.callout-warn{background:#fef9e7;border-left:4px solid #f39c12;color:#784212}
.callout-tip {background:#e9f7ef;border-left:4px solid #27ae60;color:#1a5e3a}
.type-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px;margin:16px 0}
.type-card{background:#fafafa;border:1px solid #e8e8e8;border-radius:7px;padding:14px 16px}
.type-card .type-name{font-weight:700;font-size:13px;color:var(--maroon-d);margin-bottom:4px}
.type-card .type-badge{display:inline-block;background:#eee;color:#555;font-size:10px;font-family:monospace;padding:1px 6px;border-radius:3px;margin-bottom:6px}
.type-card p{font-size:12px;color:#666;margin:0;line-height:1.55}
.step-list{counter-reset:steps;list-style:none;padding:0}
.step-list li{counter-increment:steps;display:flex;gap:14px;margin-bottom:16px;align-items:flex-start}
.step-list li::before{content:counter(steps);background:var(--maroon-d);color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;margin-top:2px}
.stg-table{width:100%;border-collapse:collapse;font-size:12px;margin:12px 0}
.stg-table th{background:#550D0E;color:#fff;padding:8px 12px;text-align:left}
.stg-table td{padding:8px 12px;border-bottom:1px solid #eee}
.stg-table tr:nth-child(even) td{background:#fafafa}
.stg-table code{background:#e8e8e8;padding:1px 5px;border-radius:2px}
.role-compare{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin:16px 0}
.role-box{border-radius:7px;padding:16px;border:1px solid #e8e8e8}
.role-box h4{margin-top:0;font-size:13px}
.role-box ul{margin-bottom:0}
.badge-doc{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600}
</style>
@endpush
@section('content')

<div class="docs-wrap">

  {{-- Table of Contents --}}
  <aside class="docs-toc" id="toc">
    <h3>Contents</h3>
    <a href="#overview">Overview</a>
    <a href="#roles">Roles & Access</a>
    <div class="toc-section">
      <a href="#surveys">Surveys</a>
      <a href="#lifecycle" style="padding-left:20px;font-size:12px">→ Lifecycle</a>
      <a href="#creating" style="padding-left:20px;font-size:12px">→ Creating</a>
    </div>
    <div class="toc-section">
      <a href="#builder">Question Builder</a>
      <a href="#question-types" style="padding-left:20px;font-size:12px">→ Question Types</a>
      <a href="#skip-logic" style="padding-left:20px;font-size:12px">→ Skip Logic</a>
    </div>
    <a href="#sharing">Sharing Surveys</a>
    <a href="#taking">Taking a Survey</a>
    <a href="#offline">Offline Mode</a>
    <a href="#responses">Viewing Responses</a>
    <a href="#export">STG Export</a>
    <a href="#users">User Management</a>
    <a href="#credentials">Default Credentials</a>
  </aside>

  {{-- Documentation Body --}}
  <div class="docs-body">

    {{-- OVERVIEW --}}
    <section id="overview">
      <h2>Overview</h2>
      <p><strong>SurveySays</strong> is a web-based survey data collection platform (CAWI — Computer-Assisted Web Interviewing) designed for field research. It supports building structured questionnaires, collecting responses online or offline, and exporting data in a format compatible with <strong>Survey to Go (STG)</strong>.</p>
      <p>Key capabilities:</p>
      <ul>
        <li>Build surveys with 9 question types including PH location cascading dropdowns</li>
        <li>Activate surveys and share a public URL with respondents</li>
        <li>Collect responses offline — saves to the device and syncs when back online</li>
        <li>View and export responses as STG-compatible CSV</li>
        <li>Skip/branching logic to show/hide questions based on answers</li>
      </ul>
    </section>

    {{-- ROLES --}}
    <section id="roles">
      <h2>Roles &amp; Access</h2>
      <p>There are two roles in SurveySays:</p>
      <div class="role-compare">
        <div class="role-box">
          <h4><span class="badge-doc" style="background:#f0e6ff;color:#5a2d91">Admin</span></h4>
          <ul>
            <li>All Researcher capabilities</li>
            <li>View and manage <strong>all</strong> surveys (not just their own)</li>
            <li>Create, edit, deactivate user accounts</li>
            <li>Change user roles</li>
          </ul>
        </div>
        <div class="role-box">
          <h4><span class="badge-doc" style="background:#e6f3ff;color:#1a5f9e">Researcher</span></h4>
          <ul>
            <li>Create and manage their own surveys</li>
            <li>Build questions, set skip logic</li>
            <li>Activate, close, and duplicate surveys</li>
            <li>View and export responses</li>
          </ul>
        </div>
      </div>
    </section>

    {{-- SURVEYS --}}
    <section id="surveys">
      <h2>Surveys</h2>

      <h3 id="lifecycle">Survey Lifecycle</h3>
      <p>Every survey passes through three states:</p>
      <ul>
        <li><span class="badge badge-draft">Draft</span> — being built; the public URL is not yet accepting responses.</li>
        <li><span class="badge badge-active">Active</span> — open to respondents via the public URL.</li>
        <li><span class="badge badge-closed">Closed</span> — no longer accepting responses; data is preserved.</li>
      </ul>
      <div class="callout callout-warn">
        <strong>Note:</strong> Once a survey is activated, questions cannot be deleted. Close the survey first, then duplicate it if you need to revise the question set.
      </div>

      <h3 id="creating">Creating a Survey</h3>
      <ol class="step-list">
        <li>Go to <strong>My Surveys → New Survey</strong>.</li>
        <li>Enter a <strong>Title</strong> and optional description, date window, and settings.</li>
        <li>Click <strong>Create Survey</strong>. You will be taken to the survey detail page.</li>
        <li>Click <strong>Open Builder</strong> to add questions.</li>
        <li>When ready, click <strong>Activate Survey</strong> to open it to respondents.</li>
      </ol>

      <h4>Survey Settings</h4>
      <table class="stg-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
          <tr><td><strong>Title</strong></td><td>Shown on the survey header and thank-you page.</td></tr>
          <tr><td><strong>Description</strong></td><td>Internal note — not shown to respondents.</td></tr>
          <tr><td><strong>Start / End Date</strong></td><td>Optional window. If set, the survey auto-closes after the end date.</td></tr>
          <tr><td><strong>Show Progress Bar</strong></td><td>Displays a gold progress bar at the top of the survey form.</td></tr>
          <tr><td><strong>Allow Partial Save</strong></td><td>Lets respondents resume a partially filled survey in the same browser session.</td></tr>
        </tbody>
      </table>

      <h3>Duplicating a Survey</h3>
      <p>From the survey detail page, click <strong>Duplicate</strong>. A new draft copy is created with all questions and options — but no responses. Useful for creating a new wave of data collection from an existing instrument.</p>
    </section>

    {{-- BUILDER --}}
    <section id="builder">
      <h2>Question Builder</h2>
      <p>The builder is the right-hand panel on the <strong>Builder</strong> page. Fill in the fields and click <strong>Save Question</strong>. Questions appear in the left list and can be reordered by dragging the <code>⣿</code> handle.</p>

      <h4>Common Fields</h4>
      <table class="stg-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
          <tr><td><strong>Variable Code</strong></td><td>The STG column header (e.g. <code>PROVINCE</code>, <code>Q1</code>). Must be unique within the survey. Only letters, numbers, underscores, and hyphens.</td></tr>
          <tr><td><strong>Question Label</strong></td><td>The question text shown to the respondent.</td></tr>
          <tr><td><strong>Type</strong></td><td>See question types below.</td></tr>
          <tr><td><strong>Required?</strong></td><td>Whether the respondent must answer before submitting.</td></tr>
          <tr><td><strong>Help Text</strong></td><td>Optional sub-text shown below the question label in smaller grey text.</td></tr>
        </tbody>
      </table>

      <h3 id="question-types">Question Types</h3>
      <div class="type-grid">
        <div class="type-card">
          <div class="type-name">Single Choice</div>
          <div class="type-badge">single_choice</div>
          <p>Radio buttons. Respondent picks exactly one option. Add options with a <strong>Code</strong> (e.g. <code>Y</code>) and a <strong>Label</strong> (e.g. <code>Yes</code>). The code is what gets exported to STG.</p>
        </div>
        <div class="type-card">
          <div class="type-name">Multi Select</div>
          <div class="type-badge">multi_select</div>
          <p>Checkboxes. Multiple options can be selected. In the STG export, expands to one column per option (<code>{CODE}_{OPTION_CODE}</code>) with values <code>1</code> or <code>0</code>.</p>
        </div>
        <div class="type-card">
          <div class="type-name">Open Text</div>
          <div class="type-badge">open_text</div>
          <p>Free-text textarea. No options needed. Exported as a single text column.</p>
        </div>
        <div class="type-card">
          <div class="type-name">Rating Scale</div>
          <div class="type-badge">rating</div>
          <p>Clickable numbered buttons (e.g. 1–5 or 1–10). Add scale points as options. Exported as the selected number.</p>
        </div>
        <div class="type-card">
          <div class="type-name">Number</div>
          <div class="type-badge">number</div>
          <p>Numeric input. Set min/max via the config. Exported as a single number column.</p>
        </div>
        <div class="type-card">
          <div class="type-name">Date</div>
          <div class="type-badge">date</div>
          <p>Date picker (<code>YYYY-MM-DD</code>). Exported as a date string.</p>
        </div>
        <div class="type-card">
          <div class="type-name">Time</div>
          <div class="type-badge">time</div>
          <p>Time picker (<code>HH:MM</code>). Exported as a time string.</p>
        </div>
        <div class="type-card">
          <div class="type-name">Grid / Checklist</div>
          <div class="type-badge">grid</div>
          <p>Matrix table. Add <strong>Columns</strong> (options) and <strong>Rows</strong>. Each cell is a free-text input. In STG export, expands to <code>{CODE}_{ROW_CODE}_{COL_CODE}</code> columns.</p>
        </div>
        <div class="type-card">
          <div class="type-name">PH Location</div>
          <div class="type-badge">ph_location</div>
          <p>3 cascading dropdowns: Province → City/Municipality → Barangay. No options needed — data comes from the built-in PSGC database (83 provinces, 1,624 cities). In STG export: <code>{CODE}</code>, <code>{CODE}_CITY</code>, <code>{CODE}_BRGY</code>.</p>
        </div>
      </div>

      <h3 id="skip-logic">Skip Logic</h3>
      <p>Skip logic hides a range of questions when a condition is met. It is configured directly in the database or via a future builder UI. Currently supported operators:</p>
      <ul>
        <li><code>equals</code> — answer equals a specific value</li>
        <li><code>not_equals</code> — answer does not equal a value</li>
        <li><code>selected</code> — an option is selected (multi-select)</li>
        <li><code>contains</code> — answer text contains a substring</li>
      </ul>
      <div class="callout callout-info">
        <strong>How it works:</strong> When the <em>source question</em> matches the condition, all questions between it and the <em>target question</em> (exclusive) are hidden. The logic runs in real-time as the respondent answers — no page reload required.
      </div>

      <p><strong>Example:</strong> If <em>Q1 "Was the child ever breastfed?"</em> = <code>No</code>, skip directly to <em>Q5 "Did the child drink from a bottle?"</em> — hiding Q2, Q3, Q4.</p>
    </section>

    {{-- SHARING --}}
    <section id="sharing">
      <h2>Sharing Surveys</h2>
      <p>Each survey has a unique, permanent public URL. Find it on the <strong>Survey Detail</strong> page:</p>
      <pre>https://surveysays.webprvw.xyz/s/{public_token}</pre>
      <p>Share this link with respondents or field interviewers. The URL only works when the survey status is <strong>Active</strong>. If the survey is Draft or Closed, visitors will see a <em>"Survey Not Available"</em> page.</p>
      <div class="callout callout-tip">
        <strong>Tip:</strong> The public token never changes — you can print QR codes or share the link before activating, and activate later when ready to collect data.
      </div>
    </section>

    {{-- TAKING --}}
    <section id="taking">
      <h2>Taking a Survey</h2>
      <p>Respondents open the public URL in any modern browser (no login required). The survey form shows all questions on a single scrollable page.</p>
      <ul>
        <li>Required questions are marked with a red asterisk <span style="color:#dc3545">*</span>.</li>
        <li>Skip logic hides irrelevant questions automatically as answers are entered.</li>
        <li>The gold progress bar (if enabled) shows how many questions are currently visible.</li>
        <li>Clicking <strong>Submit Survey</strong> sends the response and shows a thank-you page.</li>
      </ul>
      <h4>Partial Save &amp; Resume</h4>
      <p>If <em>Allow Partial Save</em> is enabled, the respondent's progress is stored in their browser session. If they close the browser and reopen the same URL within the session, their answers are pre-filled.</p>
    </section>

    {{-- OFFLINE --}}
    <section id="offline">
      <h2>Offline Mode (PWA)</h2>
      <p>SurveySays works as a <strong>Progressive Web App</strong>. Once a survey page is loaded while online, it is cached on the device and can be used in areas without internet access.</p>

      <h3>How to use offline</h3>
      <ol class="step-list">
        <li><strong>Load the survey while online</strong> at least once. The service worker caches the page and all PSGC location data automatically.</li>
        <li><strong>Go offline</strong> (no WiFi or mobile data). Open the same survey URL.</li>
        <li>An amber banner appears: <em>"You are offline — answers will be saved to this device."</em></li>
        <li>The <strong>Submit Survey</strong> button changes to <strong>Save Offline</strong>.</li>
        <li>Fill in the survey and tap <strong>Save Offline</strong>. The response is stored in the browser's <strong>IndexedDB</strong> on the device.</li>
        <li>A "Saved Offline" confirmation page appears with a pending queue count.</li>
      </ol>

      <h3>Syncing offline responses</h3>
      <p>When internet is restored:</p>
      <ul>
        <li><strong>Automatic sync:</strong> The app detects the connection and uploads all pending responses automatically.</li>
        <li><strong>Manual sync:</strong> On the confirmation page, tap <strong>Upload Now</strong> to force an immediate upload.</li>
        <li>A blue banner shows how many responses are pending: <em>"X responses saved offline, pending upload."</em></li>
        <li>Each response is deduplicated server-side — uploading twice never creates a duplicate record.</li>
      </ul>

      <div class="callout callout-warn">
        <strong>Important:</strong> Offline responses are stored <em>in the browser</em> on the specific device used. If the browser data is cleared or a different browser/device is used, pending responses will be lost. Always sync as soon as connectivity is available.
      </div>

      <h3>Installing as an app (optional)</h3>
      <p>On Android Chrome and compatible browsers, you will see an <em>"Add to Home Screen"</em> prompt. Installing the app gives a full-screen experience and faster launch — useful for field interviewers who use the same survey daily.</p>
    </section>

    {{-- RESPONSES --}}
    <section id="responses">
      <h2>Viewing Responses</h2>
      <p>From the survey detail page, click <strong>View Responses</strong>. The responses list shows:</p>
      <ul>
        <li><strong>Serial</strong> — unique ID in the format <code>S{survey_id}-{000001}</code></li>
        <li><strong>Status</strong> — <span class="badge badge-complete">Complete</span> or <span class="badge badge-partial">Partial</span></li>
        <li><strong>Started At</strong> — when the form was first opened</li>
        <li><strong>Duration</strong> — time between opening and submitting</li>
      </ul>
      <p>Click a serial number to view the full individual response with all answers.</p>
      <div class="callout callout-info">
        Responses synced from offline devices are stored identically to online submissions and are included in all exports.
      </div>
    </section>

    {{-- STG EXPORT --}}
    <section id="export">
      <h2>STG Export</h2>
      <p>Go to <strong>Survey Detail → Export Data</strong>. You can filter by status and date range, then click <strong>Download CSV</strong>.</p>

      <h3>Fixed Columns (always present)</h3>
      <table class="stg-table">
        <thead><tr><th>Column</th><th>Content</th></tr></thead>
        <tbody>
          <tr><td><code>Serial</code></td><td>Response serial number (e.g. S1-000001)</td></tr>
          <tr><td><code>Status</code></td><td>1 = Complete, 2 = Partial</td></tr>
          <tr><td><code>IntDate</code></td><td>Date the survey was started (YYYY-MM-DD)</td></tr>
          <tr><td><code>StartTime</code></td><td>Time started (HH:MM:SS)</td></tr>
          <tr><td><code>EndTime</code></td><td>Time completed (HH:MM:SS)</td></tr>
          <tr><td><code>Duration</code></td><td>Duration in seconds</td></tr>
          <tr><td><code>InterviewerId</code></td><td>Empty (not captured in web mode)</td></tr>
          <tr><td><code>InterviewerName</code></td><td>Empty (not captured in web mode)</td></tr>
          <tr><td><code>GPSLat</code></td><td>Empty (not captured in web mode)</td></tr>
          <tr><td><code>GPSLon</code></td><td>Empty (not captured in web mode)</td></tr>
        </tbody>
      </table>

      <h3>Dynamic Columns (per question)</h3>
      <table class="stg-table">
        <thead><tr><th>Question Type</th><th>Column(s) Generated</th><th>Values</th></tr></thead>
        <tbody>
          <tr><td>Single Choice</td><td><code>{variable_code}</code></td><td>The selected option's code</td></tr>
          <tr><td>Open Text, Number, Date, Time</td><td><code>{variable_code}</code></td><td>The raw text/value entered</td></tr>
          <tr><td>Rating Scale</td><td><code>{variable_code}</code></td><td>Selected number</td></tr>
          <tr><td>Multi Select</td><td><code>{variable_code}_{OPTION_CODE}</code> <em>× options</em></td><td><code>1</code> if selected, <code>0</code> if not</td></tr>
          <tr><td>Grid / Checklist</td><td><code>{variable_code}_{ROW_CODE}_{COL_CODE}</code> <em>× rows × cols</em></td><td>Cell value entered</td></tr>
          <tr><td>PH Location</td><td><code>{variable_code}</code>, <code>{variable_code}_CITY</code>, <code>{variable_code}_BRGY</code></td><td>Province name, City name, Barangay name</td></tr>
        </tbody>
      </table>

      <h4>Example CSV</h4>
      <pre>Serial,Status,IntDate,...,PROVINCE,PROVINCE_CITY,PROVINCE_BRGY,Q1,Q1_A,Q1_B
S1-000001,1,2026-04-01,...,Abra,Bangued,Poblacion,Yes,1,0</pre>

      <div class="callout callout-tip">
        <strong>Merging with STG:</strong> Because the column structure mirrors STG's flat export format, you can paste SurveySays CSV rows directly beneath STG export rows in Excel, as long as the same survey instrument was used and the <em>variable codes match</em>.
      </div>
    </section>

    {{-- USERS --}}
    <section id="users">
      <h2>User Management <span class="badge badge-admin" style="vertical-align:middle;font-size:11px">Admin only</span></h2>
      <p>Go to <strong>Admin → Users</strong> to manage accounts.</p>
      <ul>
        <li><strong>Create User</strong> — set name, email, password, role, and active status.</li>
        <li><strong>Edit User</strong> — update any field. Leave the password blank to keep the existing one.</li>
        <li><strong>Deactivate</strong> — uncheck <em>"Account is active"</em>. The user can no longer log in but their surveys and data are preserved.</li>
      </ul>
      <div class="callout callout-warn">
        There is currently no self-service password reset. An admin must reset passwords manually via the Edit User form.
      </div>
    </section>

    {{-- CREDENTIALS --}}
    <section id="credentials">
      <h2>Default Credentials</h2>
      <div class="callout callout-warn">
        <strong>Change these immediately</strong> after your first login. Default credentials are seeded for initial setup only.
      </div>
      <table class="stg-table">
        <thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
        <tbody>
          <tr><td><span class="badge badge-admin">Admin</span></td><td><code>admin@surveysays.test</code></td><td><code>Admin@1234!</code></td></tr>
          <tr><td><span class="badge badge-researcher">Researcher</span></td><td><code>researcher@surveysays.test</code></td><td><code>Admin@1234!</code></td></tr>
        </tbody>
      </table>
      <p>To change your password: log in → ask an Admin to update it via <strong>Admin → Users → Edit</strong>.</p>
    </section>

  </div>{{-- /.docs-body --}}
</div>{{-- /.docs-wrap --}}

<script>
// Highlight active TOC item on scroll
const sections = document.querySelectorAll('.docs-body section[id]');
const tocLinks  = document.querySelectorAll('.docs-toc a[href^="#"]');
const observer  = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      tocLinks.forEach(a => a.classList.remove('active'));
      const link = document.querySelector(`.docs-toc a[href="#${entry.target.id}"]`);
      if (link) link.classList.add('active');
    }
  });
}, { rootMargin: '-80px 0px -70% 0px' });
sections.forEach(s => observer.observe(s));
</script>
@endsection
