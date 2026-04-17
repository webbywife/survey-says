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
.stg-table td{padding:8px 12px;border-bottom:1px solid #eee;vertical-align:top}
.stg-table tr:nth-child(even) td{background:#fafafa}
.stg-table code{background:#e8e8e8;padding:1px 5px;border-radius:2px}
.role-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin:16px 0}
.role-box{border-radius:7px;padding:14px 16px;border:1px solid #e8e8e8}
.role-box h4{margin-top:0;font-size:13px;margin-bottom:8px}
.role-box ul{margin-bottom:0;font-size:12px;padding-left:16px}
.role-box li{margin-bottom:4px;color:#444}
.badge-doc{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600}
.perm-yes{color:#155724;font-weight:700}
.perm-no{color:#ccc}
</style>
@endpush
@section('content')

<div class="docs-wrap">

  {{-- Table of Contents --}}
  <aside class="docs-toc" id="toc">
    <h3>Contents</h3>
    <a href="#overview">Overview</a>
    <a href="#roles">Roles &amp; Permissions</a>
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
    <a href="#collaborators">Collaborators</a>
    <a href="#taking">Taking a Survey</a>
    <a href="#offline">Offline Mode</a>
    <a href="#responses">Viewing Responses</a>
    <a href="#editing">Editing Responses</a>
    <a href="#check-approve">Check &amp; Approve</a>
    <a href="#analytics">Analytics</a>
    <a href="#import">Importing Responses</a>
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
        <li>View, edit, and export responses as STG-compatible CSV</li>
        <li>Import responses from STG-format CSV files</li>
        <li>Skip/branching logic to show/hide questions based on answers</li>
        <li>Response analytics with charts and numeric summaries</li>
        <li>Supervisor check and study leader approval workflow</li>
        <li>Role-based access: six roles control who can do what</li>
      </ul>
    </section>

    {{-- ROLES & PERMISSIONS --}}
    <section id="roles">
      <h2>Roles &amp; Permissions</h2>
      <p>Every user account is assigned one of six roles. Roles control which actions a user can perform across the system.</p>

      <div class="role-grid">
        <div class="role-box">
          <h4><span class="badge-doc" style="background:#f0e6ff;color:#5a2d91">Admin</span></h4>
          <ul>
            <li>All capabilities of every role</li>
            <li>View &amp; manage <strong>all</strong> surveys</li>
            <li>Create, edit, deactivate users</li>
            <li>Delete responses</li>
            <li>Check &amp; Approve responses</li>
          </ul>
        </div>
        <div class="role-box">
          <h4><span class="badge-doc" style="background:#e6f3ff;color:#1a5f9e">Researcher</span></h4>
          <ul>
            <li>Create and manage their own surveys</li>
            <li>Build questions &amp; skip logic</li>
            <li>Activate, close, duplicate surveys</li>
            <li>View, edit &amp; export responses</li>
            <li>Import responses via CSV</li>
            <li>Add collaborators</li>
          </ul>
        </div>
        <div class="role-box">
          <h4><span class="badge-doc" style="background:#fde8e8;color:#7a1c1c">Interviewer</span></h4>
          <ul>
            <li>Access surveys they are added to as a collaborator</li>
            <li>View &amp; edit responses</li>
            <li>Export responses</li>
            <li>Import responses via CSV</li>
          </ul>
        </div>
        <div class="role-box">
          <h4><span class="badge-doc" style="background:#e8f8f0;color:#155724">Supervisor</span></h4>
          <ul>
            <li>All Interviewer capabilities</li>
            <li><strong>Mark responses as Checked</strong></li>
          </ul>
        </div>
        <div class="role-box">
          <h4><span class="badge-doc" style="background:#fdf6e8;color:#7a5c00">Study Leader</span></h4>
          <ul>
            <li>All Supervisor capabilities</li>
            <li><strong>Approve responses</strong></li>
            <li>Edit responses</li>
          </ul>
        </div>
        <div class="role-box" style="border-style:dashed">
          <h4><span class="badge-doc" style="background:#f0f0f0;color:#555">Collaborator</span></h4>
          <ul>
            <li>Not a role — a per-survey grant</li>
            <li>Added by the survey owner</li>
            <li>Can view &amp; edit responses</li>
            <li>Cannot access the Question Builder</li>
          </ul>
        </div>
      </div>

      <h3>Permissions Reference Table</h3>
      <table class="stg-table">
        <thead>
          <tr>
            <th>Permission</th>
            <th>Admin</th>
            <th>Researcher</th>
            <th>Study Leader</th>
            <th>Supervisor</th>
            <th>Interviewer</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Manage users</td><td class="perm-yes">✓</td><td class="perm-no">—</td><td class="perm-no">—</td><td class="perm-no">—</td><td class="perm-no">—</td></tr>
          <tr><td>Create surveys</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-no">—</td><td class="perm-no">—</td><td class="perm-no">—</td></tr>
          <tr><td>Build questions</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-no">—</td><td class="perm-no">—</td><td class="perm-no">—</td></tr>
          <tr><td>View responses</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td></tr>
          <tr><td>Edit responses</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td></tr>
          <tr><td>Import responses</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td></tr>
          <tr><td>Export responses</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td></tr>
          <tr><td>Mark as Checked</td><td class="perm-yes">✓</td><td class="perm-no">—</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-no">—</td></tr>
          <tr><td>Approve responses</td><td class="perm-yes">✓</td><td class="perm-no">—</td><td class="perm-yes">✓</td><td class="perm-no">—</td><td class="perm-no">—</td></tr>
          <tr><td>Delete responses</td><td class="perm-yes">✓</td><td class="perm-no">—</td><td class="perm-no">—</td><td class="perm-no">—</td><td class="perm-no">—</td></tr>
          <tr><td>Add collaborators</td><td class="perm-yes">✓</td><td class="perm-yes">✓ (own surveys)</td><td class="perm-no">—</td><td class="perm-no">—</td><td class="perm-no">—</td></tr>
          <tr><td>View analytics</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td><td class="perm-yes">✓</td></tr>
        </tbody>
      </table>
      <div class="callout callout-info">
        <strong>Collaborator access:</strong> When a user is added as a collaborator to a specific survey, they gain Interviewer-level access to that survey — even if their system role (e.g. Supervisor or Study Leader) would normally grant more. The survey owner or an Admin adds collaborators from the Survey Detail page.
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
        <li>Click <strong>Question Builder</strong> to add questions.</li>
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
      <p>From the survey detail page, click <strong>Duplicate Survey</strong>. A new draft copy is created with all questions and options — but no responses. Useful for creating a new wave of data collection from an existing instrument.</p>
    </section>

    {{-- BUILDER --}}
    <section id="builder">
      <h2>Question Builder</h2>
      <p>Click <strong>Question Builder</strong> from the survey detail page. Fill in the fields in the right-hand panel and click <strong>Save Question</strong>. Questions appear in the left list and can be reordered by dragging the <code>⣿</code> handle.</p>

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
          <p>3 cascading dropdowns: Province → City/Municipality → Barangay. No options needed — data comes from the built-in PSGC database. In STG export: <code>{CODE}</code>, <code>{CODE}_CITY</code>, <code>{CODE}_BRGY</code>.</p>
        </div>
      </div>

      <h3 id="skip-logic">Skip Logic</h3>
      <p>Skip logic hides a range of questions when a condition is met. Currently supported operators:</p>
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

    {{-- COLLABORATORS --}}
    <section id="collaborators">
      <h2>Collaborators</h2>
      <p>Collaborators are users who have been granted access to a specific survey. This lets you share data collection and response management with interviewers, supervisors, or study leaders without giving them access to all surveys in the system.</p>

      <h3>How to add a collaborator</h3>
      <ol class="step-list">
        <li>Open the <strong>Survey Detail</strong> page of the survey you want to share.</li>
        <li>Scroll down to the <strong>Collaborators</strong> section (only visible to the survey owner and admins).</li>
        <li>Enter the collaborator's <strong>email address</strong> and click <strong>Add Collaborator</strong>.</li>
        <li>The user must already have an account in the system. If not, create their account first under <strong>Admin → Users</strong>.</li>
      </ol>

      <h3>How to remove a collaborator</h3>
      <p>In the Collaborators section, click the <strong>Remove</strong> button next to the user's name. They will immediately lose access to that survey.</p>

      <div class="callout callout-info">
        <strong>What collaborators can do:</strong> Collaborators can view responses, edit responses, export data, and import CSV responses for that survey. They cannot access the Question Builder or change survey settings. Their capabilities also depend on their system role — a Study Leader collaborator can also check and approve responses.
      </div>
    </section>

    {{-- TAKING --}}
    <section id="taking">
      <h2>Taking a Survey</h2>
      <p>Respondents open the public URL in any modern browser — no login required. The survey form shows all questions on a single scrollable page.</p>
      <ul>
        <li>Required questions are marked with a red asterisk <span style="color:#dc3545">*</span>.</li>
        <li>Skip logic hides irrelevant questions automatically as answers are entered.</li>
        <li>The gold progress bar (if enabled) shows how many questions are currently visible.</li>
        <li>Clicking <strong>Submit Survey</strong> sends the response and shows a thank-you page.</li>
      </ul>
      <div class="callout callout-tip">
        <strong>Tip:</strong> The public token never changes — you can print QR codes or share the link before activating, and activate later when ready to collect data.
      </div>

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

    {{-- VIEWING RESPONSES --}}
    <section id="responses">
      <h2>Viewing Responses</h2>
      <p>From the survey detail page, click <strong>View Responses</strong>. The responses table shows:</p>
      <ul>
        <li><strong>Serial</strong> — unique ID in the format <code>S{survey_id}-{000001}</code></li>
        <li><strong>Name</strong> — the respondent's name, pulled automatically from the first question whose variable code or label contains "NAME"</li>
        <li><strong>Status</strong> — <span class="badge badge-complete">Complete</span> or <span class="badge badge-partial">Partial</span></li>
        <li><strong>Started At</strong> — when the form was first opened</li>
        <li><strong>Completed At</strong> — time of submission</li>
        <li><strong>Duration</strong> — time between opening and submitting (mm:ss)</li>
        <li><strong>IP Address</strong> — the respondent's IP</li>
      </ul>
      <p>Click <strong>View</strong> next to any response to see all answers in detail. The action buttons visible depend on your role:</p>
      <ul>
        <li><strong>View</strong> — available to all authorized users</li>
        <li><strong>Edit</strong> — Admins, Researchers, Interviewers, Supervisors, Study Leaders</li>
        <li><strong>✓ Check / Checked</strong> — Admins, Supervisors, Study Leaders</li>
        <li><strong>★ Approve / Approved</strong> — Admins, Study Leaders</li>
        <li><strong>× Delete</strong> — Admins only</li>
      </ul>
      <div class="callout callout-info">
        Responses synced from offline devices appear identically to online submissions and are included in all exports.
      </div>
    </section>

    {{-- EDITING RESPONSES --}}
    <section id="editing">
      <h2>Editing Responses</h2>
      <p>Authorized users can correct or complete any response after it has been submitted. This is useful for fixing data entry errors, filling in missing answers, or updating information collected in the field.</p>

      <h3>How to edit a response</h3>
      <ol class="step-list">
        <li>Go to <strong>Survey Detail → View Responses</strong>.</li>
        <li>Find the response you want to edit. Click the <strong>Edit</strong> button in its row.</li>
        <li>The edit form shows every question in the survey with the existing answer pre-filled.</li>
        <li>Update any answers as needed — text fields, dropdowns, checkboxes, and grid cells are all editable.</li>
        <li>Click <strong>Save Changes</strong>. You will be taken back to the response detail view.</li>
      </ol>

      <div class="callout callout-warn">
        <strong>Who can edit:</strong> Admins, Researchers, Interviewers, Supervisors, and Study Leaders who have access to the survey. The <strong>Edit</strong> button is hidden if your role does not permit editing.
      </div>
      <div class="callout callout-tip">
        <strong>Tip:</strong> Editing a response does not change its serial number, timestamp, or status. It only updates the stored answers. If a response was imported via CSV, it can also be edited here.
      </div>
    </section>

    {{-- CHECK & APPROVE --}}
    <section id="check-approve">
      <h2>Check &amp; Approve Workflow</h2>
      <p>SurveySays supports a two-step quality control workflow. Supervisors review and mark responses as <strong>Checked</strong>. Study Leaders (or Admins) then give final <strong>Approval</strong>. This mirrors standard field data quality procedures.</p>

      <h3>How it works</h3>
      <table class="stg-table">
        <thead><tr><th>Action</th><th>Who can do it</th><th>What it does</th></tr></thead>
        <tbody>
          <tr>
            <td><strong>✓ Check</strong></td>
            <td>Admin, Supervisor, Study Leader</td>
            <td>Marks the response as reviewed. Records the timestamp and the reviewer's name. Button turns blue when checked.</td>
          </tr>
          <tr>
            <td><strong>★ Approve</strong></td>
            <td>Admin, Study Leader</td>
            <td>Marks the response as approved for final use. Records the timestamp and approver's name. Button turns gold when approved.</td>
          </tr>
        </tbody>
      </table>

      <h3>How to check or approve a response</h3>
      <ol class="step-list">
        <li>Open the <strong>Responses List</strong> or the individual <strong>Response Detail</strong> page.</li>
        <li>Click <strong>✓ Check</strong> to mark the response as checked. The button turns blue to confirm.</li>
        <li>Click again to <strong>undo</strong> the check (the button returns to grey).</li>
        <li>Once checked, a Study Leader or Admin can click <strong>★ Approve</strong>. The button turns gold.</li>
        <li>Click again to undo the approval if needed.</li>
      </ol>

      <div class="callout callout-info">
        <strong>Note:</strong> Check and Approve are independent — a response can be approved without being checked first if needed. Both actions are fully reversible by clicking the button again.
      </div>
    </section>

    {{-- ANALYTICS --}}
    <section id="analytics">
      <h2>Analytics</h2>
      <p>The Analytics page gives a visual summary of all responses collected for a survey. Access it from <strong>Survey Detail → View Analytics</strong>.</p>

      <h3>What you'll find</h3>
      <ul>
        <li><strong>Summary row</strong> — total responses, number of questions analysed, and current survey status.</li>
        <li><strong>Response Timeline</strong> — a bar chart showing how many responses were collected each day. Useful for tracking collection progress over time.</li>
        <li><strong>Numeric Summary cards</strong> — for Number and Rating questions, shows <em>median</em>, <em>mean</em>, <em>min</em>, <em>max</em>, and the number of valid answers (n).</li>
        <li><strong>Distribution charts</strong> — for Single Choice and Multi Select questions, shows a horizontal bar chart of how many respondents chose each option, with percentages.</li>
      </ul>

      <div class="callout callout-tip">
        <strong>Tip:</strong> Analytics updates in real time — every newly submitted or imported response is immediately reflected. Refresh the page to see the latest data.
      </div>
    </section>

    {{-- IMPORTING RESPONSES --}}
    <section id="import">
      <h2>Importing Responses</h2>
      <p>You can bulk-import responses from a CSV file — for example, data collected through STG or another source. The CSV format must exactly match the survey's STG export column structure.</p>

      <h3>How to import</h3>
      <ol class="step-list">
        <li>Go to <strong>Survey Detail → Import Responses (CSV)</strong>.</li>
        <li>On the import page, the right-hand panel shows the <strong>exact column headers</strong> your CSV must contain, in the correct order.</li>
        <li>Prepare your CSV file using those headers. The easiest way is to <strong>download a fresh STG Export</strong> and use that file as a template.</li>
        <li>Click <strong>Choose File</strong>, select your CSV, then click <strong>⬆ Import Responses</strong>.</li>
        <li>After upload, a summary shows how many rows were <em>imported</em>, <em>skipped</em>, or had errors.</li>
      </ol>

      <h3>CSV Format Rules</h3>
      <table class="stg-table">
        <thead><tr><th>Rule</th><th>Details</th></tr></thead>
        <tbody>
          <tr><td>File encoding</td><td>UTF-8. Max file size: 10 MB.</td></tr>
          <tr><td>First row</td><td>Must be column headers — no data in row 1.</td></tr>
          <tr><td><code>Serial</code> column</td><td>Leave blank to auto-generate a new serial. If a serial already exists in this survey, the row is <strong>skipped</strong> (no duplicate).</td></tr>
          <tr><td>Single-choice columns</td><td>Enter the option's <strong>code</strong> (e.g. <code>M</code> for Male, <code>F</code> for Female).</td></tr>
          <tr><td>Multi-select columns</td><td>One column per option. Use <code>1</code> = selected, <code>0</code> or blank = not selected.</td></tr>
          <tr><td>Date columns</td><td>Format: <code>YYYY-MM-DD</code></td></tr>
          <tr><td>Time columns</td><td>Format: <code>HH:MM:SS</code></td></tr>
          <tr><td>Grid columns</td><td>One column per cell: <code>{CODE}_{ROW_CODE}_{COL_CODE}</code></td></tr>
        </tbody>
      </table>

      <h3>Import History</h3>
      <p>The bottom of the import page shows a log of all past imports: date, uploaded filename, who ran the import, row counts, and any row-level errors. Expand the <em>"N error(s)"</em> link to see which rows failed and why.</p>

      <div class="callout callout-warn">
        <strong>Who can import:</strong> Admins, Researchers, Interviewers, Supervisors, and Study Leaders with access to the survey.
      </div>
    </section>

    {{-- STG EXPORT --}}
    <section id="export">
      <h2>STG Export</h2>
      <p>Go to <strong>Survey Detail → Export Data (STG CSV)</strong>. You can filter by status and date range, then click <strong>Download CSV</strong>.</p>

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
        <li><strong>Deactivate</strong> — uncheck <em>"Account is active"</em>. The user can no longer log in but their data is preserved.</li>
      </ul>

      <h4>Available Roles</h4>
      <table class="stg-table">
        <thead><tr><th>Role</th><th>Intended for</th></tr></thead>
        <tbody>
          <tr><td><strong>Admin</strong></td><td>System administrators who manage all surveys and users.</td></tr>
          <tr><td><strong>Researcher</strong></td><td>Staff who design surveys and own the data collection process.</td></tr>
          <tr><td><strong>Interviewer</strong></td><td>Field staff who collect and enter data; need access to specific surveys only.</td></tr>
          <tr><td><strong>Supervisor</strong></td><td>Field supervisors who review and mark responses as checked.</td></tr>
          <tr><td><strong>Study Leader</strong></td><td>Principal investigators who give final approval on responses.</td></tr>
        </tbody>
      </table>

      <div class="callout callout-warn">
        There is currently no self-service password reset. An admin must reset passwords manually via the <strong>Edit User</strong> form.
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
      <p>To change a password: log in → ask an Admin to update it via <strong>Admin → Users → Edit</strong>.</p>
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
